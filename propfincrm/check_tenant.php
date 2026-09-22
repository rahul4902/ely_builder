<?php
$con = mysqli_connect("127.0.0.1", "root", "", "propfin_crm");
if (!$con) die("Failed: " . mysqli_connect_error());

echo "=== TENANTS TABLE ===\n";
$res = mysqli_query($con, "SELECT * FROM tenants LIMIT 10");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        echo json_encode($r) . "\n";
    }
} else {
    echo "Error querying tenants: " . mysqli_error($con) . "\n";
}

echo "\n=== COMPANY_USER TABLE ===\n";
$res2 = mysqli_query($con, "SELECT * FROM company_user LIMIT 10");
if ($res2) {
    while ($r = mysqli_fetch_assoc($res2)) {
        echo json_encode($r) . "\n";
    }
} else {
    echo "Error querying company_user: " . mysqli_error($con) . "\n";
}

echo "\n=== CHECK admin_tenant_master DATABASE ===\n";
$con2 = @mysqli_connect("127.0.0.1", "root", "", "admin_tenant_master");
if ($con2) {
    $r = mysqli_query($con2, "SHOW TABLES");
    while ($t = mysqli_fetch_row($r)) {
        echo "Table: {$t[0]}\n";
    }
    // Check companies/tenants in admin_tenant_master
    $tRes = mysqli_query($con2, "SELECT * FROM tenants LIMIT 5");
    if ($tRes) {
        while ($row = mysqli_fetch_assoc($tRes)) {
            echo "Tenant: " . json_encode($row) . "\n";
        }
    }
}
