<?php 
include('connection.php');


if(isset($_POST['key']))
{
   
	$lead_id=$_POST['lead_id'];
    $name=mysqli_real_escape_string($con,$_POST['name']);
    $email=mysqli_real_escape_string($con,$_POST['email']);
    $state=mysqli_real_escape_string($con,$_POST['state']);
	
	$city=mysqli_real_escape_string($con,$_POST['city']);
    //$email=mysqli_real_escape_string($con,$_POST['email']);
    $location=mysqli_real_escape_string($con,$_POST['location']);
	
	$project=mysqli_real_escape_string($con,$_POST['project']);
    $requirement=mysqli_real_escape_string($con,$_POST['requirement']);
	$lead_type_raw = $_POST['lead_type'] ?? $_POST['lead_typ'] ?? '';
	$lead_type = mysqli_real_escape_string($con, $lead_type_raw);
	$token = mysqli_real_escape_string($con, $_POST['key']);
	
	
	
    $sql_token="select * from users where user_token='$token'";

    $res_token=mysqli_query($con,$sql_token);
  
  
   while($rows = mysqli_fetch_assoc($res_token))
     {

     $user_id  = $rows['id'];

     }
  
  
  
  
  if($num1= mysqli_num_rows( $res_token) >= 1)
  {
    
	
	 
	 
         $sql1="update leads set name='$name',email='$email',state='$state',city='$city',location='$location', 
		 project='$project',requirement='$requirement',Budget='$Budget',lead_type='$lead_type'
		 where  id='$lead_id'";

		 $res1=mysqli_query($con,$sql1);
		 
		  $json=array();
   
        
		$json[]=array('msg'=>"Lead updated..!",'status'=>1);
        //	$json[]=array('msg'=>"Lead followup added..!",'key'=>$token,'status'=>1);
		
         echo json_encode(array('Lead_updated'=>$json));
   
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