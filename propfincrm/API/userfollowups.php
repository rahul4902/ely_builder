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
		$data = array();

		// Resolve company
		$company_id = 1;
		$comp_q = mysqli_query($con, "SELECT company_id, role FROM company_user WHERE user_id = '$user_id' AND is_active = 1 LIMIT 1");
		if ($comp_q && ($comp_row = mysqli_fetch_assoc($comp_q))) {
			$company_id = (int)$comp_row['company_id'];
		}
		$scope_t1 = "(`t1`.`company_id` = '$company_id' OR `t1`.`user_assigned_id` = '$user_id')";

		$sql="select t1.*, t2.id as followup_id, t2.follow_up_date, t2.comment, t2.status as followup_status 
		      from leads t1 
		      inner join lead_follow_up t2 on t1.id=t2.lead_id 
		      where $scope_t1 and t1.status = 1 and t1.status != 5 
		      order by t2.follow_up_date DESC LIMIT 0, 200";
	    $res1 = mysqli_query($con,$sql);
   
		if($res1)
		{
			while($row = mysqli_fetch_assoc($res1))
			{
				$data[] = $row; 
			}
		}

		echo json_encode(array(
			'Userfollowups' => $data,
			'followup' => $data
		));
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
	
