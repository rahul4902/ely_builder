<?php

declare(strict_types=1);

require __DIR__ . '/connection.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
date_default_timezone_set('Asia/Kolkata');

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function normalizeIndianPhone(string $phone): ?string
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
        $digits = substr($digits, 2);
    }

    return strlen($digits) === 10 ? $digits : null;
}

function redactedPhone(string $phone): string
{
    return strlen($phone) > 4 ? str_repeat('*', strlen($phone) - 4) . substr($phone, -4) : 'invalid';
}

function addIssue(array &$summary, array $issue): void
{
    // Keep large imports bounded while still returning enough detail to investigate.
    if (count($summary['issue_samples']) < 100) {
        $summary['issue_samples'][] = $issue;
        return;
    }

    $summary['issues_truncated']++;
}

function fetch99AcresLeads(string $url, array $params): string
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_FAILONERROR => false,
    ]);

    $body = curl_exec($curl);
    $error = curl_error($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($body === false || $error !== '' || $status < 200 || $status >= 300) {
        throw new RuntimeException('99acres request failed' . ($status ? " (HTTP {$status})" : '') . ($error ? ": {$error}" : ''));
    }

    return $body;
}

if (!isset($con) || mysqli_connect_errno()) {
    error_log('99acres importer: database connection failed.');
    respond(503, ['ok' => false, 'error' => 'Database connection is unavailable.']);
}

mysqli_set_charset($con, 'utf8mb4');

try {
    $end = new DateTimeImmutable('now');
    $start = $end->sub(new DateInterval('P1D'));

    // Configure these through the environment before rotating the vendor credentials.
    $url = getenv('NINETY_NINE_ACRES_URL') ?: 'https://www.99acres.com/99api/v1/getmy99Response/OeAuXClO43hwseaXEQ/uid/';
    $username = getenv('NINETY_NINE_ACRES_USERNAME') ?: 'pf@99acres';
    $password = getenv('NINETY_NINE_ACRES_PASSWORD') ?: 'Help@99acres';
    $xml = sprintf(
        "<?xml version='1.0'?><query><user_name>%s</user_name><pswd>%s</pswd><start_date>%s</start_date><end_date>%s</end_date></query>",
        htmlspecialchars($username, ENT_XML1),
        htmlspecialchars($password, ENT_XML1),
        $start->format('Y-m-d H:i:s'),
        $end->format('Y-m-d H:i:s')
    );

    $response = fetch99AcresLeads($url, ['xml' => $xml]);
    libxml_use_internal_errors(true);
    $content = simplexml_load_string($response, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
    if ($content === false) {
        throw new RuntimeException('99acres returned invalid XML.');
    }

    $findExisting = mysqli_prepare($con, 'SELECT id, status FROM leads WHERE contact_no = ? LIMIT 1');
    $insert = mysqli_prepare(
        $con,
        'INSERT INTO leads (name, contact_no, email, source, country, project, status, user_assigned_id, client_id, user_created_id, created_at, lead_type) VALUES (?, ?, ?, ?, ?, ?, 1, 1, 1, 1, NOW(), ?)'
    );
    if (!$findExisting || !$insert) {
        throw new RuntimeException('Could not prepare lead import queries.');
    }

    $summary = ['received' => 0, 'inserted' => 0, 'duplicates' => 0, 'rejected' => 0, 'failed' => 0, 'issue_samples' => [], 'issues_truncated' => 0];

    foreach ($content->Resp as $item) {
        $summary['received']++;
        $name = trim((string) $item->CntctDtl->Name);
        $email = trim((string) $item->CntctDtl->Email);
        $phone = normalizeIndianPhone((string) $item->CntctDtl->Phone);
        $project = trim((string) $item->QryDtl->ProjName);

        if ($name === '' || $phone === null) {
            $summary['rejected']++;
            addIssue($summary, ['type' => 'invalid_lead', 'phone' => redactedPhone((string) $item->CntctDtl->Phone)]);
            continue;
        }

        mysqli_stmt_bind_param($findExisting, 's', $phone);
        if (!mysqli_stmt_execute($findExisting)) {
            $summary['failed']++;
            error_log('99acres importer: duplicate lookup failed.');
            continue;
        }

        $existing = mysqli_stmt_get_result($findExisting);
        if ($existingLead = mysqli_fetch_assoc($existing)) {
            $summary['duplicates']++;
            addIssue($summary, ['type' => 'duplicate_phone', 'phone' => redactedPhone($phone), 'existing_status' => (int) $existingLead['status']]);
            continue;
        }

        $source = '99acres';
        $country = 'India';
        $leadType = 'Hot';
        mysqli_stmt_bind_param($insert, 'sssssss', $name, $phone, $email, $source, $country, $project, $leadType);
        if (!mysqli_stmt_execute($insert)) {
            $summary['failed']++;
            error_log('99acres importer: insert failed for phone ending ' . substr($phone, -4) . '.');
            continue;
        }

        $summary['inserted']++;
    }

    mysqli_stmt_close($findExisting);
    mysqli_stmt_close($insert);
    respond($summary['failed'] > 0 ? 207 : 200, [
        'ok' => $summary['failed'] === 0,
        'window' => ['from' => $start->format(DateTimeInterface::ATOM), 'to' => $end->format(DateTimeInterface::ATOM)],
        'summary' => $summary,
    ]);
} catch (Throwable $exception) {
    error_log('99acres importer: ' . $exception->getMessage());
    respond(502, ['ok' => false, 'error' => 'Lead import failed. Check the server error log.']);
}
