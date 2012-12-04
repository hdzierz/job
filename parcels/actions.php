<?
// This function is an emergency function for a bug I could not fix before I left to GER. The system stores the rates onm the redemption line
// Every now and then those rates are zero. This function will change those to the current rate. 
// This function is invoked by a button on the parcel front screen.
if($action=="update_pjr"){
	$now = date("Y-m-d");
	$qry = "SELECT * FROM parcel_rates WHERE '$now' BETWEEN start_date AND end_date AND type='CD'";
	$res = query($qry);
	$cd = mysql_fetch_object($res);
	
	$qry = "SELECT * FROM parcel_rates WHERE '$now' BETWEEN start_date AND end_date AND type='CP'";
	$res = query($qry);
	$cp = mysql_fetch_object($res);

	$qry = "SELECT * FROM parcel_rates WHERE '$now' BETWEEN start_date AND end_date AND type='SR'";
	$res = query($qry);
	$sr = mysql_fetch_object($res);
	
	$qry = "SELECT * FROM parcel_rates WHERE '$now' BETWEEN start_date AND end_date AND type='RP'";
	$res = query($qry);
	$rp = mysql_fetch_object($res);


	$qry = "UPDATE parcel_job_route SET red_rate_pickup=$cd->red_rate_pickup,red_rate_deliv=$cd->red_rate_deliv,distr_payment_deliv=$cd->distr_payment_deliv,distr_payment_pickup=$cd->distr_payment_pickup WHERE type='CD' AND red_rate_pickup=0;";
	query($qry);		
	
	$qry = "UPDATE parcel_job_route SET red_rate_pickup=$cp->red_rate_pickup,red_rate_deliv=$cp->red_rate_deliv,distr_payment_deliv=$cp->distr_payment_deliv,distr_payment_pickup=$cp->distr_payment_pickup WHERE type='CP' AND red_rate_pickup=0;";
	query($qry);	
	
	$qry = "UPDATE parcel_job_route SET red_rate_pickup=$sr->red_rate_pickup,red_rate_deliv=$sr->red_rate_deliv,distr_payment_deliv=$sr->distr_payment_deliv,distr_payment_pickup=$sr->distr_payment_pickup WHERE type='SR' AND red_rate_pickup=0;";
	query($qry);	
	
	$qry = "UPDATE parcel_job_route SET red_rate_pickup=$rp->red_rate_pickup,red_rate_deliv=$rp->red_rate_deliv,distr_payment_deliv=$rp->distr_payment_deliv,distr_payment_pickup=$rp->distr_payment_pickup WHERE type='RP' AND red_rate_pickup=0;";
	query($qry);	
	
	
	$action="";	
}

// Delete a ticket note.
if($target=="ticket_notes"){
	if($action=="delete"){
		$qry = "DELETE FROM parcel_ticket_note WHERE parcel_ticket_note_id='$record'";
			query($qry);		
		$action="show_ticket_notes";
		$target="show_ticket_notes";
	}
	
}
// INSERT UPDATE action for ticket notes
if($action=="ticket_notes" ){
	switch($submit){
		case "Add":
			if(!$final_ticket) $final_ticket=$start_ticket;
			$note = addslashes($note);
			$qry = "INSERT INTO parcel_ticket_note SET start='$start_ticket',end='$final_ticket',note='$note'";
			query($qry);
		case "Update":
			if(!$final_ticket) $final_ticket=$start_ticket;
			$note = addslashes($note);
			$qry = "UPDATE parcel_ticket_note SET start='$start_ticket',end='$final_ticket',note='$note' WHERE parcel_ticket_note_id='$parcel_ticket_note_id'";
			query($qry);
		break;
	}
	$action="show_ticket_notes";
}

//INSERT UPDATE action for receiving tickets
if($action=="receive_tickets"){

	switch($submit){
		case "Receive":
			
		
			$parcel_th_receipt_id = get("parcel_th_receipt","parcel_th_receipt_id","WHERE date = '$date'");
			
			if($parcel_th_receipt_id){
				$qry_command = "UPDATE ";
				$qry_where = "WHERE parcel_th_receipt_id='$parcel_th_receipt_id'";
			}
			else{
				$qry_command = "INSERT INTO ";
				$qry_where = "";
			}
			
			
			$qry = "$qry_command parcel_th_receipt 
					SET supplier='".addslashes($supplier)."',
						branch_id='$branch_id',
						date='$date'
					$qry_where";
			query($qry);

			if(!$parcel_th_receipt_id) $parcel_th_receipt_id = mysql_insert_id();
			
			$qry = "DELETE FROM parcel_ticket_th WHERE parcel_th_receipt_id='$parcel_th_receipt_id'";
			query($qry);
			
			
			
			foreach($start as $s){
				list(, $e) = each($end);
				list(, $q) = each($qty);
				list(, $t) = each($type);
				# Checking whether ticket range intersects with already received ones
				$parcel_th_receipt_id1 = get("parcel_ticket_th","parcel_th_receipt_id","WHERE type='$t' AND '$s' BETWEEN start AND end");
				$parcel_th_receipt_id2 = get("parcel_ticket_th","parcel_th_receipt_id","WHERE type='$t' AND '$e' BETWEEN start AND end");
				
				if($parcel_th_receipt_id1 || $parcel_th_receipt_id2){
					$ERROR .= "Range ($s to $e) contains tickets already received. <br />";
					$error=true;
				}
				else{
					$qry = "INSERT INTO parcel_ticket_th
							SET  parcel_th_receipt_id='$parcel_th_receipt_id',
								type = '$t',
								start = '$s',
								end = '$e',
								qty = '$q'
								";
					if($s && $e && $q && $t){
						query($qry);
					}
					
				}
					
			}// foreach
			
			if(!$error) $MESSAGE = "Tickets for $date received.<br />";
			unset($start);
			unset($end);
			unset($qty);
			unset($type);
			$parcel_th_receipt_id++;
			
		break;
		case "Close":
			$action=false;
			$ERROR = "Action cancelled.";
		break;
	}
}

// INSERT UPDATE action for parcel rates
if($action=="manage_rates"){
	
	switch($submit){
		case "Save":
			process_dates($start_date);
			$qry = "UPDATE parcel_rates
					SET red_rate_pickup = '$red_rate_pickup_red',
						red_rate_deliv = '$red_rate_deliv_red',
						distr_payment_pickup = '$distr_payment_pickup_red',
						distr_payment_deliv = '$distr_payment_deliv_red',
						sell_rate_std = '$sell_rate_std_red',
						sell_rate_disc = '$sell_rate_disc_red',
						qty_per_book = '$qty_per_book_red'
					WHERE type='CD' AND start_date='$start_date'";
			query($qry);
			$qry = "UPDATE parcel_rates
					SET red_rate_pickup = '$red_rate_pickup_green',
						red_rate_deliv = '$red_rate_deliv_green',
						distr_payment_pickup = '$distr_payment_pickup_green',
						distr_payment_deliv = '$distr_payment_deliv_green',
						sell_rate_std = '$sell_rate_std_green',
						sell_rate_disc = '$sell_rate_disc_green',
						qty_per_book = '$qty_per_book_green'
					WHERE type='CP' AND start_date='$start_date'";
			query($qry);
			$qry = "UPDATE parcel_rates
					SET red_rate_pickup = '$red_rate_pickup_yellow',
						red_rate_deliv = '$red_rate_deliv_yellow',
						distr_payment_pickup = '$distr_payment_pickup_yellow',
						distr_payment_deliv = '$distr_payment_deliv_yellow',
						sell_rate_std = '$sell_rate_std_yellow',
						sell_rate_disc = '$sell_rate_disc_yellow',
						qty_per_book = '$qty_per_book_yellow'
					WHERE type='SR' AND start_date='$start_date'";
			query($qry);
			$qry = "UPDATE parcel_rates
					SET red_rate_pickup = '$red_rate_pickup_purple',
						red_rate_deliv = '$red_rate_deliv_purple',
						distr_payment_pickup = '$distr_payment_pickup_purple',
						distr_payment_deliv = '$distr_payment_deliv_purple',
						sell_rate_std = '$sell_rate_std_purple',
						sell_rate_disc = '$sell_rate_disc_purple',
						qty_per_book = '$qty_per_book_purple'
					WHERE type='RP' AND start_date='$start_date'";
			query($qry);
			$MESSAGE = "Rates saved and will be used for the next bookings.";
		break;
		case "Close":
			$action=false;
			$ERROR = "Action cancelled.";
		break;
		default:
			//$action=false;
		break;
	}
}

// Create a new booking id for receiving tickets
function create_new_book_id(){
	$max_id = get("parcel_th","MAX(book_id)","");
	return $max_id++;
}

// If searched
if($action=="show_tickets"){
	$job_id = get("parcel_job","job_id","WHERE job_no='$job_no'");
	if(!$job_id && $job_no)
		$MESSAGE = "Please specify valid delivery number.";
}

// 
if($action=="add_order_books"){
	switch($submit){
		case "Change":
		case "Continue":
			if($client_op_id) $client_id = $client_op_id;
			$comments = addslashes($comments);

			//$client_id = get("client","client_id","WHERE name='$client'");
			$job_no = get("parcel_job","job_no","WHERE job_id='$job_id'");
			if(!$job_no)
				$job_no = create_job_no();
			if($job_id){
				$qry_pre_job = "UPDATE parcel_job";
				$qry_where = "WHERE job_id='$job_id'";			
				$qry_pre_job_rate = "UPDATE parcel_job_rate";
				$qry_where_job_rate = "WHERE job_id='$job_id' AND type = '%s'\n";
			}
			else{
				$qry_pre_job = "INSERT INTO parcel_job\n";
				$qry_pre_job_rate = "INSERT INTO parcel_job_rate\n";
			}
			
			$order_datetime = $order_date." ".$time.":00";
			if(!$has_discount) $has_discount=0;
			$discount = get("client","discount","WHERE client_id='$client_id'");
			if(!$discount) $discount=0;
			$discount = 1-$discount;
			$gst = $GST_PARCEL*100;
			$qry = "$qry_pre_job
					SET order_date='$order_datetime',
						comments = '$comments',
						job_no = '$job_no',
						branch_id = '$branch_id',
						order_no = '$order_no',
						instructions = '$instructions',
						client_id = '$client_id',
						ordered_by = '$ordered_by',
						foreign_order_no = '$foreign_order_no',
						branch_id1 = '$branch_id1'+0,
						branch_id2 = '$branch_id2'+0,
						branch_id3 = '$branch_id3'+0,
						gst='$gst'\n
						$qry_where";
			//echo nl2br($qry);die();
			query($qry);
			
			if(!$job_id) $job_id = mysql_insert_id();
			
			foreach($quantity as $type=>$q){
				list(, $r) = each($rate);
				
				$red_rate_pickup = get("parcel_rates","red_rate_pickup","WHERE type = '$type' AND '$order_date' BETWEEN start_date AND end_date");
				$red_rate_deliv = get("parcel_rates","red_rate_deliv","WHERE type = '$type' AND '$order_date' BETWEEN start_date AND end_date");
				$distr_payment_deliv = get("parcel_rates","distr_payment_deliv","WHERE type = '$type' AND '$order_date' BETWEEN start_date AND end_date");
				$distr_payment_pickup = get("parcel_rates","distr_payment_pickup","WHERE type = '$type' AND '$order_date' BETWEEN start_date AND end_date");
				$sell_rate_std = get("parcel_rates","sell_rate_std","WHERE type = '$type' AND '$order_date' BETWEEN start_date AND end_date");
				$sell_rate_disc = get("parcel_rates","sell_rate_disc","WHERE type = '$type' AND '$order_date' BETWEEN start_date AND end_date");
				
				if($qry_where_job_rate) $qry_where_job_rate_use = sprintf($qry_where_job_rate,$type);
				if($has_discount) $sell_rate = $sell_rate_disc;
				else $sell_rate = $sell_rate_std;
				
				$sellrate *= $discount;
				
				$qry = "$qry_pre_job_rate
						SET job_id 			= '$job_id',
							type 			= '$type',
							rate 			= '$r'+0,
							red_rate_pickup = '$red_rate_pickup'+0,
							red_rate_deliv 	= '$red_rate_deliv'+0,
							distr_payment_deliv	='$distr_payment_deliv'+0,
							distr_payment_pickup	='$distr_payment_pickup'+0,
							sell_rate		='$sell_rate'+0,
							qty = '$q'+0
						$qry_where_job_rate_use";
				//echo nl2br($qry);die();
				query($qry);
			}
			
	
	
			$action="sell_tickets";
		break;
		case "Close":
			$ERROR = "Action cancelled.";
			$action=false;
		break;
		default:
			//$action=false;
		break;
	}
}


//
if($action=="sell_tickets"){
	switch($submit){
		case "Change":
		case "Sell":
			if(is_array($start) && is_array($end)){
				if($submit=="Change"){
					//$qry = "DELETE FROM parcel_job_route WHERE job_id='$job_id' AND is_redeemed_P<>1 AND is_redeemed_D<>1";
					$qry = "DELETE FROM parcel_job_ticket WHERE job_id='$job_id'";
					query($qry);
				}
				foreach($type as $t){
					list(, $te) = each($end);
					list(, $ts) = each($start);
					if(!$te || !$ts) continue;
					# Checking whether ticket range intersects with already received ones
					$parcel_th_receipt_id1 = get("parcel_ticket_th","parcel_th_receipt_id","WHERE type='$t' AND '$ts' BETWEEN start AND end");
					$parcel_th_receipt_id2 = get("parcel_ticket_th","parcel_th_receipt_id","WHERE type='$t' AND '$te' BETWEEN start AND end");
					
					if(!$parcel_th_receipt_id1 || !$parcel_th_receipt_id2){
						$ERROR .= "Ticket Range contains tickets not yet received.<br />";
						continue;
					}
					
					$parcel_job_ticket_id1 = get("parcel_job_ticket","parcel_job_ticket_id","WHERE type='$t' AND '$ts' BETWEEN start AND end");
					$parcel_job_ticket_id2 = get("parcel_job_ticket","parcel_job_ticket_id","WHERE type='$t' AND '$te' BETWEEN start AND end");
					
					if($parcel_job_ticket_id1 || $parcel_job_ticket_id2){
						$ERROR .= "Ticket Range contains tickets altready been booked.<br />";
						continue;
					}
					
					if($te && $ts){
						$qry = "INSERT INTO parcel_job_ticket 
									SET job_id='$job_id',
										type='$t',
										start='$ts',
										end='$te'";
						query($qry);
						/*for($i=$ts;$i<=$te;$i++){
							
							$qry = "INSERT INTO parcel_job_route 
									SET job_id='$job_id',
										type = '$type',
										ticket_no='".$i."'";
							query($qry);
						}//for($i=$ts;$i<=$te){*/
					}
				}// foreach($start as $ts){
				$action=false;
			} // if is array

		break;
		case "Close":
			$action=false;
		break;
	}//switch submit
}


// This is the ticket redemption. At the end a INSERT action for parcel_job_route
if($action=="redeem"){
	
	switch($submit){
		case "Redeem":
			if(!$contractor) break;
			$tickets = $_POST["tickets"];
			
			// get the contractor ID from teh search string
			$date = $date_year."-".$date_month."-15";
			
			// Parse the contractor string
			$buffer = explode("[",$contractor);

			$buffer_route = explode("--",$buffer[0]);
			$route = trim($buffer_route[1]); 
			
			$buffer = explode("]",$buffer[1]);
			$contractor_id=$buffer[0];
			$where_add = "AND operator.operator_id='$contractor_id'";
			
			// Getting the route ID. 
			$route_id = get("route","route_id","WHERE code='$route'");

			
			// This month is needed to obtain the parcel_rum_id. If not existent this is a new job
			$this_month = date("Y-m",strtotime($date));
			
			$parcel_run_id = get("parcel_run","parcel_run_id","WHERE run='$run' AND dist_id='$dist_id' AND date LIKE '$this_month%'");
			
			if(!$parcel_run_id){
			
				$qry = "INSERT INTO parcel_run SET 	date='$date',
									real_date = now(),
									contractor_id='$contractor_id',
									route_id='$route_id',
									run='$run',
									dist_id='$dist_id',
									user_id='$CK_USERID',
									actual=0,
									exp_no_tickets = '$exp_no_tickets'+0";
				query($qry);
				$parcel_run_id  = mysql_insert_id();
			}
			
			// Clear the old numbers
			//echo "WHERE user_id='$CK_USERID' AND run='$run' AND dist_id='$dist_id' AND date LIKE '$this_month%'";
			$qry = "DELETE FROM parcel_job_route WHERE parcel_run_id = '$parcel_run_id'";
			query($qry);
			
			// Obtaining the route of teh contractor. A contractor may have more than one route, but as we do not exactly know what route
			// the parcel was delivered on we can neglect that.
			//$routes = get_routes_from_contr($contractor_id);
			//$route_id = $routes[0];
			
				
			
			// Creating the ticket run entries
			$ticket_count=0;
			$red_ticket_count=0;
			if(is_array($tickets)){
				foreach($tickets as $t){
					$ticket = new ticket($t);
					
					if($t && !$ticket->isRedeemed()){
						$ticket_count++;
						$year = date("Y",strtotime($date));
						$month = date("m",strtotime($date));
						// Redeem the ticket
						$ticket->redeem($contractor_id,$route_id,$year,$month,$parcel_run_id);
					}// if $t
					else{
						$red_ticket_count++;
					}
					
				}
				
				$red_ticket_count--;
				$qry = "UPDATE parcel_run SET actual=0,exp_no_tickets = '$exp_no_tickets', red_ticket_count = '$red_ticket_count'+0 WHERE parcel_run_id = '$parcel_run_id'";
				query($qry);
				
				if($red_ticket_count>0) $ERROR .= "You attempted to redeem tickets which had been redeemed already.<br />";
				$MESSAGE = "Tickets for contractor <strong>'$contractor $name'</strong> redeemed ($ticket_count tickets from $exp_no_tickets expected).<br />";
			} // if is array
		break;
	}
}

if($action=="process_xerox_scan"){
	switch($submit){
		case "Redeem":
			$files = $_POST['file'];
			$filecs = $_POST['filec'];
			
			foreach($files as $key=>$file){
				if($filec[$key]){
					
					$scan_run = new xeroxFileReader("Canonscan",$file);
					
					$ticketl = $scan_run->getTickets();
					
					$run = new run();
					$date = $year.'-'.$month.'-15';
					foreach($ticketl as $tickets){
						if(!$tickets["dist_id"]){
							$route = get("route","code","WHERE route_id=".$tickets["route_id"]);
							$ERROR.="Could not load distibutor for route $route<br />";
							continue;
						}
						$run_id = $run->writeRunWithDist($tickets["dist_id"],$tickets["contr_id"],$tickets["route_id"],$date);
						
						if(is_array($tickets["tickets"]) && $run_id){
							
							foreach($tickets["tickets"] as $ticket){
								$ticket->redeemWithDist($tickets["dist_id"],$tickets["contr_id"],$tickets["route_id"],$year,$month,$run_id);
							}
						}
					}
					$MESSAGE.= "File $file processed.<br />";
				}
				
			}
			
		case "dummy":	
			if(is_array($check)){
				
				$date = $year."-".sprintf("%02d",$month)."-15";
				$run = new run();
				$in = implode(',',array_keys($check));
				$qry = "SELECT * FROM parcel_run_pre WHERE parcel_run_pre_id IN($in)";
				$res_runs = query($qry,0);
				while($r = mysql_fetch_object($res_runs)){
					if($r->page==0)
						$parcel_run_id = $run->writeRun($r->contractor_id,$r->route_id,$date);
					else
						$parcel_run_id = $r->pacel_run_id;
						
					$page = get("parcel_run","run","WHERE parcel_run_id=$parcel_run_id");
					
					$qry = "SELECT * FROM parcel_job_route_pre WHERE parcel_run_pre_id = '$r->parcel_run_pre_id'";
					$res_tickets = query($qry,0);
					while($t = mysql_fetch_object($res_tickets)){
						if(trim(strlen($t->ticket_no))>6){
							$ticket = new ticket($t->ticket_no);
							$ticket->redeem($r->contractor_id,$r->route_id,$year,$month,$parcel_run_id);
						}
					}
					$qry = "UPDATE parcel_run_pre SET parcel_run_id=$parcel_run_id, page=".$page.", is_processed=1 WHERE parcel_run_pre_id = '$r->parcel_run_pre_id'";
					query($qry);
					
					$MESSAGE = "Redemption process finished.";
				}
			}
		break;
		
	}
}

if($action=="gst" && $gst){
	if($gst<1){
		$ERROR = "GST seems not to be in % ($gst).";
	}
	else{
		$gst_one = $gst/100;
		$qry = "UPDATE config SET value='$gst_one' WHERE name='GST_PARCEL'";
		query($qry);
		$qry = "ALTER TABLE parcel_job ALTER gst SET DEFAULT $gst";	
		query($qry);
		$GST_PARCEL = $gst_one;
		$MESSAGE = "GST for parcels changed. Check circular gst too.";
	}
	
}

?>