<?
// Removes an attached job from the reference job
function delete_alt_job($job_id){
	// get the reference job
	$job_no = get("job_id","job","WHERE job_id='$job_id'");
	$delivery_date = get("delivery_date","job","WHERE job_id='$job_id'");
	$ref_job_id = get("job_id","job","WHERE job_no = '$job_no' AND delivery_date ='$delivery_date'");
	// delete from job list
	$qry = "DELETE FROM job WHERE job_id='$job_id'";
	query($qry);
	
	// delete booked routes
	$qry = "DELETE FROM job_route WHERE job_id='$job_id'";
	query($qry);
	
	// This should set the alt_job_if to null on the reference job.
	$qry = "UPDATE job SET alt_job_id=null WHERE job_id=$ref_job_id;";	
	query($qry);
}

// Change the dest type. Due to the database structure teh dest type has to be changed also on teh booked routes. Sorry about that! Tried to be too smart.
// However, the procedure has also to change the amounts. So it is not too bad actually.
function change_type($job_id,$type){
	// Get all booked routes which are no bundles
	$qry = "SELECT * FROM job_route WHERE job_id='$job_id' AND dest_type <> 'bundles'";
	$res = query($qry);
	
	while($job_route = mysql_fetch_object($res)){
		if($type=="num_total") $type_sel = "(num_farmers+num_lifestyle)";
		else $type_sel = $type;
		
		$amount = get("route","$type_sel","WHERE route_id='$job_route->route_id'");
		
		$qry = "UPDATE job_route SET amount='$amount', orig_amt='$amount', is_edited='N', dest_type='$type' WHERE job_route_id='$job_route->job_route_id'";
		query($qry);
		
	}
	// Change the type on the ob.
	$qry = "UPDATE job SET dest_type='$type' WHERE job_id='$job_id'";
	query($qry);
}

// Creates a new job number. Goes from 0000 to 99999. Rols back after 99999 to 0000.
function create_job_no(){
	// Get the largest number. Checks for < 10000 just in case somebody has entered a large dummy number.
	$last_job_no=get_max("job","job_no","WHERE job_no<100000","");

	// If for some reason the selection of the last job_no fails then the last job number is the highest anyway or
	// if that fails too the job number will be 100000. That way a failure always shows numbers >= 100000.
	
	if(!$last_job_no){
		$last_job_no=get_max("job","job_no","","");
		if(!$last_job_no){
			$last_job_no=100000;
		}
	}
	else if($last_job_no=="99999") {
		$last_job_no=1;
	}
	else{
		$last_job_no++;
	}
	return $last_job_no;
}

// The actual drop off point is specified by the field doff in the job _route table. This routine copies alternate drop
// off ids to the doff field
function set_alt_do_contractors($job_id){
	$qry = "SELECT * FROM job_route WHERE job_id='$job_id'";
	$res = query($qry);
	while($jobr = mysql_fetch_object($res)){
		//$c_id = get("route","contractor_id","WHERE route_id='$jobr->route_id'");
		//$do_id = get("route","dropoff_id","WHERE route_id='$jobr->route_id'");
		//if(!$do_id) $do_id=0;
		if($jobr->alt_dropoff_id>0)
			$qry = "UPDATE job_route 
					SET doff = alt_dropoff_id
					WHERE job_route_id='$jobr->job_route_id'";
		
		else
			$qry = "UPDATE job_route 
					SET	doff=dropoff_id
					WHERE job_route_id='$jobr->job_route_id'";
		//echo nl2br($qry);
		query($qry);
	}
}

// Return field aliases for the numbers
function return_print_type($dest_type){
	if($dest_type=='num_total') $dest_type = 'Total';
	else if($dest_type=='num_farmers') $dest_type = 'Farmer';
	else if($dest_type=='num_dairies') $dest_type = 'Dairy';
	else if($dest_type=='num_sheep') $dest_type = 'Sheep';
	else if($dest_type=='num_beef') $dest_type = 'Beef';
	else if($dest_type=='num_sheepbeef') $dest_type = 'Sheep/Beef';
	else if($dest_type=='num_dairybeef') $dest_type = 'Dairy/Beef';
	else if($dest_type=='num_hort') $dest_type = 'Hort';
	else if($dest_type=='num_spare') $dest_type = 'Spare';
	else if($dest_type=='num_lifestyle') $dest_type = 'Lifestyle';
	else if($dest_type=='num_nzfw') $dest_type = 'F@90%';
	else if($dest_type=='bundles') $dest_type = 'Bundles';
	else $dest_type = 'Unknown';
	return $dest_type;
}


// Many reports and the main eduit screen have the same haeder. This function just writes it.
function write_header($job_id,$title){
	$qry = "SELECT * FROM job
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$obj = mysql_fetch_object($res);
	
	$name  = get("address","name","WHERE address_id='$obj->contact_name_id'");
	$phone = get("address","phone","WHERE address_id='$obj->contact_name_id'");
	$hauler_ni = get("client","name","WHERE client_id='$obj->hauler_ni_id'");
	$hauler_si = get("client","name","WHERE client_id='$obj->hauler_si_id'");
	
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else{
		$delivery_date = date("d M Y",strtotime($obj->delivery_date));
	}
	
	$dest_type = return_print_type($obj->dest_type);
	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	if($alt_job_id){
		$dest_type2 = get("job","dest_type","WHERE job_id='$alt_job_id'");
		$dest_type2 = "/".return_print_type($dest_type2);
	}
?>
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3"><?=$title?></th>
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
			<td>Farmer Type:</td>
			<td><?=$dest_type?><?=$dest_type2?></td>
		</tr>
		<tr>
			<td>Delivery Date:</td>
			<td><?=$delivery_date?></td>
			<td>NI Linehaul:</td>
			<td><?=$hauler_ni?></td>
			<td>SI Linehaul:</td>
			<td><?=$hauler_si?></td>
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
	// Some reports need the comments returned
	return $obj->comments;
}

// Some reports need the header without contact. In this case it is ther PDF version of teh header
function write_header_without_contact_pdf($tab,$job_id,$title,$margin=false,$maxw){
	$qry = "SELECT * FROM job
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$obj = mysql_fetch_object($res);
	$name  = get("address","name","WHERE address_id='$obj->contact_name_id'");
	$phone = get("address","phone","WHERE address_id='$obj->contact_name_id'");
	
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else{
		$delivery_date = date("d M Y",strtotime($obj->delivery_date));
	}
	
	$dest_type = return_print_type($obj->dest_type);
	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	if($alt_job_id){
		$dest_type2 = get("job","dest_type","WHERE job_id='$alt_job_id'");
		$dest_type2 = "/".return_print_type($dest_type2);
	}
	
	$hauler_ni = get("client","name","WHERE client_id='$obj->hauler_ni_id'");
	$hauler_si = get("client","name","WHERE client_id='$obj->hauler_si_id'");
	
	if(!$hauler_ni) $hauler_ni = $obj->hauler;
	if(!$hauler_si) $hauler_si = $obj->hauler;
	
	$tab->StartLine(8);
		$tab->WriteLine($title,'L',7,$maxw);
	$tab->StopLine();	
	
	$cellwidth=20;
	$tab->StartLine(7);
		$tab->WriteLine("Client:",'L',7,$cellwidth);
		$tab->WriteLine($obj->name,'L',7,$cellwidth);
		$tab->WriteLine("Publication:",'L',7,$cellwidth);
		$tab->WriteLine($obj->publication,'L',7,$cellwidth);
		if($margin){
			$tab->WriteLine("Margin:",'L',7,$cellwidth);
			$tab->WriteLine($margin,'L',7,$cellwidth);
		}
	$tab->StopLine();
	
	$tab->StartLine(7);
		$tab->WriteLine("Job. #:",'L',7,$cellwidth);
		$tab->WriteLine($obj->job_no,'L',7,$cellwidth);
		$tab->WriteLine("Farmer Type:",'L',7,$cellwidth);
		$tab->WriteLine($dest_type.$dest_type2,'L',7,$cellwidth);
	$tab->StopLine();
	
	$tab->StartLine(7);
		$tab->WriteLine("Delivery Date:",'L',7,$cellwidth);
		$tab->WriteLine($delivery_date,'L',7,$cellwidth);
		$tab->WriteLine("Line Hauler NI:",'L',7,$cellwidth);
		$tab->WriteLine($hauler_ni,'L',7,$cellwidth);
		$tab->WriteLine("Line Hauler SI:",'L',7,$cellwidth);
		$tab->WriteLine($hauler_si,'L',7,$cellwidth);

	$tab->StopLine();
	
	return $obj->comments;
}
// Some reports need the header without contact. 
function write_header_without_contact($job_id,$title,$margin=false){
	$qry = "SELECT * FROM job
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$obj = mysql_fetch_object($res);
	$name  = get("address","name","WHERE address_id='$obj->contact_name_id'");
	$phone = get("address","phone","WHERE address_id='$obj->contact_name_id'");
	
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else{
		$delivery_date = date("d M Y",strtotime($obj->delivery_date));
	}
	
	$dest_type = return_print_type($obj->dest_type);
	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	if($alt_job_id){
		$dest_type2 = get("job","dest_type","WHERE job_id='$alt_job_id'");
		$dest_type2 = "/".return_print_type($dest_type2);
	}
	
	$hauler_ni = get("client","name","WHERE client_id='$obj->hauler_ni_id'");
	$hauler_si = get("client","name","WHERE client_id='$obj->hauler_si_id'");
?>
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3"><?=$title?></th>
		</tr>
		<tr>
			<td>Client: </td>
			<td><?=$obj->name?></td>
			<td>Publication: </td>
			<td><?=$obj->publication?></td>		
<?
			if($margin){
?>
				<td style="color:#FF0000; font-weight:bold ">Margin:</td>
				<td style="color:#FF0000; font-weight:bold "><?=$margin?>%</td>
<?
			}
?>
		</tr>
		<tr>
			<td>Job. #:</td>
			<td><b><?=$obj->job_no?></b></td>
			<td>Farmer Type:</td>
			<td><?=$dest_type?><?=$dest_type2?></td>
		</tr>
		<tr>
			<td>Delivery Date:</td>
			<td><?=$delivery_date?></td>
			<td>NI Linehaul:</td>
			<td><?=$hauler_ni?></td>
			<td>SI Linehaul:</td>
			<td><?=$hauler_si?></td>
		</tr>
		
	</table>
	<hr />
<?	
	return $obj->comments;
}

// Some reports will have the comments from the job as well as from the booked routes printed. 
// This routine merges those comments to a single string
function collect_comment($dist,$job_id){
	$show_comments = get("job","show_comments","WHERE job_id='$job_id'");
	
	if($show_comments=='Y'){
		$comment = get("job","comments","WHERE job_id='$job_id'");
	}
	else{
		$comment = "";
	}
	
	
	$qry = "SELECT comments FROM job_route 
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			WHERE job_route.job_id='$job_id'
				AND dist_id=$dist";
	$res_dist = query($qry,0);
	
	while($route = mysql_fetch_object($res_dist)){
		if($route->comments){
			$comment.="<br />";
			$comment.=$route->comments;
		}
	}
	return $comment;
}

// This routine will recall an existing drop off detail report.
function show_do_details_from_send_out($send_report_id){
	$qry = "SELECT * FROM send_report WHERE send_report_id='$send_report_id'";
	$res = query($qry);
	$report = mysql_fetch_object($res);
	$job_id = str_replace("(","",$report->jobs);
	$job_id = str_replace(")","",$report->jobs);
	
	show_do_details($job_id);
	
}

// This routine will show the drop off details for a single job.
function show_do_details($job_id,$export=0){
	// Copy the alternative drop off points over to the doff field.
	set_alt_do_contractors($job_id);
	
	// Check whether there is an attached job
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	// Get the dest type from the attached job too
	$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
	$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));

	// INitialise the grand totals
	$grand_tot_bund=0;
	$grand_tot_circ=0;
	$grand_tot_circ_alt=0;
	
	if($island=="NI") $island_show = "North Island";
	if($island=="SI") $island_show = "South Island";
	
	// Write the haeder of teh report
	write_header($job_id,"COURAL DROP OFF DETAILS ($island_show)");
	
	// If export the report will wrute the result to an pseudo excel file for download
	if($export)
		$tab = new MySQLExport("export.html","");
	else
		$tab = new MySQLTable("proc_job.php","","");
		
	// No buttons and such.
	$tab->showRec				= 1;
	$tab->hasAddButton			= false;
	$tab->hasEditButton			= false;
	$tab->hasDeleteButton		= false;
	$tab->hasSubmitButton		= false;
	$tab->hasCheckBoxes			= false;	
	
	// Field for subtotals
	$tab->collField[$dest_type] = true;
	$tab->collField[$alt_dest_type] = true;
	$tab->collField["Bdls"] = true;
	
	
	//$tab->sumFields["Circ"] 	= 1;
	//$tab->sumGroupField["Name"] = 1;
	$tab->colWidth["Name"]		= 180;
	$tab->startTable();
	// Get the regions
		$qry = "SELECT DISTINCT region 
				FROM route
				LEFT JOIN job_route
				ON job_route.route_id=route.route_id
				WHERE job_id='$job_id'
				ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry,0);
		$start=true;
		
		$comments = collect_comment(0,$job_id);
		while($region = mysql_fetch_object($res_reg)){
			if($alt_job_id){
				$add_sel = ",SUM(IF(job_route.dest_type<>'bundles',jra.amount,0))			AS '$alt_dest_type'";
				$add = "LEFT JOIN job_route jra
						ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
			}		
			
			$qry = "SELECT  IF(
								operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
								IF(
									address.first_name2 IS NOT NULL AND address.first_name2<>'',
										CONCAT(address.first_name2,' ',IF(address.name2 IS NOT NULL,address.name2,''),' &amp ',address.first_name,' ',address.name),
										CONCAT(address.first_name,' ',address.name)
								)
							)		AS Name,
							CONCAT(operator.do_address,IF(operator.do_city<>'',', ',''),operator.do_city,IF(operator.deliv_notes<>'',', Notes:',''),operator.deliv_notes)
													AS 'Delivery Point',
							IF(job_route.alt_dropoff_id>0,'Y','N') 
													AS 'ALT DO',
						   job_route.version 		AS Version,
						   SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS Bdls,
						   SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) AS '$dest_type'
						   $add_sel	
					FROM job_route
					$add
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					LEFT JOIN operator
					ON operator.operator_id=job_route.doff
					LEFT JOIN address
					ON operator.operator_id=address.operator_id
					WHERE job_route.job_id='$job_id'
							AND route.region = '$region->region'
					GROUP BY route.region,job_route.doff,job_route.version
					ORDER BY island,route.seq_region,seq_area,company";
			//echo nl2br($qry)."<br /><br />";
			$res_t = query($qry);
			if(mysql_num_rows($res_t)>0){
				
				$tab->startNewLine();
					$tab->addLineWithStyle("Region: ".$region->region,"sql_extra_line_text10",3);
				$tab->stopNewLine();						

				$tab->writeSQLTableElement($qry,1);
				$start_doff=false;
				$tab->startNewLine();
					$tab->addLines("",2);
					$tab->addLine("Total Region:");
					
					$total = (int)$tab->getSum("Bdls");
					$tab->collFieldVal["Bdls"] = array();
					
					$grand_tot_bund+=$total;
					if(!$total) $total=0;
					$tab->addLine("$total");
					
					$total = (int)$tab->getSum($dest_type);
					$tab->collFieldVal[$dest_type] = array();
										
					$grand_tot_circ+=$total;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$total = (int)$tab->getSum($alt_dest_type);
						$tab->collFieldVal[$alt_dest_type] = array();
						
						$grand_tot_alt_circ+=$total;
						if(!$total) $total=0;
						$tab->addLine("$total");			
					}		
				$tab->stopNewLine();		
			}
		}//while($region = mysql_fetch_object($res_reg))
		
	
	$tab->startNewLine();
		$tab->addLines("",2);
		$tab->addLine("Total:");
		$tab->addLine("$grand_tot_bund");		
		$tab->addLine("$grand_tot_circ");
		if($alt_job_id){
			$tab->addLine("$grand_tot_alt_circ");		
		}
	$tab->stopNewLine();
	
	
	
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","delete_lines");
	$tab->stopTable();		
?>
	<table class="job_comments">
		<tr>
			<th align="left">Comments</th>
		</tr>
		<tr>
			<td><? echo nl2br($comments)?></td>
		</tr>
	</table>
<?	
	if($export){
?>
		<a href="export.html">Download by right click</a>
<?	
	}
}




// The same as show_do_details but has additional pagebreaks.

function show_do_details_with_pagebreak($job_id,$export=0){
	// Instantiate alternative drop off points
	set_alt_do_contractors($job_id);
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	
	$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
	$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));
	
	$islands[]="NI";
	$islands[]="SI";
	
	foreach($islands as $island){
		$qry = "SELECT DISTINCT region
				FROM job_route
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				WHERE job_route.job_id='$job_id'
					AND island='$island'
				ORDER BY island,seq_region,seq_area";
				
		$res_reg = query($qry,0);
		
		if($export)
			$tab = new MySQLExport("export.html","");
		else
			$tab = new MySQLTable("proc_job.php","","");
			
		$tab->showRec=1;
		$tab->hasAddButton=false;
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasSubmitButton=false;
		$tab->hasCheckBoxes=false;	
		$tab->colWidth["Name"]=180;		
		
		$tab->collField[$dest_type] = true;
		$tab->collField[$alt_dest_type] = true;
		
		while($region = mysql_fetch_object($res_reg)){
			
			write_header($job_id,"COURAL DROP OFF DETAILS");
			$tab->startTable();
			$qry = "SELECT DISTINCT dist_id,company 
				FROM job_route
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				LEFT JOIN operator
				ON operator.operator_id=job_route.dist_id
				WHERE job_route.job_id='$job_id'
					AND region='$region->region'
					AND island='$island'
				ORDER BY island,seq_region,seq_area";
	
			$res_dist = query($qry,0);
			$start=true;
			
			while($dist = mysql_fetch_object($res_dist)){
				$comments = collect_comment($dist->dist_id,$job_id);
				if($alt_job_id){
					$add_sel = ",SUM(IF(job_route.dest_type<>'bundles',jra.amount,0))			AS '$alt_dest_type'";
					$add = "LEFT JOIN job_route jra
							ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
				}		
			
				$qry = "SELECT  IF(operator.alias<>'',operator.alias,
									IF(address.name2 <> '',
										CONCAT(address.name,', ',address.first_name,' and ',address.name2,', ',address.first_name2)	,
										IF(address.first_name <>'',
											CONCAT(address.name,', ',address.first_name),
											CONCAT(address.name)
										)
									)
								)
														AS Name,
								CONCAT(operator.do_address,IF(operator.do_city<>'',', ',''),operator.do_city,IF(operator.deliv_notes<>'',', Notes:',''),operator.deliv_notes)
														AS 'Delivery Point',
								IF(job_route.alt_dropoff_id>0,'Y','N') 
														AS 'ALT DO',
							   job_route.version 		AS Version,
							   SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS Bdls,
							   SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) AS $dest_type
							   $add_sel	
						FROM job_route
						$add
						LEFT JOIN route
						ON route.route_id=job_route.route_id
						LEFT JOIN operator
						ON operator.operator_id=job_route.doff
						LEFT JOIN address
						ON operator.operator_id=address.operator_id
						WHERE job_route.job_id='$job_id'
								AND job_route.dist_id='$dist->dist_id'
								AND route.region = '$region->region'
								AND island='$island'
						GROUP BY route.region,job_route.doff,job_route.version
						ORDER BY island,route.seq_region,seq_area,company";
				//echo nl2br($qry)."<br />";
				$res_t = query($qry);
				if(mysql_num_rows($res_t)>0){
					$tab->startNewLine();
						$tab->addLineWithStyle("Distributor: ".$dist->company." / Region: ".$region->region,"sql_extra_line_text10",3);
					$tab->stopNewLine();					
	
					$tab->writeSQLTableElement($qry,1);
					$tab->startNewLine();
						$tab->addLines("",2);
						$tab->addLine("Total (Distr.):");
						
						$total = (int)$tab->getSum("Bdls");
						$tab->collFieldVal["Bdls"] = array();
						
						if(!$total) $total=0;
						$tab->addLine("$total");
						$grand_total_bund += $total;
						
						$total = array_sum($tab->collFieldVal[$dest_type]);
						$tab->collFieldVal[$dest_type] = array();
						if(!$total) $total=0;
						$tab->addLine("$total");
						$grand_tot_circ += $total;
						
						if($alt_job_id){
							$total = array_sum($tab->collFieldVal[$alt_dest_type]);
							$tab->collFieldVal[$alt_dest_type] = array();
							if(!$total) $total=0;
							$grand_tot_alt_circ += $total;
							$tab->addLine("$total");			
						}		
					$tab->stopNewLine();		
				}
	
			}//while($dist = mysql_fetch_object($res_reg))
			$tab->startNewLine();
				$tab->addLines("",2);
				$tab->addLine("Total:");
				$tab->addLine("$grand_tot_bund");		
				$tab->addLine("$grand_tot_circ");
				if($alt_job_id){
					$tab->addLine("$grand_tot_alt_circ");		
				}
			$tab->stopNewLine();
			$tab->stopTable();	
	?>
				<table class="job_comments">
					<tr>
						<th align="left">Comments</th>
					</tr>
					<tr>
						<td><? echo nl2br($comments)?></td>
					</tr>
				</table>
				<div class="pagebreak">&nbsp;</div>
	<?						
		}// while($region = mysql_fetch_object($res_dist))
	}
}


// Similar fo show_do_details but lists more details about teh routes and noting about drop off details
function show_job_details($job_id,$export=false,$choice){
	set_alt_do_contractors($job_id);

	
	$margin=false;
	
	//if($choice=="bbh") $where_add = "AND dropoff_id=656";
	//else if($choice=="mail") $where_add = "AND dropoff_id IN (583,584,585,586,587,588,589,657)";
	
	if($choice=="bbh") $where_add = "AND region='BAGS BOXES COUNTER'";
	else if($choice=="mail") $where_add = "AND region='MAILINGS'";
	
	else $where_add = "";

	$comments = get("job","comments","WHERE job_id='$job_id'");

	$qry = "SELECT DISTINCT dist_id,company,address.country 
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=job_route.dist_id
			LEFT JOIN address
			ON address.operator_id=operator.operator_id
			WHERE job_route.job_id='$job_id'
			AND dist_id IS NOT NULL 
            AND company IS NOT NULL
            ORDER BY island,seq_region,seq_area";
	$res_dist = query($qry,0);
	
	$tab = new MySQLTable("proc_job.php","","");
	$tab->showRec=1;
	$tab->noRepFields["Name"]=1;
	
	//$tab->noRepFields["Version"]=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;	
	$start=true;
	$tot_alt_sum = 0;
	$start_region=false;
	
	while($dist = mysql_fetch_object($res_dist)){
		
		$comments = collect_comment($dist->dist_id,$job_id);
		$qry = "SELECT DISTINCT region 
				FROM route
				LEFT JOIN job_route
				ON job_route.route_id=route.route_id
				WHERE job_route.dist_id=$dist->dist_id 
					AND job_id='$job_id'
				ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		$num_regions = mysql_num_rows($res_reg);
		$count_reg = 0;
		while($region = mysql_fetch_object($res_reg)){
			$has_lines=false;
			$qry = "SELECT doff,region,dist_id 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE region='$region->region' 
						AND dist_id='$dist->dist_id'
						AND job_route.job_id='$job_id'
					GROUP BY doff
					ORDER BY island,seq_region,seq_area";
			$res_do = query($qry);
			$start=true;
			$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles' $where_add GROUP BY job_id,dist_id ";
			$res_sum = query($qry);
			$sum = mysql_fetch_object($res_sum);

			while($do = mysql_fetch_object($res_do)){
				
				$has_bdls = false;
				if($sum->sum>0) {
					$has_bdls = true;
					$sel_add = "IF(job_route.dest_type='bundles',job_route.amount,0) 		AS Bdls,";
				}
			
				$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
				
				$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
				$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));
				$tab->collField[$dest_type] = true;
				$tab->collField[$alt_dest_type] = true;
				$tab->collField['Bdls'] = true;
				
				if($alt_job_id){
					$add_sel = ",IF(jra.dest_type<>'bundles',jra.amount,0)			AS '$alt_dest_type'";
					$add = "LEFT JOIN job_route jra
							ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
				}					
			
				$qry = "SELECT  CONCAT(IF(operator.alias<>'',operator.alias,
									IF(address.name2 <> '',
										CONCAT(address.name,', ',address.first_name,' and ',address.name2,', ',address.first_name2)	,
										IF(address.first_name <>'',
											CONCAT(address.name,', ',address.first_name),
											CONCAT(address.name)
										)
									)
								),
								IF(mail_type<>'' AND mail_type IS NOT NULL,CONCAT(' (',mail_type,')'),'') )
														AS Name,
								/*CONCAT(operator.do_address,IF(operator.do_city<>'',', ',''),operator.do_city,IF(operator.deliv_notes<>'',', Notes:',''),operator.deliv_notes)
														AS 'Delivery Point',*/
								route.code 	AS RD,
								job_route.version AS Version,
								$sel_add
							    IF(job_route.dest_type<>'bundles',job_route.amount,0) 	AS $dest_type
							    $add_sel
						FROM job_route
						$add 
						LEFT JOIN route
						ON route.route_id=job_route.route_id
						LEFT JOIN operator
						ON operator.operator_id=job_route.doff
						LEFT JOIN address
						ON operator.operator_id=address.operator_id
						WHERE job_route.job_id='$job_id'
								AND job_route.dist_id='$dist->dist_id'
								AND route.region = '$region->region'
								AND job_route.doff = '$do->doff'
								$where_add
						ORDER BY island,route.seq_region,route.seq_area,route.seq_code,job_route.dest_type,company";
				//echo nl2br($qry)."<br />";
				$res_t = query($qry);
				//echo ";<br />";
				
				if(mysql_num_rows($res_t)>0){
					$has_lines=true;
					if($start){
						write_header_without_contact($job_id,"COURAL JOB DETAILS",$margin->margin_percent);
						
						$tab->startTable();
						$tab->startNewLine();
							$tab->addLineWithStyle("Distributor: ".$dist->company." / Region: ".$dist->country,"sql_extra_line_text10",3);
						$tab->stopNewLine();			
						
						
					}
					
					$tab->writeSQLTableElement($qry,$start);				
					$num_beef_sum = $tab->getSum($dest_type,0);
					$num_beef_alt_sum = $tab->getSum($alt_dest_type,0);
					
					
					$tab->collFieldVal[$dest_type] = array();
					$tab->collFieldVal[$alt_dest_type] = array();
					
					
					$tab->startNewLine();
					$tab->addLine("");
					$tab->addLine("");
					$tab->addLine("Total:");
					
					if(!$total) $total=0;
					if($has_bdls) {
						$bundles_sum = array_sum($tab->collFieldVal['Bdls']); 
						$total = $bundles_sum;
						$tab->addLine("$total");		
						$tab->collFieldVal['Bdls'] = array();
					}
									
					$total = $num_beef_sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$total = $num_beef_alt_sum;
						$tot_alt_sum += $total;
						if(!$total) $total=0;
						$tab->addLine("$total");		
					}				
					$tab->stopNewLine();							
					$start=false;
				}
			}
			if($has_lines && $num_regions>1 && $count_reg<($num_regions-1)){
				$tab->stopTable();		
?>			
					<div class="pagebreak_after">&nbsp;</div>
<?	
				$tab->startTable();		
			}
			$count_reg++;
		}//while($region = mysql_fetch_object($res_reg))
		if($has_lines){
			$tab->startNewLine();
				$tab->addLine("");
				$tab->addLine("");
				$tab->addLine("Grand Total (Distr.):");
				$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles'  $where_add GROUP BY job_id,dist_id ";
				$res_sum = query($qry);
				$sum = mysql_fetch_object($res_sum);
				$total = $sum->sum;
				if(!$total) $total=0;
				if($has_bdls) $tab->addLine("$total");						
				$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles'  $where_add GROUP BY job_id,dist_id ";
				$res_sum = query($qry);
				$sum = mysql_fetch_object($res_sum);
				$total = $sum->sum;
				if(!$total) $total=0;
				$tab->addLine("$total");
				if($alt_job_id){
					$total_2 = $tot_alt_sum;
					$tot_alt_sum=0;
					if(!$total_2) $total_2=0;
					$tab->addLine("$total_2");		
				}				
			$tab->stopNewLine();		
			
			if($alt_job_id){
				$tab->startNewLine();
					$tab->addLine("");
					$tab->addLine("");
					$tab->addLine("Sum:");
					$tab->addLine($total_2+$total);
				$tab->stopNewLine();	
			}
		}
		
			
		if($has_lines){
			$tab->stopTable();	
		?>
			<table class="job_comments">
				<tr>
					<th align="left">Comments</th>
				</tr>
				<tr>
					<td><?=$comments?></td>
				</tr>
			</table>
			<div class="pagebreak_after">&nbsp;</div>
		<?					
		}
	}// while($dist = mysql_fetch_object($res_dist))
}







function show_print_table1($job_id){
	set_alt_do_contractors($job_id);
	write_header($job_id,"COURAL JOB DETAILS");
	
	$today = date('Y-m-d');
	$qry = "SELECT * FROM job
			LEFT JOIN client
			ON job.client_id=client.client_id
			WHERE job.job_id=$job_id";
	$res = query($qry);
	$obj = mysql_fetch_object($res);
	
	$name  = get("address","name","WHERE address_id='$obj->contact_name_id'");
	$phone = get("address","phone","WHERE address_id='$obj->contact_name_id'");
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else  $delivery_date=$obj->delivery_date;

	$qry = "SELECT DISTINCT dist_id,company 
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=route.dist_id
			WHERE job_route.job_id='$job_id'
			AND dist_id IS NOT NULL 
            AND company IS NOT NULL
            ORDER BY island,seq_region,seq_area";
	$res_dist = query($qry,0);
	
	$tab = new MySQLTable("proc_job.php","","");
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;	
	
	while($dist = mysql_fetch_object($res_dist)){
		$comments = collect_comment($dist->dist_id,$job_id);
		$tab->startTable();
		$qry = "SELECT DISTINCT region FROM route WHERE route.dist_id=$dist->dist_id ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		while($region = mysql_fetch_object($res_reg)){
			$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
			
			$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
			$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));
			
			if($alt_job_id){
				$add_sel = ",SUM(IF(jra.dest_type<>'bundles',jra.amount,0))	AS '$alt_dest_type'";
				$add = "LEFT JOIN job_route jra
						ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
			}							
			$tab->startNewLine();
				$tab->addLineWithStyle("Distributor: ".$dist->company." / Region: ".$region->region,"sql_extra_line_text10",3);
			$tab->stopNewLine();
	
			$qry = "SELECT route.area 				AS 'Area',
						   route.code 				AS RD,
						   SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS Bdls,
						   SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 	AS $dest_type
						   $add_sel
					FROM job_route
					$add
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					LEFT JOIN operator
					ON operator.operator_id=job_route.doff
					LEFT JOIN address
					ON operator.operator_id=address.operator_id
					WHERE job_route.job_id='$job_id'
							AND route.dist_id='$dist->dist_id'
							AND route.region = '$region->region'
					GROUP BY route.region,route.area,job_route.dest_type
					ORDER BY island,route.seq_region,job_route.dest_type,company";
			//echo nl2br($qry)."<br />";
			//$res = query($qry);
			$res_t = query($qry);
			if(mysql_num_rows($res_t)>0){
						
				$tab->writeSQLTableElement($qry,1);
				$tab->startNewLine();
					$tab->addLine("");
					$tab->addLine("Total (Distr.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");					
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type<>'bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");
					}
				$tab->stopNewLine();		
			}
		}//while($region = mysql_fetch_object($res_reg))
		$tab->addHiddenInput("job_id",$job_id);
		$tab->addHiddenInput("action","delete_lines");
		$tab->stopTable();	
?>
		<table class="job_comments">
			<tr>
				<th align="left">Comments</th>
			</tr>
			<tr>
				<td><? echo nl2br($comments)?></td>
			</tr>
		</table>
		<div class="pagebreak_after">&nbsp;</div>
<?				
	}// while($dist = mysql_fetch_object($res_dist))
	
	/*$tab->startNewLine();
		$tab->addLine("");
		$tab->addLine("Total (Distr.):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type<>'bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");
		}
	$tab->stopNewLine();*/
}





// Prints the booked routes on the job edit screen
function show_table($job_id){

	// This is the select for selecting altenate drop offs
	$sel = new MySQLSelect("company","operator_id","operator","proc_job.php","table","alt_dropoff_id");
	$sel->selectOnChange="";
	$sel->optionDefText="none";
	$sel->addSQLWhere("is_alt_dropoff","Y");
	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
	if($dest_type=="Rmt") $dest_type = "Total#";
	$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));
	if($alt_job_id){
		$add_bdl = "IF(jra.dest_type='bundles',jra.amount,0) AS 'Add-Bdl',";
		$add_sel = "IF(jra.dest_type<>'bundles',jra.amount,0) AS '$alt_dest_type',";
		$add = "LEFT JOIN job_route jra
				ON jra.job_id = job.alt_job_id AND jra.route_id = job_route.route_id";
	}

	$qry = "SELECT 	job_route.job_route_id 	AS Record,
					route.island 		AS Island,
					IF(LENGTH(route.region) > 15,CONCAT(LEFT(route.region,15),'...'),route.region)		AS Region,
					IF(LENGTH(route.area) > 15,CONCAT(LEFT(route.area,15),'...'),route.area)	AS Area,
					route.code			AS RD,
					IF(job_route.dest_type='bundles',job_route.amount,0) AS 'Bdls',
					$add_bdl
					IF(job_route.dest_type<>'bundles',job_route.orig_amt,0)			AS 'Orig-$dest_type',
					IF(job_route.dest_type<>'bundles',job_route.amount,0) AS '$dest_type',
					$add_sel
					job_route.version			AS Version,
					job_route.is_edited 		AS Edited,
					if(job_route.alt_dropoff_id>0,'Y','N')
												AS 'Alt'			
			FROM job
			RIGHT JOIN job_route
			ON job.job_id=job_route.job_id
			$add
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			WHERE job_route.job_id='$job_id'
			ORDER BY job_route.dest_type,island,seq_region,seq_area,seq_code,job_route.job_route_id";
	//echo nl2br($qry);
	$tab = new MySQLTable("proc_job.php",$qry,"nocoll");
	$tab->formatLine=true;
	$tab->onClickEditButtonAction="edit_line";
	$tab->onClickEditButtonAdd.="+'&job_id=$job_id'";
	$tab->cssSQLTable="sqltable_scroll";

	$tab->checkboxTitle="Select";
	$tab->submitButtonName="delete_lines";
	$tab->submitButtonValue="Delete lines";
	$tab->submitButtonName2="set_alternate_do";
	$tab->submitButtonValue2="Set Alt DO";	
	$tab->addHiddenInput("job_id",$job_id);
	$tab->selectField = $sel;
	$tab->highlightField="is_edited";
	$tab->showRec=0;
	$tab->colWidth["Action"]=1000;
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
		$tab->addLines("",3);
		$tab->addLine("Total (Distr.):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");	
		if($alt_job_id){
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type='bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");		
		}		
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");	
		if($alt_job_id){
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type<>'bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");		
		}
	$tab->stopNewLine();
	
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","action_from_table");
	$tab->stopTable();			
}



// Adds the red buttons at the top of teh edit job screen for updating affiliation as well as edit job details
function write_edit_job_details($job_id,$island,$region,$area,$code,$dest_type){
?>
	<form name="editjob" action="proc_job.php" method="get">
		<table class="form">
			<tr>
				<td>
					<input class="edit_job_details_button"  type="button" name="submit" value="Update Aff." onClick="document.location.href='proc_job.php?job_id=<?=$job_id?>&action=update_aff'" />
				</td>
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

// Tis will add a form to the edit job screen which will book the routes.
// There is some Ajax involved. The PHP scrips are all located in the 'get' folder.
function write_addroute_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route){
	$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
?>	
	<script language="javascript">
		function message(id){
			var result = "<select name=\"dummy\" size=\"10\" style=\"width:15em \"><option style=\'font-size:0.8em;background-color:#990000; color:black;\'>loading...</option></select>"
			document.getElementById(id).innerHTML = result;  
		}
	</script>
	
	
	<form name="narrow" id="narrow" method="post" action="proc_job.php?job_id=<?=$job_id?>">
		<table height="250"  class="form">
			<td>
				<th id="addjob_header" colspan="7">Add Route</th>
			</td>
			<tr>
				<th style="text-align:left " width="100">Island:</th>
				<th style="text-align:left " width="160">Region:</th>
				<th style="text-align:left " width="250">Area:</th>
				<th style="text-align:left " width="200">RD:</th>						
				<th style="text-align:left " width="160">Version:</th>			
				<th style="text-align:left " width="160">Incl. Zero Amt.:</th>			
			</tr>
			<tr valign="top">
				<td valign="top" >
					<select multiple size="5" style="width:4em " name="island[]" onchange="set_enabled()">
						<option value="NI">NI</option>
						<option value="SI">SI</option>
					</select>
					<input name="submit" type="button" value=">>" onClick="set_Button_off();get(this,'region_reg','proc_job/get/get_region.php?dest_type=<?=$dest_type?>');" />
				</td>
						
				<td valign="top">
					<span name="region_reg" id="region_reg">
						<select name="region[]" size="10" style="width:11em "></select>
						<input disabled name="sub_reg" type="button" value=">>" />
					</span>
				</td>
				<td valign="top">
					<span name="area_reg" id="area_reg">
						<select name="area[]" size="10" style="width:15em "></select>
						<input disabled name="sub_area" type="button" value=">>"  />
					</span>
				</td>
				<td valign="top">
					<span name="code_reg" id="code_reg">
						<select name="code[]" size="10" style="width:15em "></select>
					</span>						
				</td>
				<td valign="top">
					<input type="text" name="version"  value="<?=$version?>" />
				</td>	
				<td valign="top">
					<input type="checkbox" name="include_zeros"  value="1" />
				</td>					
			</tr>				
		</table>
		<span id="add_route_wrap"><input disabled name="submit1" value="Add Route(s)" type="submit" /></span>
		<input type="hidden" name="action" value="addarea" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />		
		<input type="hidden" name="bundle_price" value="<?=0.00?>" />
		<input type="hidden" name="type" value="<?=$dest_type?>" />
	</form>
<?	
}



// This form will add the bundles
function write_addbundle_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route){
	$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
	$delivery_date = get("job","delivery_date","WHERE job_id='$job_id'");
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
		
				$qry = "SELECT contractor_id 
						FROM route
						LEFT JOIN route_aff
						ON route_aff.route_id=route.route_id
						WHERE '$delivery_date'>=app_date
							AND '$delivery_date' < stop_date
							AND route.route_id='$bund_route'";
				$res_op = query($qry);
				$op_id = mysql_fetch_object($res_op);
				$bundle_contr_value = get("operator","company","WHERE operator_id='$op_id->contractor_id'");
?>
				<script type="text/javascript" src="javascripts/prototype.js"></script>
				<script type="text/javascript" src="javascripts/effects.js"></script>
				<script type="text/javascript" src="javascripts/controls.js"></script>					
				<td>
					<? 
						//$query_str = $_SERVER['QUERY_STRING']; 
						$query_str = "job_id=$job_id"; 
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
					<input type="hidden" name="bund_route" value="<?=$bund_route?>"  />
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