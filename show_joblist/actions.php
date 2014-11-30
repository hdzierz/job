<?

if($action=="send_messages"){
}


if($action=="affiliate_jobs" && $submit){
	$date1 = $year.'-'.sprintf("%02d",$month).'-01';
	$date2 = $year.'-'.sprintf("%02d",$month).'-31';
	
	$job_qry = "SELECT * FROM job WHERE delivery_date BETWEEN '$date1' AND '$date2'";
	$res = query($job_qry,0);

	$MESSAGE .= "Updated job: ";
	
	while($job = mysql_fetch_object($res)){

		$qry = "UPDATE job_route 
			SET dist_id = 
			(
				SELECT dist_id FROM route_aff WHERE job_route.route_id = route_aff.route_id
					AND '{$job->delivery_date}' BETWEEN app_date AND stop_date	 LIMIT 1
			),
			subdist_id = 
			(
				SELECT subdist_id FROM route_aff WHERE job_route.route_id = route_aff.route_id
					AND '{$job->delivery_date}' BETWEEN app_date AND stop_date  LIMIT 1
			),
			contractor_id = 
			(
				SELECT contractor_id FROM route_aff WHERE job_route.route_id = route_aff.route_id
					AND '{$job->delivery_date}' BETWEEN app_date AND stop_date  LIMIT 1
			),
            dropoff_id = 
            (
                SELECT dropoff_id FROM route_aff WHERE job_route.route_id = route_aff.route_id
                    AND '{$job->delivery_date}' BETWEEN app_date AND stop_date  LIMIT 1
            )

			WHERE job_id = {$job->job_id}";		
		query($qry);
		
		$qry = "UPDATE job_route 
				SET subdist_rate_red = (SELECT rate_red_fact FROM operator WHERE subdist_id = operator_id)
				WHERE job_id = {$job->job_id} ";
		query($qry,0);
		$MESSAGE .= " {$job->job_no} / ";
	}
	$MESSAGE .=  "<br />Finished<br />";
	
}

if($action=="clean_jobs"){
	
	if($date && $submit){
		$qry = "SELECT job_id FROM job WHERE invoice_date<='$date'";
		$res = query($qry);
		$job_str = "(0";
		while($job = mysql_fetch_object($res)){
			$job_str .= ",".$job->job_id;
		}
		$job_str .= ")";
		
		$qry = "REPLACE INTO job_route_archive(
							job_route_id,
							job_id,
							route_id,
							concoll,
							dest_type,
							amount,
							external,
							is_edited,
							version,
							bundle_price,
							orig_amt,
							alt_dropoff_id,
							dropoff_id,
							doff,
							comments,
							dist_id,
							subdist_id,
							contractor_id,
							subdist_rate_red
				) 
				SELECT job_route_id,
							job_id,
							route_id,
							concoll,
							dest_type,
							amount,
							external,
							is_edited,
							version,
							bundle_price,
							orig_amt,
							alt_dropoff_id,
							dropoff_id,
							doff,
							comments,
							dist_id,
							subdist_id,
							contractor_id,
							subdist_rate_red 
				FROM job_route WHERE job_id IN $job_str";
		query($qry);
		$qry = "DELETE FROM job_route WHERE job_id IN $job_str";
		query($qry);
		
		$qry = "REPLACE INTO invoice_archive SELECT * FROM invoice WHERE job_id IN $job_str";
		query($qry);
		$qry = "DELETE FROM invoice WHERE job_id IN $job_str";
		query($qry);
		
		
		$qry = "REPLACE INTO job_archive(job_id,
										purchase_no,
										client_id,
										publication,
										client_pub_id,
										job_no,
										pmp_job_no,
										lodge_date,
										job_no_add,
										invoice_no,
										foreign_job_no,
										invoice_date,
										delivery_date,
										change_date,
										weight,
										rate,
										inc_linehaul,
										dist_rate,
										subdist_rate,
										contr_rate,
										freight_charge,
										hauler,
										comments,
										show_comments,
										confirmed,
										finished,
										cancelled,
										lbc_charge,
										dest_type,
										invoice_qty,
										is_regular,
										qty_bbc,
										rate_bbc,
										lbc_charge_bbc,
										is_ioa,
										alt_job_id,
										is_quote,
										is_att,
										field_surcharge,
										gst,
										fuel_surcharge,
										folding_fee,
										discount,
										is_deliv_sent,
										is_pay_sent,
										hauler_ni_id,
										hauler_si_id,
										is_job_details_sent,
										ni_drop_total,
										si_drop_total,
										desc_bbc,
										add_folding_to_invoice,
										premium,
										add_premium_to_invoice,
										`group`,
										premium_sell,
										paper_source,
										rcd_weight_si,
										rcd_weight_ni,
										rcd_date_ni,
										rcd_date_si,
										disp_date_si,
										disp_date_ni,
										pick_date_ni,
										pick_date_si,
										initials_ni,
										initials_si,
										rcd_qty_si,
										rcd_qty_ni,
										bundle_sell,
										str_group
					) 
				SELECT job_id,
						purchase_no,
						client_id,
						publication,
						client_pub_id,
						job_no,
						pmp_job_no,
						lodge_date,
						job_no_add,
						invoice_no,
						foreign_job_no,
						invoice_date,
						delivery_date,
						change_date,
						weight,
						rate,
						inc_linehaul,
						dist_rate,
						subdist_rate,
						contr_rate,
						freight_charge,
						hauler,
						comments,
						show_comments,
						confirmed,
						finished,
						cancelled,
						lbc_charge,
						dest_type,
						invoice_qty,
						is_regular,
						qty_bbc,
						rate_bbc,
						lbc_charge_bbc,
						is_ioa,
						alt_job_id,
						is_quote,
						is_att,
						field_surcharge,
						gst,
						fuel_surcharge,
						folding_fee,
						discount,
						is_deliv_sent,
						is_pay_sent,
						hauler_ni_id,
						hauler_si_id,
						is_job_details_sent,
						ni_drop_total,
						si_drop_total,
						desc_bbc,
						add_folding_to_invoice,
						premium,
						add_premium_to_invoice,
						`group`,
						premium_sell,
						paper_source,
						rcd_weight_si,
						rcd_weight_ni,
						rcd_date_ni,
						rcd_date_si,
						disp_date_si,
						disp_date_ni,
						pick_date_ni,
						pick_date_si,
						initials_ni,
						initials_si,
						rcd_qty_si,
						rcd_qty_ni,
						bundle_sell,
						str_group 
				FROM job WHERE invoice_date<='$date'";
		query($qry);
		$qry = "DELETE FROM job WHERE invoice_date<='$date'";
		query($qry);
		$MESSAGE = "jobs archived.";
	}	
}

if($action=="update_aff"){
	if($start_date && $end_date){
		//$date = date();
		$qry = "SELECT job_id FROM job 
				WHERE delivery_date<='$end_date'
					AND delivery_date>='$start_date'";
		$res = query($qry);
		while($job=mysql_fetch_object($res)){
			update_route_aff($job->job_id);
		}
		$MESSAGE = "Job's affiliation updated.";
		$action="";
	}
}

if($action=="cancel"){
	$job_no = get("job","job_no","WHERE job_id='$record'");
	
	$orig_job_id = get("job","job_id","WHERE alt_job_id='$record'");
	if($orig_job_id){
		$ERROR = "You attempted to cancel an attached job ($job_no-Main). ";
	}
	else{
		$sql = "UPDATE job SET cancelled='Y' WHERE job_id=$record";
		query($sql);
		$alt_job_id = get("job","alt_job_id","WHERE job_id='$record'");
		
		if($alt_job_id){
			$sql = "UPDATE job SET cancelled='Y' WHERE job_id=$alt_job_id";
			query($sql);
		}
		$ERROR = "Job cancelled.";
	}
	$action="";
}

if($action=="reopen"){
	$job_no = get("job","job_no","WHERE job_id='$record'");
	
	$orig_job_id = get("job","job_id","WHERE alt_job_id='$record'");
	if($orig_job_id){
		$ERROR = "You attempted to cancel an attached job ($job_no-Main). ";
	}
	else{
		$sql = "UPDATE job SET cancelled='N' WHERE job_id=$record";
		query($sql);
		$alt_job_id = get("job","alt_job_id","WHERE job_id='$record'");
		
		if($alt_job_id){
			$sql = "UPDATE job SET cancelled='N' WHERE job_id=$alt_job_id";
			query($sql);
		}
		$ERROR = "Job reopened.";
	}
	$action="";
}

if($action=="unfinish"){
	$job_no = get("job","job_no","WHERE job_id='$record'");
	
	$orig_job_id = get("job","job_id","WHERE alt_job_id='$record'");
	if($orig_job_id){
		$ERROR = "You attempted to reopen an attached job ($job_no-Main). ";
	}
	else{
		$sql = "UPDATE job SET finished='N' WHERE job_id=$record";
		query($sql);
		$alt_job_id = get("job","alt_job_id","WHERE job_id='$record'");
		
		if($alt_job_id){
			$sql = "UPDATE job SET finished='N' WHERE job_id=$alt_job_id";
			query($sql);
		}
		$ERROR = "Job reopened.";
	}
	if($year)
	{
		$action="show_old_jobs";
	}
	else 
	{
		$action="show_old_jobs_by_pub";
	}
}

if($action=="finish"){
	$job_no = get("job","job_no","WHERE job_id='$record'");
	
	$orig_job_id = get("job","job_id","WHERE alt_job_id='$record'");
	if($orig_job_id){
		$ERROR = "You attempted to finish an attached job ($job_no-Main). ";
	}
	else{
		$sql = "UPDATE job SET finished='Y' WHERE job_id=$record";
		query($sql);
		$alt_job_id = get("job","alt_job_id","WHERE job_id='$record'");
		
		if($alt_job_id){
			$sql = "UPDATE job SET finished='Y' WHERE job_id=$alt_job_id";
			query($sql);
		}
		$ERROR = "Job finished.";
	}
	$action="";
}
/*
if($action=="unfinish"){
	$sql = "UPDATE job SET finished='N' WHERE job_id=$record";
	query($sql);
	$action="";
	$ERROR = "Job completed.";
}
*/

if($action=="delete"){
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$record'");
	$job_no = get("job","job_no","WHERE job_id='$record'");
	
	$orig_job_id = get("job","job_id","WHERE alt_job_id='$record'");
	if($orig_job_id){
		$ERROR = "You attempted to edit an attached job ($job_no-Main). ";
	}
	else{
		$qry = "DELETE FROM job WHERE job_id='$record'";
		query($qry);
		$qry = "DELETE FROM job_route WHERE job_id='$record'";
		query($qry);
	
		if($alt_job_id){
			$qry = "DELETE FROM job WHERE job_id='$alt_job_id'";
			query($qry);
			$qry = "DELETE FROM job_route WHERE job_id='$alt_job_id'";
			query($qry);	
		}
		
		$ERROR="Job sucessfully deleted.";	
	}
	$action="";
}

if($action=="delete_temp"){
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$record'");
	$qry = "DELETE FROM job_temp WHERE job_id='$record'";
	query($qry);
	$qry = "DELETE FROM job_route_temp WHERE job_id='$record'";
	query($qry);
	
	if($alt_job_id){
		$qry = "DELETE FROM job_temp WHERE job_id='$alt_job_id'";
		query($qry);
		$qry = "DELETE FROM job_route_temp WHERE job_id='$alt_job_id'";
		query($qry);	
	}	
	
	$ERROR="Template sucessfully deleted.";	
	$action="show_templates";	
}


if($action=="gst" && $gst){
	if($gst<1){
		$ERROR = "GST seems not to be in % ($gst).";
	}
	else{
		$gst_one = $gst/100;
		$qry = "UPDATE config SET value='$gst_one' WHERE name='GST_CIRCULAR'";
		query($qry);
		$qry = "ALTER TABLE job ALTER gst SET DEFAULT $gst";	
		query($qry);
		$GST_CIRCULAR = $gst_one;
		$MESSAGE = "GST for circulars changed. Check parcel gst.";
	}
}

if($action=="fax_email" && $email && $mode){
	$qry = "UPDATE config SET value='$email' WHERE name='FAX_EMAIL_ADDRESS'";
	query($qry);
	$qry = "UPDATE config SET value='$mode' WHERE name='FAX_NUM_EMAIL_MODE'";
	query($qry);
}
?>
