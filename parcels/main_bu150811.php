<script language="javascript" src="parcels/javascrips/aid_functions.js"></script>
<?

// Alan's MySQL get set function. I have not used a class in teh Coural system, so I might have used this function somewhere
function sql_get_set($query_string, $index = false, $single_row = false)
{ 
	$result = query($query_string);	//execute query using internal method
	if ($result === false) return false;	//query failed, return false 

	$return_array = array();
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0)
	{
		if ($single_row == false || mysql_num_fields($result) > 1) //more than one field was requested, return each row as an array
		{
			if ($index)	//make into associative array based on specified field index
			{
				while(($row = mysql_fetch_assoc($result)) !== false)
				{
					if (!isset($row[$index]))
					{
						die("get_set(): Specified index, '$index', was not found in results.");
						return false;
					}
					
					$return_array[$row[$index]] = $row;
				}
			}
			else	//make into numerically indexed array
			{
				while(($row = mysql_fetch_assoc($result)) !== false) {
					
					$return_array[] = $row;
				}
			}
		}
		else //return row as a simple value (cannot be indexed)
		{
			for ($cnt = 0; $cnt < $num_rows; $cnt++)
			{
				$return_array[] = mysql_result($result, $cnt);
			}
		}
		reset($return_array);
	}
	return $return_array;
}

	//print_r(array_keys($result));	
	

if($target=="list_runs"){
	$qry = "SELECT * FROM parcel_run";
}
	
// Coural can add notes to tickets and recall them. TYhe notes will also appear during the ticket scanning process.
if($target=="ticket_notes"){
	$qry = "SELECT * 
			FROM parcel_ticket_note
			WHERE parcel_ticket_note_id='$record'";
	$res = query($qry,0);
	$tnote = mysql_fetch_object($res);
	
?>
	<form name="manage_rates" action="parcels.php?action=<?=$target?>" method="post">
		<table>
			<tr>
				<td>Start Ticket:</td>
				<td><input type="text" name="start_ticket" value="<?=$tnote->start?> " /></td>
				<td>Final Ticket:</td>
				<td><input type="text" name="final_ticket" value="<?=$tnote->end?> " /></td>
				<td>Note:</td>
				<td><input type="text" name="note" value="<?=$tnote->note?> " /></td>
				<td colspan="4" align="center">
					<?
					if($action=="edit"){
					?>
						<input type="submit" name="submit" value="Update" />
					<?
					}
					else{
					?>
						<input type="submit" name="submit" value="Add" />
					<?
					}
					?>
					
				</td>
			</tr>
		</table>
		<input type="hidden" name="parcel_ticket_note_id" value="<?=$record?>" />
	</form>
<?	
}

// Shows the ticket notes
if($action=="show_ticket_notes"){
	if(!trim($final_ticket)) $final_ticket=$start_ticket;
	$qry = "SELECT * 
			FROM parcel_ticket_note
			WHERE start BETWEEN '$start_ticket' AND '$final_ticket'
				OR end BETWEEN '$start_ticket' AND '$final_ticket'";
	$tab = new MySQLTable("parcels.php",$qry);
	$tab->showRec=true;
	$tab->hasAddButton=true;
	$tab->hasDeleteButton=true;
	$tab->hasEditButton=true;
	
	$tab->onClickEditButtonAdd = "&target=ticket_notes";
	$tab->onClickDeleteButtonAdd = "&target=ticket_notes";
	$tab->onClickAddButtonAdd = "&target=ticket_notes";
	//$tab->hasEditButton=false;
	//$tab->hasSubmitButton=true;
	//$tab->submitButtonValue="Save Notes";
	//$tab->editableField = "Note";
	$tab->startTable();
		$tab->writeTable();
		//$tab->addHiddenInput("action",$action);
	$tab->stopTable();
}

// The system includes a search functiopn
if($action=="search_tickets"){
	switch($submit){
		case "Search":
			if(!trim($final_ticket)) $final_ticket=$start_ticket;
?>
			<h3>Ticket Search results from ticket_no <?=$start_ticket?> to <?=$final_ticket?></h3>
<?		
			
			$qry = "SELECT client.name AS Courier,
						   parcel_job.job_no AS 'Delivery #',
						   date(order_date) AS 'Date Sold',
						   ticket_no	AS Ticket,
						   IF(is_redeemed_P=1,'P','D') AS Type,
						   parcel_job_route.type AS 'P/Type',
						   #distributor.company AS Distributor,
						   date(parcel_run.real_date) AS 'Scan Date',
						   date_format(parcel_run.date, '%b %Y') AS 'Redm Mth',
						   parcel_run.run AS Page,
						   contractor.company AS Contractor,
						   code AS Route,
						   note AS Note,
						   IF(parcel_job.is_random=1,'ran',
						   	IF(is_odd=1,'old','normal')
							) AS Info
						   
					FROM parcel_job
					LEFT JOIN client
					ON client.client_id=parcel_job.client_id
					LEFT JOIN parcel_job_route
					ON parcel_job.job_id=parcel_job_route.job_id
					LEFT JOIN parcel_run
					ON parcel_run.parcel_run_id=parcel_job_route.parcel_run_id
					LEFT JOIN route
					ON route.route_id=parcel_job_route.route_id
					LEFT JOIN operator AS distributor
					ON distributor.operator_id = parcel_job_route.dist_id
					LEFT JOIN operator AS contractor
					ON contractor.operator_id = parcel_job_route.contractor_id
					LEFT JOIN parcel_ticket_note
					ON ticket_no BETWEEN start AND end
					WHERE ticket_no BETWEEN '$start_ticket' AND '$final_ticket'
					
					UNION 
					
					SELECT client.name AS Courier,
						   parcel_job.job_no AS 'Delivery #',
						   date(order_date) AS 'Date Sold',
						   CONCAT(start,'-',end)	AS Ticket,
						   '' AS Type,
						  parcel_job_ticket.type AS 'P/Type',
						   '' AS 'Scan Date',
						   '' AS 'Redm Mth',
						   '' AS Page,
						   '' AS Contractor,
						   '' AS Route,
						   '' AS Note,
						   IF(parcel_job.is_random=1,'ran',
						   	IF(is_odd=1,'old','normal')
							) AS Info
						   
					FROM parcel_job
					LEFT JOIN client
					ON client.client_id=parcel_job.client_id
					LEFT JOIN parcel_job_ticket
					ON parcel_job.job_id=parcel_job_ticket.job_id
					WHERE '$start_ticket' BETWEEN start AND end
						OR '$final_ticket' BETWEEN start AND end
					
					";
			$tab = new MySQLTable("parcels.php",$qry);
			$tab->showRec=true;
			$tab->hasAddButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasEditButton=false;
			//$tab->hasEditButton=false;
			//$tab->hasSubmitButton=true;
			//$tab->submitButtonValue="Save Notes";
			//$tab->editableField = "Note";
			$tab->startTable();
				$tab->writeTable();
				//$tab->addHiddenInput("action",$action);
			$tab->stopTable();
		break;
	}
}

// The user has to receive the tickets first before  the user can redeem them. It is a ticket threshhold.
if($action=="receive_tickets"){
		if($date_show){
			$date = $date_show;
		}
		if(!$date){
			$date = date("Y-m-d");
		}

		if($date){
			$parcel_th_receipt_id = get("parcel_th_receipt","parcel_th_receipt_id","WHERE date = '$date'");
			$supplier = get("parcel_th_receipt","supplier","WHERE parcel_th_receipt_id = '$parcel_th_receipt_id'");
		}
		
		$qry = "SELECT * FROM parcel_ticket_th WHERE parcel_th_receipt_id='$parcel_th_receipt_id'";
		$res = query($qry);
		
		while($th = mysql_fetch_object($res)){
			$type[] = $th->type;
			$start[] = $th->start;
			$end[] = $th->end;
		}
		
		
		
	?>
		<script language="javascript">
			function calcNumTickets(pos){
				
				var start = document.getElementById('start['+pos+']').value;
				var end = document.getElementById('end['+pos+']').value;
				var qty = document.getElementById('qty['+pos+']').value;
				var target_num = document.getElementById('sum['+pos+']');
				var target_books = document.getElementById('books['+pos+']');
				
				if(start){
					target_num.value = parseInt(end)-parseInt(start)+1;
					target_books.value =target_num.value / qty;
				}
				else if(start==""){
					target_num.value = "";
					target_qty.value = "";
				}
			}
			
			function Right(str, n)
			/***
					IN: str - the string we are RIGHTing
						n - the number of characters we want to return
	
					RETVAL: n characters from the right side of the string
			***/
			{
					if (n <= 0)     // Invalid bound, return blank string
					   return "";
					else if (n > String(str).length)   // Invalid bound, return
					   return str;                     // entire string
					else { // Valid bound, return appropriate substring
					   var iLen = String(str).length;
					   return String(str).substring(iLen, iLen - n);
					}
			}

			function checkStartEndValues(start,end,qty){
				if(start!="" && end!="" && qty!=""){
					if((end-start+1)%qty!=0){
						return false;
					}
				}
				return true;
			}
			
			function validate(){
				for(var i=0;i<3;i++){
					var start = document.getElementById("start["+i+"]").value;
					var end = document.getElementById("end["+i+"]").value;
					var qty = document.getElementById("qty["+i+"]").value;
					if(!checkStartEndValues(start,end,qty)){
						alert("Start and end values and tickets per book inconsistent.");
						return false;
					}
					if(start!=""){
						if(Right(start, 1)!='1'){
							alert("Start value in row "+(i+1)+" does not end wih '1'");
							return false;
						}
					}
				}
				return true;
			}
		</script>
		<form action="parcels.php?action=receive_tickets" method="post" onKeyPress="return no_enter(this,event);">
			<fieldset style="width:80%"><legend>Numbers</legend>
			<table>
				<tr>
					<td>Receipt Number:</td>
					<td><?=$parcel_th_receipt_id?></td>
				</tr>
				<tr>
					<td>Branch:</td>
					<td>
					<?
						if(!$branch_id) $branch_id=1;
						$sel = new MySQLSelect("name","branch_id","branch","parcels.php?action=$action","form","branch_id");
						$sel->setOptionIsVal($branch_id);
						$sel->startSelect();
							$sel->writeSelect();
						$sel->stopSelect();
						
					?>
				</td>
				</tr>
				<tr>
					<td>Supplier:</td>
					<td><input type="text" name="supplier" id="supplier" value="<?=$supplier?>" /></td>
				</tr>
				<tr>
					<td>Date:</td>
					<td>
						<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>Type</td>
					<td>Ticket Start#</td>
					<td>Ticket Final#</td>
					<td>Tickets per Book</td>
					<td>Num Books</td>
					<td>Num Tickets</td>
				</tr>
				<?
				for($i=0;$i<3;$i++){
					if(!$qty[$i]) $qty[$i]=20;
				?>
					<tr>
						<td>Line: <?=$i+1?></td>
						<td>
							
							<?
								
								$sel = new Select("type[$i]");
								if($i==0) $sel->setOptionIsVal("CD");
								if($i==1) $sel->setOptionIsVal("CP");
								if($i==2) $sel->setOptionIsVal("SR");
								if($i==3) $sel->setOptionIsVal("RP");
								$sel->isReadOnly=true;
								$sel->start();
									$sel->addOption("CD","Documents");
									$sel->addOption("CP","Parcels");
									$sel->addOption("SR","Signature");
									$sel->addOption("RP","NEW ONE");
								$sel->stop();
								
							?>
						</td>
						<td><input type="text" name="start[<?=$i?>]" id="start[<?=$i?>]" value="<?=$start[$i]?>" onKeyUp="calcNumTickets(<?=$i?>)" /></td>
						<td><input type="text" name="end[<?=$i?>]" id="end[<?=$i?>]" value="<?=$end[$i]?>" onKeyUp="calcNumTickets(<?=$i?>)" /></td>
						<td><input style="width:5em; " type="text" name="qty[<?=$i?>]" id="qty[<?=$i?>]" value="<?=$qty[$i]?>" onKeyUp="calcNumTickets(<?=$i?>)" /></td>
						<td><input disabled style="width:5em; " type="text" name="books[<?=$i?>]" id="books[<?=$i?>]" value="<?=($end[$i]-$start[$i]+1)/$qty[$i]?>" onKeyUp="calcNumTickets(<?=$i?>)" /></td>
						<td><input disabled style="width:5em; " type="text" name="sum[<?=$i?>]" id="sum[<?=$i?>]" value="<?= ($end[$i]-$start[$i]+1) ?>" /></td>
					</tr>
				<?
				}
				?>
				<tr>
					<td></td>
					<td>
						<input type="submit" name="submit" value="Receive" onClick="return validate();" />
						<input type="submit" name="submit" value="Close" />
					</td>
				</tr>
			</table>
			</fieldset>
		</form>
	<?	
}

// After the tickets have been received the user can sell those tickets. The result will be a threshhold of tickets per type whic
// the user then can redeem.
if($action=="sell_tickets"){
		$qry = "SELECT * FROM parcel_job WHERE job_id='$job_id'";
		$res = query($qry);
		
		$job = mysql_fetch_object($res);
		$branch_id = $job->branch_id;
		$job_no = $job->job_no;
		$order_no = $job->order_no;
		$order_date = $job->order_date;
		$client_id = $job->client_id;
		$ordered_by = $job->ordered_by;
		$foreign_order_no = $job->foreign_order_no;
		
		$instructions = $job->instructions;
		$comments = $job->comments;
		
		$client = get("client","name","WHERE client_id='$client_id'");
		
		$qry = "SELECT * FROM parcel_job_ticket WHERE job_id='$job_id'";
		$res = query($qry);
		$tot_qty=0;		
		$exp_qty[] = array();
		while($lines = mysql_fetch_object($res)){
			if($lines->end>0){
				$tot_qty+=($lines->end-$lines->start+1);
				$diff[]=$lines->end-$lines->start+1;
			}
			else $diff[]=0;
				
			$types[]=$lines->type;
			$start[]=$lines->start;
			$end[]=$lines->end;
			
			$qry = "SELECT qty*20 AS exp_qty FROM parcel_job_rate WHERE job_id='$job_id' AND type='$lines->type'";
			$res_exp = query($qry,0);
			$lines_exp = mysql_fetch_object($res_exp);
			$exp_qty[] = $lines_exp->exp_qty;
		}
		
		// A book of tickets consists of 20 tickets.
		if(is_array($quantity)){
			$exp_qty[0] = $quantity["CD"]*20;
			$exp_qty[1] = $quantity["CP"]*20;
			$exp_qty[2] = $quantity["SR"]*20;
			$exp_qty[3] = $quantity["RP"]*20;
		}
		
		
		$qry = "SELECT SUM(qty)*20 AS qty FROM parcel_job_rate WHERE job_id='$job_id'";
		$res = query($qry);
		$lines = mysql_fetch_object($res);
		$tot_book_qty = $lines->qty;
		
	?>
		<script language="javascript">
			function getFinalNumber(field,pos,qty,evt){
				var value = field.value;
				var target = document.getElementById('end['+pos+']');
				
				if(value){
					target.value = parseInt(value)+parseInt(qty);
				}
				else if(value==""){
					target.value = "";
				}
			}
			
			function calcNumTickets(pos){
				var start = document.getElementById('start['+pos+']').value;
				var end = document.getElementById('end['+pos+']').value;
				var qty = document.getElementById('qty['+pos+']');
				
				if(start!=''){
					qty.value = parseInt(end)-parseInt(start)+1;
				}
				else{
					qty.value = 0;
				}
				
				var qty_tot = document.getElementById('tot_qty');
				var qty_tot_val = 0;
				for(var i=0;i<6;i++){
					var v = document.getElementById('qty['+i+']').value;
					if(v=='') v=0;
					else v=parseInt(document.getElementById('qty['+i+']').value);
					qty_tot_val += v;
				}
				qty_tot.value = qty_tot_val;
			}
			
			function validate_sell(){
				var qty_tot = parseInt(document.getElementById('tot_qty').value);
				var qty_book_tot = parseInt(document.getElementById('tot_book_qty').value);
				if((qty_tot-qty_book_tot)!=0){
					alert("Booking numbers inconsistent");
					return false;
				}
				return true;
			}
		</script>
		<form action="parcels.php?action=sell_tickets" method="post" onKeyPress="return no_enter(this,event);">
			<fieldset style="width:80%"><legend>PRE PAID TICKETS</legend>
			<table class="form">
				<tr>
					<td>Delivery #</td>
					<td><?=$job_no?></td>
				</tr>
				<tr>
					<td>Date</td>
					<td><?=$order_date?></td>
				</tr>
				<tr>
					<td>Customer</td>
					<td><?=$client?></td>
				</tr>
				<tr>
					<td>Cust./Ref.</td>
					<td><?=$foreign_order_no?></td>
				</tr>
				<tr>
					<td>Ordered By</td>
					<td><?=$ordered_by?></td>
				</tr>
				<tr>
					<td>Date</td>
					<td><?=$order_date?></td>
				</tr>
			</table>
			</fieldset>
			<fieldset style="width:80%"><legend>Numbers</legend>
			<table>
				<tr>
					<td></td>
					<td>Type</td>
					<td>Ticket Start#</td>
					<td>Ticket Final#</td>
					<td>Tot Qty</td>
				</tr>
				<?
				for($i=0;$i<6;$i++){
					//if(!$qty[$i]) $qty[$i]=20;
				?>
					<tr>
						<td>Line: <?=$i+1?></td>
						<td>
							
							<?
								
								$sel = new Select("type[$i]");
								if(!$types[$i]){
									if($i==0) $sel->setOptionIsVal("CD");
									if($i==1) $sel->setOptionIsVal("CP");
									if($i==2) $sel->setOptionIsVal("SR");
									if($i==3) $sel->setOptionIsVal("RP");
								}
								else{
									$sel->setOptionIsVal($types[$i]);
								}
								$sel->start();
									$sel->addOption("CD","Documents");
									$sel->addOption("CP","Parcels");
									$sel->addOption("SR","Signature");
									$sel->addOption("RP","NEW ONE");
								$sel->stop();
								
							?>
						</td>
						<td><input type="text" name="start[<?=$i?>]" id="start[<?=$i?>]" value="<?=$start[$i]?>" onKeyUp="calcNumTickets(<?=$i?>)" /></td>
						<td><input type="text" name="end[<?=$i?>]" id="end[<?=$i?>]" value="<?=$end[$i]?>" onKeyUp="calcNumTickets(<?=$i?>)" /></td>
						<td><input type="text" disabled name="qty[<?=$i?>]" id="qty[<?=$i?>]"  value="<?=$diff[$i]?>" /></td>
						<td>of: <input type="text" disabled name="exp_qty[<?=$i?>]" id="exp_qty[<?=$i?>]"  value="<?=$exp_qty[$i]?>" /></td>
					</tr>
				<?
				}
				?>
				<tr>
					<td colspan="4"></td>
					<td><input type="text" disabled name="tot_qty" id="tot_qty"  value="<?=$tot_qty?>" /></td>
					<td>of: <input disabled type="text" disabled name="tot_book_qty" id="tot_book_qty"  value="<?=$tot_book_qty?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" name="submit" value="Sell" onClick="return validate_sell()" />
						<input type="submit" name="submit" value="Close" />
					</td>
				</tr>
			</table>
			</fieldset>
			<input type="hidden" name="job_id" value="<?=$job_id?>" />
		</form>
	<?	
}

// Before tickets can be sold the user has to allocate a client
if($action=="choose_client"){
?>
	<form action="parcels.php?action=add_order_books" method="post">
		<fieldset style="width:80%">
		<legend>ORDER FORM</legend>
		<table class="form">
			<tr>
				<td>Choose Client:</td>
				<td>
					<?
						$sel_name = new MySQLSelect("name","client_id","client","parcel.php","client_id","client_id");	
						$sel_name->selectWidth=15;
						$sel_name->addSQLWhere("is_parcel_courier",1);
						$sel_name->setOptionIsVal($client_id);
						$sel_name->startSelect();
							$sel_name->writeSelect();					
						$sel_name->stopSelect();
					?>
				</td>

				<td>
					<input type="submit" name="submit" value="Continue to Sell" />
				</td>
			</tr>
		</table>
		</fieldset>
	</form>
<?		
}


// Add order books is teh actual tickets selling process
if($action=="add_order_books" ||$target=="add_order_books" || $action=="show_tickets"){
	
	if($job_no && !$job_id) $job_id = get("parcel_job","job_id","WHERE job_no='$job_no'");
	
	if($record) $job_id=$record;
	
	
	
	$has_discount = get("client","has_discount","WHERE client_id='$client_id'");
	if($has_discount){
		$rate_name = sell_rate_disc;
	}
	else{
		$rate_name = sell_rate_std;
	}
	
	$now = date("Y-m-d");
	
	$rate['CD'] = get("parcel_rates","$rate_name","WHERE type='CD' AND '$now' BETWEEN start_date AND end_date");
	$rate['CP'] = get("parcel_rates","$rate_name","WHERE type='CP' AND '$now' BETWEEN start_date AND end_date");
	$rate['SR'] = get("parcel_rates","$rate_name","WHERE type='SR' AND '$now' BETWEEN start_date AND end_date");

	
	
	$qry = "SELECT * FROM parcel_job WHERE job_id='$job_id'";
	$res = query($qry);
	
	if(mysql_num_rows($res)>0){
		$job = mysql_fetch_object($res);
		$branch_id = $job->branch_id;
		$job_no = $job->job_no;
		
		$order_date = date("Y-m-d",strtotime($job->order_date));
		$client_id = $job->client_id;
		$ordered_by = $job->ordered_by;
		$foreign_order_no = $job->foreign_order_no;
		$branch_id1 = $job->branch_id1;
		$branch_id2 = $job->branch_id2;
		$branch_id3 = $job->branch_id3;
		$has_discount = $job->has_discount;
	
		$instructions = $job->instructions;
		$comments = $job->comments;
	
		
		$qry = "SELECT * FROM parcel_job_rate WHERE job_id='$job_id'";
		$res = query($qry);
		$quantity = array();
		$rate = array();
		$tot = array();
		$grand_tot=0;
		while($r = mysql_fetch_object($res)){
			$quantity[$r->type]=number_format($r->qty,0,'.','');
			$rate[$r->type]=number_format($r->rate,2,'.','');
			$tot[$r->type]=number_format($r->qty*$r->rate,2,'.','');
			$grand_tot+=number_format($r->qty*$r->rate,2,'.','');
		}
	}
	if(!$job_no) $job_no="TBA";
	if(!$order_date) $order_date = date("Y-m-d");
	
	$show=true;
	
	if($action=="show_tickets" && !$job_id){
		$show=false;
	}
	
	if($show){
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<script language="javascript">
		function getTotals(type){
			var rate = document.getElementById('rate['+type+']');
			var qty = document.getElementById('quantity['+type+']');
			
			var tot = rate.value*qty.value;
			
			
			document.getElementById('tot['+type+']').value = tot;
			
			return tot;
		}
		
		function updateTotals(){
			
			var tot_red = getTotals('CD');
			var tot_green = getTotals('CP');
			var tot_yellow = getTotals('SR');
			
			document.getElementById('grand_tot').value = tot_red+tot_green+tot_yellow;
		}
	</script>
	<form action="parcels.php?action=add_order_books" method="post">
		<fieldset style="width:80%">
		<legend>ORDER FORM</legend>
		<table class="form">
			<tr>
				<td>Branch:</td>
				<td>
					<?
						if(!$branch_id) $branch_id=1;
						$sel = new MySQLSelect("name","branch_id","branch","parcels.php?action=$action","form","branch_id");
						$sel->setOptionIsVal($branch_id);
						$sel->startSelect();
							$sel->writeSelect();
						$sel->stopSelect();
						
					?>
				</td>
			</tr>
			<tr>
				<td>Delivery  Number:</td>
				<td><input disabled type="text" name="job_no" id="job_no" value="<?=$job_no?>" /></td>
				<td>Date:</td>
				<td><script language="javascript">DateInput("order_date", true, "YYYY-MM-DD","<?=$order_date?>")</script></td>
			</tr>
			<tr>
				<td>Client:</td>
				<td>
					<?
						$sel_name = new MySQLSelect("name","client_id","client","parcel.php","client_id","client_id_show");	
						$sel_name->selectWidth=15;
						$sel_name->isDisabled=true;
						$sel_name->addSQLWhere("is_parcel_courier",1);
						$sel_name->setOptionIsVal($client_id);
						$sel_name->startSelect();
							$sel_name->writeSelect();					
						$sel_name->stopSelect();
					?>
					<input type="hidden" name="client_id" value="<?=$client_id?>" />
				</td>
				<td>Time:</td>
				<td><input type="text" name="time" id="time" value="<?=date("h:i")?>" /></td>
			</tr>
			<tr>
				<td>Ordered By:</td>
				<td><input type="text" name="ordered_by" id="ordered_by" value="<?=$ordered_by?>" /></td>
				<td>Order #:</td>
				<td><input type="text" name="foreign_order_no" id="foreign_order_no" value="<?=$foreign_order_no?>" /></td>
			</tr>
			<tr>
				<td>Deliver to:</td>
				<td>
					<?
					$sel_name = new MySQLSelect("address","client_branch_id","client_branch","parcel.php","client_branch_id","branch_id1");	
						$sel_name->selectWidth=15;
						
						$sel_name->setOptionIsVal($branch_id1);
						$sel_name->addSQLWhere("client_id",$client_id);
						$sel_name->startSelect();
							$sel_name->writeSelect();					
						$sel_name->stopSelect();
					?>
				</td>
			</tr>

		</table>
		</fieldset>
		<fieldset style="width:80%"><legend>Quantities and Costs</legend>
		<table>
			
			<tr>
				<td></td>
				<td>Quantity</td>
				<td>Price per book</td>
				<td>Total</td>
			</tr>
			<tr>
				<td>Document:</td>
				<td><input type="text" name="quantity[CD]" id="quantity[CD]" onKeyUp="updateTotals()" value="<?=$quantity["CD"]?>" /></td>
				<td><input type="text" name="rate[CD]" id="rate[CD]" onKeyUp="updateTotals()" value="<?=$rate["CD"]?>" /></td>
				<td><input readonly type="text" name="tot[CD]" id="tot[CD]" value="<?=$tot["CD"]?>" /></td>
			</tr>
			
			<tr>
				<td>Parcel:</td>
				<td><input type="text" name="quantity[CP]" id="quantity[CP]"onKeyUp="updateTotals()" value="<?=$quantity["CP"]?>" /></td>
				<td><input type="text" name="rate[CP]" id="rate[CP]" onKeyUp="updateTotals()" value="<?=$rate["CP"]?>" /></td>
				<td><input readonly type="text" name="tot[CP]" id="tot[CP]" value="<?=$tot["CP"]?>" /></td>
			</tr>
			
			<tr>
				<td>Signature:</td>
				<td><input type="text" name="quantity[SR]" id="quantity[SR]" onKeyUp="updateTotals()" value="<?=$quantity["SR"]?>" /></td>
				<td><input type="text" name="rate[SR]" id="rate[SR]" onKeyUp="updateTotals()" value="<?=$rate["SR"]?>" /></td>
				<td><input readonly type="text" name="tot[SR]" id="tot[SR]" value="<?=$tot["SR"]?>" /></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>Total</td>
				<td><input readonly  type="text" name="grand_tot" id="grand_tot" value="<?=$grand_tot?>" /></td>
			</tr>
		</table>
		</fieldset>
		<fieldset style="width:80%"><legend>Comments and Instructions</legend>
		<table>
			<tr>
				<td>Special Intructions:</td>
				<td>
					<textarea cols="30" rows="6" name="instructions"><?=$instructions?></textarea>
				</td>
			</tr>
			
			<tr>
				<td></td>
				<td>
					<?
					if($action=="edit"){
					?>
						<input type="submit" name="submit" value="Change" />
					<?
					}
					else{
					?>
						<input type="submit" name="submit" value="Continue" />
					<?
					}
					?>
					<input type="submit" name="submit" value="Close" />
				</td>
			</tr>
		</table>
		</fieldset>
		<input type="hidden" name="job_id" value="<?=$job_id?>" />
	</form>

<?
	}// if show
}




// The rates consist of selling rates as well as redemption rates for the contractors. They may change from time to time.
if($action=="manage_rates"){
	if(!$start_date) $start_date = date("Y-m-d");
	
	$qry = "SELECT * FROM parcel_rates WHERE '$start_date' BETWEEN start_date AND end_date ";
	$res = query($qry);
	while($rate = mysql_fetch_object($res)){
		$type = $rate->type;
		$start_date = $rate->start_date;
		$red_rate_pickup[$type] = $rate->red_rate_pickup;
		$red_rate_deliv[$type] = $rate->red_rate_deliv;
		$distr_payment_pickup[$type] = $rate->distr_payment_pickup;
		$distr_payment_deliv[$type] = $rate->distr_payment_deliv;
		$sell_rate_std[$type] = $rate->sell_rate_std;
		$sell_rate_disc[$type] = $rate->sell_rate_disc;
	}
	
	
	
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form action="parcels.php?action=manage_rates" method="post">
		<fieldset  style="width:90% "> <legend>Date from when the rates are valid</legend>
			<table>
				<tr>
					<td>Start Date:</td>
					<td>
						<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset  style="width:90% "> <legend>Charge Rates</legend>
		<table>
			<tr>
				<td></td>
				<td >Documents</td>
				<td >Parcels</td>
				<td >Signature</td>
			</tr>
			<tr>
				<td>Redemption Rate - Pickup:</td>
				<td><input type="text" name="red_rate_pickup_red" id="red_rate_pickup_red" value="<?=$red_rate_pickup["CD"]?>" /></td>
				<td><input type="text" name="red_rate_pickup_green" id="red_rate_pickup_green" value="<?=$red_rate_pickup["CP"]?>" /></td>
				<td><input type="text" name="red_rate_pickup_yellow" id="red_rate_pickup_yellow" value="<?=$red_rate_pickup["SR"]?>" /></td>
			</tr>
			<tr>
				<td>Redemption Rate - Delivery:</td>
				<td><input type="text" name="red_rate_deliv_red" id="red_rate_deliv_red" value="<?=$red_rate_deliv["CD"]?>" /></td>
				<td><input type="text" name="red_rate_deliv_green" id="red_rate_deliv_green" value="<?=$red_rate_deliv["CP"]?>" /></td>
				<td><input type="text" name="red_rate_deliv_yellow" id="red_rate_deliv_yellow" value="<?=$red_rate_deliv["SR"]?>" /></td>
			</tr>
			<tr>
				<td>Distributor Payment - Pickup:</td>
				<td><input type="text" name="distr_payment_pickup_red" id="distr_payment_pickup_red" value="<?=$distr_payment_pickup["CD"]?>" /></td>
				<td><input type="text" name="distr_payment_pickup_green" id="distr_payment_pickup_green" value="<?=$distr_payment_pickup["CP"]?>" /></td>
				<td><input type="text" name="distr_payment_pickup_yellow" id="distr_payment_pickup_yellow" value="<?=$distr_payment_pickup["SR"]?>" /></td>
			</tr>		
			<tr>
				<td>Distributor Payment - Delivery:</td>
				<td><input type="text" name="distr_payment_deliv_red" id="distr_payment_deliv_red" value="<?=$distr_payment_deliv["CD"]?>" /></td>
				<td><input type="text" name="distr_payment_deliv_green" id="distr_payment_deliv_green" value="<?=$distr_payment_deliv["CP"]?>" /></td>
				<td><input type="text" name="distr_payment_deliv_yellow" id="distr_payment_deliv_yellow" value="<?=$distr_payment_deliv["SR"]?>" /></td>
			</tr>			
			
		</table>
		</fieldset>
	
		<fieldset style="width:90% "> <legend>Sell Rates per Book</legend>
		<table>
			<tr>
				<td></td>
				<td >Documents</td>
				<td >Parcels</td>
				<td >Signature</td>
			</tr>
			<tr>
				<td>Standard:</td>
				<td><input type="text" name="sell_rate_std_red" id="sell_rate_std_red" value="<?=$sell_rate_std["CD"]?>" /></td>
				<td><input type="text" name="sell_rate_std_green" id="sell_rate_std_green" value="<?=$sell_rate_std["CP"]?>" /></td>
				<td><input type="text" name="sell_rate_std_yellow" id="sell_rate_std_yellow" value="<?=$sell_rate_std["SR"]?>" /></td>
			</tr>
			<tr>
				<td>Discount:</td>
				<td><input type="text" name="sell_rate_disc_red" id="sell_rate_disc_red" value="<?=$sell_rate_disc["CD"]?>" /></td>
				<td><input type="text" name="sell_rate_disc_green" id="sell_rate_disc_green" value="<?=$sell_rate_disc["CP"]?>" /></td>
				<td><input type="text" name="sell_rate_disc_yellow" id="sell_rate_disc_yellow" value="<?=$sell_rate_disc["SR"]?>" /></td>
			</tr>
			
		</table>
		</fieldset>
		<input type="submit" name="submit" value="Save" />
		<input type="submit" name="submit" value="Close" />
	</form>
	
<?
}

// The actual redemption process. 
if($action=="redeem" || $action=="show_redeemed"){
?>

<? 

	$qry = "SELECT date FROM parcel_run ORDER BY parcel_run_id DESC LIMIT 1";
	$res = query($qry);
	$d = mysql_fetch_object($res);
	$date = $d->date;
	
	$test_date = date("Y-m",strtotime($date));
	
	$tickets = array();
	
	if($run_inter) $run=$run_inter;

	if($dist_id && $run && $date) {
		if( $action=="show_redeemed"){
			$contractor_id=get("parcel_run","contractor_id","WHERE dist_id='$dist_id' AND run='$run' AND real_date LIKE '$real_date%'");
			$route_id=get("parcel_run","route_id","WHERE dist_id='$dist_id' AND run='$run' AND real_date LIKE '$real_date%'");
			$parcel_run_id=get("parcel_run","parcel_run_id","WHERE dist_id='$dist_id' AND run='$run' AND real_date LIKE '$real_date%'");
			$date = get("parcel_run","date","WHERE dist_id='$dist_id' AND run='$run' AND real_date LIKE '$real_date%'");
			$test_date = date("Y-m",strtotime($date));
		}
		else{
			$contractor_id=get("parcel_run","contractor_id","WHERE dist_id='$dist_id' AND run='$run' AND date LIKE '$test_date%'");
			$route_id=get("parcel_run","route_id","WHERE dist_id='$dist_id' AND run='$run' AND date LIKE '$test_date%'");
			$parcel_run_id=get("parcel_run","parcel_run_id","WHERE dist_id='$dist_id' AND run='$run' AND date LIKE '$test_date%'");
		}
		echo $date;
		$contractor=get("operator","company","WHERE operator_id='$contractor_id'");
		$route=get("route","code","WHERE route_id='$route_id'");
		$contractor.="--".$route." [".$contractor_id."]";

		$dist_name=get("operator","company","WHERE operator_id='$dist_id'");
		
		if($action=="show_redeemed" && $submit=="Show"){
			$qry = "SELECT * FROM parcel_job_route WHERE parcel_run_id='$parcel_run_id' AND (is_redeemed_P=1 OR is_redeemed_D=1) ORDER BY ticket_no;";
			$res = query($qry);
			$count=1;
			$d_count = 0;
			$p_count = 0;
			while($ticket = mysql_fetch_object($res)){
				if($ticket->is_random) $ran='!'; else $ran="";
				if($ticket->is_redeemed_D){
					$d_count++;
					//$tickets[] = $count.". ".$ticket->type.$ran.sprintf("%07d",$ticket->ticket_no).'D';
					$tickets[] = $ticket->type.$ran.sprintf("%07d",$ticket->ticket_no).'D';
				}
				if($ticket->is_redeemed_P){
					$p_count++;
					//$tickets[] = $count.". ".$ticket->type.$ran.sprintf("%07d",$ticket->ticket_no).'P';
					$tickets[] = $ticket->type.$ran.sprintf("%07d",$ticket->ticket_no).'P';
				}
				$count++;
			}
		}
	}
	if(!$count) $exp_count=72;
	else  $exp_count=$count-1;
	
	$date_month = date("m",strtotime($date));
	$date_year = date("Y",strtotime($date));
	
	if($action=="redeem" || ($action=="show_redeemed" && $submit=="Show")){
		if($action=="show_redeemed" && $submit=="Show") $can_redeem=true;
		else $can_redeem=false;

?>	
	<script type="text/javascript" src="javascripts/prototype.js"></script>
	<script type="text/javascript" src="javascripts/effects.js"></script>
	<script type="text/javascript" src="javascripts/controls.js"></script>		
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	
	<script language="javascript">
		function call_autocomp(){
			new Ajax.Autocompleter("contractor_new","hint","parcels/get/get_affiliation.php?action=<?=$action?>");
		}
	</script>
	<form id="redeem_form"  action="parcels.php?action=redeem" method="post" onKeyPress="return no_enter(this,event);">
		
		<fieldset style="width:90% ">
			<legend>General Information</legend>
			<table>
				<!-- This might be an alternative when teh otehr auto suggest for contractors might have to be replaced.
				<tr>
					<td>Contractor:</td>
					<td>
						<input type="text" id="contractor_new" name="contractor_new" />
						<div id="hint" class="hint"></div>
						 <script type="text/javascript">	
							call_autocomp();
						</script>  
					</td>
				</tr>-->
				<tr>
					<!-- This is an autosuggest. You will find the Javascript call for teh auto suggest controller in footer.php-->
					<td>Contractor:</td>
					<td valign="top">
						<input tabindex="1" onFocus="restart_scan(this,true)" style="width:20em; " autocomplete='off' type="text" name="contractor" id="contractor" value="<?=$contractor?>" />
						
					</td>
					
					
					<td>Date: </td>
					<td>
						<?
							$sel = new Select("date_year");
							$sel->setOptionIsVal($date_year);
							$sel->writeYearSelectFT();
							
							$sel = new Select("date_month");
							$sel->setOptionIsVal($date_month);
							$sel->writeMonthSelect();
						?>
						
						<!--<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>-->
					</td>
					<td id="wrap_dist">
						Distributor: <?=$dist_name?>
						<strong> / Page:  <input style="width:3em; " type="text" name="run" id="run_field"  value="<?=$run?>"  /></strong>
						<input style="width:3em; " type="hidden" name="dist_id" value="<?=$dist_id?>" />
					</td>
				</tr>
				<!-- 
				This is an auto suggest. You will find the Javascript call for the auto suggest controller in footer.php
				<tr>
					<td>Name:</td>
					<td><input onkeypress="focus_item(event)" onFocus="restart_scan(this)" style="width:20em; " autocomplete='off' type="text" name="name" id="name" value="<?=$name?>" /></td>
				</tr>-->
			</table>
		</fieldset>
		<fieldset style="width:90% ">
			<legend>Scanning</legend>
			<table border="0">
				<tr>
					<td><input style="width:10em; " readonly="true" type="text" id="scan_message" value="Waiting" /> </td>
					<td colspan="5">No Expected tickets: <input onkeyup="validate_entered_tickets();" style="width:5em " type="text" name="exp_no_tickets" id="exp_no_tickets" value="<?=$exp_count?>" /></td>
				</tr>

				<tr>
					<td width="10">Scan in here <sup>1</sup></td>
					<td width="10">Tickets</td>
					<td width="10"></td>
					<td width="10">Dump and Duplicates</td>
					<td>Deliveries</td>
					<td>Action</td>
				</tr>
				<tr>
					<td valign="top">
						<?
							if($submit=="Show"){
						?>
								<input tabindex="2"  type="text" name="barcode_input" id="barcode_input" onkeypress="get_tickets(this,event);"  />
						<?
								}
							else{
						?>
								<input onFocus="get_dist(event,<?=$CK_USERID?>)"  tabindex="2"  type="text" name="barcode_input" id="barcode_input" onkeypress="get_tickets(this,event);"  />
						<?
							}
						?>
						
					</td>
					<td valign="top">
						<select multiple size="20" style="width:15em; " name="tickets[]" id="tickets">
							<option value=""></option>
							<?
								foreach($tickets as $ticket){
							?>
									<option value="<?=$ticket?>"><?=$ticket?></option>
							<?
								}
							?>
						</select>
					</td>
					<td>
						<input type="button" value=">>" onClick="put_option('tickets','ticket_dump')" /><br />
						<input type="button" value="<<" onClick="put_option('ticket_dump','tickets')"  />
						<!--<input type="button" value="SET" onClick="edit_att('tickets')"  />-->
					</td>
					<td valign="top">
						<select multiple size="20" style="width:15em; " name="ticket_dump" id="ticket_dump">
							<option value=""></option>
						</select>
					</td>
					<td valign="top">
						<input style="width:3em " type="text" id="d_ticket_counter" value="<?=$d_count--?>" /> D<br />
						<input style="width:3em " type="text" id="p_ticket_counter" value="<?=$p_count--?>" /> P<br />
						<input style="width:3em " type="text" id="t_ticket_counter" value="<?=$count-1?>" /> Tot
						<!--<input style="width:3em " type="text" id="u_ticket_counter" value="0" /> unknown<br />-->
						
					</td>
					<td valign="top">
						<input style="width:10em; " type="button" name="clear_button" value="Reset" onClick="restart_scan(this,false)" /><br />
						<input <? if(!$can_redeem){?> disabled="true" <? }?> style="width:10em; " type="submit" id="submit" name="submit" value="Redeem" onClick="sel_all(); return validate();"  /><br />
						<div style="color:red;font-size:0.6em; " id="return_message"></div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan='10'>
						<textarea readonly rows="10" cols="60" id="wrap_redeemed">ALREADY REDEEMED TICKETS (Will be ignored):</textarea>
					</td>
				</tr>
				<tr>
					<td colspan="4"> <sup>1</sup> Type 'ticket type  '!' redemption type' for creating a random numer in to the scanning area (e.g. CD!P) and push enter</td>
				</tr>
			</table>
		</fieldset>
	</form>

<?	
	}// Show
}

// The table parcel_job holds the sold ticket information. The customer expects printes dockets as proof
if($action=="print_docket"){
	$qry = "SELECT * FROM parcel_job WHERE job_id='$job_id'";
	$res = query($qry);
	$job = mysql_fetch_object($res);
	
	$client_name = get("client","name","WHERE client_id='$job->client_id'");
	$invoice_details = nl2br(get("client","invoice_details","WHERE client_id='$job->client_id'"));
	
	$delivery_details = nl2br(get("client_branch","address","WHERE client_branch_id='$job->branch_id1'"));
	
	if(!$delivery_details){
		$delivery_details = $invoice_details;
	}

	
	
	
	$invoice_no=get("parcel_invoice","invoice_no","WHERE date LIKE '$month%' AND dist_id='$dist_id'");
	if(!$invoice_no){
		$invoice_no = "TBA";
	}
?>
	<div style="font-size:10pt ">
	<div style="text-align:left">
		
		<div style="float:right; text-align:right; ">
			<img src="images/logo_large.jpg" height="60" /><br />
			<br />
				<font size="-2">GST no: 24-992-802</font>
			<br />
			<br />
			
			Tax Invoice/Delivery Advice Number: <?=$job->job_no?><br />
			Date: <?=date("d M Y",strtotime($job->order_date))?>
		</div>
		
		<strong>Rural Couriers Society Limited<br />
		P O Box 1233<br />
		Palmerston North, 4410<br /></strong>
		<br />
		P: &nbsp;06&nbsp;357&nbsp;3129<br />
		F: &nbsp;06&nbsp;356&nbsp;6618<br />
		E: &nbsp;coural@coural.co.nz<br />
		W: &nbsp;www.coural.co.nz<br />
	</div>
		
	
	
	<div style="margin-bottom:3em; margin-top:1em;">
		<table style="position: absolute;left: 30em;  "> 
			<tr>
				<td>Deliver to:</td>
			</tr>
			<tr>
				<td valign="top" style="border: solid 3px; width: 20em; height:15ex;">
					<?=$client_name?><br />
					<?=$delivery_details?>
				</td>
			</tr>			
		</table>
		<table >
			<tr>
				<td>Invoice to:</td>
			</tr>
			<tr>
				<td valign="top" style="border: solid 3px; width: 20em; height:15ex;">
					<?=$client_name?><br />
					<?=$invoice_details?>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<p>
			<strong>Order No: <?=$job->foreign_order_no?></strong><br />
			<strong>Ordered by: <?=$job->ordered_by?></strong>
		</p>
	</div>
<?	
	$qry = "SELECT parcel_job_ticket.job_id AS Record,
					qty AS 'Quantity (Books)',
					IF(parcel_job_ticket.type='CD',
						GROUP_CONCAT(DISTINCT CONCAT('Document Ticket Numbers CD',start,' - CD',end) SEPARATOR '<BR />'),
						IF(parcel_job_ticket.type='CP',
							GROUP_CONCAT(DISTINCT CONCAT('Parcel Ticket Numbers CP',start,' - CP',end) SEPARATOR '<BR />'),
							IF(parcel_job_ticket.type='SR',
								GROUP_CONCAT(DISTINCT CONCAT('Signature Ticket Numbers SR',start,' - SR',end) SEPARATOR '<BR />'),
								''
							)
						)
					)
						Description,
					rate AS Price,
					ROUND(rate*qty,2) AS Amount
			FROM parcel_job_rate 
			LEFT JOIN parcel_job_ticket
			ON parcel_job_ticket.job_id=parcel_job_rate.job_id
				AND parcel_job_ticket.type=parcel_job_rate.type
			WHERE parcel_job_rate.job_id='$job_id'
				AND qty>0
			GROUP BY parcel_job_rate.job_id,parcel_job_rate.type";
	$tab = new MySQLTable("parcels.php",$qry);
	
	$tab->cssSQLTable = "sqltable_10";
	
	$tab->showRec=false;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->formatLine = true;
	$tab->collField["Amount"]=true;
	$tab->colWidth["Quantity (Books)"] = "30";
	$tab->colWidth["Description"] = "700";
	
	
	$tab->startTable();
		$tab->writeTable();
		$total = array_sum($tab->collFieldVal["Amount"]);
		$gst = $total*$GST_PARCEL;
		$total_gst = $total + $gst;
		$total = number_format($total,2);
		$gst = number_format($gst,2);
		$total_gst = number_format($total_gst,2);
		$tab->startNewLine();
			$tab->addLineWithStyle("","sql_extra_line_number",1);
			$tab->addLineWithStyle("Total excl. GST","sql_extra_line_number",1);
			$tab->addLineWithStyle("","sql_extra_line_number",1);
			$tab->addLineWithStyle($total,"sql_extra_line_number");
		$tab->stopNewLine();
		
		$tab->startNewLine();
			$tab->addLineWithStyle("","sql_extra_line_number",1);
			$tab->addLineWithStyle("GST","sql_extra_line_number",1);
			$tab->addLineWithStyle("","sql_extra_line_number",1);
			$tab->addLineWithStyle($gst,"sql_extra_line_number");
		$tab->stopNewLine();
		
		$tab->startNewLine();
			$tab->addLineWithStyle("","sql_extra_line_number",1);
			$tab->addLineWithStyle("Total","sql_extra_line_number",1);
			$tab->addLineWithStyle("","sql_extra_line_number",1);
			$tab->addLineWithStyle($total_gst,"sql_extra_line_number");
		$tab->stopNewLine();
		
	$tab->stopTable();
	?>
		<table width="100%">
			<tr>
				<td>Notes</td>
			</tr>
			<tr>
				<td style="border: solid 1px;" > 
					<?=$job->comments?>
				</td>
			</tr>
		</table>
	</div>
	<?
}


// This is the parcel front screen. It lists 'jobs'. The user can select a date range which is by default teh current month.
if(!$action){
	$date_final = date("Y-m-d",strtotime($date_final." +1 day"));
	$qry = "SELECT 	parcel_job.job_id AS Record,
					job_no 		AS 'Delivery #',
					client.name AS Courier,
					client_branch.address AS Branch,
					job_no		AS 'Ticket Book',
					order_date	AS Date,
					
					ROUND(SUM(IF(parcel_job_rate.type='CD',parcel_job_rate.rate,0)),2) AS 'Rate Red',
					ROUND(SUM(IF(parcel_job_rate.type='CP',parcel_job_rate.rate,0)),2) AS 'Rate Green',
					ROUND(SUM(IF(parcel_job_rate.type='SR',parcel_job_rate.rate,0)),2) AS 'Rate Yellow',
					CONCAT('<a href=\'parcels.php?action=print_docket&job_id=',parcel_job.job_id,'\'>print</a>') AS Print
					
					
			FROM parcel_job
			LEFT JOIN parcel_job_rate
			ON parcel_job.job_id=parcel_job_rate.job_id
			LEFT JOIN client
			ON client.client_id=parcel_job.client_id
			LEFT JOIN client_branch
			ON client_branch.client_branch_id=parcel_job.branch_id1
			LEFT JOIN branch
			ON branch.branch_id = parcel_job.branch_id
			WHERE order_date BETWEEN '$date_start' AND '$date_final' 
				AND finished='N'
				AND is_odd<>1 
				AND is_random<>1
			GROUP BY parcel_job.job_id";
	//echo nl2br($qry);
	$tab = new MySQLTable("parcels.php",$qry);
	$tab->showRec=false;
	$tab->hasAddButton=false;
	$tab->hasDeleteButton=false;
	
	$tab->onClickEditButtonAdd = "&target=add_order_books";
	
	$tab->startTable();
		$tab->writeTable();
	$tab->stopTable();

}

// Shows the ticket notes
if($action=="process_xerox_scan_ticket_control"){
?>
	<a href="parcels.php?action=process_xerox_scan&filter=Show&dist_id=<?=$dist_id?>&date=<?=$date?>&is_processed=<?=$is_processed?>">Back</a>
<?
		$qry = "SELECT 	ticket_pre_id 	AS Record,
						dist.company 	AS Distributor,
						contr.company 	AS Contractor,
						route.code		AS Route,
						ticket_no 		AS Ticket,
						is_random 		AS Random
						
						
				FROM parcel_job_route_pre
				LEFT JOIN parcel_run_pre
					ON parcel_run_pre.parcel_run_pre_id = parcel_job_route_pre.parcel_run_pre_id
				LEFT JOIN operator contr
					ON contr.operator_id=parcel_run_pre.contractor_id
				LEFT JOIN route_aff
					ON route_aff.env_contractor_id = parcel_run_pre.contractor_id
						AND DATE_FORMAT(parcel_run_pre.real_date,'%Y-%m-%d') BETWEEN app_date AND stop_date
				LEFT JOIN operator dist
					ON dist.operator_id = route_aff.env_dist_id
				LEFT JOIN route
					ON route.route_id = route_aff.route_id
				
				WHERE parcel_run_pre.parcel_run_pre_id='$record'
				AND route.route_id = parcel_run_pre.route_id
				ORDER BY Distributor,Contractor,Ticket;";
			//echo $qry;
			$tab = new MySQLTable("parcels.php",$qry);
			$tab->showRec=false;
			$tab->hasAddButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasEditButton=false;
			$tab->hasForm=false;
			
			$tab->startTable();
				$tab->writeTable();
			$tab->stopTable();
}


if($action=="select_xerox_scan"){

?>
	<script language='javascript'>
		function select_all(){
			
			for(i=0;i<1000;i++){
				var cb = document.getElementById("filec["+i+"]");
				if(cb)
					cb.checked=true;
				else
					return;
			}
		}

		function select_all_unproc(){
			select_none();
			for(i=0;i<10;i++){
				var cb = document.getElementById("filec["+i+"]");
				var filen = document.getElementById("file["+i+"]");
				if(cb && filen){
					if(filen.value.indexOf('Processed')==-1 && cb) cb.checked=true;
				}
				else{
					return;
				}
			}
		}
		function select_none(){
			for(i=0;i<1000;i++){
				var cb = document.getElementById("filec["+i+"]");
				if(cb)
					cb.checked=false;
				else
					return;
			}
		}
	</script>
	<?php
		$redeem_date = get("parcel_run","MAX(date)","",0);
		$year = date("Y",strtotime($redeem_date));
		$month = date("m",strtotime($redeem_date));
	
	?>
		
		<form name="redeem_form" action="parcels.php" method="post" >
			<fieldset style="width:90% ">
				<legend>Xerox Ticket Redemption</legend>
			<table width="40%">
				<tr>
					<td>Month:</td>
					<td>
	<?	
						$month_sel = new Select("month");		
						$month_sel->setOptionIsVal($month);	
						$month_sel->writeMonthSelect();
	?>				
					</td>
					<td>Year:</td>
					<td>
	<?	
						$year_sel = new Select("year");		
						$year_sel->setOptionIsVal($year);	
						$year_sel->writeYearSelectFT();
	?>				
					</td>
					<td>
						<input type="submit" name="submit" value="Redeem" />
					</td>
				</tr>
			</table>
			<br />
			<br />	
			<input type="hidden" name="action" value="process_xerox_scan" />
			<input type="button" name="selall" value="Select all" onclick="select_all();" />
			<input type="button" name="selallup" value="Select all unproc" onclick="select_all_unproc();" />
			<input type="button" name="selnone" value="Select none" onclick="select_none();" />
			<table id="scan_table">
		<?	
			$counter=0;
			foreach(dir_list("Canonscan") as $file){
				if(strpos(strtolower($file),'.csv')!==false){
		?>
				<tr>
					<td>
						<!-- <a href="parcels.php?action=process_xerox_scan&submit=preprocess&file=<?=$file?>"><?=$file?></a><br />-->
						<?=$file?><br />	
						<input type="hidden" id="file[<?php echo $counter;?>]" name="file[<?php echo $counter;?>]" value="<?php echo $file;?>" />		
					</td>
					<td>
						<?php
						
							if(strpos($file,'Processed')===false){
							?>
								<input id="filec[<?php echo $counter;?>]" type="checkbox" name="filec[<?php echo $counter;?>]" checked='true' />
							<?php 
							}
							else{
							?>
								<input id="filec[<?php echo $counter;?>]" type="checkbox" name="filec[<?php echo $counter;?>]"  />			
							<?php
							} 
						?>				
					</td>
		<?	
				$counter++;
				}
			}
		?>
			</table>
		</form>
	</fieldset>
	
<?php
}

if($action=="process_xerox_scan"){
	
}

if($action=="process_xerox_scan2"){
	if($submit || $filter){
		$redeem_date = get("parcel_run","MAX(date)","",0);
		$year = date("Y",strtotime($redeem_date));
		$month = date("m",strtotime($redeem_date));
	
	?>
		
		<form name="redeem_form" action="parcels.php" method="get" >
			<fieldset style="width:90% ">
				<legend>Xerox Ticket Redemption</legend>
			<table width="40%">
				<tr>
					<td>Month:</td>
					<td>
	<?	
						$month_sel = new Select("month");		
						$month_sel->setOptionIsVal($month);	
						$month_sel->writeMonthSelect();
	?>				
					</td>
					<td>Year:</td>
					<td>
	<?	
						$year_sel = new Select("year");		
						$year_sel->setOptionIsVal($year);	
						$year_sel->writeYearSelect(2,1);
	?>				
					</td>
					<td>
						<input type="submit" name="submit" value="Redeem" />
					</td>
				</tr>
			</table>
	<?
				
				if(!$is_processed) $is_processed = 0;
				$now = date("Y-m-d");
				
				if($dist_id) 
				{
					$where_add_dist = " AND dist.operator_id='$dist_id'";
					$where_add_dist .= " AND parcel_run_pre.dist_id='$dist_id'";
				}
				if($type) $where_add_type = " AND ticket_no LIKE '$type%'";
				

				$qry = "SELECT DISTINCT	parcel_run_pre.parcel_run_pre_id AS Record,
								dist.company AS Distributor,
								contr.company AS Contractor,
								route.code AS Route,
								parcel_run_pre.page AS Page,
								parcel_run_pre.real_date AS Date
								
								
						FROM parcel_run_pre
						LEFT JOIN operator contr
							ON contr.operator_id=contractor_id
						LEFT JOIN route_aff
							ON route_aff.env_contractor_id = parcel_run_pre.contractor_id
								AND DATE_FORMAT(parcel_run_pre.real_date,'%Y-%m-%d') BETWEEN app_date AND stop_date
						LEFT JOIN operator dist
							ON dist.operator_id = route_aff.env_dist_id
						LEFT JOIN route
							On route.route_id=parcel_run_pre.route_id

						WHERE is_processed=$is_processed
							$where_add_dist
							$where_add_date
							AND real_date LIKE '$date%'
							AND route.route_id = route_aff.route_id
						ORDER BY Distributor,Contractor,Page;";						
				//$qry = "CALL select_redeem_pre(0)";
				
				$tab = new MySQLTable("parcels.php",$qry);
				$tab->showRec=false;
				$tab->hasAddButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasEditButton=true;
				$tab->hasForm=false;
				if(!$is_processed){
					$tab->hasCheckBoxes=true;
					
					$tab->checkDefaultOn = true;
				}
				
				$tab->onClickEditButtonAction = "process_xerox_scan_ticket_control";
				$tab->onClickEditButtonAdd = "&dist_id=$dist_id&date=$date&is_processed=$is_processed";
				
				$tab->startTable();
					$tab->writeTable();
					$tab->addHiddenInput("dist_id",$dist_id);
					$tab->addHiddenInput("date",$date);
					$tab->addHiddenInput("type",$type);
				$tab->stopTable();
	?>
			<input type="hidden" name="action" id="action" value="<?=$action?>" />
			</fieldset>
		</form>
	<?
	}//if submit
}

if($action=="print_ticket_header_sheet"){
	if($filter){
		if($dist_id) $where_add_dist = " AND route_aff.env_dist_id='$dist_id'";
		if($contr_id) $where_add_contr = " AND route_aff.env_contractor_id='$contr_id'";
		
		$qry = "SELECT 	route_aff.route_aff_id AS Record,
						dist.operator_id AS dist_id,
						dist.company AS Distributor,
						contr.company AS Contractor,
						contr.operator_id AS contr_id,
						route.region AS region,
						route.area AS area,
						route.code AS code,
						route.route_id AS route_id
				FROM route_aff
				RIGHT JOIN operator contr
					ON route_aff.env_contractor_id = contr.operator_id
						AND DATE_FORMAT(now(),'%Y-%m-%d') BETWEEN app_date AND stop_date
				LEFT JOIN operator dist
					ON dist.operator_id = route_aff.env_dist_id
				LEFT JOIN route
					On route.route_id=route_aff.route_id
				WHERE route_aff_id IS NOT NULL
					$where_add_dist
					$where_add_contr
				AND (route.no_ticket_header = 'N' or route.no_ticket_header = '')
				# AND route.is_hidden <> 'Y' # Removed as per customer request. HD
				ORDER BY Distributor,Contractor;";					
		$res_contr = query($qry,0);
		while($contr = mysql_fetch_object($res_contr)){
			$contr_address = get("address","CONCAT(name,', ',first_name)","WHERE operator_id = $contr->contr_id");
			$dist_address = get("address","CONCAT(name,', ',first_name)","WHERE operator_id = $contr->dist_id");
	?>
			<h1>TICKET HEADER SHEET</h1>
			<br />
			<h3 style="text-align:center "><?=sprintf("%04d",$contr->dist_id)?>-<?=sprintf("%04d",$contr->contr_id)?>-<?=sprintf("%04d",$contr->route_id)?></h3>
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			
			<ul>
				<li>Distributor: <?=$dist_address?>, Trading as: <?=$contr->Distributor?></li>
				<li>Contractor: <strong><?=$contr_address?></strong>, Trading as: <strong><?=$contr->Contractor?></strong></li>
				<li>Route: <?=$contr->region?>/<?=$contr->area?>/<strong><?=$contr->code?></strong></li>
			</ul>
			<div class="pagebreak">&nbsp;</div>
	<?		
		}
	}
}

?>