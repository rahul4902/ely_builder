<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyIntegration;
use App\Models\Lead;
use App\Models\User;
use App\Services\CompanyLeadSyncService;
use App\Services\LeadProviders\NinetyNineAcresLeadProvider;
use App\Support\CurrentCompany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompanyLeadIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'tenancy.database.central_connection' => 'sqlite',
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->unsignedBigInteger('teamlead_user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'user_id']);
        });

        Schema::create('source_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('company_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('provider', 64);
            $table->string('name', 120);
            $table->text('credentials');
            $table->text('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('sync_frequency', 32)->default('every_15_minutes');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('provider', 64)->nullable();
            $table->string('provider_lead_id', 128)->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('contact_no', 32)->nullable();
            $table->string('contact_number', 32)->nullable();
            $table->string('project', 128)->nullable();
            $table->string('location', 128)->nullable();
            $table->string('requirement', 128)->nullable();
            $table->string('Budget', 64)->nullable();
            $table->string('source', 64)->nullable();
            $table->text('external_payload')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('user_assigned_id');
            $table->unsignedBigInteger('user_created_id');
            $table->integer('status')->default(1);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function test_99acres_provider_parses_valid_xml_and_normalizes_data(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Resp>
    <Status>SUCCESS</Status>
    <Query>
        <QryDtl>
            <EnqId>99A-1001</EnqId>
            <Name>Rajesh Sharma</Name>
            <Email>rajesh@example.com</Email>
            <CntctNo>+91-9876543210</CntctNo>
            <Project>Green Acres Villa</Project>
            <Location>Whitefield, Bangalore</Location>
            <Requirement>3 BHK Villa</Requirement>
            <Budget>1.5 Cr</Budget>
            <QryDate>2026-09-13 14:30:00</QryDate>
        </QryDtl>
        <QryDtl>
            <EnqId>99A-1002</EnqId>
            <Name></Name>
            <Email></Email>
            <CntctNo>09123456789</CntctNo>
            <Project>Palm Heights</Project>
            <QryDate>2026-09-13 15:00:00</QryDate>
        </QryDtl>
    </Query>
</Resp>
XML;

        $provider = new NinetyNineAcresLeadProvider();
        $leads = $provider->parseXmlResponse($xml);

        $this->assertCount(2, $leads);

        // Lead 1: Full info with +91 stripped
        $this->assertSame('Rajesh Sharma', $leads[0]['name']);
        $this->assertSame('rajesh@example.com', $leads[0]['email']);
        $this->assertSame('9876543210', $leads[0]['contact_no']);
        $this->assertSame('9876543210', $leads[0]['contact_number']);
        $this->assertSame('Green Acres Villa', $leads[0]['project']);
        $this->assertSame('Whitefield, Bangalore', $leads[0]['location']);
        $this->assertSame('3 BHK Villa', $leads[0]['requirement']);
        $this->assertSame('1.5 Cr', $leads[0]['budget']);

        // Lead 2: Empty name generates fallback; leading 0 stripped
        $this->assertSame('99acres Enquiry (6789)', $leads[1]['name']);
        $this->assertSame('9123456789', $leads[1]['contact_no']);
    }

    public function test_99acres_provider_detects_error_xml_and_throws(): void
    {
        $errorXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Resp>
    <Status>FAILED</Status>
    <error>IP 203.0.113.10 is not whitelisted for user 'propfin_crm'</error>
</Resp>
XML;

        $provider = new NinetyNineAcresLeadProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("99acres API error: IP 203.0.113.10 is not whitelisted for user 'propfin_crm'");

        $provider->parseXmlResponse($errorXml);
    }

    public function test_company_sync_service_creates_leads_and_updates_timestamps(): void
    {
        $company = Company::create(['name' => 'PropFin Estates', 'slug' => 'propfin-estates']);
        $admin = User::create(['name' => 'Admin User', 'email' => 'admin@propfin.test', 'password' => Hash::make('secret')]);
        $admin->companies()->attach($company, ['role' => 'company_admin', 'is_active' => true]);

        app(CurrentCompany::class)->set($company);

        $integration = CompanyIntegration::create([
            'company_id' => $company->id,
            'provider' => '99acres',
            'name' => '99acres Test Feed',
            'sync_frequency' => 'every_15_minutes',
            'credentials' => [
                'endpoint' => 'https://example.com/api',
                'username' => 'testuser',
                'password' => 'testpass',
            ],
            'is_active' => true,
        ]);

        $mockProvider = $this->createMock(NinetyNineAcresLeadProvider::class);
        $mockProvider->method('fetch')->willReturn([
            [
                'name' => 'Sunil Kumar',
                'email' => 'sunil@test.com',
                'contact_no' => '9876500001',
                'project' => 'Sunset Boulevard',
                'location' => 'Sector 62',
                'requirement' => '3 BHK',
                'budget' => '1.2 Cr',
                'provider_lead_id' => '99-1',
                'payload' => ['enq_id' => '99-1'],
            ],
            [
                'name' => 'Amit Verma',
                'email' => 'amit@test.com',
                'contact_no' => '9876500002',
                'project' => 'Sky Tower',
                'location' => 'Golf Course Rd',
                'requirement' => '4 BHK',
                'budget' => '2.5 Cr',
                'provider_lead_id' => '99-2',
                'payload' => ['enq_id' => '99-2'],
            ],
        ]);

        $this->app->instance(NinetyNineAcresLeadProvider::class, $mockProvider);
        $syncService = app(CompanyLeadSyncService::class);
        $result = $syncService->sync($integration);

        $this->assertSame(2, $result['received']);
        $this->assertSame(2, $result['created']);
        $this->assertSame(0, $result['duplicates']);

        $integration->refresh();
        $this->assertNotNull($integration->last_synced_at);
        $this->assertNull($integration->last_error);

        // Verify leads in DB
        $leads = Lead::where('company_id', $company->id)->get();
        $this->assertCount(2, $leads);
        $this->assertSame('Sunil Kumar', $leads[0]->name);
        $this->assertSame('9876500001', $leads[0]->contact_no);
        $this->assertSame('Sunset Boulevard', $leads[0]->project);
        $this->assertSame($admin->id, $leads[0]->user_assigned_id);

        // Test second sync with same lead IDs -> should be duplicates!
        $result2 = $syncService->sync($integration);
        $this->assertSame(2, $result2['received']);
        $this->assertSame(0, $result2['created']);
        $this->assertSame(2, $result2['duplicates']);
    }

    public function test_sync_service_captures_error_and_saves_last_error(): void
    {
        $company = Company::create(['name' => 'PropFin', 'slug' => 'propfin']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@test.test', 'password' => Hash::make('secret')]);
        $admin->companies()->attach($company, ['role' => 'company_admin', 'is_active' => true]);

        $integration = CompanyIntegration::create([
            'company_id' => $company->id,
            'provider' => '99acres',
            'name' => 'Failing Feed',
            'sync_frequency' => 'hourly',
            'credentials' => ['endpoint' => 'https://example.com', 'username' => 'u', 'password' => 'p'],
            'is_active' => true,
        ]);

        $mockProvider = $this->createMock(NinetyNineAcresLeadProvider::class);
        $mockProvider->method('fetch')->willThrowException(new \RuntimeException('Connection timed out to 99acres'));
        $this->app->instance(NinetyNineAcresLeadProvider::class, $mockProvider);
        $syncService = app(CompanyLeadSyncService::class);

        try {
            $syncService->sync($integration);
            $this->fail('Expected exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Connection timed out to 99acres', $e->getMessage());
        }

        $integration->refresh();
        $this->assertSame('Connection timed out to 99acres', $integration->last_error);
    }

    public function test_artisan_leads_sync_integrations_with_now_and_frequencies(): void
    {
        $company = Company::create(['name' => 'PropFin', 'slug' => 'propfin-crm']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@crm.test', 'password' => Hash::make('secret')]);
        $admin->companies()->attach($company, ['role' => 'company_admin', 'is_active' => true]);

        // Integration 1: every 15 minutes, never synced -> is due!
        $int1 = CompanyIntegration::create([
            'company_id' => $company->id,
            'provider' => '99acres',
            'name' => 'Int 15m',
            'sync_frequency' => 'every_15_minutes',
            'credentials' => ['endpoint' => 'https://example.com', 'username' => 'u', 'password' => 'p'],
            'is_active' => true,
            'last_synced_at' => null,
        ]);

        // Integration 2: manual -> should NOT be due automatically
        $int2 = CompanyIntegration::create([
            'company_id' => $company->id,
            'provider' => '99acres',
            'name' => 'Int Manual',
            'sync_frequency' => 'manual',
            'credentials' => ['endpoint' => 'https://example.com', 'username' => 'u', 'password' => 'p'],
            'is_active' => true,
            'last_synced_at' => null,
        ]);

        // Bind mock sync service into app
        $mockSyncService = $this->createMock(CompanyLeadSyncService::class);
        $mockSyncService->expects($this->once())
            ->method('sync')
            ->with($this->callback(fn($i) => $i->id === $int1->id))
            ->willReturn(['received' => 1, 'created' => 1, 'duplicates' => 0]);

        $this->app->instance(CompanyLeadSyncService::class, $mockSyncService);

        // Run scheduler command with --now synchronously
        $exitCode = Artisan::call('leads:sync-integrations', ['--now' => true]);
        $this->assertSame(0, $exitCode);
    }
}
