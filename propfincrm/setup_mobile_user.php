<?php
$databases = ['propfin_crm', 'tenant8c327d11-e9cd-4b8f-b6f2-e0feba930967', 'elyleads_central'];
foreach ($databases as $dbName) {
    $con = @mysqli_connect('127.0.0.1', 'root', '', $dbName);
    if ($con) {
        mysqli_query($con, "UPDATE users SET work_number = '9312413520', personal_number = '9312413520', user_token = '9716164902' WHERE id = 197 OR work_number = '9312413520'");
        @mysqli_query($con, "INSERT INTO user_token (user_id, token, status) VALUES (197, '9716164902', 1) ON DUPLICATE KEY UPDATE status = 1");
        echo "Updated user in $dbName\n";
    }
}
echo "Done.\n";
