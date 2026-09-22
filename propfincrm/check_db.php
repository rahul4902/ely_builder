<?php
$con = mysqli_connect("127.0.0.1", "root", "", "propfin_crm");
if (!$con) die("Connection failed: " . mysqli_connect_error());

echo "=== CHECK 9716164902 ===\n";
$r = mysqli_query($con, "SELECT id, name, email, work_number, personal_number FROM users WHERE work_number = '9716164902' OR personal_number = '9716164902'");
$found = false;
while ($row = mysqli_fetch_assoc($r)) {
    $found = true;
    echo "Found user: ID={$row['id']}, Name={$row['name']}, Work={$row['work_number']}\n";
}
if (!$found) {
    echo "9716164902 does not exist yet in users.\n";
}

echo "\n=== MANAGERS / TEAM LEADERS (Role 2) ===\n";
$mgr = mysqli_query($con, "SELECT u.id, u.name, u.work_number, count(l.id) as lead_count 
                           FROM users u 
                           JOIN role_user ru ON u.id = ru.user_id 
                           LEFT JOIN leads l ON u.id = l.user_assigned_id 
                           WHERE ru.role_id = 2 
                           GROUP BY u.id");
while ($row = mysqli_fetch_assoc($mgr)) {
    echo "Manager: ID={$row['id']} | Name={$row['name']} | Work: {$row['work_number']} | Leads: {$row['lead_count']}\n";
}

echo "\n=== ADMINS (Role 1 or 5) ===\n";
$adm = mysqli_query($con, "SELECT u.id, u.name, u.work_number, r.name as role_name, count(l.id) as lead_count 
                           FROM users u 
                           JOIN role_user ru ON u.id = ru.user_id 
                           JOIN roles r ON ru.role_id = r.id 
                           LEFT JOIN leads l ON u.id = l.user_assigned_id 
                           WHERE ru.role_id IN (1, 5) 
                           GROUP BY u.id");
while ($row = mysqli_fetch_assoc($adm)) {
    echo "Admin: ID={$row['id']} | Name={$row['name']} | Work: {$row['work_number']} | Role: {$row['role_name']} | Leads: {$row['lead_count']}\n";
}
