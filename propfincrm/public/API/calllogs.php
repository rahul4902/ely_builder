<?php 
include("connection.php");
date_default_timezone_set('Asia/Kolkata');

if (isset($_POST['key'])) {
    $token = mysqli_real_escape_string($con, $_POST['key']);

    $sql_token = "SELECT * FROM users WHERE user_token = '$token' LIMIT 1";
    $res_token = mysqli_query($con, $sql_token);

    $user_id = null;
    if ($res_token && ($rows = mysqli_fetch_assoc($res_token))) {
        $user_id = $rows['id'];
    }

    if ($user_id) {
        $data = array();

        $sql = "SELECT t1.*, 
                       TIMESTAMPDIFF(SECOND, t1.call_start_datetime, t1.call_end_datetime) AS duration_seconds,
                       COALESCE(NULLIF(t3.name, ''), CONCAT('Lead #', t1.lead_id)) AS name, 
                       COALESCE(NULLIF(t3.contact_no, ''), '') AS contact_no 
                FROM call_logs t1 
                LEFT JOIN users t2 ON t1.user_id = t2.id 
                LEFT JOIN leads t3 ON t3.id = t1.lead_id 
                WHERE t1.user_id = '$user_id' 
                ORDER BY t1.call_start_datetime DESC 
                LIMIT 0, 100";

        mysqli_set_charset($con, "utf8");
        $res1 = mysqli_query($con, $sql);

        if ($res1) {
            while ($row = mysqli_fetch_assoc($res1)) {
                $data[] = $row;
            }
        }

        echo json_encode(array('calllogs' => $data));
    } else {
        echo json_encode(array('calllogs' => array(), 'message' => array(array('msg' => 'no record found..!', 'status' => 0))));
    }
} else {
    echo json_encode(array('calllogs' => array(), 'message' => array(array('msg' => 'You are not login ..!', 'status' => 0))));
}
