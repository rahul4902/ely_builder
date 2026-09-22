<?php 
include('connection.php');


try{
$input=print_r($_POST,true);

$txt = "    Time- ".date('d-m-Y H:i:s')."  File path - ".$_SERVER['PHP_SELF']."  input- ".$input;


file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

fclose($myfile);
}catch(Exception $e){

}



if(isset($_POST['key']))
{
   
	
    $lat=mysqli_real_escape_string($con,$_POST['lat']);
    $long=mysqli_real_escape_string($con,$_POST['longt']);
    $token=mysqli_real_escape_string($con,$_POST['key']);
    $sql_token="select * from users where user_token='$token'";
	
	
	$comment=mysqli_real_escape_string($con,$_POST['comment']);
	$senior_visit=mysqli_real_escape_string($con,$_POST['senior_visit']);
	$meeting_type=mysqli_real_escape_string($con,$_POST['meeting_type']);
	$follow_up_status=mysqli_real_escape_string($con,$_POST['follow_up_status']);
	

    $res_token=mysqli_query($con,$sql_token);
  
   if($num=mysqli_num_rows($res_token)>0)
    {
		while($row=mysqli_fetch_assoc($res_token))
        {
			$user_id=$row['id'];
			$name=$row['name'];
		}
	}
  
   $follow_id=mysqli_real_escape_string($con,$_POST['follow_id']);
   
  
  
  if($num1= mysqli_num_rows( $res_token) == 1)
  {
    
	    
		date_default_timezone_set('Asia/Kolkata');
		
		$curdate=date("Y-m-d H:i:s");
	
		$query1="update lead_follow_up set meeting_date='$curdate',comment='$comment',senior_visit='$senior_visit',
		meeting_type='$meeting_type',follow_up_status='$follow_up_status',action_date='$curdate'
		where id='$follow_id'";
	  
	 
	
	
	
		if(mysqli_query($con,$query1))
		{
            $id=mysqli_insert_id($con);
		}
	  
	    $getleadIdQuery="select * from lead_follow_up where id='$follow_id' limit 1";
				$getleadIdQueryRun=mysqli_query($con,$getleadIdQuery);

				if($num=mysqli_num_rows($getleadIdQueryRun)>0){
					while($row=mysqli_fetch_assoc($getleadIdQueryRun)){
						$lead_id=$row['lead_id']; 
					}
				}
	  
	    $getleadStatusUpdate1="update leads set action_date='$curdate' where id='$lead_id'";
						$getleadIdQueryRun12=mysqli_query($con,$getleadStatusUpdate1);
	  
	  
		  if(in_array(trim($follow_up_status),["Not Interested","Broker","Number Not Valid"])){
				
					if($lead_id){
					    //comment on 24 sep 2024
						//$getleadStatusUpdate="update leads set status=5,action_date='$curdate' where id='$lead_id'";
						//$getleadIdQueryRun=mysqli_query($con,$getleadStatusUpdate);
					}
				
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