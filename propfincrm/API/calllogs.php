<?php 
include("connection.php");

if(isset($_POST['key']))
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

	
		$sql="select t1.*,t3.name,t3.contact_no from call_logs t1 inner join users t2 on t1.user_id=t2.id inner join leads t3 on t3.id=t1.lead_id where t1.user_id='$user_id' order by call_start_datetime desc ";
	    $res1  = mysqli_query($con,$sql);
   
		 while($row = mysqli_fetch_assoc($res1))
		 {
			
			$data[] = $row; 
		
		 }

		 echo json_encode(array('calllogs'=>$data));
		 
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
	
