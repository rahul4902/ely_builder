<?php

namespace App\Http\Controllers;

use App\Models\CompanyIntegration;
use App\Services\CompanyLeadSyncService;
use App\Services\LeadProviders\MetaLeadAdsProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LeadWebhookController extends Controller
{
    /** Meta's subscription handshake. Configure this exact callback URL in Meta. */
    public function verifyMeta(Request $request, int $integration)
    {
        $connection = $this->activeConnection($integration, 'meta_lead_ads');
        $verifyToken = $connection->credentials['webhook_verify_token'] ?? null;
        if ($request->query('hub_mode') !== 'subscribe' || !$verifyToken || !hash_equals($verifyToken, (string) $request->query('hub_verify_token'))) {
            abort(403, 'Webhook verification failed.');
        }
        return response((string) $request->query('hub_challenge'), 200)->header('Content-Type', 'text/plain');
    }

    /** Receives a Meta leadgen notification, then retrieves the full lead securely from Graph API. */
    public function receiveMeta(Request $request, int $integration, CompanyLeadSyncService $imports, MetaLeadAdsProvider $meta)
    {
        $connection = $this->activeConnection($integration, 'meta_lead_ads');
        $this->assertMetaSignature($request, $connection);

        try {
            foreach ((array) $request->input('entry', []) as $entry) {
                foreach ((array) ($entry['changes'] ?? []) as $change) {
                    $value = $change['value'] ?? [];
                    if (($change['field'] ?? null) !== 'leadgen' || empty($value['leadgen_id'])) {
                        continue;
                    }
                    if (!empty($connection->credentials['page_id']) && (string) ($value['page_id'] ?? '') !== (string) $connection->credentials['page_id']) {
                        continue;
                    }
                    $imports->import($connection, [$meta->fetchLead($connection, (string) $value['leadgen_id'])]);
                }
            }
            $connection->update(['last_synced_at' => now(), 'last_error' => null]);
        } catch (\Throwable $e) {
            $connection->update(['last_error' => $e->getMessage()]);
            Log::warning('Meta lead webhook failed', ['integration_id' => $connection->id, 'error' => $e->getMessage()]);
            // A non-2xx response asks Meta to retry a transient Graph API or
            // database failure instead of silently losing a lead.
            return response()->json(['received' => false], 500);
        }
        return response()->json(['received' => true]);
    }

    /** Receive standard JSON leads from a website or any compatible webhook sender. */
    public function receiveGeneric(Request $request, int $integration, string $token, CompanyLeadSyncService $imports)
    {
        $connection = CompanyIntegration::withoutGlobalScopes()->findOrFail($integration);
        abort_unless($connection->is_active && in_array($connection->provider, ['website_api', 'generic_webhook', 'google_forms'], true), 404);
        abort_unless(hash_equals((string) ($connection->credentials['webhook_token'] ?? ''), $token), 404);

        $secret = $connection->credentials['webhook_secret'] ?? null;
        if ($secret && !hash_equals($secret, (string) $request->header('X-ElyLeads-Signature'))) {
            abort(401, 'Invalid webhook signature.');
        }
        $payload = $request->json()->all() ?: $request->all();
        $mapping = $connection->configuration['field_map'] ?? [];
        $get = static function (string $field) use ($payload, $mapping) {
            $key = $mapping[$field] ?? $field;
            return data_get($payload, $key);
        };
        $phone = preg_replace('/\D+/', '', (string) $get('phone'));
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) $phone = substr($phone, 2);
        if (strlen($phone) < 7 || strlen($phone) > 15) throw new RuntimeException('A valid phone value is required.');

        $imports->import($connection, [[
            'name' => (string) ($get('name') ?: 'Website lead (' . substr($phone, -4) . ')'), 'contact_no' => $phone,
            'email' => $get('email'), 'project' => $get('project'), 'location' => $get('location'), 'requirement' => $get('requirement'), 'budget' => $get('budget'),
            'provider_lead_id' => (string) ($get('id') ?: sha1(json_encode($payload))), 'payload' => $payload,
        ]]);
        $connection->update(['last_synced_at' => now(), 'last_error' => null]);
        return response()->json(['received' => true], 202);
    }

    private function activeConnection(int $id, string $provider): CompanyIntegration
    {
        $connection = CompanyIntegration::withoutGlobalScopes()->findOrFail($id);
        abort_unless($connection->is_active && $connection->provider === $provider, 404);
        return $connection;
    }

    private function assertMetaSignature(Request $request, CompanyIntegration $connection): void
    {
        $secret = $connection->credentials['app_secret'] ?? null;
        if (!$secret) throw new RuntimeException('Meta App Secret is missing.');
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        abort_unless(hash_equals($expected, (string) $request->header('X-Hub-Signature-256')), 401, 'Invalid Meta signature.');
    }
}
