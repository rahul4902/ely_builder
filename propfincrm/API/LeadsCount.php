<?php
include('connection.php');

date_default_timezone_set('Asia/Kolkata');

if(isset($_POST['key']))
{
	$token=mysqli_real_escape_string($con,$_POST['key']);
	
	
	
	$sql_token="select * from users where user_token='$token'";
	
	mysqli_set_charset($con,"utf8");
	
	$res_token=mysqli_query($con,$sql_token);
	$current_date = date('Y-m-d') ;



	while($rows = mysqli_fetch_assoc($res_token))
	{

		$user_id  = $rows['id'];

	}

	$sql_for_role="SELECT t2.name FROM role_user as t1 INNER JOIN roles as t2 on t1.role_id=t2.id where t1.user_id = '$user_id' and t1.user_id is not null limit 1";
	$getRole=mysqli_query($con,$sql_for_role);
	
	

	while($rows = mysqli_fetch_assoc($getRole))
	{
		$usertype  = $rows['name'];
	}

	
	



	if($num1= mysqli_num_rows( $res_token) == 1)
	{
		// Resolve company
		$company_id = 1;
		$comp_q = mysqli_query($con, "SELECT company_id, role FROM company_user WHERE user_id = '$user_id' AND is_active = 1 LIMIT 1");
		if ($comp_q && ($comp_row = mysqli_fetch_assoc($comp_q))) {
			$company_id = (int)$comp_row['company_id'];
		}
		$scope = "(`leads`.`company_id` = '$company_id' OR `leads`.`user_assigned_id` = '$user_id')";

		$sql="SELECT status,count(*) as leadcount FROM `leads` where $scope and status != 5
		group by status";
		



		$res=mysqli_query($con,$sql);

		$stopen="open";
		$stclose="close";
		$totaltxt="total";
		$ptxt="Progress";
		$TodayTotalTXT="TodayTotal";
		$TodayOpenTXT="TodayOpen";
		$TodayCloseTXT="TodayClose";
		$TodayPendingTXT="TodayPending";

		if($num=mysqli_num_rows($res)>0)
		{
			$total=mysqli_num_rows($res);
			

			while($row=mysqli_fetch_assoc($res))
			{
				if ($row['status']==1)
				{
					$open= (int)$row['leadcount'];

				}
				if ($row['status']==2)
				{
					$close= (int)$row['leadcount'];

				}
				if(!isset($open))
				{
					$open=0;
				}
				if(!isset($close))
				{
					$close=0;
				}



			}
			$total=$open+$close;



			//$sql2="SELECT distinct source_id FROM activity_log t1 inner join leads t2 on t1.source_id=t2.id and t2.user_assigned_id=t1.user_id  
			//where user_assigned_id='$user_id' and status=1";
			
			$sql2 = "SELECT count(DISTINCT leads.id) as cnt FROM leads STRAIGHT_JOIN lead_follow_up ON lead_follow_up.lead_id = leads.id WHERE $scope AND leads.status = 1 AND leads.status != 5";
			$res2 = mysqli_query($con, $sql2);
			$row2 = ($res2) ? mysqli_fetch_assoc($res2) : null;
			$pending = isset($row2['cnt']) ? (int)$row2['cnt'] : 0;

			////////////Today///////////////
			$start_of_today = "$current_date 00:00:00";
			$end_of_today = "$current_date 23:59:59";

			$TodayTotalQuery = "SELECT count(DISTINCT leads.id) as cnt FROM leads STRAIGHT_JOIN lead_follow_up ON lead_follow_up.lead_id = leads.id WHERE $scope AND leads.status != 5 AND lead_follow_up.meeting_date IS NULL AND (leads.action_date < '$start_of_today' OR leads.action_date > '$end_of_today') AND lead_follow_up.follow_up_date >= '$start_of_today' AND lead_follow_up.follow_up_date <= '$end_of_today'"; 
			$TodayTotalQueryData = mysqli_query($con, $TodayTotalQuery);
			$tt_row = ($TodayTotalQueryData) ? mysqli_fetch_assoc($TodayTotalQueryData) : null;
			$TodayTotal = isset($tt_row['cnt']) ? (int)$tt_row['cnt'] : 0;
			
			$TodayOpenQuery = "SELECT count(DISTINCT leads.id) as cnt FROM leads STRAIGHT_JOIN lead_follow_up ON lead_follow_up.lead_id = leads.id WHERE lead_follow_up.action_date >= '$start_of_today' AND lead_follow_up.action_date <= '$end_of_today' AND $scope AND leads.status != 5";
			$TodayOpenQueryData = mysqli_query($con, $TodayOpenQuery);
			$to_row = ($TodayOpenQueryData) ? mysqli_fetch_assoc($TodayOpenQueryData) : null;
			$TodayOpen = isset($to_row['cnt']) ? (int)$to_row['cnt'] : 0;

			$TodayCloseQuery = "SELECT count(DISTINCT leads.id) as cnt FROM leads STRAIGHT_JOIN lead_follow_up ON lead_follow_up.lead_id = leads.id WHERE lead_follow_up.action_date >= '$start_of_today' AND lead_follow_up.action_date <= '$end_of_today' AND $scope AND leads.status in (5,2)";
			$TodayCloseQueryData = mysqli_query($con, $TodayCloseQuery);
			$tc_row = ($TodayCloseQueryData) ? mysqli_fetch_assoc($TodayCloseQueryData) : null;
			$TodayClose = isset($tc_row['cnt']) ? (int)$tc_row['cnt'] : 0;

			$TodayPendingQuery = "SELECT count(DISTINCT leads.id) as cnt FROM leads STRAIGHT_JOIN lead_follow_up ON lead_follow_up.lead_id = leads.id WHERE $scope AND leads.status NOT IN (2, 5) AND lead_follow_up.meeting_date IS NULL AND leads.action_date < '$start_of_today' AND lead_follow_up.follow_up_date < '$start_of_today' AND lead_follow_up.follow_up_date > leads.action_date";
			$TodayPendingQueryData = mysqli_query($con, $TodayPendingQuery);
			$tp_row = ($TodayPendingQueryData) ? mysqli_fetch_assoc($TodayPendingQueryData) : null;
			$TodayPending = isset($tp_row['cnt']) ? (int)$tp_row['cnt'] : 0;
			

		 
			$json=array(
				array(
					$stopen=>$open-$pending,
					$stclose=>$close,
					$totaltxt=>$total,
					$ptxt=>$pending,	
				)
			);
			$json1=array(
				array(					 
					$TodayTotalTXT=>($TodayTotal == null)?0:$TodayTotal,
					$TodayOpenTXT=>($TodayOpen == null)?0:$TodayOpen,
					$TodayCloseTXT=>($TodayClose == null)?0:$TodayClose,
					$TodayPendingTXT=>($TodayPending == null)?0:$TodayPending,

				)
			);

			$json2=array(
				array(
					"TotalOpen"=>1,
					"TotalClose"=>2,
					"TotalLeads"=>3,
					"TotalPending"=>4,
					"TotalDumpLead"=>5,
					"TodayTotal"=>6,
					"TodayActivity"=>7,
					"TodayPending"=>8,
					"TodayClose"=>9
				)
			);

 
			echo json_encode(array('record_count'=>$json,'today_count'=>$json1,'status_for_list'=>$json2));


		}
		else
		{
			$json=array(
				array(
					$stopen=>0,
					$stclose=>0,
					$totaltxt=>0,
					$ptxt=>0,	
				)
			);
			$json1=array(
				array(					 
					$TodayTotalTXT=>0,
					$TodayOpenTXT=>0,
					$TodayCloseTXT=>0,
					$TodayPendingTXT=>0,

				)
			);

			$json2=array(
				array(
					"TotalOpen"=>1,
					"TotalClose"=>2,
					"TotalLeads"=>3,
					"TotalPending"=>4,
					"TotalDumpLead"=>5,
					"TodayTotal"=>6,
					"TodayActivity"=>7,
					"TodayPending"=>8,
					"TodayClose"=>9
				)
			);

 
			echo json_encode(array('record_count'=>$json,'today_count'=>$json1,'status_for_list'=>$json2));
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
