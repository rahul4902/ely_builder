<?php 
include('connection.php');


if(isset($_POST['key']))
{
   
	
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
		}
	}
  
   $name=mysqli_real_escape_string($con,$_POST['name']);
   $contact_no=mysqli_real_escape_string($con,$_POST['contact_no']);
   $email=mysqli_real_escape_string($con,$_POST['email']);
   
   $country=mysqli_real_escape_string($con,$_POST['country']);
   $state=mysqli_real_escape_string($con,$_POST['state']);
   $city=mysqli_real_escape_string($con,$_POST['city']);
   $Pincode=mysqli_real_escape_string($con,$_POST['Pincode']);
   
   $location=mysqli_real_escape_string($con,$_POST['location']);
   $source=mysqli_real_escape_string($con,$_POST['source']);
   $project=mysqli_real_escape_string($con,$_POST['project']);
   
  
   $requrement=mysqli_real_escape_string($con,$_POST['requrement']);
   
   $Budget=mysqli_real_escape_string($con,$_POST['Budget']);
  
  
   $lead_type=mysqli_real_escape_string($con,$_POST['lead_type']);
  
  
  if($num1= mysqli_num_rows( $res_token) == 1)
  {
	  
	date_default_timezone_set('Asia/Kolkata'); 
	 
	$curdate=date("Y-m-d H:i:s");
    
	$query1="insert into leads (name,contact_no,email,country,state,city,pin,location,source,project,requirement,Budget,
	lead_type,status,user_assigned_id,client_id,user_created_id,contact_date,created_at) values('$name','$contact_no','$email','$country','$state','$city',
	'$Pincode','$location','$source','$project','$requrement','$Budget','$lead_type','1','$user_id',1,'$user_id','$curdate','$curdate')";
	
	//echo $query1;
	
	if(mysqli_query($con,$query1))
    {
            $id=mysqli_insert_id($con);
	}
	 	   	  
    //$res=mysqli_query($con,$sql);
    $json=array();
   
        
	$json[]=array('msg'=>"Lead Detail added..!",'key'=>$token,'status'=>1);
       
         
    echo json_encode(array('lead_follow_up'=>$json));

    
    
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