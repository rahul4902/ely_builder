<?php
$con = mysqli_connect("127.0.0.1", "root", "", "propfin_crm");
if (!$con) die("Failed to connect: " . mysqli_connect_error());

$query = "
SELECT 
    u.id, 
    u.name, 
    u.work_number, 
    r.name as role_slug,
    r.display_name as role_name,
    count(l.id) as lead_count
FROM users u
JOIN role_user ru ON u.id = ru.user_id
JOIN roles r ON ru.role_id = r.id
LEFT JOIN leads l ON u.id = l.user_assigned_id
WHERE u.work_number IS NOT NULL AND u.work_number != ''
GROUP BY u.id, r.name, r.display_name
ORDER BY r.id ASC, lead_count DESC
";

$res = mysqli_query($con, $query);
echo json_encode(mysqli_fetch_all($res, MYSQLI_ASSOC), JSON_PRETTY_PRINT);
