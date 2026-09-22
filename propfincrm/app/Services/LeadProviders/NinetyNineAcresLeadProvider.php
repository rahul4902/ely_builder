<?php

namespace App\Services\LeadProviders;

use App\Models\CompanyIntegration;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NinetyNineAcresLeadProvider implements LeadProviderContract
{
    public function fetch(CompanyIntegration $integration): array
    {
        $credentials = $integration->credentials;
        $config = $integration->configuration ?? [];
        $endpoint = $credentials['endpoint'] ?? $config['endpoint'] ?? null;
        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$endpoint || !$username || !$password) {
            throw new RuntimeException('99acres endpoint, username, and password are required.');
        }

        $end = now();
        if ($integration->last_synced_at) {
            // Overlap by 15 minutes to guarantee no leads are missed at boundaries
            $start = $integration->last_synced_at->copy()->subMinutes(15);
        } else {
            $syncDays = (int) ($config['sync_days'] ?? 2);
            $start = $end->copy()->subDays(max(1, $syncDays));
        }

        $xml = sprintf(
            "<?xml version='1.0'?><query><user_name>%s</user_name><pswd>%s</pswd><start_date>%s</start_date><end_date>%s</end_date></query>",
            htmlspecialchars($username, ENT_XML1),
            htmlspecialchars($password, ENT_XML1),
            $start->format('Y-m-d H:i:s'),
            $end->format('Y-m-d H:i:s')
        );

        $response = Http::timeout(60)->withoutVerifying()->asForm()->post($endpoint, ['xml' => $xml]);
        $response->throw();

        return $this->parseXmlResponse($response->body());
    }

    /**
     * Parse 99acres XML response body into normalized lead arrays.
     *
     * @param string $xmlString
     * @return array
     */
    public function parseXmlResponse(string $xmlString): array
    {
        if (trim($xmlString) === '') {
            return [];
        }

        $xmlResponse = @simplexml_load_string($xmlString, \SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
        if (!$xmlResponse) {
            throw new RuntimeException('99acres returned invalid XML: ' . substr(strip_tags($xmlString), 0, 150));
        }

        // Check for 99acres error responses
        if (isset($xmlResponse->error) && trim((string) $xmlResponse->error) !== '') {
            throw new RuntimeException('99acres API error: ' . trim((string) $xmlResponse->error));
        }
        if (isset($xmlResponse->Error) && trim((string) $xmlResponse->Error) !== '') {
            throw new RuntimeException('99acres API error: ' . trim((string) $xmlResponse->Error));
        }
        if (strtolower((string) $xmlResponse->getName()) === 'error') {
            throw new RuntimeException('99acres API error: ' . trim((string) $xmlResponse));
        }
        if (isset($xmlResponse->status) && in_array(strtoupper((string) $xmlResponse->status), ['FAILED', 'ERROR', 'FAIL'], true)) {
            $msg = (string) ($xmlResponse->message ?? $xmlResponse->msg ?? $xmlResponse->status);
            throw new RuntimeException('99acres API error: ' . trim($msg));
        }
        if (isset($xmlResponse->Resp->error) && trim((string) $xmlResponse->Resp->error) !== '') {
            throw new RuntimeException('99acres API error: ' . trim((string) $xmlResponse->Resp->error));
        }
        if (isset($xmlResponse->Resp->Error) && trim((string) $xmlResponse->Resp->Error) !== '') {
            throw new RuntimeException('99acres API error: ' . trim((string) $xmlResponse->Resp->Error));
        }
        if (isset($xmlResponse->Resp->status) && in_array(strtoupper((string) $xmlResponse->Resp->status), ['FAILED', 'ERROR', 'FAIL'], true)) {
            $msg = (string) ($xmlResponse->Resp->msg ?? $xmlResponse->Resp->message ?? $xmlResponse->Resp->status);
            throw new RuntimeException('99acres API error: ' . trim($msg));
        }

        $leads = [];
        $items = $xmlResponse->xpath('//QryDtl');
        if (empty($items)) {
            $items = $xmlResponse->xpath('//Resp');
        }
        if (empty($items)) {
            $items = [$xmlResponse];
        }

        foreach ($items as $item) {
            $rawPhone = (string) (
                $item->CntctNo ??
                $item->Phone ??
                $item->Mobile ??
                $item->CntctDtl->Phone ??
                $item->CntctDtl->Mobile ??
                $item->CntctDtl->CntctNo ??
                ''
            );
            $phone = preg_replace('/\D+/', '', $rawPhone);

            // Strip India country code (+91 / 91) or leading zero
            if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
                $phone = substr($phone, 2);
            } elseif (strlen($phone) === 11 && str_starts_with($phone, '0')) {
                $phone = substr($phone, 1);
            }

            // Must have a valid 10-digit mobile number
            if (strlen($phone) !== 10) {
                continue;
            }

            $name = trim((string) (
                $item->Name ??
                $item->CustName ??
                $item->CntctDtl->Name ??
                ''
            ));
            if ($name === '') {
                $name = '99acres Enquiry (' . substr($phone, -4) . ')';
            }

            $email = trim((string) (
                $item->Email ??
                $item->CntctDtl->Email ??
                ''
            ));

            $project = trim((string) (
                $item->Project ??
                $item->ProjName ??
                $item->ProjectName ??
                $item->QryDtl->ProjName ??
                ''
            ));

            $location = trim((string) (
                $item->Location ??
                $item->Locality ??
                $item->City ??
                $item->QryDtl->Locality ??
                $item->QryDtl->City ??
                ''
            ));

            $requirement = trim((string) (
                $item->Requirement ??
                $item->Bedrooms ??
                $item->QryDtl->Bedrooms ??
                $item->QryDtl->Requirement ??
                ''
            ));

            $budget = trim((string) (
                $item->Budget ??
                $item->Price ??
                $item->QryDtl->Price ??
                $item->QryDtl->Budget ??
                ''
            ));

            $leadId = (string) (
                $item->EnqId ??
                $item->EnquiryId ??
                $item->LeadId ??
                sha1((string) $item->asXML())
            );

            $leads[] = [
                'name' => $name,
                'contact_no' => $phone,
                'contact_number' => $phone,
                'email' => $email ?: null,
                'project' => $project ?: null,
                'location' => $location ?: null,
                'requirement' => $requirement ?: null,
                'budget' => $budget ?: null,
                'provider_lead_id' => $leadId,
                'payload' => json_decode(json_encode($item), true),
            ];
        }

        return $leads;
    }
}

