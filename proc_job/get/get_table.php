<?
	include "../../includes/mysql.php";
	include "../../includes/MySQLSelect.php";
	include "../../includes/MySQLTable.php";
	include "../../includes/mysql_aid_functions.php";
	
	$job_id = $_POST['job_id'];
	$sel = new MySQLSelect("company","operator_id","operator","proc_job.php","table","alt_dropoff_id");
	$sel->selectOnChange="";
	$sel->optionDefText="none";
	$sel->addSQLWhere("is_alt_dropoff","Y");

	$qry = "SELECT 	job_route.job_route_id 	AS Record,
					route.island 		AS Island,
					route.region		AS Region,
					route.area			AS Area,
					route.code			AS RD,
					IF(job_route.dest_type='num_total','Total',
						IF(job_route.dest_type='num_farmers','Farmer',
							IF(job_route.dest_type='num_dairies','Dairy',
								IF(job_route.dest_type='num_sheep','sheep',
									IF(job_route.dest_type='num_beef','Beef',
										IF(job_route.dest_type='num_sheepbeef','Sheep/Beef',
											IF(job_route.dest_type='num_dairybeef','Dairy/Beef',
												IF(job_route.dest_type='num_hort','Hort',
													IF(job_route.dest_type='num_lifestyle','Lifestyle',
														IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
					)))))))))) AS Type,
					job_route.orig_amt			AS 'Orig.-Number',
					job_route.amount			AS Number,
					job_route.version			AS Version,
					job_route.is_edited 		AS Edited,
					if(job_route.alt_dropoff_id>0,'Y','N')
												AS 'Alt'			
			FROM job
			RIGHT JOIN job_route
			ON job.job_id=job_route.job_id
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			WHERE job_route.job_id='$job_id'
			ORDER BY job_route.dest_type,island,seq_region,seq_area,seq_code,type,job_route.job_route_id";
	$tab = new MySQLTable("proc_job.php",$qry,"nocoll");
	
	$tab->onClickEditButtonAction="edit_line";
	$tab->onClickEditButtonAdd.="+'&job_id=$job_id'";
	$tab->cssSQLTable="sqltable_scroll";

	$tab->checkboxTitle="Select";
	$tab->submitButtonName="delete_lines";
	$tab->submitButtonValue="Delete lines";
	$tab->submitButtonName2="set_alternate_do";
	$tab->submitButtonValue2="Set Alt DO";	
	$tab->selectField = $sel;
	$tab->showRec=0;
	$tab->hasAddButton=false;
	$tab->hasEditButton=true;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=true;
	$tab->hasSubmitButton2=true;
	$tab->hasSelectField=true;
	$tab->hasCheckBoxes=true;
	$tab->startTable();
	$tab->writeTable();
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total (excl. bundles and externals):");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type<>'bundles' and (external<>'Y' OR external IS NULL)","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles' and (external<>'Y' OR external IS NULL)","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Externals:");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type<>'bundles' and external='Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles' and external='Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Bundles:");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		$tab->addLine("$total");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		$tab->addLine("$total");		
	$tab->stopNewLine();
	
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","action_from_table");
	$tab->stopTable();			
?>