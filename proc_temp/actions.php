
<?

function delete_alt_job($job_id){
	$qry = "DELETE FROM job_temp WHERE job_id='$job_id'";
	query($qry);
	$qry = "DELETE FROM job_route_temp WHERE job_id='$job_id'";
	query($qry);
	$qry = "UPDATE job_temp SET alt_job_id=null WHERE job_id=$job_id;";	
	query($qry);
}

function update_route_aff_temp($job_id){
	$qry = "select * FROM job_route_temp 
			LEFT JOIN job_temp 
			ON job_temp.job_id=job_route_temp.job_id 
			WHERE job_temp.job_id='$job_id'";
	$res = query($qry);
	while($route = mysql_fetch_object($res)){
		$date=$route->delivery_date;
		
		$qry = "SELECT * 
				FROM route_aff 
				WHERE route_id='$route->route_id'
					AND '$date' >= app_date
					AND '$date' < stop_date
				ORDER BY app_date DESC LIMIT 1";
		$res_b = query($qry);
		$ids = mysql_fetch_object($res_b);
		
		$qry = "UPDATE job_route_temp 
				SET dist_id='$ids->dist_id',
					subdist_id='$ids->subdist_id',
					contractor_id='$ids->contractor_id',
					dropoff_id='$ids->dropoff_id',
					doff = IF(alt_dropoff_id>0,alt_dropoff_id,'$ids->dropoff_id')
				WHERE job_route_id='$route->job_route_id'";
		query($qry);
	}
}

if($action=="update_aff"){
	update_route_aff_temp($job_id);
	$MESSAGE = "Route affiliations updated.";
	$action="edit";
}

if($action=="detach_job"){
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
	delete_alt_job($alt_job_id);
	$qry = "UPDATE job_temp SET alt_job_id=null WHERE job_id=$job_id;";	
	query($qry);	
	$action="";
	$MESSAGE="Job detached";
}



if($action=="do_attach_job"){
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	//echo "Hello".$alt_job_id." ".$job_id;
	if($alt_job_id){
		delete_alt_job($alt_job_id);
	}
	$job_no = get("job","job_no","WHERE job_id='$job_id'");
	$job_no_att = 'L';
	$job_no_main = "L-Main";
	//$job_no = create_job_no();
	$qry = "INSERT INTO job_temp(client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						job_no_add,
						foreign_job_no,
						hauler,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
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
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						is_quote,
						is_att)
						
						
						SELECT client_id,
							publication,
							'0',
							purchase_no,
							'$job_no',
							'$job_no_att',
							foreign_job_no,
							hauler,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
							rate_bbc,
							dist_rate,
							subdist_rate,
							contr_rate,
							freight_charge,
							lbc_charge,
							lbc_charge_bbc,
							qty_bbc,
							'$alt_dest_type',
							comments,
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							IF(is_quote='Y','Y','N'),
							invoice_qty,
							is_regular,
							is_quote,
							'Y'
						 FROM job WHERE job_id=$job_id";
	query($qry);
	$new_job_id = mysql_insert_id();
	
	$qry = "UPDATE job_temp SET alt_job_id = '$new_job_id',job_no_add='$job_no_main' WHERE job_id='$job_id'";
	query($qry);
	$type = $alt_dest_type;

	if($type=="num_total") $sel_field = "route.num_lifestyle+route.num_farmers";
	else $sel_field = $type;
	
	$qry = "INSERT INTO job_route_temp(job_id,
								  route_id,
								  dest_type,
								  amount,
								  orig_amt,
								  version,
								  alt_dropoff_id,
								  bundle_price,
								  external,
								  is_edited)
			SELECT '$new_job_id',
					job_route_temp.route_id,
					IF(
						job_route_temp.dest_type='bundles','bundles','$type'
					),
					IF(
						job_route_temp.dest_type='bundles',job_route.amount,
						IF(
							job_route_temp.amount<>job_route.orig_amt,job_route.amount,$sel_field
						)
					),
					IF(
						job_route_temp.dest_type='bundles',job_route.orig_amt,$sel_field
					),
					version,
				 	alt_dropoff_id,
					bundle_price,
				    job_route.external,
					job_route.is_edited
			FROM job_route_temp
			LEFT JOIN route
			ON route.route_id=job_route_temp.route_id
			WHERE job_id=$job_id";
	query($qry);	
	$MESSAGE = "Attached job created with updated numbers (edited numbers retained)";
	//$job_id=$new_job_id;
	$action="edit";
}




//////////////////////////////////////////////////////////
// SECTION INVOICE										//
//////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
// ACTION SAVE_INVOICE                                 	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.invoice / coural.job /coural.client	//
//////////////////////////////////////////////////////////

if($action=="save_job" || $action=="add_job"){

	$error=false;
	
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
	if($cancel=="Cancel"){
		$action="";
	}
	else if(!$error){
		$today=date("Y-m-d");
		$comments = addslashes($comments);
	
		if($action=="add_job"){
			$job_no = create_job_no();
			$sql = "INSERT INTO job_temp(
						client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						job_no_add,
						foreign_job_no,
						hauler,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
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
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular)
					VALUES(
						'$client_id',
						'$publication',
						'$invoice_no',
						'$purchase_no',
						'$job_no',
						'',
						'$foreign_job_no',
						'$hauler',
						'$delivery_date',
						'$is_ioa',
						'$today',
						'$weight',
						'$rate',
						'$rate_bbc',
						'$dist_rate',
						'$subdist_rate',
						'$contr_rate',
						'$freight_charge',
						'$lbc_charge',
						'$lbc_charge_bbc',
						'$qty_bbc',
						'$dest_type',
						'$comments',
						'N',
						'N',
						'N',
						'$invoice_qty',
						'$regular')";		
			query($sql);	
			$job_id=mysql_insert_id();
			$action="";
		}
		else{			
			// Has alternate job
			$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
			// Is alternate job_temp
			$alt_job_id_self = get("job_temp","alt_job_id","WHERE alt_job_id='$job_id'");
			if($alt_job_id_self){
				$sql = "UPDATE job_temp SET
						change_date			='$today',
						qty_bbc				='$qty_bbc',
						invoice_qty			='$invoice_qty'
					WHERE job_id='$job_id'";
				query($sql);			
			}
			else{
				$sql = "UPDATE job_temp SET
							client_id			='$client_id',
							publication			='$publication',
							invoice_no			='$invoice_no',
							purchase_no			='$purchase_no',
							foreign_job_no		='$foreign_job_no',
							hauler				='$hauler',
							delivery_date		='$delivery_date',
							is_ioa				='$is_ioa',
							change_date			='$today',
							weight				='$weight',
							rate				='$rate',
							rate_bbc			='$rate_bbc',
							dist_rate			='$dist_rate',
							subdist_rate		='$subdist_rate',
							contr_rate			='$contr_rate',
							freight_charge		='$freight_charge',
							lbc_charge			='$lbc_charge',
							lbc_charge_bbc		='$lbc_charge_bbc',
							qty_bbc				='$qty_bbc',
							comments			='$comments',
							invoice_qty			='$invoice_qty',
							is_regular			='$regular'
						WHERE job_id='$job_id'";
				query($sql);
			}
			if($alt_job_id){
				$sql = "UPDATE job_temp SET
							client_id			='$client_id',
							publication			='$publication',
							invoice_no			='$invoice_no',
							purchase_no			='$purchase_no',
							foreign_job_no		='$foreign_job_no',
							hauler				='$hauler',
							delivery_date		='$delivery_date',
							is_ioa				='$is_ioa',
							change_date			='$today',
							weight				='$weight',
							rate				='$rate',
							rate_bbc			='$rate_bbc',
							dist_rate			='$dist_rate',
							subdist_rate		='$subdist_rate',
							contr_rate			='$contr_rate',
							freight_charge		='$freight_charge',
							lbc_charge			='$lbc_charge',
							lbc_charge_bbc		='$lbc_charge_bbc',
							comments			='$comments',
							is_regular			='$regular'
						WHERE job_id='$alt_job_id'";				
				query($sql);	
			}			
		}

		$action="";
		$MESSAGE = "Job details successfully created/changed.";

	}
	else
		$action=$dest;
}


//////////////////////////////////////////////////////////
// SECTION JOB											//
//////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.job / coural.job_route_temp				//
//////////////////////////////////////////////////////////

if($action=="delete"){
	query("DELETE FROM job_route_temp WHERE job_route_id=$record");
	$action="";
	$ERROR = "Route successfully deleted from job.";
}


if($action=="save_line"){
	if(!$cancel=="Cancel"){
		$qry = "UPDATE job_route_temp 
				SET amount=$amount,
					version='$version',
					bundle_price='$bundle_price',
					comments='$notes',
					is_edited='Y' 
				WHERE job_route_id='$job_route_id'";
		query($qry);
		$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
		
		if($alt_job_id){
			$route_id = get("job_route_temp","route_id","WHERE job_route_id='$job_route_id'");
			$qry = "UPDATE job_route_temp SET amount='$amount2',bundle_price='$bundle_price' 
					WHERE route_id='$route_id' AND job_id='$alt_job_id'";
			query($qry);
		}
		$MESSAGE = "Route changed.";
	}
	else{
		$MESSAGE = "Action cancelled";
	}
	$action="";
}


//////////////////////////////////////////////////////////
// ACTION DELETE LINES                                 	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.job_temp / coural.job_route_temp				//
//////////////////////////////////////////////////////////

if($action=="action_from_table"){
	if($delete_lines=="Delete lines"){
		$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
		
		$qry = "SELECT * FROM job_route_temp WHERE job_id=$job_id";
		$res_routes = query($qry);
		while($line = mysql_fetch_object($res_routes)){
			if($check[$line->job_route_id]=="on"){
				//echo "Hello".$alt_job_id." ".$job_id;
				if($alt_job_id){
					$route_id = get("job_route_temp","route_id","WHERE job_route_id = '$line->job_route_id'");
					$qry = "DELETE FROM job_route_temp WHERE route_id='$route_id' AND job_id='$alt_job_id'";
					query($qry);
				}					
				$qry = "DELETE FROM job_route_temp WHERE job_route_id=$line->job_route_id";
				query($qry);		
			}
		}
		$action="";
		$ERROR = "Routes removed.";
	}
	else if($set_alternate_do=="Set Alt DO"){
		$qry = "SELECT * FROM job_route_temp WHERE job_id=$job_id";
		$res_routes = query($qry);
		while($line = mysql_fetch_object($res_routes)){
		if($check[$line->job_route_id]=="on"){
			$qry = "UPDATE job_route_temp 
					SET alt_dropoff_id = '$alt_dropoff_id'
					WHERE job_route_id=$line->job_route_id";
			query($qry);
		}
	}
	$action="";
	$MESSAGE = "Alternate Drop Offs set.";	
	}
}


if($action=="update"){
	$qry = "SELECT job_route_id,route_id,dest_type FROM job_route_temp WHERE job_id=$job_id";
	$res = query($qry);
	while($row = mysql_fetch_object($res)){
		if($row->dest_type!="bundles"){
			if($row->dest_type=="num_total"){
				$tgt = "num_lifestyle+num_farmers";
			}
			else
				$tgt = $row->dest_type;
			$new_num = get("route",$tgt,"WHERE route_id = $row->route_id");
			$qry = "UPDATE job_route_temp SET amount=$new_num,orig_amt=$new_num WHERE job_route_id=$row->job_route_id";
			query($qry);
		}
	}
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
	if($alt_job_id){
		$qry = "SELECT job_route_id,route_id,dest_type FROM job_route_temp WHERE job_id=$alt_job_id";
		$res = query($qry);
		while($row = mysql_fetch_object($res)){
			if($row->dest_type!="bundles"){
				if($row->dest_type=="num_total"){
					$tgt = "num_lifestyle+num_farmers";
				}
				else
					$tgt = $row->dest_type;
				$new_num = get("route",$tgt,"WHERE route_id = $row->route_id");
				$qry = "UPDATE job_route_temp SET amount=$new_num,orig_amt=$new_num WHERE job_route_id=$row->job_route_id";
				query($qry);
			}
		}	
	}
	$action="edit";
}

if($action=="save_as_new"){
	if(!$job_no){
		$job_no = create_job_no();
	}
	$tab_job="job";
	$tab_job_route = "job_route";
	
	$from_job= "job_temp";
	$from_job_route= "job_route_temp";

	if($new_date)
		$date_str = "'$new_date',";
	else 
		$date_str ="delivery_date,";
		
	$today = date("Y-m-d");
	$qry = "INSERT INTO $tab_job(client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						job_no_add,
						foreign_job_no,
						hauler,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
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
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						alt_job_id)
						
						
						SELECT client_id,
							publication,
							invoice_no,
							purchase_no,
							'$job_no',
							job_no_add,
							foreign_job_no,
							hauler,
							$date_str
							is_ioa,
							'$today',
							weight,
							rate,
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
							'N',
							'N',
							'N',
							invoice_qty,
							is_regular,
							alt_job_id
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
								  external)
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
				    external
			FROM $from_job_route
			WHERE job_id=$job_id";
	query($qry);	
	
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
		
	if($alt_job_id){
		//$job_no_alt = get("job_temp","job_no","WHERE job_id='$alt_job_id'");
		$job_no_alt = $job_no;
		$job_no_orig = $job_no;
		$qry = "INSERT INTO $tab_job(client_id,
							publication,
							invoice_no,
							purchase_no,
							job_no,
							job_no_add,
							foreign_job_no,
							hauler,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
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
							confirmed,
							finished,
							cancelled,
							invoice_qty,
							is_regular)
							
							
							SELECT client_id,
								publication,
								invoice_no,
								purchase_no,
								'$job_no_alt',
								job_no_add,
								foreign_job_no,
								hauler,
								$date_str
								is_ioa,
								'$today',
								weight,
								rate,
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
								'N',
								'N',
								'N',
								invoice_qty,
								is_regular
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
	}
	
	$MESSAGE = "New job created";
	?>
		<a href="proc_job.php?job_id=<?=$new_job_id?>&action=edit">Go to Job Booking Screen.</a>
	<?
}


if($action=="load_temp"){
	if(!$job_id) $job_id=$record;
	$job_no = create_job_no();
	if($new_date)
		$date_str = "'$new_date'";
	else 
		$date_str ="delivery_date";
		
	$today = date("Y-m-d");
	
	$qry = "INSERT INTO job_temp(client_id,
						publication,
						invoice_no,
						purchase_no,
						job_no,
						foreign_job_no,
						hauler,
						delivery_date,
						is_ioa,
						invoice_date,
						weight,
						rate,
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
						confirmed,
						finished,
						cancelled,
						invoice_qty,
						is_regular,
						alt_job_id)
						
						
						SELECT client_id,
							publication,
							invoice_no,
							purchase_no,
							'$job_no',
							foreign_job_no,
							hauler,
							$date_str,
							is_ioa,
							'$today',
							weight,
							rate,
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
							'N',
							'N',
							'N',
							invoice_qty,
							is_regular,
							alt_job_id
						 FROM job_temp WHERE job_id=$job_id";
	query($qry);
	$new_job_id = mysql_insert_id();

	$qry = "INSERT INTO job_route_temp(job_id,
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
				    external
			FROM job_route_temp
			WHERE job_id=$job_id";
	query($qry);	
	
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
		
	if($alt_job_id){
		$job_no.='L';
		$orig_job_no=$job_no."-Main";
		$qry = "INSERT INTO job_temp(client_id,
							publication,
							invoice_no,
							purchase_no,
							job_no,
							foreign_job_no,
							hauler,
							delivery_date,
							is_ioa,
							invoice_date,
							weight,
							rate,
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
							confirmed,
							finished,
							cancelled,
							invoice_qty,
							is_regular)
							
							
							SELECT client_id,
								publication,
								invoice_no,
								purchase_no,
								'$job_no',
								foreign_job_no,
								hauler,
								$date_str,
								is_ioa,
								'$today',
								weight,
								rate,
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
								'N',
								'N',
								'N',
								invoice_qty,
								is_regular
							 FROM job_temp WHERE job_id=$alt_job_id";
		query($qry);
		$new_job_id2 = mysql_insert_id();
		$qry = "INSERT INTO job_route_temp(job_id,
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
				FROM job_route_temp
				WHERE job_id=$alt_job_id";
		query($qry);			
		$qry = "UPDATE job_temp SET alt_job_id='$new_job_id2',job_no = '$orig_job_no' WHERE job_id='$new_job_id'";
		query($qry);			
	}
	
	$MESSAGE = "New job loaded from template";
	$job_id=$new_job_id;	
	
	$action="edit";
}




//////////////////////////////////////////////////////////
// ACTION addarea                                      	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.job_temp coural.job_route_temp					//
//////////////////////////////////////////////////////////

if($action=="addarea"){
	if(0) {
		$ERROR="Either region or area or RD not selected!";
		$error=true;
	}
	else{

		$dest_type = get("job_temp","dest_type","WHERE job_id='$job_id'");
		
		
		
		if(!$job_id||!$type){
			$ERROR="Not all data set!";
		}
		else{
			if($type=="bundles"){
				$route_id=$bund_route;
				//$route_id=get("route","route_id","WHERE contractor_id='$bund_contr'");
				if(!$route_id) {
					$contr_name = get("operator","company","WHERE operator_id='$bund_contr'");
					$ERROR="ERROR: Could not find route for Contractor: $contr_name.";
					$action ="";
					$error=true;
				}
				$qry = "SELECT route_id,region FROM route 
						WHERE route_id = '$route_id'";
			}
			else{
				$qry = "SELECT route_id,region FROM route 
						WHERE route_id IS NOT NULL ";
				$start=true;
				$count=0;
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
				else{
					
					foreach($code_list as $route){
						$tester = get("job_route_temp","route_id","WHERE route_id='$route' AND job_id='$job_id'");
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
			//echo nl2br($qry);
			$routes = query($qry);
			while($route=mysql_fetch_object($routes)){
					$boxes=explode(',',$route->region);
					$box=$boxes[0];
					
					if($route->region=="MAILINGS"||$box=="BAGS")
						$external='Y';
					else
						$external='N';

					if($type=="num_total"){
						$amount = get("route","(num_farmers+num_lifestyle)","WHERE route_id='$route->route_id'");
						
					}
					else if($type=="bundles"){
						$amount=$amount;
					}
					else{
						$amount = get("route",$type,"WHERE route_id='$route->route_id'");
					}
					
					$delivery_date = get("job_temp","delivery_date","WHERE job_id='$job_id'");
					
					$qry = "SELECT * 
							FROM route_aff 
							WHERE route_id='$route->route_id'
								AND app_date<='$delivery_date' 
								AND stop_date>'$delivery_date' 
							ORDER BY app_date DESC LIMIT 1";
					$res_b = query($qry);
					$ids = mysql_fetch_object($res_b);
					
					//$dropoff_id = get("route","dropoff_id","WHERE route_id='$route->route_id'");
					$dropoff_id=$ids->dropoff_id;
					if(!$dropoff_id) $dropoff_id = 0;
					
					if(!$amount) $amount=0;
					//echo "Hello:".$route->route_id." ".$amount."<br>";
					$qry = "INSERT INTO job_route_temp(job_id,
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
					if($amount>0){
						query($qry);
					}
					else{
						$ERROR="Attempted to add routes with 0 numbers.";						
					}
					$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
					//echo "Hello".$alt_job_id." ".$job_id;
					if($alt_job_id){
						if($type=="bundles"){
							$amount=$amount;
							$alt_type=$type;
						}
						else{
							$alt_type = get("job_temp","dest_type","WHERE job_id='$alt_job_id'");
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
						$qry = "INSERT INTO job_route_temp(job_id,
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


if($action=="alter_job"){
	if($sub_reduce=="Reduce" && $sub_round!="Round"){
		if(!$reduce_amt||$reduce_amt==0){
			$ERROR="The reduction amount seems to be zero.";
			$action=$dest;
		}
		else{
			$total = get_sum("job_route_temp","amount","WHERE job_id='$job_id' AND dest_type<>'bundles'","GROUP BY job_id");
			$perc = $reduce_amt/$total;
			$qry = "UPDATE job_route_temp SET amount=amount*$perc WHERE job_id='$job_id' AND dest_type<>'bundles'";
			query($qry);
			$action=$dest;
			$perc*=100;
			$perc = 100-round($perc);
			$MESSAGE="Route Numbers reduced by $perc%";
		}
	}
	if($sub_round=="Round" && $sub_reduce!="Reduce"){
		$qry = "UPDATE job_route_temp SET amount=round(amount,-1) WHERE job_id='$job_id' AND dest_type<>'bundles'";
		query($qry);
		$action=$dest;
		$MESSAGE="Route Numbers rounded.";
	}	
}


?>