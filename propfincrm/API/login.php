<?php
include("connection.php");
session_start();

if(isset($_POST['mobile']))
{
	

    $mobile=mysqli_real_escape_string($con,$_POST['mobile']);
	
	$pushtoken=mysqli_real_escape_string($con,$_POST['pushtoken']);
	
	
	
   // $password=mysqli_real_escape_string($con,$_POST['']);
    $query="select * from users where work_number ='$mobile' ";
	
	 
    $res=mysqli_query($con,$query);
    $count=mysqli_num_rows($res);
	$id = null;
	$name = '';
	$existing_token = '';
	while($rows = mysqli_fetch_assoc($res))
	{
		$id = $rows['id'];
		$name = $rows['name']; 
		$existing_token = $rows['user_token'] ?? '';
	}
	 
	if($count == 1)
	{
		$key = (!empty($existing_token) && strlen($existing_token) >= 10) ? $existing_token : md5(uniqid(mt_rand(),true));
		
		$query1="UPDATE users SET pushtoken='$pushtoken',user_token='$key' where work_number= '$mobile' ";
		mysqli_query($con, $query1);

		@mysqli_query($con, "INSERT INTO user_token (user_id, token, status) VALUES ('$id', '$key', 1)");
		
		$json[]=array('msg'=>"Success..!",'status'=>1,"Name"=>$name,"key"=>$key);
		echo json_encode(array('record'=>$json));
	}
	
	
    else

    {
              $json[]=array('msg'=>"Fail..!",'status'=>0);
              echo json_encode(array('record'=>$json));

    }
    
}
else
{
              $json[]=array('msg'=>"Fail..!",'status'=>0);
              echo json_encode(array('record'=>$json));

}

?>