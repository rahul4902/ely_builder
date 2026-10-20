<?php

namespace App\Services\LeadProviders;

use App\Models\CompanyIntegration;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/** Meta Graph API adapter for Facebook and Instagram instant-form leads. */
class MetaLeadAdsProvider implements LeadProviderContract
{
    private const API_VERSION = 'v22.0';

    public function fetch(CompanyIntegration $integration): array
    {
        $credentials = $integration->credentials ?? [];
        $formId = $credentials['form_id'] ?? ($integration->configuration['form_id'] ?? null);
        if (!$formId) {
            throw new RuntimeException('A Meta instant Form ID is required to run a historical test sync. New leads can still arrive through the webhook after setup.');
        }

        $response = $this->client($credentials)->get($this->url($formId . '/leads'), [
            'fields' => 'id,created_time,field_data,ad_id,form_id',
            'limit' => 100,
        ]);
        $response->throw();

        return array_map(fn ($lead) => $this->normalizeLead($lead, $integration->configuration['meta_field_map'] ?? []), $response->json('data', []));
    }

    public function fetchLead(CompanyIntegration $integration, string $leadId): array
    {
        $response = $this->client($integration->credentials ?? [])->get($this->url($leadId), [
            'fields' => 'id,created_time,field_data,ad_id,form_id',
        ]);
        $response->throw();
        return $this->normalizeLead($response->json(), $integration->configuration['meta_field_map'] ?? []);
    }

    public function normalizeLead(array $lead, array $fieldMap = []): array
    {
        $fields = [];
        foreach (($lead['field_data'] ?? []) as $field) {
            $key = strtolower((string) ($field['name'] ?? ''));
            $fields[$key] = implode(', ', (array) ($field['values'] ?? []));
        }
        $value = static function (string $field, array $aliases) use ($fields, $fieldMap): ?string {
            $keys = array_filter(array_merge([(string) ($fieldMap[$field] ?? '')], $aliases));
            foreach ($keys as $key) {
                $key = strtolower(trim($key));
                if (!empty($fields[$key])) {
                    return trim($fields[$key]);
                }
            }
            return null;
        };
        $phone = preg_replace('/\D+/', '', (string) $value('phone', ['phone_number', 'phone', 'mobile_number', 'mobile']));
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) < 7 || strlen($phone) > 15) {
            throw new RuntimeException('Meta lead ' . ($lead['id'] ?? 'unknown') . ' has no valid phone number. Add the Phone number field to the Meta instant form.');
        }

        $name = $value('name', ['full_name', 'name']);
        if (!$name) {
            $name = trim(implode(' ', array_filter([$value('first_name', ['first_name']), $value('last_name', ['last_name'])])));
        }
        $name = $name ?: 'Meta lead (' . substr($phone, -4) . ')';
        return [
            'name' => $name,
            'contact_no' => $phone,
            'email' => $value('email', ['email', 'email_address']),
            'project' => $value('project', ['project', 'project_name']),
            'location' => $value('location', ['city', 'location', 'locality']),
            'requirement' => $value('requirement', ['requirement', 'property_type', 'bedrooms']),
            'budget' => $value('budget', ['budget', 'price_range']),
            'provider_lead_id' => (string) ($lead['id'] ?? sha1(json_encode($lead))),
            'payload' => $lead,
        ];
    }

    private function client(array $credentials)
    {
        $token = $credentials['access_token'] ?? null;
        if (!$token) {
            throw new RuntimeException('A Meta long-lived Page access token is required.');
        }
        return Http::timeout(30)->acceptJson()->withToken($token);
    }

    private function url(string $path): string
    {
        return 'https://graph.facebook.com/' . self::API_VERSION . '/' . ltrim($path, '/');
    }
}
