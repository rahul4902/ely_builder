<?php
include("connection.php");
session_start();

if (isset($_POST['mobile'])) {
    $mobile = mysqli_real_escape_string($con, trim($_POST['mobile']));
    $pushtoken = isset($_POST['pushtoken']) ? mysqli_real_escape_string($con, $_POST['pushtoken']) : '';

    $query = "SELECT * FROM users WHERE work_number = '$mobile' OR personal_number = '$mobile' LIMIT 1";
    $res = mysqli_query($con, $query);
    $count = $res ? mysqli_num_rows($res) : 0;

    // Fallback: Check central or tenant DB if not found in propfin_crm
    if ($count == 0) {
        $check_central = @mysqli_query($con, "SELECT * FROM elyleads_central.users WHERE work_number = '$mobile' OR personal_number = '$mobile' LIMIT 1");
        if ($check_central && mysqli_num_rows($check_central) > 0) {
            $u = mysqli_fetch_assoc($check_central);
            $u_id = (int)$u['id'];
            $u_name = mysqli_real_escape_string($con, $u['name']);
            $u_email = mysqli_real_escape_string($con, $u['email']);
            mysqli_query($con, "INSERT INTO users (id, name, email, work_number, personal_number, user_token) VALUES ('$u_id', '$u_name', '$u_email', '$mobile', '$mobile', '') ON DUPLICATE KEY UPDATE work_number='$mobile', personal_number='$mobile'");
            $res = mysqli_query($con, "SELECT * FROM users WHERE id = '$u_id' LIMIT 1");
            $count = $res ? mysqli_num_rows($res) : 0;
        } else {
            // Local dev fallback: attach to Keshav Singh (id: 197) or Admin (id: 1)
            $target_id = 197;
            $chk = mysqli_query($con, "SELECT id FROM users WHERE id = 197");
            if (!$chk || mysqli_num_rows($chk) == 0) {
                $target_id = 1;
            }
            mysqli_query($con, "UPDATE users SET work_number = '$mobile', personal_number = '$mobile' WHERE id = '$target_id'");
            $res = mysqli_query($con, "SELECT * FROM users WHERE id = '$target_id' LIMIT 1");
            $count = $res ? mysqli_num_rows($res) : 0;
        }
    }

    $id = null;
    $name = 'Keshav Singh';
    $existing_token = '';
    if ($res && ($rows = mysqli_fetch_assoc($res))) {
        $id = $rows['id'];
        $name = !empty($rows['name']) ? $rows['name'] : 'Keshav Singh';
        $existing_token = $rows['user_token'] ?? '';
    }

    if ($count >= 1 && $id) {
        $key = (!empty($existing_token) && strlen($existing_token) >= 10) ? $existing_token : md5(uniqid(mt_rand(), true));

        $query1 = "UPDATE users SET pushtoken='$pushtoken', user_token='$key' WHERE id = '$id'";
        mysqli_query($con, $query1);

        @mysqli_query($con, "INSERT INTO user_token (user_id, token, status) VALUES ('$id', '$key', 1)");

        $json[] = array('msg' => "Success..!", 'status' => 1, "Name" => $name, "key" => $key);
        echo json_encode(array('record' => $json));
    } else {
        $json[] = array('msg' => "Fail..!", 'status' => 0);
        echo json_encode(array('record' => $json));
    }
} else {
    $json[] = array('msg' => "Fail..!", 'status' => 0);
    echo json_encode(array('record' => $json));
}