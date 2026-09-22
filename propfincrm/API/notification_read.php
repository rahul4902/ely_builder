<?php 
include('connection.php');
date_default_timezone_set('Asia/Kolkata');

if(isset($_POST['key']))
{
   

   
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
	
		
			
		$query1="update app_notifications set n_read=1 where user_id='$user_id'";
	
	
	    
	
		if(mysqli_query($con,$query1))
		{
            $id=mysqli_insert_id($con);
		}
	
	
		
	
		
		
		
		
	}

	
	
	
	 	   	  
    //$res=mysqli_query($con,$sql);
    $json=array();
   
        
	$json[]=array('msg'=>"not read..!",'status'=>1);
        //	$json[]=array('msg'=>"Lead followup added..!",'key'=>$token,'status'=>1);

       
         
    echo json_encode(array('read'=>$json));

    
    
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