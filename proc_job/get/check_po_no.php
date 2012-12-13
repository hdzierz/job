<?php
	include "../../includes/mysql.php";
	include "../../includes/mysql_aid_functions.php";
	
	if(!$_GET['no']) die();
	
	$qry = "SELECT * FROM job WHERE job_id<>".$_GET['job_id']." AND purchase_no=".$_GET['no'];
	$res = query($qry);
	
	$num = mysql_num_rows($res);
	
	if($num==0){
		echo "";
	}
	else{
		$str = "";
		while($job = mysql_fetch_object($res)){
			$str.= " ".$job->job_no;
		}
		echo "Number used in ".$str;
	}
	
?>