<?php
include("connection.php");
session_start();

if(isset($_POST['key']))
{
    
    	
	$key=mysqli_real_escape_string($con,$_POST['key']);
	$_date=mysqli_real_escape_string($con,$_POST['_date']);
	//$emp_code1=mysqli_real_escape_string($con,$_POST['emp_code']);
	
   // $password=mysqli_real_escape_string($con,$_POST['']);
    $query="select * from   users where user_token ='$key'";
    $res=mysqli_query($con,$query);
    $count=mysqli_num_rows($res);
	while($rows = mysqli_fetch_assoc($res))
     {
            $user_id=$row['id'];
			$name=$row['name'];

     }
     if($count >= 1)
     {
        
		$sql="Select user_id,check_in_lat,check_in_lng,check_out_lat,check_out_lng,check_in_rem,check_out_rem,check_in_date as check_in_time ,
		check_out_date as check_out_time from users_checkin_checkout where user_id='$user_id' and date(check_in_date)=date('$_date') order by check_in_date desc";
         
		
        $res1  = mysqli_query($con,$sql);
   
		 while($rows = mysqli_fetch_assoc($res1))
		 {
			
			//$data[] = $row; 
			$data[] = $rows; 
		
		 }
		
	
						
         $json = array( "msg"=>"employeetrack" ,"key"=>$key,'employeetrack'=>$data);
          $json1 = array('status'=>"success","data"=> $json);
          header('Content-type: application/json');   
		  echo json_encode($json1);
		

    }

    else

    {
        
             $json = array("msg"=>"login failed..! Not Valid ", "key"=>$key,);
			 $json1 = array('status'=>"failed", "data"=> $json);
			  header('Content-type: application/json'); 
             echo json_encode($json1);

    }
    
}
else
{
   
               $json = array( "msg"=>"Not receive required data..!" );
	           $json1 = array('status'=>"failed","data"=> $json);
			    header('Content-type: application/json'); 
               echo json_encode($json1);

}

?>