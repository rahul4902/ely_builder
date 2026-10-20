<?php

namespace Tests\Unit;

use App\Services\LeadProviders\MetaLeadAdsProvider;
use PHPUnit\Framework\TestCase;

class MetaLeadAdsProviderTest extends TestCase
{
    public function test_it_normalizes_a_standard_meta_instant_form_lead(): void
    {
        $lead = (new MetaLeadAdsProvider())->normalizeLead([
            'id' => 'meta-lead-42',
            'field_data' => [
                ['name' => 'full_name', 'values' => ['Asha Mehta']],
                ['name' => 'phone_number', 'values' => ['+91 98765 43210']],
                ['name' => 'email', 'values' => ['asha@example.test']],
                ['name' => 'project', 'values' => ['Skyline Residences']],
            ],
        ]);

        $this->assertSame('Asha Mehta', $lead['name']);
        $this->assertSame('9876543210', $lead['contact_no']);
        $this->assertSame('asha@example.test', $lead['email']);
        $this->assertSame('Skyline Residences', $lead['project']);
        $this->assertSame('meta-lead-42', $lead['provider_lead_id']);
    }

    public function test_it_uses_configured_custom_question_names_without_dropping_international_phone_numbers(): void
    {
        $lead = (new MetaLeadAdsProvider())->normalizeLead([
            'id' => 'meta-lead-custom',
            'field_data' => [
                ['name' => 'customer name', 'values' => ['Maya Chen']],
                ['name' => 'contact mobile', 'values' => ['+1 (415) 555-0188']],
                ['name' => 'preferred community', 'values' => ['North Point']],
            ],
        ], [
            'name' => 'customer name',
            'phone' => 'contact mobile',
            'project' => 'preferred community',
        ]);

        $this->assertSame('Maya Chen', $lead['name']);
        $this->assertSame('14155550188', $lead['contact_no']);
        $this->assertSame('North Point', $lead['project']);
    }
}
