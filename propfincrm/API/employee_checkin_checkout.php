<?php
include("connection.php");
session_start();

date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)
$curtime= date('Y-m-d H:i:s');

if(isset($_POST['key']))
{
	$key = isset($_POST['key']) ? mysqli_real_escape_string($con, $_POST['key']) : '';
	$lat = isset($_POST['lat']) ? mysqli_real_escape_string($con, $_POST['lat']) : '0.0';
	$lng = isset($_POST['lng']) ? mysqli_real_escape_string($con, $_POST['lng']) : '0.0';
	$type = isset($_POST['type']) ? mysqli_real_escape_string($con, $_POST['type']) : '';
	$check_in_rem = isset($_POST['check_in_rem']) ? mysqli_real_escape_string($con, $_POST['check_in_rem']) : '';
	$check_out_rem = isset($_POST['check_out_rem']) ? mysqli_real_escape_string($con, $_POST['check_out_rem']) : '';
	
	$check_in_address = isset($_POST['check_in_address']) ? mysqli_real_escape_string($con, $_POST['check_in_address']) : '';
	$check_out_address = isset($_POST['check_out_address']) ? mysqli_real_escape_string($con, $_POST['check_out_address']) : '';
	
	$check_in_date = isset($_POST['check_in_date']) ? mysqli_real_escape_string($con, $_POST['check_in_date']) : '';
	$check_out_date = isset($_POST['check_out_date']) ? mysqli_real_escape_string($con, $_POST['check_out_date']) : '';
	
	if ($lat=='0.0')
	{
	        // $json = array("msg"=>"login failed..! Not Valid ", "key"=>$key,);
			// $json1 = array('status'=>"failed", "data"=> $json);
			// header('Content-type: application/json'); 
            // echo json_encode($json1);
           //  exit();
	}
	
   // $password=mysqli_real_escape_string($con,$_POST['']);
    $query="select * from   users where user_token ='$key'";
    $res=mysqli_query($con,$query);
    $count=mysqli_num_rows($res);
	while($rows = mysqli_fetch_assoc($res))
     {
            $user_id=$rows['id'];
			$name=$rows['name'];

     }
     //print_r($user_id);
     //exit();
     if($count == 1)
     {
        
		if ($type=='checkin')
		{
			
			 $querycheck="select * from   users_checkin_checkout where user_id ='$user_id' and date(check_in_date)=date('$curtime') and check_out_date is null";
             $resquerycheck=mysqli_query($con,$querycheck);
             $countquerycheck=mysqli_num_rows($resquerycheck);
             if ($countquerycheck==0)
             {
			
    			$query="insert into users_checkin_checkout(user_id,check_in_lat,check_in_lng,check_in_rem,check_in_address,check_in_date)
    			values ('$user_id','$lat','$lng','$check_in_rem','$check_in_address','$curtime')";
    			mysqli_query($con, $query);
             }
		 
		   // mysqli_close($con);
		     $json = array( "msg"=>"check in done successfully!" ,"key"=>$key);
          $json1 = array('status'=>"success","data"=> $json);
          header('Content-type: application/json');   
		  echo json_encode($json1);
		
		}
		else
		{
			date_default_timezone_set("Asia/Calcutta"); 
			$curdate = date("Y-m-d H:i:s");
			
			$query="update users_checkin_checkout set check_out_lat='$lat',check_out_lng='$lng',check_out_rem='$check_out_rem',check_out_date='$curdate',check_out_address='$check_out_address',check_out_date='$curtime'
			where user_id='$user_id' and check_out_date is null and date(check_in_date)=date('$check_in_date')";
			mysqli_query($con, $query);
			
			//mysqli_close($con);
			 $json = array( "msg"=>"check out done successfully!" ,"key"=>$key);
          $json1 = array('status'=>"success","data"=> $json);
          header('Content-type: application/json');   
		  echo json_encode($json1);
			
		}
			
		
						
        
		

    }

    else

    {
       // mysqli_close($con);
             $json = array("msg"=>"login failed..! Not Valid ", "key"=>$key,);
			 $json1 = array('status'=>"failed", "data"=> $json);
			  header('Content-type: application/json'); 
             echo json_encode($json1);

    }
    
}
else
{
    //mysqli_close($con);
               $json = array( "msg"=>"Not receive required data..!" );
	           $json1 = array('status'=>"failed","data"=> $json);
			    header('Content-type: application/json'); 
               echo json_encode($json1);

}

?>