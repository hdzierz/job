<?
function get_joblist_query($with_ioa,$mode,$job,$client,$pub,$start_date=false,$final_date=false){
	if($with_ioa) $addwhere="OR j.is_ioa='Y'";
	
	if($final_date && $start_date){
		$where_add_date = " AND j.delivery_date BETWEEN '$start_date' AND '$final_date'";
	}
	
	$today = date("Y-m-15");
	$last_date = date("Y-m-d",strtotime(date("Y-m-d")." +4 days"));
	
	$this_month = date("Y-m");
	$prev_month = date("Y-m",strtotime($today." -1 month"));
	$next_month = date("Y-m",strtotime($today." +1 month"));

	if($mode=="this_month"){
		$where = " AND DATE_FORMAT(j.delivery_date,'%Y-%m')='$this_month'";
	}
	else if($mode=="prev_month"){
		$where = " AND DATE_FORMAT(j.delivery_date,'%Y-%m')<='$prev_month'";
	}
	else if($mode=="next_month"){
		$where = " AND DATE_FORMAT(j.delivery_date,'%Y-%m')>='$next_month'";
	}
	
	$pub = addslashes($pub);
	if($client) $client_where = " AND c.client_id='$client' ";
	if($pub) $pub_where = " AND j.publication='$pub' ";
	if($pub) $is_pub=1;
	if($job) $job_where = " AND j.job_no='$job' ";
	

	$qry = "SELECT	j.job_id         AS 'Record',
					IF(j.is_deliv_sent=0,
						IF(j.delivery_date<'$last_date','<font color=\'red\'>N</font>','N'),
						'Y')	 
									 AS 'Processed',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_regular='Y','Y','N') 	 AS 'Reg',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Delivery Date',
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))         AS 'Job No.',
					j.invoice_no     AS 'Invoice #',
					j.purchase_no    AS 'Purchase #',
					/*j.foreign_job_no AS 'Cust. Ref.',*/
					IF(
						j.hauler_ni_id=j.hauler_si_id,
						CONCAT('NI/SI: ',ni_hauler.name),
						CONCAT('NI: ',ni_hauler.name,'<br />','SI: ',si_hauler.name)
					)
						
									AS Linehaul,
                    j.print_advices AS 'P/Adv',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type,
					j.cancelled 	 AS 'Cancelled',
					CONCAT('<a class=\'sqlhref\' href=\'proc_job.php?action=edit&first_entry=1&client=$client&pub=$is_pub&job_id=',j.job_id,'\' >edit</a>') AS Edit,
					CONCAT('<a class=\'sqlhref\' href=\'proc_job.php?action=edit_job&dest=job&client=$client&pub=$is_pub&job_id=',j.job_id,'\' >go</a>') AS Det
			FROM job j
			# LEFT JOIN job ja
			# ON ja.alt_job_id=j.job_id
			LEFT JOIN client c
			ON j.client_id=c.client_id
			LEFT JOIN client ni_hauler
				ON ni_hauler.client_id=j.hauler_ni_id
			LEFT JOIN client si_hauler
				ON si_hauler.client_id=j.hauler_si_id
				
			WHERE j.finished!='Y' 
				$where
				$client_where
				$pub_where
				$job_where
				$where_add_date
			GROUP BY j.job_id
			ORDER BY c.name,j.publication,j.job_no,j.job_no_add,j.delivery_date";
	//echo nl2br($qry);
	return $qry;
}

function get_regular_joblist_query($date1,$date2){
	$qry = "SELECT	j.job_id         AS 'Record',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Delivery Date',
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))         AS 'Job No.',
					# j.invoice_no     AS 'Invoice No.',
					j.paper_source AS 'Paper Src',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type			
					# j.cancelled 	 AS 'Cancelled'
					# CONCAT('<a class=\'sqlhref\' href=index.php?action=unfinish&year=".date("Y", strtotime($date1))."&month=".date("m", strtotime($date1))."&record=',j.job_id,'>reopen</a>') AS Action
			FROM job j
			LEFT JOIN client c
			ON j.client_id=c.client_id
			WHERE (j.delivery_date BETWEEN '$date1' AND '$date2') 
				AND j.is_regular='Y'
			GROUP BY j.job_id
			ORDER BY c.name,j.publication,j.delivery_date DESC,j.job_no";
	return $qry;
}


function get_finished_joblist_query($date1,$date2){
	$qry = "SELECT	j.job_id         AS 'Record',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Delivery Date',
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))         AS 'Job No.',
					j.invoice_no     AS 'Invoice No.',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type,			
					j.cancelled 	 AS 'Cancelled',
					CONCAT('<a class=\'sqlhref\' href=index.php?action=unfinish&year=".date("Y", strtotime($date1))."&month=".date("m", strtotime($date1))."&record=',j.job_id,'>reopen</a>') AS Action
			FROM job j
			LEFT JOIN job ja
			ON ja.alt_job_id=j.job_id			
			LEFT JOIN client c
			ON j.client_id=c.client_id
			WHERE (j.delivery_date BETWEEN '$date1' AND '$date2') 
				AND j.finished='Y'
			GROUP BY j.job_id
			ORDER BY c.name,j.publication,j.delivery_date,j.job_no";
	return $qry;
}

function get_finished_joblist_by_pub_query($publication,$client_id){
	if($publication) $pub = "AND j.publication='$publication'";
	if($client_id) $cli = "AND c.client_id='$client_id'";
	
	$qry = "SELECT	j.job_id         AS 'Record',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Delivery Date',
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))         AS 'Job No.',
					j.invoice_no     AS 'Invoice No.',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type,			
					j.cancelled 	 AS 'Cancelled',
					CONCAT('<a class=\'sqlhref\' href=index.php?action=unfinish&client_id=".$client_id."&publication=".str_replace(" ","+", $publication)."&record=',j.job_id,'>reopen</a>') AS Action
			FROM job j
			LEFT JOIN job ja
			ON ja.alt_job_id=j.job_id			
			LEFT JOIN client c
			ON j.client_id=c.client_id
			WHERE j.job_id IS NOT NULL
				$pub
				$cli
				AND j.finished='Y'
			GROUP BY j.job_id
			ORDER BY j.delivery_date,j.publication,c.name,j.job_no";
	return $qry;
}


function get_quote_joblist_query(){
	$qry = "SELECT	j.job_id         AS 'Record',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Delivery Date',
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))         AS 'Job No.',
					j.invoice_no     AS 'Invoice No.',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type,
					CONCAT('<a class=\'sqlhref\' href=\'proc_job.php?action=edit&job_id=',j.job_id,'\' >edit</a>') AS Edit
					#CONCAT('<a class=\'sqlhref\' href=index.php?action=unfinish&record=',j.job_id,'>reopen</a>') AS Action
			FROM job j
			LEFT JOIN job ja
			ON ja.alt_job_id=j.job_id			
			LEFT JOIN client c
			ON j.client_id=c.client_id
			WHERE j.is_quote='Y'
			GROUP BY j.job_id
			ORDER BY c.name,j.publication,j.delivery_date,j.job_no";
	return $qry;
}

function get_att_joblist_query($date1,$date2){
	$qry = "SELECT	j.job_id         AS 'Record',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Delivery Date',
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))         AS 'Job No.',
					j.invoice_no     AS 'Invoice No.',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type,			
					j.cancelled 	 AS 'Cancelled'
					
			FROM job j
			LEFT JOIN job ja
			ON ja.alt_job_id=j.job_id	
			LEFT JOIN client c
			ON j.client_id=c.client_id
			WHERE (j.delivery_date BETWEEN '$date1' AND '$date2') 
				AND j.finished='Y'
			GROUP BY j.job_id
			ORDER BY c.name,j.publication,j.delivery_date,j.job_no";
	return $qry;
}


function get_template_query(){
	$qry = "SELECT	j.job_id         AS 'Record',
					c.name           AS 'Client',
					j.publication	 AS 'Publication',
					IF(j.is_ioa='Y','IOA',
						j.delivery_date)  
									 AS 'Orig. Delivery Date',
					j.invoice_qty    AS 'Invoiced',
					ROUND(j.weight,0)		 AS 'Weight',
					IF(j.dest_type='num_total','Total',
								IF(j.dest_type='num_farmers','Farmer',
									IF(j.dest_type='num_dairies','Dairy',
										IF(j.dest_type='num_sheep','Sheep',
											IF(j.dest_type='num_beef','Beef',
												IF(j.dest_type='num_sheepbeef','Sheep/Beef',
													IF(j.dest_type='num_dairybeef','Dairy/Beef',
														IF(j.dest_type='num_hort','Hort',
															IF(j.dest_type='num_nzfw','F@90%',
																IF(j.dest_type='num_spare','Spare',
																	IF(j.dest_type='num_lifestyle','Lifestyle',
																		IF(j.dest_type='bundles','Bundles',j.dest_type	
							)))))))))))) AS Type,
					CONCAT(j.job_no,IF(j.job_no_add IS NOT NULL,j.job_no_add,''))    AS 'From Job No.',
					CONCAT('<a href=\'proc_temp.php?action=edit_job&dest=template&job_id=',j.job_id,'\' >Go to</a>') AS Det
			FROM job_temp j
			LEFT JOIN job_temp ja
			ON ja.alt_job_id=j.job_id	
			LEFT JOIN client c
			ON j.client_id=c.client_id
			GROUP BY j.job_id
			ORDER BY c.name,j.publication,j.delivery_date,j.job_no";
	return $qry;
}
?>
