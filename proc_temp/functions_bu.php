<?

function create_job_no(){
	$last_id=get_max("job","job_id","","");
	$last_job_no = get("job","job_no","WHERE job_id='$last_id'");
	if(strpos($last_job_no,'L')){
		$last_job_no = substr($last_job_no,0,strpos($last_job_no,'L'));
	}
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
		//$c_id = get("route","contractor_id","WHERE route_id='$jobr->route_id'");
		$do_id = get("route","dropoff_id","WHERE route_id='$jobr->route_id'");
		if(!$do_id) $do_id=0;
		if($jobr->alt_dropoff_id>0)
			$qry = "UPDATE job_route 
					SET doff = alt_dropoff_id,
						dropoff_id='$do_id'
					WHERE job_route_id='$jobr->job_route_id'";
		
		else
			$qry = "UPDATE job_route 
					SET	doff='$do_id',
						dropoff_id='$do_id'
					WHERE job_route_id='$jobr->job_route_id'";
		query($qry);
	}
}


function show_print_table($job_id,$export=0){
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
	
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else  $delivery_date=$obj->delivery_date;
	
?>
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3">COURAL DROP OFF DETAILS</th>
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
			<td><?=$obj->dest_type?></td>
		</tr>
		<tr>
			<td>Delivery Date:</td>
			<td><?=$delivery_date?></td>
			<td>Line Hauler:</td>
			<td><?=$obj->hauler?></td>
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
            ORDER BY island,seq_region,seq_area";
	$res_dist = query($qry,0);
	
	if($export)
		$tab = new MySQLExport("export.html","","");
	else
		$tab = new MySQLTable("proc_job.php","","");
		
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;	
	$tab->colWidth["Name"]=180;
	$tab->startTable();
	while($dist = mysql_fetch_object($res_dist)){
		$qry = "SELECT DISTINCT region FROM route WHERE route.dist_id=$dist->dist_id ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		$start=true;
		while($region = mysql_fetch_object($res_reg)){
			$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
			if($alt_job_id){
				$add_sel = ",IF(jra.dest_type='num_total','Total',
								IF(jra.dest_type='num_farmers','Farmer',
									IF(jra.dest_type='num_dairies','Dairy',
										IF(jra.dest_type='num_sheep','sheep',
											IF(jra.dest_type='num_beef','Beef',
												IF(jra.dest_type='num_sheepbeef','Sheep/Beef',
													IF(jra.dest_type='num_dairybeef','Dairy/Beef',
														IF(jra.dest_type='num_hort','Hort',
															IF(jra.dest_type='num_nzfw','NZFW',
																IF(jra.dest_type='num_spare','Spare',
																	IF(jra.dest_type='num_lifestyle','Lifestyle',
																		IF(jra.dest_type='bundles','Bundles',job_route.dest_type	
							)))))))))))) AS Type,
							SUM(jra.amount)			AS 'Add.-Number'";
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
							IF(job_route.dest_type='num_total','Total',
								IF(job_route.dest_type='num_farmers','Farmer',
									IF(job_route.dest_type='num_dairies','Dairy',
										IF(job_route.dest_type='num_sheep','sheep',
											IF(job_route.dest_type='num_beef','Beef',
												IF(job_route.dest_type='num_sheepbeef','Sheep/Beef',
													IF(job_route.dest_type='num_dairybeef','Dairy/Beef',
														IF(job_route.dest_type='num_hort','Hort',
															IF(job_route.dest_type='num_nzfw','NZFW',
																IF(job_route.dest_type='num_spare','Spare',														
																	IF(job_route.dest_type='num_lifestyle','Lifestyle',
																		IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
							)))))))))))) AS Type,									
						   job_route.version 		AS Version,
						   SUM(job_route.amount) 	AS Number
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
					GROUP BY route.region,job_route.doff,job_route.dest_type
					ORDER BY island,route.seq_region,job_route.dest_type,company";
			//echo nl2br($qry)."<br />";
			$res_t = query($qry);
			if(mysql_num_rows($res_t)>0){
				if($start){
					$tab->startNewLine();
						$tab->addLine("Distributor: ".$dist->company." / Region: ".$region->region,3);
					$tab->stopNewLine();					
					$start=false;
				}				
				$tab->writeSQLTableElement($qry,1);
				$tab->startNewLine();
					$tab->addLines("",3);
					$tab->addLine("Total (Distr.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles'  AND region='$region->region' GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$tab->addLine("");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type<>'bundles'  AND region='$region->region' GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");			
					}		
				$tab->stopNewLine();		
				$tab->startNewLine();
					$tab->addLines("",3);
					$tab->addLine("Total (Bund.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles'  AND region='$region->region'  GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$tab->addLine("");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type='bundles'  AND region='$region->region'  GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");					
					}
				$tab->stopNewLine();		
			}
		}//while($region = mysql_fetch_object($res_reg))
		
	}// while($dist = mysql_fetch_object($res_dist))
	
	$tab->startNewLine();
		$tab->addLines("",3);
		$tab->addLine("Total (Distr.):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$tab->addLine("");
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type<>'bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");		
		}
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",3);
		$tab->addLine("Total Bund.:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$tab->addLine("");
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type='bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");
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
			<td><? echo nl2br($obj->comments)?></td>
		</tr>
	</table>
<?	
	if($export){
?>
		<a href="export.html">Download by right click</a>
<?	
	}
}


function show_print_table_pb($job_id,$export=0){
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
	
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else  $delivery_date=$obj->delivery_date;
	
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
			<td><?=$delivery_date?></td>
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
            ORDER BY island,seq_region,seq_area";
	$res_dist = query($qry,0);
	
	if($export)
		$tab = new MySQLExport("export.html","","");
	else
		$tab = new MySQLTable("proc_job.php","","");
		
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;	
	$tab->colWidth["Name"]=180;
	//$tab->startTable();
	while($dist = mysql_fetch_object($res_dist)){
		$tab->startTable();
		$qry = "SELECT DISTINCT region FROM route WHERE route.dist_id=$dist->dist_id ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		$start=true;
		while($region = mysql_fetch_object($res_reg)){
			$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
			if($alt_job_id){
				$add_sel = ",IF(jra.dest_type='num_total','Total',
								IF(jra.dest_type='num_farmers','Farmer',
									IF(jra.dest_type='num_dairies','Dairy',
										IF(jra.dest_type='num_sheep','sheep',
											IF(jra.dest_type='num_beef','Beef',
												IF(jra.dest_type='num_sheepbeef','Sheep/Beef',
													IF(jra.dest_type='num_dairybeef','Dairy/Beef',
														IF(jra.dest_type='num_hort','Hort',
															IF(jra.dest_type='num_nzfw','NZFW',
																IF(jra.dest_type='num_spare','Spare',
																	IF(jra.dest_type='num_lifestyle','Lifestyle',
																		IF(jra.dest_type='bundles','Bundles',job_route.dest_type	
							)))))))))))) AS Type,
							SUM(jra.amount)			AS 'Add.-Number'";
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
							IF(job_route.dest_type='num_total','Total',
								IF(job_route.dest_type='num_farmers','Farmer',
									IF(job_route.dest_type='num_dairies','Dairy',
										IF(job_route.dest_type='num_sheep','sheep',
											IF(job_route.dest_type='num_beef','Beef',
												IF(job_route.dest_type='num_sheepbeef','Sheep/Beef',
													IF(job_route.dest_type='num_dairybeef','Dairy/Beef',
														IF(job_route.dest_type='num_hort','Hort',
															IF(job_route.dest_type='num_nzfw','NZFW',
																IF(job_route.dest_type='num_spare','Spare',														
																	IF(job_route.dest_type='num_lifestyle','Lifestyle',
																		IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
							)))))))))))) AS Type,									
						   job_route.version 		AS Version,
						   SUM(job_route.amount) 	AS Number
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
					GROUP BY route.region,job_route.doff,job_route.dest_type
					ORDER BY island,route.seq_region,job_route.dest_type,company";
			//echo nl2br($qry)."<br />";
			$res_t = query($qry);
			if(mysql_num_rows($res_t)>0){
				if($start){
					$tab->startNewLine();
						$tab->addLine("Distributor: ".$dist->company." / Region: ".$region->region,3);
					$tab->stopNewLine();					
					$start=false;
				}				
				$tab->writeSQLTableElement($qry,1);
				$tab->startNewLine();
					$tab->addLines("",3);
					$tab->addLine("Total (Distr.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles'  AND region='$region->region' GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$tab->addLine("");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type<>'bundles'  AND region='$region->region' GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");			
					}		
				$tab->stopNewLine();		
				$tab->startNewLine();
					$tab->addLines("",3);
					$tab->addLine("Total (Bund.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles'  AND region='$region->region'  GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$tab->addLine("");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type='bundles'  AND region='$region->region'  GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");					
					}
				$tab->stopNewLine();		
			}
		}//while($region = mysql_fetch_object($res_reg))
		$tab->stopTable();	
	?>
	<table class="job_comments">
		<tr>
			<th align="left">Comments</th>
		</tr>
		<tr>
			<td><? echo nl2br($obj->comments)?></td>
		</tr>
	</table>
		<span class="pagebreak_after">&nbsp;</span>
	<?	
		
	}// while($dist = mysql_fetch_object($res_dist))
}


function show_print_table_check($job_id,$export=false){
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
	
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else  $delivery_date=$obj->delivery_date;
	
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
			<td><?=$delivery_date?></td>
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
            ORDER BY island,seq_region,seq_area";
	$res_dist = query($qry,0);
	
	if($export)
		$tab = new MySQLExport("export.html","","");
	else
		$tab = new MySQLTable("proc_job.php","","");
		
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasCheckBoxes=false;	
	$tab->startTable();
	while($dist = mysql_fetch_object($res_dist)){
		$qry = "SELECT DISTINCT region FROM route WHERE route.dist_id=$dist->dist_id ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		while($region = mysql_fetch_object($res_reg)){
		
			$qry = "SELECT doff,region,dist_id 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE region='$region->region' 
						AND dist_id='$dist->dist_id'
						AND job_route.job_id='$job_id'
					GROUP BY doff";
			$res_do = query($qry);
			$start=true;
			while($do = mysql_fetch_object($res_do)){
				$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
				if($alt_job_id){
					$add_sel = ",IF(jra.dest_type='num_total','Total',
									IF(jra.dest_type='num_farmers','Farmer',
										IF(jra.dest_type='num_dairies','Dairy',
											IF(jra.dest_type='num_sheep','sheep',
												IF(jra.dest_type='num_beef','Beef',
													IF(jra.dest_type='num_sheepbeef','Sheep/Beef',
														IF(jra.dest_type='num_dairybeef','Dairy/Beef',
															IF(jra.dest_type='num_hort','Hort',
																IF(jra.dest_type='num_nzfw','NZFW',
																	IF(jra.dest_type='num_spare','Spare',
																		IF(jra.dest_type='num_lifestyle','Lifestyle',
																			IF(jra.dest_type='bundles','Bundles',job_route.dest_type	
								)))))))))))) AS Type,
								jra.amount			AS 'Add.-Number'";
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
								/*CONCAT(operator.do_address,IF(operator.do_city<>'',', ',''),operator.do_city,IF(operator.deliv_notes<>'',', Notes:',''),operator.deliv_notes)
														AS 'Delivery Point',*/
								route.area 	AS Area,
								route.code 	AS RD,
								IF(job_route.dest_type='num_total','Total',
									IF(job_route.dest_type='num_farmers','Farmer',
										IF(job_route.dest_type='num_dairies','Dairy',
											IF(job_route.dest_type='num_sheep','sheep',
												IF(job_route.dest_type='num_beef','Beef',
													IF(job_route.dest_type='num_sheepbeef','Sheep/Beef',
														IF(job_route.dest_type='num_dairybeef','Dairy/Beef',
															IF(job_route.dest_type='num_hort','Hort',
																IF(job_route.dest_type='num_nzfw','NZFW',
																	IF(job_route.dest_type='num_spare','Spare',		
																		IF(job_route.dest_type='num_lifestyle','Lifestyle',
																			IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
								)))))))))))) AS Type,			
							   job_route.amount	 		AS Number
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
								AND job_route.doff = '$do->doff'
						ORDER BY island,route.seq_region,job_route.dest_type,company";
				//echo nl2br($qry)."<br />";
				$res_t = query($qry);
				//echo ";<br />";
				if(mysql_num_rows($res_t)>0){
					if($start){
						$tab->startNewLine();
							$tab->addLine("Distributor: ".$dist->company." / Region: ".$region->region,3);
						$tab->stopNewLine();		
						$start=false;			
					}
					$tab->writeSQLTableElement($qry,1);
					$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total (Distr.):");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles'  AND region='$region->region' AND job_route.doff = '$do->doff' GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");
						if($alt_job_id){
							$tab->addLine("");
							$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type<>'bundles'  AND region='$region->region' AND job_route.doff = '$do->doff' GROUP BY job_id,dist_id ";
							$res_sum = query($qry);
							$sum = mysql_fetch_object($res_sum);
							$total = $sum->sum;
							if(!$total) $total=0;
							$tab->addLine("$total");		
						}				
					$tab->stopNewLine();		
					$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total (Bund.):");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles'  AND region='$region->region'  AND job_route.doff = '$do->doff' GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");
						if($alt_job_id){
							$tab->addLine("");
							$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type='bundles'  AND region='$region->region'  AND job_route.doff = '$do->doff' GROUP BY job_id,dist_id ";
							$res_sum = query($qry);
							$sum = mysql_fetch_object($res_sum);
							$total = $sum->sum;
							if(!$total) $total=0;		
							$tab->addLine("$total");				
						}
					$tab->stopNewLine();		
				}
			}
		}//while($region = mysql_fetch_object($res_reg))
		
	}// while($dist = mysql_fetch_object($res_dist))
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total (Distr.):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$tab->addLine("");
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type<>'bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");		
		}
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Bund.:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$tab->addLine("");
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type='bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");	
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
			<td><? echo nl2br($obj->comments)?></td>
		</tr>
	</table>
<?	
	if($export){
?>
		<a href="export.html">Download by right click</a>
<?	
	}
}


function show_print_table1($job_id){
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
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else  $delivery_date=$obj->delivery_date;
?>
	
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3">COURAL JOB DETAILS</th>
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
			<td><?=$delivery_date?></td>
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
            ORDER BY island,seq_region,seq_area";
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
		$qry = "SELECT DISTINCT region FROM route WHERE route.dist_id=$dist->dist_id ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		while($region = mysql_fetch_object($res_reg)){
			$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
			if($alt_job_id){
				$add_sel = ",IF(jra.dest_type='num_total','Total',
								IF(jra.dest_type='num_farmers','Farmer',
									IF(jra.dest_type='num_dairies','Dairy',
										IF(jra.dest_type='num_sheep','sheep',
											IF(jra.dest_type='num_beef','Beef',
												IF(jra.dest_type='num_sheepbeef','Sheep/Beef',
													IF(jra.dest_type='num_dairybeef','Dairy/Beef',
														IF(jra.dest_type='num_hort','Hort',
															IF(jra.dest_type='num_nzfw','NZFW',
																IF(jra.dest_type='num_spare','Spare',																
																	IF(jra.dest_type='num_lifestyle','Lifestyle',
																		IF(jra.dest_type='bundles','Bundles',job_route.dest_type	
							)))))))))))) AS Type,
							jra.amount			AS 'Add.-Number'";
				$add = "LEFT JOIN job_route jra
						ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
			}							
			$tab->startNewLine();
				$tab->addLine("Distributor: ".$dist->company." / Region: ".$region->region,3);
			$tab->stopNewLine();
	
			$qry = "SELECT  route.area 				AS 'Area',
							IF(job_route.dest_type='num_total','Total',
								IF(job_route.dest_type='num_farmers','Farmer',
									IF(job_route.dest_type='num_dairies','Dairy',
										IF(job_route.dest_type='num_sheep','sheep',
											IF(job_route.dest_type='num_beef','Beef',
												IF(job_route.dest_type='num_sheepbeef','Sheep/Beef',
													IF(job_route.dest_type='num_dairybeef','Dairy/Beef',
														IF(job_route.dest_type='num_hort','Hort',
															IF(job_route.dest_type='num_nzfw','NZFW',
																	IF(job_route.dest_type='num_spare','Spare',	
																		IF(job_route.dest_type='num_lifestyle','Lifestyle',
																			IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
							)))))))))))) AS Type,			
						   job_route.version 		AS Version,
						   SUM(job_route.amount) 	AS Number
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
					$tab->addLines("",1);
					$tab->addLine("Total (Distr.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$tab->addLine("");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type<>'bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");
					}
				$tab->stopNewLine();		
				$tab->startNewLine();
					$tab->addLines("",1);
					$tab->addLine("Total (Bund.):");
					$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
					$res_sum = query($qry);
					$sum = mysql_fetch_object($res_sum);
					$total = $sum->sum;
					if(!$total) $total=0;
					$tab->addLine("$total");
					if($alt_job_id){
						$tab->addLine("");
						$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$alt_job_id' and dest_type='bundles' AND region='$region->region'  GROUP BY job_id,dist_id ";
						$res_sum = query($qry);
						$sum = mysql_fetch_object($res_sum);
						$total = $sum->sum;
						if(!$total) $total=0;
						$tab->addLine("$total");
					}
				$tab->stopNewLine();		
			}
		}//while($region = mysql_fetch_object($res_reg))
		
	}// while($dist = mysql_fetch_object($res_dist))
	
	$tab->startNewLine();
		$tab->addLines("",1);
		$tab->addLine("Total (Distr.):");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$tab->addLine("");
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type<>'bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");
		}
	$tab->stopNewLine();
	
	$tab->startNewLine();
		$tab->addLines("",1);
		$tab->addLine("Total Bund.:");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");
		if($alt_job_id){
			$tab->addLine("");
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type='bundles'","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");		
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
			<td><? echo nl2br($obj->comments)?></td>
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

function show_table_with_sub_totals($job_id){
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
	if($obj->is_ioa=='Y') $delivery_date="IOA";
	else  $delivery_date=$obj->delivery_date;
?>
	
	<table class="job_header" cellpadding="2">
		<tr>
			<th colspan="3">COURAL JOB DETAILS</th>
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
			<td><?=$delivery_date?></td>
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


	$qry = "SELECT DISTINCT area FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE job_id='$job_id' ORDER BY island,seq_region,seq_area";
	$res = query($qry);
	
	$tab = new MySQLTable("proc_job.php","","nocoll");
	$tab->cssSQLTable="sqltable_scroll";	
	$tab->showRec=0;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasSubmitButton=false;
	$tab->hasSubmitButton2=false;
	$tab->hasSelectField=false;
	$tab->hasCheckBoxes=false;
	
	$tab->startTable();
	while($area=mysql_fetch_object($res)){
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
														IF(job_route.dest_type='num_nzfw','NZFW',
															IF(job_route.dest_type='num_spare','Spare',
																IF(job_route.dest_type='num_lifestyle','Lifestyle',
																	IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
						)))))))))))) AS Type,
						job_route.orig_amt			AS 'Orig.-Number',
						job_route.amount			AS Number,
						job_route.version			AS Version
				FROM job
				RIGHT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				WHERE job_route.job_id='$job_id'
					AND area='$area->area'
				ORDER BY job_route.dest_type,island,seq_region,seq_area,seq_code,type,job_route.job_route_id";

		$tab->writeSQLTableElement($qry,1);
		$tab->startNewLine();
			$tab->addLines("",4);
			$tab->addLine("Total (Distr.):");
			$qry = "SELECT SUM(orig_amt) AS orig_amt 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE job_id='$job_id' 
						AND dest_type<>'bundles' 
						AND (job_route.external<>'Y' OR job_route.external IS NULL) 
						AND area='$area->area' 
					GROUP BY job_id";
			$res_1 = query($qry);
			$obj = mysql_fetch_object($res_1);
			$total = $obj->orig_amt;
			if(!$total) $total=0;
			$tab->addLine("$total");		
			$qry = "SELECT SUM(amount) AS amount 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE job_id='$job_id' 
						AND dest_type<>'bundles' 
						AND (job_route.external<>'Y' OR job_route.external IS NULL) 
						AND area='$area->area' 
					GROUP BY job_id";
			$res_1 = query($qry);
			$obj = mysql_fetch_object($res_1);
			$total = $obj->amount;			
			if(!$total) $total=0;
			$tab->addLine("$total");		
		$tab->stopNewLine();
		

		$tab->startNewLine();
			$tab->addLines("",4);
			$tab->addLine("Total Bundles:");
			$qry = "SELECT SUM(orig_amt) AS orig_amt 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE job_id='$job_id' 
						AND dest_type='bundles' 
						AND area='$area->area' 
					GROUP BY job_id";
			$res_1 = query($qry);
			$obj = mysql_fetch_object($res_1);
			$total = $obj->amount;						
			if(!$total) $total=0;
			$tab->addLine("$total");
			$qry = "SELECT SUM(amount) AS amount 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE job_id='$job_id' 
						AND dest_type='bundles' 
						AND area='$area->area' 
					GROUP BY job_id";
			$res_1 = query($qry);
			$obj = mysql_fetch_object($res_1);			
			if(!$total) $total=0;
			$tab->addLine("$total");		
		$tab->stopNewLine();
		
	}	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total (Distr.):");
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
		if(!$total) $total=0;
		$tab->addLine("$total");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
	$tab->stopNewLine();
	
	
	$tab->stopTable();			
}


function show_table($job_id){
	$sel = new MySQLSelect("company","operator_id","operator","proc_job.php","table","alt_dropoff_id");
	$sel->selectOnChange="";
	$sel->optionDefText="none";
	$sel->addSQLWhere("is_alt_dropoff","Y");
	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	if($alt_job_id){
		$add_sel = "IF(jra.dest_type='num_total','Total',
						IF(jra.dest_type='num_farmers','Farmer',
							IF(jra.dest_type='num_dairies','Dairy',
								IF(jra.dest_type='num_sheep','Sheep',
									IF(jra.dest_type='num_beef','Beef',
										IF(jra.dest_type='num_sheepbeef','Sheep/Beef',
											IF(jra.dest_type='num_dairybeef','Dairy/Beef',
												IF(jra.dest_type='num_hort','Hort',
													IF(jra.dest_type='num_nzfw','NZFW',
														IF(jra.dest_type='num_spare','Spare',
															IF(jra.dest_type='num_lifestyle','Lifestyle',
																IF(jra.dest_type='bundles','Bundles',job_route.dest_type	
					)))))))))))) AS Type,
					jra.amount			AS 'Add.-Number',";
		$add = "LEFT JOIN job_route jra
				ON jra.job_id = job.alt_job_id AND jra.route_id = job_route.route_id";
	}

	$qry = "SELECT 	job_route.job_route_id 	AS Record,
					route.island 		AS Island,
					route.region		AS Region,
					route.area			AS Area,
					route.code			AS RD,
					IF(job_route.dest_type='num_total','Total',
						IF(job_route.dest_type='num_farmers','Farmer',
							IF(job_route.dest_type='num_dairies','Dairy',
								IF(job_route.dest_type='num_sheep','Sheep',
									IF(job_route.dest_type='num_beef','Beef',
										IF(job_route.dest_type='num_sheepbeef','Sheep/Beef',
											IF(job_route.dest_type='num_dairybeef','Dairy/Beef',
												IF(job_route.dest_type='num_hort','Hort',
													IF(job_route.dest_type='num_nzfw','NZFW',
														IF(job_route.dest_type='num_spare','Spare',
															IF(job_route.dest_type='num_lifestyle','Lifestyle',
																IF(job_route.dest_type='bundles','Bundles',job_route.dest_type	
					)))))))))))) AS Type,
					job_route.orig_amt			AS 'Orig.-Number',
					job_route.amount			AS Number,
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
			ORDER BY job_route.dest_type,island,seq_region,seq_area,seq_code,type,job_route.job_route_id";
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
		$tab->addLine("Total (Distr.):");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");		
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type<>'bundles'","GROUP BY job_id");
		if(!$total) $total=0;
		$tab->addLine("$total");	
		if($alt_job_id){
			$tab->addLine("");	
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type<>'bundles' and (external<>'Y' OR external IS NULL)","GROUP BY job_id");
			if(!$total) $total=0;
			$tab->addLine("$total");		
		}
	$tab->stopNewLine();
	
	
	$tab->startNewLine();
		$tab->addLines("",4);
		$tab->addLine("Total Bundles:");
		$total = get_sum("job_route","orig_amt","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		$tab->addLine("$total");
		$total = get_sum("job_route","amount","WHERE job_id='$job_id' and dest_type='bundles'","GROUP BY job_id");
		$tab->addLine("$total");	
		if($alt_job_id){
			$tab->addLine("");		
			$total = get_sum("job_route","amount","WHERE job_id='$alt_job_id' and dest_type='bundles'","GROUP BY job_id");
			$tab->addLine("$total");		
		}	
	$tab->stopNewLine();
	
	$tab->addHiddenInput("job_id",$job_id);
	$tab->addHiddenInput("action","action_from_table");
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
			</tr>
			<tr valign="top">
				<td valign="top" >
					<select multiple size="5" style="width:4em " name="island[]" onchange="set_enabled()">
						<option value="NI">NI</option>
						<option value="SI">SI</option>
					</select>
					<input name="submit" type="button" value=">>" onClick="set_Button_off();get(this,'region_reg','proc_job/get/get_region.php');" />
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


function write_addaltdo_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route){

	$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
	if(!$type) $type = $dest_type;
?>	
	<form name="narrow" id="narrow" method="get" action="proc_job.php">
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


function write_addbundle_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route){
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
		
				$op = get("route","contractor_id","WHERE route_id='$bund_route'");
				$bundle_contr_value = get("operator","company","WHERE operator_id='$op'");
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