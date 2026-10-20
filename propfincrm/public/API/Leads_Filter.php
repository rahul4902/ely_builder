<?php 
include("connection.php");
date_default_timezone_set('Asia/Kolkata');
if(isset($_POST['status'])&& isset($_POST['key']))
{

	Global $user_id;
	$status=mysqli_real_escape_string($con,$_POST['status']);
	$key=mysqli_real_escape_string($con,$_POST['key']);
	$sql_key="select * from users where user_token ='$key'";
	$current_date = date('Y-m-d') ;
	 

	$res_key=mysqli_query($con,$sql_key);

	while($rows = mysqli_fetch_assoc($res_key))
	{

		$user_id  = $rows['id'];

	}
	
	//echo $user_id;
    //exit();

	if(mysqli_num_rows($res_key) >= 1)
	{
		$data = array();
		global $sql;

		// Member-only scoping: only fetch leads belonging to this specific member/user
		$scope = "(`leads`.`user_assigned_id` = '$user_id' OR `leads`.`user_created_id` = '$user_id')";

        if ($status=='new')
		{
        	    $sql = "(SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status 
				FROM `leads` 
				LEFT JOIN `users` on `users`.`id` = `leads`.`user_assigned_id` 
				WHERE $scope and `leads`.`status` != 5  
                and (leads.user_assign_date >= CURRENT_DATE() or leads.created_at >= CURRENT_DATE())
                order by `leads`.`updated_at` desc limit 50)
				UNION
				(SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status 
				FROM `leads` 
				LEFT JOIN `lead_follow_up` ON `lead_follow_up`.`lead_id` = `leads`.`id`
				LEFT JOIN `users` on `users`.`id` = `leads`.`user_assigned_id` 
				WHERE $scope and `leads`.`status` != 5 and `lead_follow_up`.`lead_id` IS NULL
                order by `leads`.`updated_at` desc limit 50)
                limit 50";
		}
		if ($status=='interested')
		{
           	$sql="SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status,
					(select max(lead_follow_up.follow_up_date) from lead_follow_up where lead_follow_up.lead_id=leads.id) as nextfollowup,
					(select max(lead_follow_up.meeting_date) from lead_follow_up where lead_follow_up.lead_id=leads.id) as meetingdate 
				FROM (
					SELECT DISTINCT `lead_id` FROM `lead_follow_up` WHERE `follow_up_status` = 'interested'
				) f
				INNER JOIN `leads` ON `leads`.`id` = f.`lead_id`
				LEFT JOIN `users` ON `users`.`id` = `leads`.`user_assigned_id` 
				WHERE $scope and `leads`.`status` != 5 
				order by `leads`.`updated_at` desc limit 0,50";
		}
		if ($status=='meeting')
		{
            $sql="SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status,
					(select max(lead_follow_up.follow_up_date) from lead_follow_up where lead_follow_up.lead_id=leads.id) as nextfollowup,
					(select max(lead_follow_up.meeting_date) from lead_follow_up where lead_follow_up.lead_id=leads.id) as meetingdate 
				FROM (
					SELECT DISTINCT `lead_id` FROM `lead_follow_up` WHERE `meeting_date` IS NOT NULL
				) f
				INNER JOIN `leads` ON `leads`.`id` = f.`lead_id`
				LEFT JOIN `users` ON `users`.`id` = `leads`.`user_assigned_id` 
				WHERE $scope and `leads`.`status` != 5 
				order by `leads`.`updated_at` desc limit 0,50";
		}
		if ($status=='visit')
		{
            $sql="SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status,
					(select max(lead_follow_up.follow_up_date) from lead_follow_up where lead_follow_up.lead_id=leads.id) as nextfollowup,
					(select max(lead_follow_up.meeting_date) from lead_follow_up where lead_follow_up.lead_id=leads.id) as meetingdate 
				FROM (
					SELECT DISTINCT `lead_id` FROM `lead_follow_up` WHERE `meeting_type` = 'Site visit'
				) f
				INNER JOIN `leads` ON `leads`.`id` = f.`lead_id`
				LEFT JOIN `users` ON `users`.`id` = `leads`.`user_assigned_id` 
				WHERE $scope and `leads`.`status` != 5 
				order by `leads`.`updated_at` desc limit 0,50";
		}
		if ($status=='booked')
		{
	        $sql="SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status 
				FROM `leads` 
				LEFT JOIN `users` on `users`.`id` = `leads`.`user_assigned_id` 
				WHERE $scope and `leads`.`status` = 2 
				order by `leads`.`updated_at` desc limit 0,50";
		}
		
		mysqli_set_charset($con,"utf8");
		$res1 = mysqli_query($con,$sql);
		while($row = mysqli_fetch_assoc($res1))
		{
			$data[] = $row; 
		}
			
	

		if (!is_array($data)) {
			$data = array();
		}
		echo json_encode(array(
			'Lead_List' => $data,
			'Leads_list' => $data,
			'record' => $data
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

