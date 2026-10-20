<?php
ini_set('display_errors', '0');
error_reporting(0);

// Enable CORS for web requests from mobile app dev server / localhost
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Connect to MySQL server (without hardcoding a specific database)
$db_passwords = ["", "C@273aDWqw6"];
$con = null;
foreach (['127.0.0.1', 'localhost'] as $host) {
    foreach ($db_passwords as $pwd) {
        $con = @mysqli_connect($host, "root", $pwd);
        if ($con) break 2;
    }
}

if (!$con) {
    echo json_encode(["record" => [["msg" => "Database connection fail: " . mysqli_connect_error(), "status" => 0]]]);
    exit;
}

// Default database
$target_db = 'tenant8c327d11-e9cd-4b8f-b6f2-e0feba930967';

// Dynamically locate user's workspace/tenant database by key or mobile
if (isset($_POST['key']) && !empty($_POST['key'])) {
    $incoming_key = mysqli_real_escape_string($con, trim($_POST['key']));
    $tenant_list = @mysqli_query($con, "SHOW DATABASES LIKE 'tenant%'");
    $found = false;
    if ($tenant_list) {
        while ($t_row = mysqli_fetch_row($tenant_list)) {
            $tdb = $t_row[0];
            $chk = @mysqli_query($con, "SELECT id FROM `$tdb`.users WHERE user_token = '$incoming_key' LIMIT 1");
            if ($chk && mysqli_num_rows($chk) > 0) {
                $target_db = $tdb;
                $found = true;
                break;
            }
        }
    }
    if (!$found) {
        $chk_propfin = @mysqli_query($con, "SELECT id FROM `propfin_crm`.users WHERE user_token = '$incoming_key' LIMIT 1");
        if ($chk_propfin && mysqli_num_rows($chk_propfin) > 0) {
            $target_db = 'propfin_crm';
        }
    }
} elseif (isset($_POST['mobile']) && !empty($_POST['mobile'])) {
    $incoming_mob = mysqli_real_escape_string($con, trim($_POST['mobile']));
    $tenant_list = @mysqli_query($con, "SHOW DATABASES LIKE 'tenant%'");
    $found = false;
    if ($tenant_list) {
        while ($t_row = mysqli_fetch_row($tenant_list)) {
            $tdb = $t_row[0];
            $chk = @mysqli_query($con, "SELECT id FROM `$tdb`.users WHERE work_number = '$incoming_mob' OR personal_number = '$incoming_mob' LIMIT 1");
            if ($chk && mysqli_num_rows($chk) > 0) {
                $target_db = $tdb;
                $found = true;
                break;
            }
        }
    }
    if (!$found) {
        $chk_propfin = @mysqli_query($con, "SELECT id FROM `propfin_crm`.users WHERE work_number = '$incoming_mob' OR personal_number = '$incoming_mob' LIMIT 1");
        if ($chk_propfin && mysqli_num_rows($chk_propfin) > 0) {
            $target_db = 'propfin_crm';
        }
    }
}

mysqli_select_db($con, $target_db);

// Ensure active client session token is resolved seamlessly in the selected database
if (isset($_POST['key']) && !empty($_POST['key'])) {
    $incoming_key = mysqli_real_escape_string($con, trim($_POST['key']));
    if (strlen($incoming_key) >= 10) {
        $check_q = mysqli_query($con, "SELECT id FROM users WHERE user_token = '$incoming_key' LIMIT 1");
        if (!$check_q || mysqli_num_rows($check_q) == 0) {
            $ut_q = mysqli_query($con, "SELECT user_id FROM user_token WHERE token = '$incoming_key' LIMIT 1");
            if ($ut_q && ($ut_row = mysqli_fetch_assoc($ut_q))) {
                $matched_uid = (int)$ut_row['user_id'];
                mysqli_query($con, "UPDATE users SET user_token = '$incoming_key' WHERE id = '$matched_uid'");
            }
        }
    }
}
?>
