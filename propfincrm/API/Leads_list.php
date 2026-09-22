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

	if(mysqli_num_rows($res_key) == 1)
	{
		$data= array();
		global $sql;

		// Resolve company
		$company_id = 1;
		$comp_q = mysqli_query($con, "SELECT company_id, role FROM company_user WHERE user_id = '$user_id' AND is_active = 1 LIMIT 1");
		if ($comp_q && ($comp_row = mysqli_fetch_assoc($comp_q))) {
			$company_id = (int)$comp_row['company_id'];
		}
		$scope = "(`leads`.`company_id` = '$company_id' OR `leads`.`user_assigned_id` = '$user_id')";
		$scope_t2 = "(`t2`.`company_id` = '$company_id' OR `t2`.`user_assigned_id` = '$user_id')";

		$start_of_today = "$current_date 00:00:00";
		$end_of_today = "$current_date 23:59:59";

		if ($status==1 || $status==2)
		{
			$sql = "SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as `user_name`, 'Open' as status 
				FROM `leads` 
				LEFT JOIN `users` on `users`.`id` = `leads`.`user_assigned_id` 
				LEFT JOIN `lead_follow_up` ON `lead_follow_up`.`lead_id` = `leads`.`id`
				WHERE `leads`.`status` = '$status' and $scope and `leads`.`status` != 5 
				and `lead_follow_up`.`lead_id` IS NULL 
				ORDER BY `leads`.`updated_at` desc LIMIT 0, 100";
		}
		if ($status==3)
		{
			$sql="SELECT id, contact_no, name, email, source, location, project, budget, requirement, updated_at as next_follow_up, CASE WHEN status=1 THEN 'Open' ELSE 'Close' END as status 
				FROM `leads` 
				WHERE $scope and status!=5 
				ORDER BY updated_at desc LIMIT 0, 100";
		}
		if ($status==4)
		{
			$sql = "SELECT DISTINCT t2.id, t2.contact_no, t2.name, t2.email, source, location, project, budget, requirement, t2.updated_at as next_follow_up, 'In Process' as status 
				FROM leads as t2 
				LEFT JOIN users as t1 on t1.id = t2.user_assigned_id 
				INNER JOIN lead_follow_up as t3 on t3.lead_id = t2.id 
				WHERE t2.status != 5 and t2.status=1 and $scope_t2 
				ORDER BY t2.updated_at desc LIMIT 0, 100";
		}
		if ($status==5)
		{
			$sql="SELECT DISTINCT t2.id, contact_no, t2.name, t2.email, source, location, project, budget, requirement, t2.updated_at as next_follow_up, 'Progress' as status 
				FROM activity_log t1 
				INNER JOIN leads t2 on t1.source_id=t2.id 
				WHERE $scope_t2 and status=5 
				ORDER BY t2.updated_at desc LIMIT 0, 100";
		}

		///////////////////TODAY LEADS DATA///////////////////
		if($status!= 1 && $status!= 2 && $status!= 3 && $status!= 4 && $status!= 5 ){
			switch ($status) {
			    case 6:
			        $Todaysql="SELECT DISTINCT `leads`.*, 'Open' as status, lead_follow_up.follow_up_date 
					FROM `leads` 
					LEFT JOIN `users` ON `users`.`id` = `leads`.`user_assigned_id` 
					INNER JOIN `lead_follow_up` ON `lead_follow_up`.`lead_id` = `leads`.`id` 
					WHERE $scope AND `leads`.`status` != 5 AND `lead_follow_up`.`meeting_date` IS NULL 
					AND (`leads`.`action_date` < '$start_of_today' OR `leads`.`action_date` > '$end_of_today') 
					AND `lead_follow_up`.`follow_up_date` >= '$start_of_today' AND `lead_follow_up`.`follow_up_date` <= '$end_of_today'
					ORDER BY `leads`.`updated_at` desc LIMIT 0, 100";  
			        break;
			    case 7:
			        $Todaysql="SELECT DISTINCT t2.id, t2.contact_no, t2.name, t2.email, t2.source, t2.location, t2.project, t2.budget, t2.requirement, t2.updated_at as next_follow_up, 'Activity Done' as status 
					FROM (
						SELECT DISTINCT lead_id FROM lead_follow_up WHERE action_date >= '$start_of_today' AND action_date <= '$end_of_today'
					) t3
					INNER JOIN `leads` as t2 ON t2.id = t3.lead_id
					LEFT JOIN `users` on `users`.`id` = `t2`.`user_assigned_id` 
					WHERE t2.status != 5 and $scope_t2 
					ORDER BY t2.updated_at desc LIMIT 0, 100";
			        break;
			    case 8:
			        $Todaysql="SELECT `leads`.*, f.follow_up_date
					FROM (
						SELECT `lead_id`, MAX(`follow_up_date`) as follow_up_date
						FROM `lead_follow_up`
						WHERE `meeting_date` IS NULL AND `follow_up_date` < '$start_of_today'
						GROUP BY `lead_id`
					) f
					INNER JOIN `leads` ON `leads`.`id` = f.`lead_id`
					LEFT JOIN `users` ON `users`.`id` = `leads`.`user_assigned_id`
					WHERE $scope AND `leads`.`status` NOT IN (2, 5)
					AND `leads`.`action_date` < '$start_of_today'
					AND f.`follow_up_date` > `leads`.`action_date`
					ORDER BY `leads`.`updated_at` DESC LIMIT 0, 100";
			        break;
			    default:
			        $Todaysql="SELECT DISTINCT t2.id, t2.contact_no, t2.name, t2.email, t2.source, t2.location, t2.project, t2.budget, t2.requirement, t2.updated_at as next_follow_up, 'Closed' as status 
					FROM (
						SELECT DISTINCT lead_id FROM lead_follow_up WHERE action_date >= '$start_of_today' AND action_date <= '$end_of_today'
					) t3
					INNER JOIN `leads` as t2 ON t2.id = t3.lead_id
					LEFT JOIN `users` on `users`.`id` = `t2`.`user_assigned_id` 
					WHERE t2.status in (5, 2) and $scope_t2 
					ORDER BY t2.updated_at desc LIMIT 0, 100";
			}	
			$sql = $Todaysql;	 
		}			 
	 
		mysqli_set_charset($con,"utf8");
 
		$res1  = mysqli_query($con,$sql);

		$data = array();

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

