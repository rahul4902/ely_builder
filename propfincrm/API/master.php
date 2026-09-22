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
     
	$sql="SELECT project_name FROM `project_master` where 1=1 order by id desc";
	  
	 mysqli_set_charset($con,"utf8");
   
    $res=mysqli_query($con,$sql);
    $json=array();
    if($num=mysqli_num_rows($res)>0)
    {
        
        $sql1="SELECT requirement_name FROM `requirement_master` 
				where 1=1 ";

		$res1=mysqli_query($con,$sql1);
		$json1=array();
		if($num1=mysqli_num_rows($res1)>0)
		{
			while($row1 = mysqli_fetch_assoc($res1))
			{
			
				$data1[] = $row1; 
		
			}
		}	


		$sql2="SELECT source_name FROM `source_master` 
				where 1=1 order by id desc";

		$res2=mysqli_query($con,$sql2);
		$json1=array();
		if($num2=mysqli_num_rows($res2)>0)
		{
			while($row2 = mysqli_fetch_assoc($res2))
			{
			
				$data2[] = $row2; 
		
			}
		}

		$sql3="SELECT budget_range FROM `budget_master` 
				where 1=1 order by id desc";

		$res3=mysqli_query($con,$sql3);
		$json1=array();
		if($num3=mysqli_num_rows($res3)>0)
		{
			while($row3 = mysqli_fetch_assoc($res3))
			{
			
				$data3[] = $row3; 
		
			}
		}			
		
   
		 while($row = mysqli_fetch_assoc($res))
		 {
			
			$data[] = $row; 
		
		 }

                 echo json_encode(array('project'=>$data,'requirement'=>$data1,'source'=>$data2,'budget'=>$data3));

	//	 echo json_encode(array('DetailRecord'=>$data,'Followup'=>$data1));
	//	 echo json_encode(array('DetailRecord'=>$data));

		
		
		
		
		
       
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