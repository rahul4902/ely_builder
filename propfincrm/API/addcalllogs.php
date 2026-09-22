<?php 
include('connection.php');
date_default_timezone_set('Asia/Kolkata');

if(isset($_POST['key']))
{
   
	$call_start_datetime=mysqli_real_escape_string($con,$_POST['call_start_datetime']);
	
	$call_end_datetime=mysqli_real_escape_string($con,$_POST['call_end_datetime']);
	
    $lead_id=mysqli_real_escape_string($con,$_POST['lead_id']);
    
   
    $token=mysqli_real_escape_string($con,$_POST['key']);
    
    $sql_token="select * from users where user_token='$token'";

    $res_token=mysqli_query($con,$sql_token);
  
    if($num=mysqli_num_rows($res_token)>0)
    {
		while($row=mysqli_fetch_assoc($res_token))
        {
			$user_id=$row['id'];
			$name=$row['name'];
		}
	}
  
  
  $status=4;
  
  if($num1= mysqli_num_rows( $res_token) == 1)
  {
    
	if ($status==4)
	{
	  	
		
		$curdate=date("Y-m-d H:i:s");
		//$query1="insert into lead_follow_up (follow_up_date,lead_id,user_id, comment) 
		//values('$follow_up_date','$lead_id','$user_id', '$comment')";
		
			
		$query1="insert into call_logs (call_start_datetime,call_end_datetime,user_id,lead_id) 
		values('$call_start_datetime','$call_end_datetime','$user_id','$lead_id')";
	
	
	    
	
		if(mysqli_query($con,$query1))
		{
            $id=mysqli_insert_id($con);
		}
	
	
		
		//$query8="insert into activity_log
		//(user_id,text,source_type,source_id,ip_address,action) values 
		//('$user_id','$name updated the deadline for this lead','App\Models\Lead','$lead_id','','updated_deadline')";
	
	
		//if(mysqli_query($con,$query8))
		//{
            //$id=mysqli_insert_id($con);
		//}
		
		
		
		
		
	}

	
	
	
	 	   	  
    //$res=mysqli_query($con,$sql);
    $json=array();
   
        
	$json[]=array('msg'=>"call logs added..!",'status'=>1);
        //	$json[]=array('msg'=>"Lead followup added..!",'key'=>$token,'status'=>1);

       
         
    echo json_encode(array('call_logs'=>$json));

    
    
  }
  else
    {

       $json[]=array('msg'=>"You are not login ..!",'status'=>0);
          echo json_encode(array('message'=>$json)); 
    }
    
}
else
{
    $json[]=array('msg'=>"Not receive required data..!",'status'=>0);
          echo json_encode(array('message'=>$json));

}


?>