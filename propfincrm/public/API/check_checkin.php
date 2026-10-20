<?php
include("connection.php");
//session_start();

if(isset($_POST['key']))
{
    	
	$key=mysqli_real_escape_string($con,$_POST['key']);
	$_Date = isset($_POST['_Date']) ? mysqli_real_escape_string($con, $_POST['_Date']) : date('Y-m-d');
	
   // $password=mysqli_real_escape_string($con,$_POST['']);
     $query="select * from   users where user_token ='$key'";
    $res=mysqli_query($con,$query);
     $count=mysqli_num_rows($res);
	while($rows = mysqli_fetch_assoc($res))
     {
            $user_id=$rows['id'];
			$name=$rows['name'];

     }
     if($count >= 1)
     {
        
		  $data = array();
		  $sql="Select * from users_checkin_checkout where user_id='$user_id' 
		 and check_in_date >= '$_Date 00:00:00' and check_in_date <= '$_Date 23:59:59' order by id";
         
		
        $res1  = mysqli_query($con,$sql);
   
		 while($rows = mysqli_fetch_assoc($res1))
		 {
			
			//$data[] = $row; 
			$data[] = $rows; 
		
		 }
		
		mysqli_close($con);
						
         $json = array( "msg"=>"empcheckindata" ,"key"=>$key,'empcheckindata'=>$data);
          $json1 = array('status'=>"success","data"=> $json);
          header('Content-type: application/json');   
		  echo json_encode($json1);
		

    }

    else

    {
        mysqli_close($con);
             $json = array("msg"=>"login failed..! Not Valid ", "key"=>$key,);
			 $json1 = array('status'=>"failed", "data"=> $json);
			  header('Content-type: application/json'); 
             echo json_encode($json1);

    }
    
}
else
{
    mysqli_close($con);
               $json = array( "msg"=>"Not receive required data..!" );
	           $json1 = array('status'=>"failed","data"=> $json);
			    header('Content-type: application/json'); 
               echo json_encode($json1);

}

?>