<?php
$con = mysqli_connect("127.0.0.1", "root", "", "propfin_crm");
if (!$con) die("Failed to connect: " . mysqli_connect_error());

// Find the user with the most data (leads, followups, calls)
$res = mysqli_query($con, "SELECT user_assigned_id, count(*) as c FROM leads WHERE user_assigned_id IS NOT NULL GROUP BY user_assigned_id ORDER BY c DESC LIMIT 1");
$topUser = mysqli_fetch_assoc($res);
$topUserId = $topUser['user_assigned_id'];

echo "User with most leads is ID: {$topUserId} with {$topUser['c']} leads.\n";

// Fetch this user
$uRes = mysqli_query($con, "SELECT * FROM users WHERE id = {$topUserId}");
$user = mysqli_fetch_assoc($uRes);
echo "Original user details: Name={$user['name']}, Email={$user['email']}, WorkNumber={$user['work_number']}\n";

// Update work_number to 9716164902
$update = mysqli_query($con, "UPDATE users SET work_number = '9716164902' WHERE id = {$topUserId}");
if ($update) {
    echo "SUCCESS: Updated work_number to '9716164902' for User ID {$topUserId} ({$user['name']})!\n";
} else {
    echo "ERROR: " . mysqli_error($con) . "\n";
}

// Verify login API with 9716164902
echo "\nVerifying login query:\n";
$check = mysqli_query($con, "SELECT id, name, work_number, user_token FROM users WHERE work_number = '9716164902'");
while ($r = mysqli_fetch_assoc($check)) {
    echo "Verified User: ID={$r['id']}, Name={$r['name']}, WorkNumber={$r['work_number']}\n";
}
