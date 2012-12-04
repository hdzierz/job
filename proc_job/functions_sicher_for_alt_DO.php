<?

function create_job_no(){
	$last_job_no=get_max("job","job_no","","");
	
	if(!$last_job_no){
		$last_job_no  = 6000;
	}
	else if($last_job_no=="9999") {
		$last_job_no=1;
	}
	else{
		$last_job_no++;
	}
	return $last_job_no;
}

function set_alt_do_contractors($job_id){
	$qry = "SELECT * FROM job_route WHERE job_id='$job_id'";
	$res = query($qry);
	while($jobr = mysql_fetch_object($res)){
		$c_id = get("route","contractor_id","WHERE route_id='$jobr->route_id'");
		if($jobr->alt_dropoff_id>0)
			$qry = "UPDATE job_route 
					SET doff = '$c_id'
					WHERE job_route_id='$jobr->job_route_id'";
		
		else
			$qry = "UPDATE job_route 
					SET doff = dropoff_id
					WHERE job_route_id='$jobr->job_route_id'";
		query($qry);
	}
}


function show_print_table($job_id){
	set_alt_do_contractors($job_id);

	$today = date('Y-m-d');
	$qry = "SELECT * FROM job
			LEFT JOIN client_pub
			ON job.client_pub_id=client_pub.client_pub_id
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$obj = mysql_fetch_object($res);
	
	$name  = get("address","name","WHERE address_id='$obj->contact_name_id'");
	$phone = get("address","phone","WHERE address_id='$obj->contact_name_id'");
	$delivery_date = date("d-M-y",strtotime($job->delivery_date));
?>
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3">COURAL DROP OFF DETAILS</th>
			<th><?=$today?></th>
		</tr>
		<tr>
			<?
				if($obj->is_regular=='Y') echo "REGULAR";
				else echo "CASUAL";
			?>
		</tr>
		<tr>
			<td>Client: </td>
			<td><?=$obj->name?></td>
			<td>Publication: </td>
			<td><?=$obj->publication?></td>					
		</tr>
		<tr>
			<td>Job. #:</td>
			<td><b><?=$obj->job_no?></b></td>
			<td>Invoice #:</td>
			<td><?=$obj->invoice_no?></td>
		</tr>
		<tr>
			<td>Delivery Date:</td>
			<td><?=$obj->delivery_date?></td>
			<td>Line Hauler:</td>
			<td><?=$obj->freight?></td>
		</tr>
		<tr>
			<td>Contact Name:</td>
			<td><?=$name?></td>
			<td>Phone:</td>
			<td><?=$phone?></td>
		</tr>				
	</table>
	<hr />
<?

	$qry = "SELECT DISTINCT dist_id,company 
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=route.dist_id
			WHERE job_route.job_id='$job_id'
			AND dist_id IS NOT NULL 
            AND company IS NOT NULL
            ORDER BY seq_region";
	$res_dist = query($qry,0);
	
	$tab = new MySQLTable("proc_job.php","","");
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;	
	$tab->startTable();
	while($dist = mysql_fetch_object($res_dist)){
		$tab->startNewLine();
			$tab->addLine("Distributor:".$dist->company,3);
		$tab->stopNewLine();

		$qry = "SELECT route.region				AS Region,
					   IF(address.name2 <> '',
							CONCAT(address.name,', ',address.first_name,' and ',address.name2,', ',address.first_name2)	,
							IF(address.first_name <>'',
								CONCAT(address.name,', ',address.first_name),
								CONCAT(address.name)
							)
						)
												AS Name,
						IF(job_route.alt_dropoff_id>0,
							(SELECT CONCAT(address.address,' ',address.city) FROM address WHERE address.operator_id=job_route.alt_dropoff_id),
							CONCAT(address.address,' ',address.city))
												AS 'Delivery Point',
						IF(job_route.alt_dropoff_id>0,'Y','N') 
												AS 'ALT DO',
						IF(dest_type='num_total','Total',
							IF(dest_type='num_farmers','Farmer',
								IF(dest_type='num_dairies','Dairy',
									IF(dest_type='num_sheep','sheep',
										IF(dest_type='num_beef','Beef',
											IF(dest_type='num_sheepbeef','Sheep/Beef',
												IF(dest_type='num_dairybeef','Dairy/Beef',
													IF(dest_type='num_hort','Hort',
														IF(dest_type='num_lifestyle','Lifestyle',
															IF(dest_type='bundles','Bundles',dest_type	
						)))))))))) AS Type,			
					   job_route.version 		AS Version,
					   SUM(job_route.amount) 	AS Number
				FROM job_route
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				LEFT JOIN operator
				ON operator.operator_id=job_route.doff
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				WHERE job_route.job_id='$job_id'
						AND route.dist_id='$dist->dist_id'
				GROUP BY route.region,job_route.doff,job_route.dest_type
				ORDER BY route.seq_region,job_route.dest_type,company";
		//echo nl2br($qry)."<br />";
		//$res = query($qry);
		
		$tab->writeSQLTableElement($qry,1);
		$tab->startNewLine();
			$tab->addLines("",4);
			$tab->addLine("Total (excl. bundles and externals):");
			$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles' and job_route.external<>'Y' GROUP BY job_id,dist_id ";
			$res_sum = query($qry);
			$sum = mysql_fetch_object($res_sum);
			$total = $sum->sum;
			if(!$total) $total=0;
			$tab->addLine("$total");
		$tab->stopNewLine();		
		$tab->startNewLine();
			$tab->addLines("",4);
			$tab->addLine("Total (bundles):");
			$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles' and job_route.external<>'Y' GROUP BY job_id,dist_id ";
			$res_sum = query($qry);
			$sum = mysql_fetch_object($res_sum);
			$total = $sum->sum;
			if(!$total) $total=0;
			$tab->addLine("$total");
		$tab->stopNewLine();		
		
	}		
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total (excl. bundles and externals):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles' and external<>'Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Externals:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles' and external='Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Bundles:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
	$tab->stopNewLine();
		
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","delete_lines");
	$tab->stopTable();		
?>
	<table>
		<tr>
			<th align="left">Comments</th>
		</tr>
		<tr>
			<td><? echo nl2br($job->comments)?></td>
		</tr>
	</table>
<?	
}


function show_print_table_sicher($job_id){
	set_alt_do_contractors($job_id);

	$today = date('Y-m-d');
	$qry = "SELECT * FROM job
			LEFT JOIN client_pub
			ON job.client_pub_id=client_pub.client_pub_id
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$obj = mysql_fetch_object($res);
	
	$name  = get("address","name","WHERE address_id='$obj->contact_name_id'");
	$phone = get("address","phone","WHERE address_id='$obj->contact_name_id'");
	$delivery_date = date("d-M-y",strtotime($job->delivery_date));
?>
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3">COURAL JOB DETAILS</th>
			<th><?=$today?></th>
		</tr>
		<tr>
			<?
				if($obj->is_regular=='Y') echo "REGULAR";
				else echo "CAUSUAL";
			?>
		</tr>
		<tr>
			<td>Client: </td>
			<td><?=$obj->name?></td>
			<td>Publication: </td>
			<td><?=$obj->publication?></td>					
		</tr>
		<tr>
			<td>Job. #:</td>
			<td><b><?=$obj->job_no?></b></td>
			<td>Invoice #:</td>
			<td><?=$obj->invoice_no?></td>
		</tr>
		<tr>
			<td>Delivery Date:</td>
			<td><?=$obj->delivery_date?></td>
			<td>Line Hauler:</td>
			<td><?=$obj->freight?></td>
		</tr>
		<tr>
			<td>Contact Name:</td>
			<td><?=$name?></td>
			<td>Phone:</td>
			<td><?=$phone?></td>
		</tr>				
	</table>
<?
	$qry = "SELECT route.region				AS Region,
				   IF(address.name2 <> '',
				   		CONCAT(address.name,', ',address.first_name,' and ',address.name2,', ',address.first_name2)	,
						IF(address.first_name <>'',
							CONCAT(address.name,', ',address.first_name),
							CONCAT(address.name)
						)
					)
				   							AS Name,
					IF(job_route.alt_dropoff_id>0,
						(SELECT CONCAT(address.address,' ',address.city) FROM address WHERE address.operator_id=job_route.alt_dropoff_id),
						CONCAT(address.address,' ',address.city))
				   							AS 'Delivery Point',
					IF(job_route.alt_dropoff_id>0,'Y','N') 
											AS 'ALT DO',
					IF(dest_type='num_total','Total',
						IF(dest_type='num_farmers','Farmer',
							IF(dest_type='num_dairies','Dairy',
								IF(dest_type='num_sheep','sheep',
									IF(dest_type='num_beef','Beef',
										IF(dest_type='num_sheepbeef','Sheep/Beef',
											IF(dest_type='num_dairybeef','Dairy/Beef',
												IF(dest_type='num_hort','Hort',
													IF(dest_type='num_lifestyle','Lifestyle',
														IF(dest_type='bundles','Bundles',dest_type	
					)))))))))) AS Type,			
				   job_route.version 		AS Version,
				   SUM(job_route.amount) 	AS Number,
				   SUM(job_route.orig_amt) 	AS 'Orig.-Number'
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=job_route.doff
			LEFT JOIN address
			ON operator.operator_id=address.operator_id
			WHERE job_route.job_id='$job_id'
			GROUP BY route.region,job_route.doff,job_route.dest_type
			ORDER BY route.seq_region,job_route.dest_type,company";
	//$res = query($qry,1);
	
	$tab = new MySQLTable("proc_job.php",$qry,"nocoll");
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;
	$tab->startTable();
	$tab->writeTable();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total (excl. bundles and externals):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles' and external<>'Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type<>'bundles' and external<>'Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Externals:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles' and external='Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type<>'bundles' and external='Y'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Bundles:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		$tab->addLine("$total");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		$tab->addLine("$total");
	$tab->stopNewLine();
		
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","delete_lines");
	$tab->stopTable();		
?>
	<table>
		<tr>
			<th align="left">Comments</th>
		</tr>
		<tr>
			<td><? echo nl2br($job->comments)?></td>
		</tr>
	</table>
<?	
}

function show_print_table1($job_id){
	$today = date('Y-m-d');
	$qry = "SELECT * FROM job
			LEFT JOIN client_pub
			ON job.client_pub_id=client_pub.client_pub_id
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$job = mysql_fetch_object($res);
	
	$delivery_date = date("d-M-y",strtotime($job->delivery_date));
?>
	<table cellspacing="10">
		<tr>
			<th colspan="3">COURAL JOB DETAILS</th>
			<th><?=$today?></th>
		</tr>
		<tr>
			<td>Publication:</td>
			<td><?=$job->publication?></td>
		</tr>
		<tr>
			<td>Publisher:</td>
			<td><?=$job->name?></td>
		</tr>
		<tr>
			<td>Job No:</td>
			<td><b><?=$job->job_no?></b></td>
			<td>Line Hauler</td>
			<td><?=$job->freight?></td>
		</tr>
		<tr>
			<td>Delivery Date</td>
			<td><b><?=$delivery_date?></b></td>
		</tr>
	</table>
<?
	$qry = "SELECT route.region				AS Region,
				   route.area				AS Area,
					IF(dest_type='num_total','Total',
						IF(dest_type='num_farmers','Farmer',
							IF(dest_type='num_dairies','Dairy',
								IF(dest_type='num_sheep','sheep',
									IF(dest_type='num_beef','Beef',
										IF(dest_type='num_sheepbeef','Sheep/Beef',
											IF(dest_type='num_dairybeef','Dairy/Beef',
												IF(dest_type='num_hort','Hort',
													IF(dest_type='num_lifestyle','Lifestyle',
														IF(dest_type='bundles','Bundles',dest_type	
					)))))))))) AS Type,			
				   job_route.version 		AS Version,
				   SUM(job_route.amount) 	AS Number
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=route.dropoff_id
			LEFT JOIN address
			ON operator.operator_id=address.operator_id
			WHERE job_route.job_id='$job_id'
			GROUP BY route.region,route.area,job_route.dest_type
			ORDER BY route.seq_region,route.seq_area,job_route.dest_type,company";
	//$res = query($qry,1);
	
	$tab = new MySQLTable("proc_job.php",$qry,"nocoll");
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;
	$tab->startTable();
	$tab->writeTable();
	
	$tab->startNewLine();
	$tab->addLines("",2);
	$tab->addLine("Total (excl. Bundles):");
	$total = get_sum("job_route","amount","WHERE job_id='$job_id' AND dest_type<>'bundles'","GROUP BY job_id");
	$tab->addLine("$total");
	$tab->stopNewLine();
	
	$tab->startNewLine();
	$tab->addLines("",2);
	$tab->addLine("Total Bundles:");
	$total = get_sum("job_route","amount","WHERE job_id='$job_id' AND dest_type='bundles'","GROUP BY job_id");
	$tab->addLine("$total");
	$tab->stopNewLine();
		
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","delete_lines");
	$tab->stopTable();		
?>
	<table>
		<tr>
			<th align="left">Comments</th>
		</tr>
		<tr>
			<td><? echo nl2br($job->comments)?></td>
		</tr>
	</table>
<?	
}



/*function show_table($job_id){
?>
	<form name="show_table" action="proc_job.php" method="get">
		<input type="button" name="show_table" value="Show Table" onClick="javascript:get(this,'show_table','proc_job/get/get_table.php');" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />
	</form>
	<span name='show_table' id="show_table"></span>
<?
}*/

function show_table($job_id){
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
}



function show_bundle_table($job_id){
	$qry = "SELECT 	job_route_bundle.job_route_bundle_id 	AS Record,
					(SELECT company FROM operator WHERE operator.operaor_id=job_route_bundle.contratcor_id) 
															AS Contractor,
					job_route_bundle.amount					AS Number,
					job_route_bundle.orig_amt				AS 'Orig.-Number',
					job_route_bundle.version				AS Version,
					job_route_bundle.is_edited 				AS Edited			
			FROM job
			RIGHT JOIN job_route_bundle
			ON job.job_id=job_route_bundle.job_id
			WHERE job_route_bundle.job_id='$job_id'
			ORDER BY Contractor";
	$tab = new MySQLTable("proc_job.php",$qry,"nocoll");
	
	$tab->onClickEditButtonAction="edit_line";
	$tab->onClickEditButtonAdd.="+'&job_id=$job_id'";

	$tab->checkboxTitle="Select for Deletion";
	$tab->submitButtonName="Delete lines";
	$tab->submitButtonValue="Delete lines";
	$tab->showRec=0;
	$tab->hasAddButton=false;
	$tab->hasEditButton=true;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=true;
	$tab->hasCheckBoxes=true;
	$tab->startTable();
	$tab->writeTable();
	$tab->startNewLine();
	$tab->addLines("",4);
	$tab->addLine("Total:");
	$total = get_sum("job_route","amount","WHERE job_id='$job_id'","GROUP BY job_id");
	$tab->addLine("$total");
	$tab->stopNewLine();
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","delete_bundle_lines");
	$tab->stopTable();		
}



function write_edit_job_details($job_id,$island,$region,$area,$code,$dest_type){
?>
	<form name="editjob" action="proc_job.php" method="get">
		<table class="form">
			<tr>
				<td>
					<input class="edit_job_details_button"  type="submit" name="submit" value="Edit Job Details" />
				</td>
			</tr>
		</table>
	<input type="hidden" name="action" value="edit_job" />
	<input type="hidden" name="job_id" value="<?=$job_id?>" />
	<input type="hidden" name="dest" value="job" />
	</form>
<?
}

function write_addroute_form($action,$job_id,$island,$region,$area,$code,$type,$bund_contr){

	$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
?>	
	<form name="narrow" id="narrow" method="post" action="proc_job.php">
		<table height="250"  class="form">
			<td>
				<th id="addjob_header" colspan="7">Add Route</th>
			</td>
			<tr>
				<th width="100">Island:</th>
				<th width="160">Region:</th>
				<th width="250">Area:</th>
				<th width="200">RD:</th>						
				<th width="160">Version:</th>			
			</tr>
			<tr valign="top">
				<td valign="top" >
					<select multiple size="5" style="width:4em " name="island[]" onchange="">
						<option value="NI">NI</option>
						<option value="SI">SI</option>
					</select>
					<input name="submit" type="button" value=">>" onClick="javascript:get(this.parentNode,'region_reg','proc_job/get/get_region.php');" />
				</td>
						
				<td valign="top">
					<span name="region_reg" id="region_reg"></span>
				</td>
				<td valign="top">
					<span name="area_reg" id="area_reg"></span>
				</td>
				<td valign="top">
					<span name="code_reg" id="code_reg"></span>
				</td>
				<td valign="top">
					<input type="text" name="version" value="<?=$version?>" />
				</td>	
			</tr>				
			<tr>
				<td colspan="5" align="center"><input name="submit" value="Add Route(s)" type="submit" /></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="addarea" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />		
		<input type="hidden" name="bundle_price" value="<?=0.00?>" />
		<input type="hidden" name="type" value="<?=$dest_type?>" />
	</form>
<?	
}


function write_addaltdo_form($action,$job_id,$island,$region,$area,$code,$type,$bund_contr){

	$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
	if(!$type) $type = $dest_type;
?>	
	<form name="narrow" id="narrow" method="post" action="proc_job.php">
		<table height="300"  class="form">
			<td>
				<th id="addjob_header" colspan="7">Add </th>
			</td>
			<tr>
				<th width="160">Island:</th>
				<th width="160">Region:</th>
				<th width="160">Area:</th>
				<th width="160">RD:</th>						
				<th width="160">Version:</th>			
			</tr>
			<tr valign="top">
				<td valign="top" >
					<select multiple size="5" style="width:4em " name="island[]" onchange="">
						<option value="NI">NI</option>
						<option value="SI">SI</option>
					</select>
					<input name="submit" type="button" value=">>" onClick="javascript:get(this.parentNode,'region_reg','proc_job/get.php');" />
				</td>
						
				<td valign="top">
					<span name="region_reg" id="region_reg"></span>
				</td>
				<td valign="top">
					<span name="area_reg" id="area_reg"></span>
				</td>
				<td valign="top">
					<span name="code_reg" id="code_reg"></span>
				</td>
				<td valign="top">
					<input type="text" name="version" value="<?=$version?>" />
					<input name="submit" value="Add Route(s)" type="submit" />
				</td>	
			</tr>				
		</table>
		<input type="hidden" name="action" value="addarea" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />		
		<input type="hidden" name="bundle_price" value="<?=0.00?>" />
		<input type="hidden" name="type" value="<?=$type?>" />
	</form>
<?	
}


function write_addbundle_form($action,$job_id,$island,$region,$area,$code,$type,$bund_contr){
	$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
	if(!$type) $type = $dest_type;
?>
	<form name="narrow" method="get" action="proc_job.php">
		<table class="form">
			<tr>
				<th id="addjob_header" colspan="7">Add Bundles</th>
			</tr>
			<tr>
				<th style="color:#FF0000 ">Contractor:</th>	
				<th>&nbsp;</th>
				<th style="color:#FF0000 ">Quantity:</th>	
				<th style="color:#FF0000 ">Bundle Price:</th>
			</tr>
			<tr>
<?					
				$bundle_contr_value = get("operator","company","WHERE operator_id='$bund_contr'");
?>
				<script type="text/javascript" src="javascripts/prototype.js"></script>
				<script type="text/javascript" src="javascripts/effects.js"></script>
				<script type="text/javascript" src="javascripts/controls.js"></script>					
				<td>
					<? 
						$query_str = $_SERVER['QUERY_STRING']; 
					?>
					<input type="text" id="bundle_contr" name="bundle_contr" />
					<div id="hint" class="hint"></div>
					<script type="text/javascript">	
						new Ajax.Autocompleter("bundle_contr","hint","includes/search_server.php?<?=$query_str?>");
					</script>    					
				</td>
				<td>
					<input disabled type="text" name="bundle_contr_value" value="<?=$bundle_contr_value?>" />
				</td>					
				<td>
						<input type="text" name="amount" value="<?=$amount?>" />
				</td>					
				<td>
<?				
					if(!$bundle_price) $bundle_price=1.00;
?>
					<input type="text" name="bundle_price" value="<?=sprintf("%.2f",$bundle_price)?>" />
				</td>
				<td colspan="2" align="center">
					<input type="submit" name="sub_area" value="Add Bundle(s)"  />
					<input type="hidden" name="bundles" value="true"  />
					<input type="hidden" name="bund_contr" value="<?=$bund_contr?>"  />
				</td>						
				
			</tr>						
		</table>
		<input type="hidden" name="action" value="addarea" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />
		<input type="hidden" name="type" value="bundles" />
	</form>		
<?
		
	}
?>