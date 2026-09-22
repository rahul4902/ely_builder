<?php 
include("connection.php");

if(isset($_POST['status'])&& isset($_POST['key']))
{
    
    Global $user_id;
	$status=mysqli_real_escape_string($con,$_POST['status']);
    $key=mysqli_real_escape_string($con,$_POST['key']);
	
	$followid=mysqli_real_escape_string($con,$_POST['followid']);
	
    $sql_key="select * from users where user_token ='$key'";
    

    $res_key=mysqli_query($con,$sql_key);

     while($rows = mysqli_fetch_assoc($res_key))
     {

		$user_id  = $rows['id'];

     }

    if(mysqli_num_rows($res_key) == 1)
    {

		$data= array();

		global $sql;

	
		$sql="select * from lead_follow_up where id='$followid'";
	    $res1  = mysqli_query($con,$sql);
   
		 while($row = mysqli_fetch_assoc($res1))
		 {
			
			$data[] = $row; 
		
		 }

		 echo json_encode(array('LeadFollow'=>$data));
		 
    }

	 else
	 {
		 $data[]=array('msg'=>'no record found..!','status'=>0);
		echo json_encode(array('message'=>$data));
	 }

    }

    else
    {
        $data[]=array('msg'=>'You are not login ..!','status'=>0);
		echo json_encode(array('message'=>$data));
    }
	
