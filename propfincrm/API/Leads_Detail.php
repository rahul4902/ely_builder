<?php 
include('connection.php');


if(isset($_POST['key']))
{
   
	$lead_id=$_POST['lead_id'];
    $lat=mysqli_real_escape_string($con,$_POST['lat']);
    $long=mysqli_real_escape_string($con,$_POST['longt']);
    $token=mysqli_real_escape_string($con,$_POST['key']);
    $sql_token="select * from users where user_token='$token'";

    $res_token=mysqli_query($con,$sql_token);
  
  
   while($rows = mysqli_fetch_assoc($res_token))
     {

     $user_id  = $rows['id'];

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

		$sql = "SELECT `leads`.*, `leads`.`Budget` as budget, COALESCE(`users`.`name`, 'Unassigned') as user_name 
		        FROM `leads` 
		        LEFT JOIN `users` ON `users`.`id` = `leads`.`user_assigned_id` 
		        WHERE $scope AND `leads`.`id` = '$lead_id' LIMIT 1";

		mysqli_set_charset($con,"utf8");
   
		$res = mysqli_query($con,$sql);
		$data = array();
		$data1 = array();

		if(mysqli_num_rows($res) > 0)
		{
			$sql1 = "SELECT follow_up_date, comment, status FROM `lead_follow_up` 
			         WHERE lead_id = '$lead_id' ORDER BY follow_up_date DESC LIMIT 1";

			$res1 = mysqli_query($con,$sql1);
			if($res1 && mysqli_num_rows($res1) > 0)
			{
				while($row1 = mysqli_fetch_assoc($res1))
				{
					$data1[] = $row1; 
				}
			}			
		
			while($row = mysqli_fetch_assoc($res))
			{
				$data[] = $row; 
			}

			$record = $data;
			if (!empty($record) && !empty($data1)) {
				$record[0]['next_follow_up_date'] = $data1[0]['follow_up_date'] ?? null;
			}

			echo json_encode(array(
				'DetailRecord' => array_merge($data, $data1),
				'record' => $record
			));
		}
    else
    {
         $json[]=array('msg'=>"No record found..!",'status'=>0);
         echo json_encode(array('message'=>$json));

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