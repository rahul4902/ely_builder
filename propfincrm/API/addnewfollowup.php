<?php 
include('connection.php');
date_default_timezone_set('Asia/Kolkata');

if(isset($_POST['key']))
{
   
	$follow_up_status=mysqli_real_escape_string($con,$_POST['follow_up_status']);
	
	$comment=mysqli_real_escape_string($con,$_POST['comment']);
	
    $lat=mysqli_real_escape_string($con,$_POST['lat']);
    $long=mysqli_real_escape_string($con,$_POST['longt']);
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
  
   $lead_id=mysqli_real_escape_string($con,$_POST['lead_id']);
   $follow_up_date=mysqli_real_escape_string($con,$_POST['follow_up_date']);
   $comment=mysqli_real_escape_string($con,$_POST['comment']);
   $status=mysqli_real_escape_string($con,$_POST['status']);
  
  
  if($num1= mysqli_num_rows( $res_token) == 1)
  {
    
	if ($status==4)
	{
	  	
		
		$curdate=date("Y-m-d H:i:s");
		//$query1="insert into lead_follow_up (follow_up_date,lead_id,user_id, comment) 
		//values('$follow_up_date','$lead_id','$user_id', '$comment')";
		
			
		$query1="insert into lead_follow_up (follow_up_date,lead_id,user_id,follow_up_status,comment,action_date) 
		values('$follow_up_date','$lead_id','$user_id','$follow_up_status','$comment','$curdate')";
	
	
	    
	
		if(mysqli_query($con,$query1))
		{
            $id=mysqli_insert_id($con);
		}
	
		$query2="update leads set action_date='$curdate' where id='$lead_id'";
	
	
		if(mysqli_query($con,$query2))
		{
            
		}
		
		//$query8="insert into activity_log
		//(user_id,text,source_type,source_id,ip_address,action) values 
		//('$user_id','$name updated the deadline for this lead','App\Models\Lead','$lead_id','','updated_deadline')";
	
	
		//if(mysqli_query($con,$query8))
		//{
            //$id=mysqli_insert_id($con);
		//}
		
		
		
		
		
	}
	else if ($status==2)
	{
		
		
		
		$query1="insert into lead_follow_up (lead_id,user_id,follow_up_status,comment) 
		values('$lead_id','$user_id','$follow_up_status','$comment')";
	
	
	    
	
		if(mysqli_query($con,$query1))
		{
            //$id=mysqli_insert_id($con);
		}
		
		
		
		$query2="update leads set status='2' where id='$lead_id'";
	
	
		if(mysqli_query($con,$query2))
		{
            $id=mysqli_insert_id($con);
		}
		
		//$query8="insert into activity_log(user_id,text,source_type,source_id,ip_address,action) 
		//values ('$user_id','Lead was completed by $name','App\Models\Lead','$lead_id','','updated_status')";
	
	
		//if(mysqli_query($con,$query8))
		//{
            //$id=mysqli_insert_id($con);
		//}
	
				
	}
	  
	else if ($status==5)
	{
		
		
		
		$query1="insert into lead_follow_up (lead_id,user_id,follow_up_status,comment) 
		values('$lead_id','$user_id','$follow_up_status','$comment')";
	
	
	    
	
		if(mysqli_query($con,$query1))
		{
            //$id=mysqli_insert_id($con);
		}
		
		
		//commented on 4 dec
		//$query2="update leads set status='5',action_date='$curdate' where id='$lead_id'";
	
	
	//	if(mysqli_query($con,$query2))
	//	{
            //$id=mysqli_insert_id($con);
		//}
		
		//$query8="insert into activity_log(user_id,text,source_type,source_id,ip_address,action) 
		//values ('$user_id','Lead was completed by $name','App\Models\Lead','$lead_id','','updated_status')";
	
	
		//if(mysqli_query($con,$query8))
		//{
            //$id=mysqli_insert_id($con);
		//}
	
				
	}
	
	
	
	
	 	   	  
    //$res=mysqli_query($con,$sql);
    $json=array();
   
        
	$json[]=array('msg'=>"Lead followup added..!",'status'=>1);
        //	$json[]=array('msg'=>"Lead followup added..!",'key'=>$token,'status'=>1);

       
         
    echo json_encode(array('new_followup'=>$json));

    
    
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