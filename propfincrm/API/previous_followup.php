<?php 
include('connection.php');


if(isset($_POST['key']))
{
   
	$lead_id=$_POST['lead_id'];
    $lat=mysqli_real_escape_string($con,$_POST['lat']);
    $long=mysqli_real_escape_string($con,$_POST['longt']);
    $token=mysqli_real_escape_string($con,$_POST['key']);
    $sql_token="select * from users where user_token='$token'";
	
	mysqli_set_charset($con,"utf8");

    $res_token=mysqli_query($con,$sql_token);
  
  
   while($rows = mysqli_fetch_assoc($res_token))
     {

     $user_id  = $rows['id'];

     }
  
  
  
  
  if($num1= mysqli_num_rows( $res_token) == 1)
  {
	  $sql="SELECT t1.id, t1.follow_up_date, t1.comment, t1.meeting_date, t1.status, COALESCE(t2.name, 'Agent') as user_name 
	        FROM `lead_follow_up` t1 
	        LEFT JOIN `users` t2 ON t2.id = t1.user_id 
	        WHERE t1.lead_id='$lead_id' ORDER BY t1.id DESC";
   
	  mysqli_set_charset($con,"utf8");
   
	  $res=mysqli_query($con,$sql);
	  $data=array();
	  if(mysqli_num_rows($res)>0)
	  {
		 while($row = mysqli_fetch_assoc($res))
		 {
			$data[] = $row; 
		 }

		 echo json_encode(array(
			'PreviousFollowup' => $data,
			'followup' => $data
		 ));
	  }
	  else
	  {
		 echo json_encode(array(
			'PreviousFollowup' => array(),
			'followup' => array(),
			'message' => array(array('msg'=>'no record found..!','status'=>0))
		 ));
	  }
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