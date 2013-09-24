<?

if($action=="send_messages"){
}

if($action=="edit"){
	header("Location: proc_job.php?job_id=$record"); 
}

if($action=="sync_webpage"){
	?>
		<a href='dbupload.php' onclick="return confirm('Do you really want to syn the routes?')">Sync routes to webpage</a>
	<?
}

if($action=="show_templates"){
?>
	<h2 class="joblist_head">Templates</h2>
<?
		$qry = get_template_query();
		$tab = new MySQLTable("index.php",$qry,"templates","proc_temp.php");
		$tab->showRec=0;
		$tab->onClickEditButtonAction	="edit";
		$tab->onClickDeleteButtonAction	="delete_temp";
		$tab->hasAddButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasCancelButton=false;
		$tab->hasReopenButton=false;
		$tab->hasFinishButton=false;
		$tab->hasDeleteButton=true;
		$tab->formatLine=true;
		$tab->hiddenFields["IS_ATT2"]=1;
		$tab->startTable();
		$tab->writeTable();
		$tab->stopTable();
}

if($action=="show_old_jobs"){
	$today_show		= date("l jS F Y");
	$start_show 	= date("j F Y",mktime(0, 0, 0, $month,1, $year));
	$start      	= date("Y-m-d",mktime(0, 0, 0, $month,1, $year));
	$end_show 		= date("j F Y",mktime(0, 0, 0, $month,date("t",mktime(0, 0, 0, $month,1,  $year)), $year));
	$end      		= date("Y-m-d",mktime(0, 0, 0, $month,date("t",mktime(0, 0, 0, $month,1,  $year)), $year));
	
?>
	<h1><?=$today_show?></h1>
<?
	if($year && $month){
?>	
		<h2 class="joblist_head">Jobs from: <?=$start_show?> -- <?=$end_show?></h2>
<?
		$qry = get_finished_joblist_query($start,$end);
		$tab = new MySQLTable("index.php",$qry);
		$tab->showRec=0;
		$tab->hasAddButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasCancelButton=false;
		$tab->hasReopenButton=false;
		$tab->hasFinishButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasEditButton=false;
		$tab->onClickEditButtonAction = "unfinish";
		$tab->formatLine=true;
		$tab->hiddenFields["IS_ATT2"]=1;
		$tab->startTable();
		$tab->writeTable();
		$tab->stopTable();
	}
}

if($action=="show_old_jobs_by_pub"){
	$today_show		= date("l jS F Y");
	$start_show 	= date("j F Y",mktime(0, 0, 0, $month,1, $year));
	$start      	= date("Y-m-d",mktime(0, 0, 0, $month,1, $year));
	$end_show 		= date("j F Y",mktime(0, 0, 0, $month,date("t",mktime(0, 0, 0, $month,1,  $year)), $year));
	$end      		= date("Y-m-d",mktime(0, 0, 0, $month,date("t",mktime(0, 0, 0, $month,1,  $year)), $year));
	
?>
	<h1><?=$today_show?></h1>
<?
	if($client_id || $publication){
?>	
		<h2 class="joblist_head">Jobs from: <?=$start_show?> -- <?=$end_show?></h2>
<?
		$qry = get_finished_joblist_by_pub_query($publication,$client_id);
		$tab = new MySQLTable("index.php",$qry);
		$tab->showRec=0;
		$tab->hasAddButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasCancelButton=false;
		$tab->hasReopenButton=false;
		$tab->hasFinishButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasEditButton=false;
		$tab->onClickEditButtonAction = "unfinish";
		$tab->formatLine=true;
		$tab->hiddenFields["IS_ATT2"]=1;
		$tab->startTable();
		$tab->writeTable();
		$tab->stopTable();
	}
}

if($action=="show_quotes"){
	$today_show		= date("l jS F Y");
	$start_show 	= date("j F Y",mktime(0, 0, 0, $month,1, $year));
	$start      	= date("Y-m-d",mktime(0, 0, 0, $month,1, $year));
	$end_show 		= date("j F Y",mktime(0, 0, 0, $month,date("t",mktime(0, 0, 0, $month,1,  $year)), $year));
	$end      		= date("Y-m-d",mktime(0, 0, 0, $month,date("t",mktime(0, 0, 0, $month,1,  $year)), $year));
	
?>
	<h1>Quotes <?=$today_show?></h1>
<?
	$qry = get_quote_joblist_query();
	$tab = new MySQLTable("index.php",$qry);
	$tab->showRec=0;
	$tab->hasAddButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasCancelButton=false;
	$tab->hasReopenButton=false;
	$tab->hasFinishButton=false;
	$tab->hasDeleteButton=true;
	$tab->hasEditButton=false;
	$tab->formatLine=true;
	$tab->hiddenFields["IS_ATT2"]=1;
	$tab->startTable();
	$tab->writeTable();
	$tab->stopTable();
}


/////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.job / coural.job_route				//
//////////////////////////////////////////////////////////
if($action=="" || !isset($action)){
	$user_id = $_COOKIE["coural_userid"];
	$today_show = date("l jS F Y");
	$this_month = date("F");
	$next_month = date("F",strtotime("+1 Month",strtotime(date("Y-m-15"))));
	$prev_month = date("F",strtotime("-1 Month",strtotime(date("Y-m-15"))));

	?>
	<h1><?=$today_show?></h1>
	<h2 class="joblist_head">Jobs in and before <?=$prev_month?></h2>
<?
	if($remove==1){
		$qry = "UPDATE current_job_screen SET publication=null,client_id=null,job_id=null WHERE user_id='$CK_USERID'";
		query($qry);	
	}
	
	if(!$client && !$pub && !$job){
		$qry 	 = "SELECT * FROM current_job_screen  WHERE user_id='$CK_USERID'";
		$res 	 = query($qry);
		$current = mysql_fetch_object($res);
		$client  = $current->client_id;
		$pub 	 = $current->publication;
		$job     = $current->job_id;
	}

	if($submit == "Filter"){
		$qry = get_joblist_query(1,"prev_month",$job,$client,$pub,$start_date,$final_date);
	}
	else{
		$qry = get_joblist_query(1,"prev_month",$job,$client,$pub);
	}
	//echo nl2br($qry);
	$tab = new MySQLTable("index.php",$qry,"table","proc_job.php");
	$tab->showRec=0;
	
	/*$tab->hasForm=true;
	$tab->formPage="rep_revenue.php?report=job_delivery";
	$tab->hasSubmitButton = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Process Delivery";*/
	
	$tab->hasAddButton=false;
	//$tab->hasCheckBoxes=true;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasCancelButton=true;
	$tab->hasReopenButton=true;
	$tab->hasFinishButton=true;
	$tab->hasDeleteButton=true;
	$tab->formatLine=true;
	$tab->hiddenFields["IS_ATT2"]=1;
	$tab->startTable();
	$tab->writeTable();
	$tab->stopTable();

?>
	<h2 class="joblist_head">Jobs in <?=$this_month?></h2>
<?
	if($remove==1){
		$qry = "UPDATE current_job_screen SET publication=null,client_id=null,job_id=null  WHERE user_id='$CK_USERID'";
		query($qry);	
	}
	
	if(!$client && !$pub){
		$qry 	 = "SELECT * FROM current_job_screen  WHERE user_id='$CK_USERID'";
		$res 	 = query($qry);
		$current = mysql_fetch_object($res);
		$client  = $current->client_id;
		$pub 	 = $current->publication;
	}
	if($submit == "Filter"){
		$qry = get_joblist_query(1,"this_month",$job,$client,$pub,$start_date,$final_date);
	}
	else{
		$qry = get_joblist_query(1,"this_month",$job,$client,$pub);
	}
	$tab = new MySQLTable("index.php",$qry,"table","proc_job.php");
	$tab->showRec=0;
	
	/*$tab->hasForm=true;
	$tab->formPage="rep_revenue.php?report=job_delivery";
	$tab->hasSubmitButton = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Process Delivery";*/
	
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	//$tab->hasCheckBoxes=true;
	$tab->hasCancelButton=true;
	$tab->hasReopenButton=true;
	$tab->hasFinishButton=true;
	$tab->hasDeleteButton=true;
	$tab->formatLine=true;
	$tab->hiddenFields["IS_ATT2"]=1;
	$tab->startTable();
	$tab->writeTable();
	$tab->stopTable();

?>	
	<h2 class="joblist_head">Jobs from <?=$next_month?> onwards</h2>
<?

	if($submit == "Filter"){
		$qry = get_joblist_query(0,"next_month",$job,$client,$pub,$start_date,$final_date);
	}
	else{
		$qry = get_joblist_query(0,"next_month",$job,$client,$pub);
	}
	
	$tab = new MySQLTable("index.php",$qry,"table","proc_job.php");
	$tab->showRec=0;
	
	/*$tab->hasForm=true;
	$tab->formPage="rep_revenue.php?report=job_delivery";
	$tab->hasSubmitButton = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Process Delivery";*/
	
	$tab->hasAddButton=false;
	$tab->hasCancelButton=true;
	//$tab->hasCheckBoxes=true;
	$tab->hasReopenButton=true;	
	$tab->hasFinishButton=true;	
	$tab->hasDeleteButton=true;
	$tab->hiddenFields["IS_ATT2"]=1;
	$tab->formatLine=true;
	$tab->startTable();
	$tab->writeTable();
	$tab->stopTable();
}


if($action=="regular_jobs"){
	
	
	$qry = get_regular_joblist_query($start_date,$final_date);
	$tab = new MySQLTable("index.php",$qry,"table","proc_job.php");
	$tab->showRec=0;
	
	/*$tab->hasForm=true;
	$tab->formPage="rep_revenue.php?report=job_delivery";
	$tab->hasSubmitButton = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Process Delivery";*/
	
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	//$tab->hasCheckBoxes=true;
	$tab->hasCancelButton=false;
	$tab->hasReopenButton=false;
	$tab->hasFinishButton=false;
	$tab->hasDeleteButton=false;
	$tab->formatLine=true;
	$tab->hiddenFields["IS_ATT2"]=1;
	$tab->startTable();
	$tab->writeTable();
	$tab->stopTable();

	
}
?>