<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


include("connection.php");
// session_start();
try {
 
    if (isset($_POST['mobile'])) {
        echo "in5";


        $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
        $pwd = mysqli_real_escape_string($con, $_POST['pwd']);

        $pushtoken = mysqli_real_escape_string($con, $_POST['pushtoken']);



        // $password=mysqli_real_escape_string($con,$_POST['']);
        $query = "select * from users where work_number ='$mobile' ";




        $res = mysqli_query($con, $query);
        $count = mysqli_num_rows($res);
        while ($rows = mysqli_fetch_assoc($res)) {

            $id = $rows['id'];
            $name = $rows['name'];
            $dbpass = $rows['password'];
        }

        //	echo $count;
        // exit();

        if ($count == 1) {
            $row = mysqli_fetch_assoc($res);
            $key = md5(uniqid(mt_rand(), true));

            // echo $dbpass;
            // exit();

            $Str = password_verify($pwd, $dbpass);



            if (!$Str) {
                $json[] = array('msg' => "Fail..!", 'status' => 0);
                echo json_encode(array('record' => $json));
                exit();
            } else {
                $query1 = "UPDATE users SET pushtoken='$pushtoken',user_token='$key' where work_number= '$mobile' ";
                mysqli_query($con, $query1);


                $json[] = array('msg' => "Success..!", 'status' => 1, "Name" => $name, "key" => $key);
                echo json_encode(array('record' => $json));
            }
        } else {
            $json[] = array('msg' => "Fail..!", 'status' => 0);
            echo json_encode(array('record' => $json));
        }
    } else {
        $json[] = array('msg' => "Fail..!", 'status' => 0);
        echo json_encode(array('record' => $json));
    }
} catch (Exception $e) {
    exit($e->getMessage());
}
