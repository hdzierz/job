
<?

// Updates the affiliation info of a job
if($action=="update_aff"){
	$m = update_route_aff($job_id);
	$MESSAGE = "Route affiliations updated.".$m;
	$action="edit";
}

// Detach a job from a reference job
if($action=="detach_job"){
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	delete_alt_job($alt_job_id);

	$action="";
	$MESSAGE="Job detached";
}

// Attach a job to a reference job
if($action=="do_attach_job"){

	// Remove an existing attached job. Just in case.
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	//echo "Hello".$alt_job_id." ".$job_id;
	if($alt_job_id){
		delete_alt_job($alt_job_id);
	}
	
	// Copy the current job information to the attached job
	$job_no_att = 'L';
	$job_no_main = "L-Main";
	//$job_no = create_job_no();
	$qry = "INSERT INTO job(client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						pmp_job_no,
						lodge_date,
						job_no_add,
						foreign_job_no,
						hauler_ni_id,
						hauler_si_id,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
						inc_linehaul,
                        print_advices,
						rate_bbc,
						dist_rate,
						subdist_rate,
						contr_rate,
						folding_fee,
						discount,
						fuel_surcharge,
						freight_charge,
						lbc_charge,
						lbc_charge_bbc,
						qty_bbc,
						dest_type,
						comments,
						show_comments,
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						is_quote,
						is_att,
						ni_drop_total,
						si_drop_total,
						desc_bbc,
						add_folding_to_invoice,
						premium,
						premium_sell,
						add_premium_to_invoice)
						
						
						SELECT client_id,
							publication,
							'0',
							purchase_no,
							job_no,
							pmp_job_no,
							lodge_date,
							'$job_no_att',
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							folding_fee,
							discount,
							fuel_surcharge,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							'$alt_dest_type',
							comments,
							show_comments,
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							invoice_qty,
							is_regular,
							is_quote,
							'Y',
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							premium_sell,
							add_premium_to_invoice
						 FROM job WHERE job_id=$job_id";
	query($qry);
	$new_job_id = mysql_insert_id();
	
	// Update the reference job to mark it as a job which has another job attached to it
	$qry = "UPDATE job SET alt_job_id = '$new_job_id',job_no_add='$job_no_main' WHERE job_id='$job_id'";
	query($qry);
	$type = $alt_dest_type;

	// Copy the booked routes over but add the correct numbers from the routes table
	if($type=="num_total") $sel_field = "route.num_lifestyle+route.num_farmers";
	else $sel_field = $type;
	
	$qry = "INSERT INTO job_route(job_id,
								  route_id,
								  dest_type,
								  amount,
								  orig_amt,
								  version,
								  alt_dropoff_id,
								  dropoff_id,
								  doff,
								  dist_id,
								  subdist_id,
								  contractor_id,
								  bundle_price,
								  external,
								  is_edited)
			SELECT '$new_job_id',
					job_route.route_id,
					IF(
						job_route.dest_type='bundles','bundles','$type'
					),
					IF(
						job_route.dest_type='bundles',job_route.amount,
						IF(
							job_route.amount<>job_route.orig_amt,job_route.amount,$sel_field
						)
					),
					IF(
						job_route.dest_type='bundles',job_route.orig_amt,$sel_field
					),
					version,
				 	alt_dropoff_id,
					job_route.dropoff_id,
				    job_route.doff,
				    job_route.dist_id,
				    job_route.subdist_id,
				    job_route.contractor_id,
				    bundle_price,
				    job_route.external,
					job_route.is_edited
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			WHERE job_id=$job_id";
	query($qry);	
	$MESSAGE = "Attached job created with updated numbers (edited numbers retained)";
	
	// Bring the user back to the edit screen
	$action="edit";
}


//////////////////////////////////////////////////////////
// SECTION INVOICE										//
//////////////////////////////////////////////////////////


// Create a new or change an existing job
if($action=="save_job" || $action=="add_job"){
    if($print_advices<>'Y') $print_advices='N';
    if($inc_linehaul<>'Y') $inc_linehaul='N';
	$error=false;
	// Validate submit
	if(!$delivery_date && $is_ioa){
		$ERROR = "No Delivery Date!";
		$error=true;
	}
	if(!$dest_type && $action!="save_job"){
		$ERROR = "No Farmer Type Set!";
		$error=true;
	}	
	if(!$weight){
		$weight=0.0;
	}
	if(!$rate){
		$rate=0.0;
	}
	if($contact_name==""){
		$contact_name=0.0;
	}	
	if(!$dist_rate){
		$dist_rate=0.0;
	}
	if(!$subdist_rate){
		$subdist_rate=0.0;
	}
	if(!$contr_rate){
		$contr_rate=0.0;
	}
	if(!$malcove_charge){
		$malcove_charge=0.0;	
	}
	if(!$lbc_charge){
		$lbc_charge=0.0;	
	}	
	if(!$coural_allowance){
		$coural_allowance=0.0;	
	}
	if(!$publication){
		$ERROR = "No Publication!";
		$error=true;
	}	
	
	if(!$hauler_ni_id || !$hauler_si_id){
		$ERROR = "Please specify hauler!";
		$error=true;
	}	
	
	// if user cancelled bring him back to main screen
	if($cancel=="Cancel"){
		$action="";
	}
	else if(!$error){
		$today=date("Y-m-d");
		$comments = addslashes($comments);
		
		// Get some information from the existing job 
		$old_quote = get("job","is_quote","WHERE job_id='$job_id'");
		$cancelled = get("job","cancelled","WHERE job_id='$job_id'");
		$finished  = get("job","finished","WHERE job_id='$job_id'");

		if(!$add_folding_to_invoice) $add_folding_to_invoice = 'N';
		if(!$add_premium_to_invoice) $add_premium_to_invoice = 'N'; 
		
		// quote is always finished and cancelled to prevent them appearing in reports
		if($quote=='Y'){
			$cancelled='Y';
			$finished='Y';
		}
		else{
			$quote='N';
			if($old_quote=='Y'){
				$cancelled='N';
				$finished='N';		
			}
		}
		
		// Make show_sommenst = 'N' if empty
		if($show_comments<>'Y') $show_comments='N';
		
		// add slashes to comment. Otherwise quotation marks cause trouble
		$publication = addslashes($publication);
		// Create a new job
		if($action=="add_job"){
			// Create a new job number
			$job_no = create_job_no();
			$sql = "INSERT INTO job(
						client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						pmp_job_no,
						lodge_date,
						job_no_add,
						foreign_job_no,
						hauler_ni_id,
						hauler_si_id,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
						inc_linehaul,
                        print_advices,
						rate_bbc,
						dist_rate,
						subdist_rate,
						contr_rate,
						folding_fee,
						discount,
						fuel_surcharge,
						freight_charge,
						lbc_charge,
						lbc_charge_bbc,
						qty_bbc,
						dest_type,
						comments,
						show_comments,
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						is_quote,
						ni_drop_total,
						si_drop_total,
						desc_bbc,
						add_folding_to_invoice,
						premium,
						premium_sell,
						paper_source,
						add_premium_to_invoice,
						bundle_sell)
					VALUES(
						'$client_id',
						'$publication',
						'$invoice_no',
						'$purchase_no',
						'$job_no',
						'$pmp_job_no',
						'$lodge_date',
						'',
						'$foreign_job_no',
						'$hauler_ni_id',
						'$hauler_si_id',
						'$delivery_date',
						'$is_ioa',
						'$today',
						'$weight',
						'$rate',
						'$inc_linehaul',
                        '$print_advices',
						'$rate_bbc',
						'$dist_rate',
						'$subdist_rate',
						'$contr_rate',
						'$folding_fee',
						'$discount',
						'$fuel_surcharge',
						'$freight_charge',
						'$lbc_charge',
						'$lbc_charge_bbc',
						'$qty_bbc',
						'$dest_type',
						'$comments',
						'$show_comments',
						'N',
						'$finished',
						'$cancelled',
						'$invoice_qty',
						'$regular',
						'$quote',
						'$ni_drop_total',
						'$si_drop_total',
						'$desc_bbc',
						'$add_folding_to_invoice',
						'$premium',
						'$paper_source',
						'$premium_sell',
						'$add_premium_to_invoice',
						'$bundle_sell')";		
			query($sql);	
			$job_id=mysql_insert_id();
			// Bring user back to main screen
			$action="";
		}
		// Update existing one
		else{			
			// Has alternate job
			$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
			// Is alternate job
			$alt_job_id_self = get("job","alt_job_id","WHERE alt_job_id='$job_id'");
			// User changes attached job. Only changing teh quantities is allowed.
			if($alt_job_id_self){
				
				$sql = "UPDATE job SET
						change_date			='$today',
						qty_bbc				='$qty_bbc',
						invoice_qty			='$invoice_qty'
					WHERE job_id='$job_id'";
				query($sql);			
			}
			// User changes normal job
			else{
				// if user chanegs dest_type the system has to update those in the job_route table as well.
				$dest_type_cur = get("job","dest_type","WHERE job_id='$job_id'");
				if($dest_type_cur!=$dest_type){
					change_type($job_id,$dest_type);
					// In that case changed numbers are overridden.
					$ERROR .= "<br /> Type changed. Potentially existing edited numbers overridden.<br />";
				}
				// Change the job
				$sql = "UPDATE job SET
							client_id			='$client_id',
							publication			='$publication',
							invoice_no			='$invoice_no',
							purchase_no			='$purchase_no',
							pmp_job_no			='$pmp_job_no',
							lodge_date			='$lodge_date',
							foreign_job_no		='$foreign_job_no',
							hauler_ni_id				='$hauler_ni_id',
							hauler_si_id				='$hauler_si_id',
							delivery_date		='$delivery_date',
							is_ioa				='$is_ioa',
							change_date			='$today',
							weight				='$weight',
							rate				='$rate',
							inc_linehaul		='$inc_linehaul',
                            print_advices       ='$print_advices',
							rate_bbc			='$rate_bbc',
							dist_rate			='$dist_rate',
							subdist_rate		='$subdist_rate',
							contr_rate			='$contr_rate',
							folding_fee			='$folding_fee',
							discount			= '$discount',
							fuel_surcharge		='$fuel_surcharge',
							
							freight_charge		='$freight_charge',
							lbc_charge			='$lbc_charge',
							lbc_charge_bbc		='$lbc_charge_bbc',
							qty_bbc				='$qty_bbc',
							comments			='$comments',
							show_comments		='$show_comments',
							invoice_qty			='$invoice_qty',
							is_regular			='$regular',
							is_quote			='$quote',
							cancelled			= '$cancelled',
							finished			= '$finished',
							ni_drop_total		= '$ni_drop_total',
							si_drop_total		= '$si_drop_total',
							desc_bbc			= '$desc_bbc',
							add_folding_to_invoice = '$add_folding_to_invoice',
							premium				= '$premium',
							paper_source		= '$paper_source',
							premium_sell		= '$premium_sell',
							add_premium_to_invoice = '$add_premium_to_invoice',
							bundle_sell         = '$bundle_sell'
						WHERE job_id='$job_id'";
				query($sql);
			}
			// Change the attached job too.
			if($alt_job_id){
				$sql = "UPDATE job SET
							client_id			='$client_id',
							publication			='$publication',
							invoice_no			='$invoice_no',
							purchase_no			='$purchase_no',
							foreign_job_no		='$foreign_job_no',
							hauler_ni_id				='$hauler_ni_id',
							hauler_si_id				='$hauler_si_id',
							delivery_date		='$delivery_date',
							is_ioa				='$is_ioa',
							change_date			='$today',
							weight				='$weight',
							rate				='$rate',
							inc_linehaul		='$inc_linehaul',
                            print_advices       ='$print_advices',
							rate_bbc			='$rate_bbc',
							dist_rate			='$dist_rate',
							subdist_rate		='$subdist_rate',
							contr_rate			='$contr_rate',
							folding_fee			='$folding_fee',
							discount			='$discount',
							fuel_surcharge		='$fuel_surcharge',
							
							freight_charge		='$freight_charge',
							lbc_charge			='$lbc_charge',
							lbc_charge_bbc		='$lbc_charge_bbc',
							comments			='$comments',
							show_comments		='$show_comments',
							is_regular			='$regular',
							is_quote			='$quote',
							cancelled			= '$cancelled',
							finished			= '$finished',
							ni_drop_total		= '$ni_drop_total',
							si_drop_total		= '$si_drop_total',
							desc_bbc			= '$desc_bbc',
							add_folding_to_invoice = '$add_folding_to_invoice',
							premium				= '$premium',
							paper_source		= '$paper_source',
							premium_sell		= '$premium_sell',
							add_premium_to_invoice = '$add_premium_to_invoice'
							is_att				= 'Y'
						WHERE job_id='$alt_job_id'";				
				query($sql);	
			}			
		}
		// Bring user back to main screen
		$action="";
		$MESSAGE = "Job details successfully created/changed.";

	}
	// If error bring user back to edit/add screen
	else
		$action=$dest;
}



// Delete a single booked route
if($action=="delete"){
	query("DELETE FROM job_route WHERE job_route_id=$record");
	// bring user back to main screen
	$action="";
	$ERROR = "Route successfully deleted from job.";
}

// Change a single booking
if($action=="save_line"){
	if(!$cancel=="Cancel"){
		// Update the route
		$qry = "UPDATE job_route 
				SET amount=$amount+0,
					version='$version',
					bundle_price='$bundle_price'+0,
					comments='$notes',
					is_edited='Y' 
				WHERE job_route_id='$job_route_id'";
		query($qry);
		
		// Change attached line too.
		$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
		
		if($alt_job_id){
			$route_id = get("job_route","route_id","WHERE job_route_id='$job_route_id'");
			$qry = "UPDATE job_route SET amount='$amount2'+0,bundle_price='$bundle_price'+0 
					WHERE route_id='$route_id' AND job_id='$alt_job_id'";
			query($qry);
		}
		$MESSAGE = "Route changed.";
	}
	else{
		$MESSAGE = "Action cancelled";
	}
	
	// Bring user back to main screen
	$action="";
}




// There are some actions from the main screen table where the table appears itself as a form. You may delete a line or 
// allocate an alternative drop off point for a booked route
if($action=="action_from_table"){

	// Create list of selected rows
	$in = "(-1";
	foreach($check as $jr=>$checked){
		if($checked) $in.=",$jr";
	}
	$in .= ")";

	// Delete a line
	if($delete_lines=="Delete lines"){
		$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
		
		
		
		
		$qry = "SELECT * FROM job_route WHERE job_id=$job_id AND job_route_id IN $in";
		$res_routes = query($qry);
		while($line = mysql_fetch_object($res_routes)){
			// Delete attached route
			if($alt_job_id){
				// To find the attached route we need the route
				$route_id = get("job_route","route_id","WHERE job_route_id = '$line->job_route_id'");
				// We need also the dest_type to distinguish bundles.
				$dest_type = get("job_route","dest_type","WHERE job_route_id='$line->job_route_id'");
				$qry = "DELETE FROM job_route WHERE route_id='$route_id' AND job_id='$alt_job_id' AND dest_type='$dest_type'";
				query($qry);
			}					
			// Delet booked route
			$qry = "DELETE FROM job_route WHERE job_route_id=$line->job_route_id";
			query($qry);		

		}
		// Bring you back to main screen
		$action="";
		$ERROR = "Routes removed.";
	}
	// Sets alterantive drop off point
	else if($set_alternate_do=="Set Alt DO"){
		$qry = "SELECT * FROM job_route WHERE job_id=$job_id AND job_route_id IN $in";
		$res_routes = query($qry);
		while($line = mysql_fetch_object($res_routes)){
			$qry = "UPDATE job_route 
					SET alt_dropoff_id = '$alt_dropoff_id'
					WHERE job_route_id=$line->job_route_id";
			query($qry);
		}
		// Bring user back to main screen
		$action="";
		$MESSAGE = "Alternate Drop Offs set.";	
	}
}


// Create a new job from an existing one
if($action=="save_as_new"||$action=="save_as_template"){
	if(!$job_no){
		$job_no = create_job_no();
	}
	if($action=="save_as_template") {
		$tab_job = "job_temp";
		$tab_job_route = "job_route_temp";
		$job_no = get("job","job_no","WHERE job_id='$job_id'");
	}
	else{
		$tab_job="job";
		$tab_job_route = "job_route";
	}
	
	if($action=="load_temp") {
		$job_id=$record;
		$from_job = "job_temp";
		$from_job_route = "job_route_temp";
	}
	else{
		$from_job="job";
		$from_job_route = "job_route";
	}
	if($new_date)
		$date_str = "'$new_date',";
	else 
		$date_str ="delivery_date,";
		
	$today = date("Y-m-d");
	$qry = "INSERT INTO $tab_job(client_id,
						publication,
						#invoice_no,
						#purchase_no,
						job_no,
						#pmp_job_no,
						lodge_date,
						job_no_add,
						foreign_job_no,
						hauler_ni_id,
						hauler_si_id,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
						inc_linehaul,
                        print_advices,
						rate_bbc,
						dist_rate,
						subdist_rate,
						contr_rate,
						folding_fee,
						discount,
						fuel_surcharge,
						freight_charge,
						lbc_charge,
						lbc_charge_bbc,
						qty_bbc,
						dest_type,
						comments,
						show_comments,
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						is_quote,
						alt_job_id,
						ni_drop_total,
						si_drop_total,
						desc_bbc,
						add_folding_to_invoice,
						premium,
						paper_source,
						premium_sell,
						add_premium_to_invoice,
						bundle_sell)
						
						
						SELECT client_id,
							publication,
							#invoice_no,
							#purchase_no,
							'$job_no',
							#pmp_job_no,
							lodge_date,
							job_no_add,
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							$date_str
							is_ioa,
							'$today',
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							folding_fee,
							discount,
							fuel_surcharge,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							dest_type,
							comments,
							show_comments,
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							invoice_qty,
							is_regular,
							is_quote,
							alt_job_id,
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							paper_source,
							premium_sell,
							add_premium_to_invoice,
							bundle_sell
						 FROM $from_job WHERE job_id=$job_id";
	query($qry);
	$new_job_id = mysql_insert_id();
	
	$qry = "INSERT INTO $tab_job_route(job_id,
								  route_id,
								  dist_id,
								  subdist_id,
								  contractor_id,
								  dest_type,
								  amount,
								  orig_amt,
								  version,
								  alt_dropoff_id,
								  bundle_price,
								  external,
								  comments)
			SELECT '$new_job_id',
					route_id,
					dist_id,
				    subdist_id,
				    contractor_id,
					dest_type,
					amount,
					orig_amt,
					version,
				 	alt_dropoff_id,
					bundle_price,
				    external,
					comments
			FROM $from_job_route
			WHERE job_id=$job_id";
	query($qry);	
	
	update_route_aff($new_job_id,$today);
	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
		
	if($alt_job_id){
		//$job_no_alt = get("job","job_no","WHERE job_id='$alt_job_id'");
		$job_no_alt = $job_no;
		$job_no_orig = $job_no;
		$qry = "INSERT INTO $tab_job(client_id,
							publication,
							#invoice_no,
							#purchase_no,
							job_no,
							#pmp_job_no,
							lodge_date,
							job_no_add,
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							folding_fee,
							discount,
							fuel_surcharge,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							dest_type,
							comments,
							show_comments,
							confirmed,
							finished,
							cancelled,
							invoice_qty,
							is_regular,
							is_quote,
							is_att,
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							paper_source,
							premium_sell,
							add_premium_to_invoice)
							
							
							SELECT client_id,
								publication,
								#invoice_no,
								#purchase_no,
								'$job_no_alt',
								#pmp_job_no,
								lodge_date,
								job_no_add,
								foreign_job_no,
								hauler_ni_id,
								hauler_si_id,
								$date_str
								is_ioa,
								'$today',
								weight,
								rate,
								inc_linehaul,
                                print_advices,
								rate_bbc,
								dist_rate,
								subdist_rate,
								contr_rate,
								folding_fee,
								discount,
								fuel_surcharge,
								freight_charge,
								lbc_charge,
								lbc_charge_bbc,
								qty_bbc,
								dest_type,
								comments,
								show_comments,
								IF(is_quote='Y','Y','N'),
								IF(is_quote='Y','Y','N'),
								IF(is_quote='Y','Y','N'),
								invoice_qty,
								is_regular,
								is_quote,
								'Y',
								ni_drop_total,
								si_drop_total,
								desc_bbc,
								add_folding_to_invoice,
								premium,
								paper_source,
								premium_sell,
								add_premium_to_invoice
							 FROM $from_job WHERE job_id=$alt_job_id";
		query($qry);
		$new_job_id2 = mysql_insert_id();
	
		$qry = "INSERT INTO $tab_job_route(job_id,
									  route_id,
									  dist_id,
									  subdist_id,
									  contractor_id,
									  dest_type,
									  amount,
									  orig_amt,
									  version,
									  alt_dropoff_id,
									  bundle_price,
									  external)
				SELECT '$new_job_id2',
						route_id,
						dist_id,
					    subdist_id,
					    contractor_id,
						dest_type,
						amount,
						orig_amt,
						version,
						alt_dropoff_id,
						bundle_price,
						external
				FROM $from_job_route
				WHERE job_id=$alt_job_id";
		query($qry);			
		$qry = "UPDATE $tab_job SET alt_job_id='$new_job_id2',job_no='$job_no_orig' WHERE job_id='$new_job_id'";
		query($qry);			
		update_route_aff($new_job_id2,$today);
	}
	
	if($action=="save_as_template") {
		$MESSAGE = "Template created";
		$job_id=$job_id;
	}
	else if($action=="load_temp") {
		$MESSAGE = "New job loaded from template";
		$job_id=$new_job_id;	
	}
	else{
		$MESSAGE = "New job created";
		$job_id=$new_job_id;
	}
	
	$action="edit";
}


if($action=="load_temp"){
	if(!$job_id) $job_id=$record;
	$job_no = create_job_no();
	if($new_date)
		$date_str = "'$new_date'";
	else 
		$date_str ="delivery_date";
		
	$today = date("Y-m-d");
	
	$qry = "INSERT INTO job(client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						pmp_job_no,
						lodge_date,
						job_no_add,
						foreign_job_no,
						hauler_ni_id,
						hauler_si_id,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
						inc_linehaul,
                        print_advices,
						rate_bbc,
						dist_rate,
						subdist_rate,
						contr_rate,
						freight_charge,
						lbc_charge,
						lbc_charge_bbc,
						qty_bbc,
						dest_type,
						comments,
						show_comments,
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						is_quote,
						alt_job_id,
						ni_drop_total,
						si_drop_total,
						desc_bbc,
						add_folding_to_invoice,
						premium,
						paper_source,
						premium_sell,
						add_premium_to_invoice)
						
						
						SELECT client_id,
							publication,
							invoice_no,
							purchase_no,
							'$job_no',
							pmp_job_no,
							lodge_date,
							job_no_add,
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							$date_str,
							is_ioa,
							'$today',
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							dest_type,
							comments,
							show_comments,
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							invoice_qty,
							is_regular,
							is_quote,
							alt_job_id,
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							paper_source,
							premium_sell,
							add_premium_to_invoice
						 FROM job_temp WHERE job_id=$job_id";
	query($qry);
	$new_job_id = mysql_insert_id();

	$qry = "INSERT INTO job_route(job_id,
								  route_id,
								  dest_type,
								  amount,
								  orig_amt,
								  version,
								  alt_dropoff_id,
								  bundle_price,
								  external,
								  comments)
			SELECT '$new_job_id',
					route_id,
					dest_type,
					amount,
					orig_amt,
					version,
				 	alt_dropoff_id,
					bundle_price,
				    external,
					comments
			FROM job_route_temp
			WHERE job_id=$job_id";
	query($qry);	
	update_route_aff($new_job_id,$today);
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
		
	if($alt_job_id){
		$job_no.='L';
		$orig_job_no=$job_no."-Main";
		$qry = "INSERT INTO job(client_id,
							publication,
							invoice_no,
							purchase_no,
							job_no,
							job_no_add,
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							dest_type,
							comments,
							show_comments,
							confirmed,
							finished,
							cancelled,
							invoice_qty,
							is_regular,
							is_quote,
							is_att,
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							paper_source,
							premium_sell,
							add_premium_to_invoice)
							
							
							SELECT client_id,
								publication,
								invoice_no,
								purchase_no,
								'$job_no',
								job_no_add,
								foreign_job_no,
								hauler_ni_id,
								hauler_si_id,
								$date_str,
								is_ioa,
								'$today',
								weight,
								rate,
								inc_linehaul,
                                print_advices,
								rate_bbc,
								dist_rate,
								subdist_rate,
								contr_rate,
								freight_charge,
								lbc_charge,
								lbc_charge_bbc,
								qty_bbc,
								dest_type,
								comments,
								show_comments,
								IF(is_quote='Y','Y','N'),
								IF(is_quote='Y','Y','N'),
								IF(is_quote='Y','Y','N'),
								invoice_qty,
								is_regular,
								is_quote,
								'Y',
								ni_drop_total,
								si_drop_total,
								desc_bbc,
								add_folding_to_invoice,
								premium,
								paper_source,
								premium_sell,
								add_premium_to_invoice
							 FROM job_temp WHERE job_id=$alt_job_id";
		query($qry);
		$new_job_id2 = mysql_insert_id();
		$qry = "INSERT INTO job_route(job_id,
									  route_id,
									  dest_type,
									  amount,
									  orig_amt,
									  version,
									  alt_dropoff_id,
									  bundle_price,
									  external)
				SELECT '$new_job_id2',
						route_id,
						dest_type,
						amount,
						orig_amt,
						version,
						alt_dropoff_id,
						bundle_price,
						external
				FROM job_route_temp
				WHERE job_id=$alt_job_id";
		query($qry);			
		$qry = "UPDATE job SET alt_job_id='$new_job_id2',job_no = '$orig_job_no' WHERE job_id='$new_job_id'";
		query($qry);			
		update_route_aff($new_job_id2,$today);
	}
	
	$MESSAGE = "New job loaded from template";
	$job_id=$new_job_id;	
	
	$action="edit";
}


// Create a new job from an existing one. It is called upated becasue it updates the numbers
if($action=="save_as_new_updated"){

	// Create a job number for the job
	$job_no = create_job_no();
	
	$qry = "INSERT INTO job(client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						pmp_job_no,
						lodge_date,
						job_no_add,
						foreign_job_no,
						hauler_ni_id,
						hauler_si_id,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
						inc_linehaul,
                        print_advices,
						rate_bbc,
						dist_rate,
						subdist_rate,
						contr_rate,
						folding_fee,
						discount,
						fuel_surcharge,
						freight_charge,
						lbc_charge,
						lbc_charge_bbc,
						qty_bbc,
						dest_type,
						comments,
						show_comments,
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						is_quote,
						ni_drop_total,
						si_drop_total,
						desc_bbc,
						add_folding_to_invoice,
						premium,
						paper_source,
						premium_sell,
						add_premium_to_invoice,
						bundle_sell)
						
						
						SELECT client_id,
							publication,
							invoice_no,
							purchase_no,
							'$job_no',
							pmp_job_no,
							lodge_date,
							job_no_add,
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							folding_fee,
							discount,
							fuel_surcharge,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							dest_type,
							comments,
							show_comments,
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							invoice_qty,
							is_regular,
							is_quote,
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							paper_source,
							premium_sell,
							add_premium_to_invoice,
							bundle_sell
						 FROM job WHERE job_id=$job_id";
	query($qry);
	$new_job_id = mysql_insert_id();
	
	// Get dest_type
	$type = get("job","dest_type","WHERE job_id=$job_id");
	
	if($type=="num_total") $sel_field = "route.num_lifestyle+route.num_farmers";
	else $sel_field = $type;
	
	// Create new booked routes
	$qry = "INSERT INTO job_route(job_id,
								  route_id,
								  dest_type,
								  amount,
								  orig_amt,
								  version,
								  alt_dropoff_id,
								  bundle_price,
								  external,
								  is_edited,
								  comments)
			SELECT '$new_job_id',
					job_route.route_id,
					dest_type,
					IF(
						job_route.dest_type='bundles',job_route.amount,
						IF(
							job_route.amount<>job_route.orig_amt,job_route.amount,$sel_field
						)
					),
					IF(
						job_route.dest_type='bundles',job_route.orig_amt,$sel_field
					),
					version,
				 	alt_dropoff_id,
					bundle_price,
				    job_route.external,
					job_route.is_edited,
					job_route.comments
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			WHERE job_id=$job_id";
	query($qry);	
	
	// Updates the route affiliation
	update_route_aff($new_job_id,$today);

	// Create the attached job if it exists	
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	if($alt_job_id){
		$job_no = create_job_no();
		
		$qry = "INSERT INTO job(client_id,
							publication,
							invoice_no,
							purchase_no,
							job_no,
							pmp_job_no,
							lodge_date,
							job_no_add,
							foreign_job_no,
							hauler_ni_id,
							hauler_si_id,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
							inc_linehaul,
                            print_advices,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							folding_fee,
							discount,
							fuel_surcharge,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							dest_type,
							comments,
							show_comments,
							confirmed,
							finished,
							cancelled,
							invoice_qty,
							is_regular,
							is_quote,
							is_att,
							ni_drop_total,
							si_drop_total,
							desc_bbc,
							add_folding_to_invoice,
							premium,
							paper_source,
							premium_sell,
							add_premium_to_invoice)
							
							
							SELECT client_id,
								publication,
								invoice_no,
								purchase_no,
								'$job_no',
								pmp_job_no,
								lodge_date,
								job_no_add,
								foreign_job_no,
								hauler_ni_id,
								hauler_si_id,
								delivery_date,
								is_ioa,
								invoice_date,
								weight,
								rate,
								inc_linehaul,
                                print_advices,
								rate_bbc,
								dist_rate,
								subdist_rate,
								contr_rate,
								folding_fee,
								discount,
								fuel_surcharge,
								freight_charge,
								lbc_charge,
								lbc_charge_bbc,
								qty_bbc,
								dest_type,
								comments,
								show_comments,
								IF(is_quote='Y','Y','N'),
								IF(is_quote='Y','Y','N'),
								IF(is_quote='Y','Y','N'),
								invoice_qty,
								is_regular,
								is_quote,
								'Y',
								ni_drop_total,
								si_drop_total,
								desc_bbc,
								add_folding_to_invoice,
								premium,
								paper_source,
								premium_sell
								add_premium_to_invoice
							 FROM job WHERE job_id=$alt_job_id";
		query($qry);
		$new_job_id2 = mysql_insert_id();
		
		$type = get("job","dest_type","WHERE job_id=$alt_job_id");
		
		if($type=="num_total") $sel_field = "route.num_lifestyle+route.num_farmers";
		else $sel_field = $type;
		
		$qry = "INSERT INTO job_route(job_id,
									  route_id,
									  dest_type,
									  amount,
									  orig_amt,
									  version,
									  alt_dropoff_id,
									  bundle_price,
									  external,
									  is_edited)
				SELECT '$new_job_id2',
						job_route.route_id,
						dest_type,
						IF(
							job_route.dest_type='bundles',job_route.amount,
							IF(
								job_route.amount<>job_route.orig_amt,job_route.amount,$sel_field
							)
						),
						IF(
							job_route.dest_type='bundles',job_route.orig_amt,$sel_field
						),
						version,
						alt_dropoff_id,
						bundle_price,
						job_route.external,
						job_route.is_edited
				FROM job_route
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				WHERE job_id=$alt_job_id";
		query($qry);		
		
		// Update the new job woth the attached job id
		$qry = "UPDATE job SET alt_job_id='$new_job_id2' WHERE job_id='$new_job_id'";
		query($qry);			
		update_route_aff($new_job_id2,$today);	
	}	
	$MESSAGE = "New job created with updated numbers (edited numbers retained)";
	$job_id=$new_job_id;
	
	// bring user to job edit screen with the new job
	$action="edit";
}


// Badly named. Should be add route. Boiokd a route to the job

if($action=="addarea"){
	if(0) {
		$ERROR="Either region or area or RD not selected!";
		$error=true;
	}
	else{
		// Get the dest type to be stored on the route
		$dest_type = get("job","dest_type","WHERE job_id='$job_id'");
		
		// The whole next section creates the query which selects the routes from the database
		if(!$job_id||!$type){
			$ERROR="Not all data set!";
		}
		else{
			// Add bundles to the job
			if($type=="bundles"){
				$route_id=$bund_route;
				
				if(!$route_id) {
					$contr_name = get("operator","company","WHERE operator_id='$bund_contr'");
					$ERROR="ERROR: Could not find route for Contractor: $contr_name.";
					$action ="";
					$error=true;
				}
				$qry = "SELECT * FROM route 
						WHERE route_id = '$route_id'";
			}
			else{
				$qry = "SELECT * FROM route 
						WHERE route_id IS NOT NULL ";
				$start=true;
				$count=0;
				// Check for the seledcted routes. If the list is '0' then all routes have been selected
				$code_list = $_POST['code'];
				if($code_list[0]=='0'){
					$island_list =  $_POST['island'];				
					$start=true;	
					foreach($island_list as $island){
						if($start){
							$qry.=" AND (island='$island'";
							$start=false;
						}
						else
							$qry.=" OR island='$island'";							
					}
					$qry.=")";
					$region_list =  $_POST['region'];
					$start=true;	
					if($region_list[0]!='0'){
						foreach($region_list as $region){
							if($start){
								$qry.=" AND (region='$region'";
								$start=false;
							}
							else
								$qry.=" OR region='$region'";							
						}
						$qry.=")";
					}
					$area_list = $_POST['area'];
					$start=true;
					if($area_list[0]!='0'){
						foreach($area_list as $area){
							if($start){
								$qry.=" AND (area='$area'";
								$start=false;
							}
							else
								$qry.=" OR area='$area'";							
						}
						$qry.=")";
					}					
				}
				// Routes explicit selected
				else{
					
					foreach($code_list as $route){
						// Omit adding duplicate routes
						$tester = get("job_route","route_id","WHERE route_id='$route' AND job_id='$job_id'");
						if(!$tester){
							if($start){
								$qry.=" AND (route_id='$route'";
								$start=false;
								$count++;
							}
							else{
								$qry.=" OR route_id='$route'";
								$count++;
							}
						}
						else{
							$ERROR="Omitted Duplicate Routes!";
						}
					}
					if(!$start)
						$qry .= ")";
					if($count==0) $error=1;
				}
			}
		}
		
		if(!$error){
			// Omit using hidden routes. Shoudl have been prevented in the selcetin process anyway.
			$qry .=  " AND route.is_hidden='N' ";
			
			$routes = query($qry);
			while($route=mysql_fetch_object($routes)){
					$boxes=explode(',',$route->region);
					$box=$boxes[0];
					
					if($route->region=="MAILINGS"||$box=="BAGS")
						$external='Y';
					else
						$external='N';
					
					// If num_total then sum farmers and lifestyle
					if($type=="num_total"){
						$amount = get("route","(num_farmers+num_lifestyle)","WHERE route_id='$route->route_id'");
					}
					// If bundels take the amount from response
					else if($type=="bundles"){
						$amount=$amount;
					}
					// Otherwise select amount from route
					else{
						$amount = get("route",$type,"WHERE route_id='$route->route_id'");
					}
					
					// Get the route affiliation
					$delivery_date = get("job","delivery_date","WHERE job_id='$job_id'");
					
					$qry = "SELECT * 
							FROM route_aff 
							WHERE route_id='$route->route_id'
								AND '$delivery_date'>=app_date
								AND '$delivery_date'<=stop_date
							ORDER BY app_date DESC LIMIT 1";
					$res_b = query($qry);
					$ids = mysql_fetch_object($res_b);
					$error_id=false;
					if(!$ids) $error_id=true;
					//$dropoff_id = get("route","dropoff_id","WHERE route_id='$route->route_id'");
					$dropoff_id=$ids->dropoff_id;
					if(!$dropoff_id) $dropoff_id = 0;
					// Book the route
					if(!$amount) $amount=0;
					//echo "Hello:".$route->route_id." ".$amount."<br>";
					$qry = "INSERT INTO job_route(job_id,
												route_id,
												dist_id,
												subdist_id,
												contractor_id,
												dropoff_id,
												alt_dropoff_id,
												dest_type,
												amount,
												orig_amt,
												version,
												bundle_price,
											    external)
							VALUES(	'$job_id',
									'$route->route_id',
									'$ids->dist_id',
									'$ids->subdist_id',
									'$ids->contractor_id',
									'$ids->dropoff_id',
									'0',
									'$type',
									'$amount',
									'$amount',
									'$version',
									'$bundle_price',
									'$external')";
					//echo nl2br($qry);
					if($include_zeros && !$error_id){
						query($qry);
					}
					else if($amount>0 && !$error_id){
						query($qry);
					}
					else if($error_id){
						$ERROR.="Attempted to add routes with affiliation error ($route->code).<br />";	
					}
					else if($amount==0){
						//$ERROR.="Attempted to add routes with 0 numbers.<br />";						
					}
					
					// Repeat the whole thing for attached job
					$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
					//echo "Hello".$alt_job_id." ".$job_id;
					if($alt_job_id){
						if($type=="bundles"){
							$amount=$amount;
							$alt_type=$type;
						}
						else{
							$alt_type = get("job","dest_type","WHERE job_id='$alt_job_id'");
							if($alt_type=="num_total"){
								$amount = get("route","(num_farmers+num_lifestyle)","WHERE route_id='$route->route_id'");
								
							}
							else{
								$amount = get("route",$alt_type,"WHERE route_id='$route->route_id'");
							}
						}
						$dropoff_id = get("route","dropoff_id","WHERE route_id='$route->route_id'");
						if(!$dropoff_id) $dropoff_id = 0;
						
						if(!$amount)$amount=0;
						//echo "Hello:".$route->route_id." ".$amount."<br>";
						$qry = "INSERT INTO job_route(job_id,
													route_id,
													dist_id,
													subdist_id,
													contractor_id,
													dropoff_id,
													dest_type,
													amount,
													orig_amt,
													version,
													alt_dropoff_id,
													bundle_price,
													external)
								VALUES(	'$alt_job_id',
										'$route->route_id',
										'$ids->dist_id,
										'$ids->subdist_id,
										'$ids->contractor_id',
										''$ids->dropoff_id',
										'$alt_type',
										'$amount',
										'$amount',
										'$version',
										'$dropoff_id',
										'0',
										'$bundle_price',
										'$external')";
						//echo nl2br($qry);
						if($amount>0)
							query($qry);						
					}
			}
		}
	}
	$action="";
	if(!$error)
		$MESSAGE = "Routes added.";
}

// The user can alter the numbers. They can be redced over the whole table or rounded.
if($action=="alter_job"){
	// You can specify a total number. The system will then attempt to reduce the individual numbers per route to match that number.
	if($sub_reduce=="Reduce" && $sub_round!="Round"){
		if(!$reduce_amt||$reduce_amt==0){
			$ERROR="The reduction amount seems to be zero.";
			$action=$dest;
		}
		else{
			$total = get_sum("job_route","amount","WHERE job_id='$job_id' AND dest_type<>'bundles'","GROUP BY job_id");
			$perc = $reduce_amt/$total;
			$qry = "UPDATE job_route SET amount=amount*$perc WHERE job_id='$job_id' AND dest_type<>'bundles'";
			query($qry);
			$action=$dest;
			$perc*=100;
			$perc = 100-round($perc);
			$MESSAGE="Route Numbers reduced by $perc%";
		}
	}
	// Roud to the 10s (e.g. 145 > 150)
	if($sub_round=="Round" && $sub_reduce!="Reduce"){
		$qry = "UPDATE job_route SET amount=round(amount,-1) WHERE job_id='$job_id' AND dest_type<>'bundles'";
		query($qry);
		$action=$dest;
		$MESSAGE="Route Numbers rounded.";
	}	
}


?>
