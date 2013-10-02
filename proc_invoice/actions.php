<?
$check = $_POST["check"];

if($close)  $action="close_jobs";


function create_invoice_no(){
	$max_inv = get_max("job","invoice_no","WHERE job_id IS NOT NULL AND LEFT(invoice_no,1) = 'C'","");
	
	$max_no = substr($max_inv,1);
	$max_no++;
	
	$new_inv = 'C'.$max_no;

	return 'C'.$max_no;
}

function create_invoice_null_no(){
	$max_inv = get_max("job","invoice_no","WHERE job_id IS NOT NULL AND LEFT(invoice_no,1) = 'N'","");
	
	$max_no = substr($max_inv,1);
	$max_no++;
	
	$new_inv = 'N'.$max_no;

	return 'N'.$max_no;
}

function update_invoice_no($job_id,$invoice_no){
	
	$qry = "UPDATE job SET invoice_no='$invoice_no' WHERE job_id='$job_id'";
	//echo $qry."<br />";
	query($qry);
}

function create_item_no($count){
	$result = 'CI/'.$count;
	return $result;
}

function create_description($job){
	$delivery_date = date("d M Y",strtotime($job->delivery_date));
	
	/*if($job->is_ioa=='Y'){
		$delivery_date = "IOA";
	}*/
	
	if($job->folding_fee>0  && $job->add_folding_to_invoice == 'Y'){
		$ff = number_format(1000*$job->folding_fee,0);
		$add_ff = "Includes folding fee \$$ff/1000 cents.";
	}
	
	if($job->premium_sell>0){
		$pr = number_format(1000*$job->premium_sell,0);
		$add_pr = "Includes premium fee \$$pr/1000.";
	}
	
	if($job->discount>0){
		$discount = number_format($job->discount,1);
		$add_dc = "Includes discount of $discount %.";
	}
	
	if($job->dest_type=="num_dairies")
		$dest_type = "Boxholder Type: Dairy.";
	else
		$dest_type = "Boxholder Type: ".ucfirst(str_replace('num_','',$job->dest_type)).'.';
	
	if($job->inc_linehaul=='Y'){
		$add_linehaul = "Includes Linehaul.";
	}
	
	$result = $job->publication." - Job ".$job->job_no." - Delivery ".$delivery_date." - ".sprintf("%d",$job->weight)." grams. $add_ff $add_pr $add_dc $dest_type $add_linehaul";
	return $result;
}

function create_description_bbc($job){
	$delivery_date = date("d M Y",strtotime($job->delivery_date));
	
	/*if($job->is_ioa=='Y'){
		$delivery_date = "IOA";
	}*/
	
	if($job->folding_fee>0){
		$ff = number_format($job->folding_fee,4);
		$add_ff = "Includes folding fee $ff cents.";
	}
	
	if($job->premium>0 && $job->add_premium_to_invoice == 'Y'){
		$pr = number_format($job->folding_fee,4);
		$add_pr = "Includes premium fee $pr cents.";
	}
	
	if($job->discount>0){
		$discount = number_format($job->discount,1);
		$add_dc = "Includes discount of $discount %.";
	}
	
	if($job->inc_linehaul=='Y'){
		$add_linehaul = "Includes Linehaul.";
	}
	
	
	
	$result = $job->publication." - Job ".$job->job_no." - Delivery ".$delivery_date." - ".sprintf("%d",$job->weight)." grams. ".$job->desc_bbc.". $add_linehaul";
	return $result; 
}


function write_header($fp){
	fwrite($fp,"ContactName,InvoiceNumber,Reference,InvoiceDate,DueDate,Description,Quantity,UnitAmount,Discount,AccountCode,TaxType,TrackingName1,TrackingOption1\n");
}

function write_fuel_surcharge($fp,$job,$invoiceno,$fuel_surcharge,$gst,$date){
	$amount = get("job","SUM((folding_fee+rate)*invoice_qty*(1-discount/100))","WHERE invoice_no='$invoiceno'");
//echo "SUM((folding_fee+rate)*invoice_qty*(1-discount/100))","WHERE invoice_no='$invoiceno'";
	$qry = "SELECT SUM(amount) AS amount,bundle_price 
			FROM job_route 
			LEFT JOIN job
			ON job.job_id=job_route.job_id
			WHERE invoice_no='$invoiceno' AND job_route.dest_type='bundles' GROUP BY bundle_price ORDER BY bundle_price";
	$res = query($qry);
	
	while($amt = mysql_fetch_object($res)){
		$amount += $amt->amount*$amt->bundle_price;
	}
	
	$fuel_surcharge/=100;
	$add = 0.0;
	if($job->add_premium_to_invoice == 'Y'){
		$add+=$job->premium;
	}
	if($job->add_folding_to_invoice == 'Y'){
		$add+=$job->folding_fee;
	}
	
	$add = number_format($add,4);
	$contact            = get("client","name","WHERE client_id= {$job->client_id}");
	$cust_po			= $job->purchase_no;
	$delivery_status 	= 'I';
	$item_no			= 'FS';
	$qty				= number_format($amount,3,'.','');
	$price				= number_format($fuel_surcharge,4,'.','');
	$price_gst			= number_format($price*$gst,4,'.','');
	$total 				= number_format($qty*$price,2,'.','');
	//echo $fuel_surcharge." ".$invoiceno." ".$job->job_no."<br />";
	$total_gst			= number_format($total*$gst,2,'.','');
	
	$gst				= number_format($total_gst - $total,2,'.','');
	$gst_code			= 'S';
	$journal			= $job->publication;
	$sale_stat			= 'I';
	//$terms				= 4;
	//$balance_dd			= 20;
	$card_id			= $job->card_id;
	$description		= "Fuel Surcharge";
	
	if($total>0) 
		fwrite($fp, "$contact,$invoiceno,$cust_po,{$job->invoice_date},{$job->delivery_date},$description,$qty,$price,$discount,100,15% GST on income,Division,Circulars\n");

		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst\t$sale_stat\t$card_id\n");
		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$journal\t$gst_code\t$gst\t$total_gst\t$sale_stat\t$card_id\n");
}
function write_bundles($fp,$job,$invoiceno,$fuel_surcharge,$gst,$date){
	
	$qry = "SELECT SUM(amount) AS amount,bundle_price FROM job_route WHERE job_id='$job->job_id' AND dest_type='bundles' GROUP BY bundle_price ORDER BY bundle_price";
	$res = query($qry);
	while($amt = mysql_fetch_object($res)){
		$item_add = round(100*$amt->bundle_price,0);
	
		$add = 0.0;
		if($job->add_premium_to_invoice == 'Y'){
			$add+=$job->premium;
		}
		if($job->add_folding_to_invoice == 'Y'){
			$add+=$job->folding_fee;
		}
		
		$add = number_format($add,4);
		$contact            = get("client","name","WHERE client_id= {$job->client_id}");	
		$cust_po			= $job->purchase_no;
		$delivery_status 	= 'I';
		$item_no			= 'C'.$item_add;
		$qty				= number_format($amt->amount,3,'.','');
		$price				= number_format($amt->bundle_price,4,'.','');
		$price_gst			= number_format($price*$gst,4,'.','');
		$total 				= number_format($qty*$price,2,'.','');
		$total_gst			= number_format($total*$gst,2,'.','');
		$gst_val				= number_format($total_gst - $total,2,'.','');
		$gst_code			= 'S';
		$journal			= $job->publication;
		$sale_stat			= 'I';
		//$terms				= 4;
		//$balance_dd			= 20;
		$card_id			= $job->card_id;
		$description		= "Bundles";
		fwrite($fp, ",$invoiceno,$cust_po,$date,{$job->delivery_date},$description,$qty,$price,$discount,100,15% GST on income,Division,Circulars\n");

		//echo $price." ".$price_gst." ".$gst."<br />";
		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
	}	
}
function write_bbc($fp,$job,$invoiceno,$gst,$date,$count){
	if($job->qty_bbc>0){
		$count++;
		$add = 0.0;
		if($job->add_premium_to_invoice == 'Y'){
			$add+=$job->premium;
		}
		if($job->add_folding_to_invoice == 'Y'){
			$add+=$job->folding_fee;
		}
		
		$add = number_format($add,4);
		$contact            = get("client","name","WHERE client_id= {$job->client_id}");
			
		$item_no			= create_item_no($count);
		$cust_po			= $job->purchase_no;
		$delivery_status 	= 'I';
		$qty				= number_format($job->qty_bbc,3,'.','');
	
		$price				= number_format($job->rate_bbc,4,'.','');
	
		$price_gst			= number_format($price*$gst,4,'.','');
		$total 				= number_format($qty*$price,2,'.','');
		$total_gst			= number_format($total*$gst,2,'.','');
		$gst_val			= number_format($total_gst - $total,2,'.','');
		$gst_code			= 'S';
		$journal			= $job->publication;
		$sale_stat			= 'I';
		//$terms				= 4;
		//$balance_dd			= 20;
		$card_id			= $job->card_id;
		$description		= create_description_bbc($job);
		fwrite($fp, ",$invoiceno,$cust_po,$date,{$job->delivery_date},$description,$qty,$price,$discount,100,15% GST on income,Division,Circulars\n");

		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
	}
	return $count;
}


// redirect page using javascript when header has already sent.
/*function redirect($filename,$att=false) 
{
		//header('Location: '.$filename);
		header('Content-Type: text/plain');	
		header('Content-Disposition: attachment; filename="'.$filename.'"');
}*/
function save_group($arrGroups){
	foreach($arrGroups as $job_id => $group){
		$qry = "UPDATE job SET `group` = '$group' WHERE job_id = '$job_id'";
		query($qry,0);
	}
}

function get_group($intJobId){
	$qry = "SELECT * FROM job WHERE job_id = '$intJobId'";
	$res = query($qry,0);
	$job = mysql_fetch_object($res);
	return $job->group;
}


function check_invoice_no($intGroupId){
	$qry = "SELECT * FROM job WHERE `group` = '$intGroupId' ORDER BY invoice_no DESC";
	$res = query($qry,0);
	$invoice_no = "";
	while($job = mysql_fetch_object($res)){
		$buffer = $job->invoice_no;
		if($buffer) return $buffer;
	}
	return create_invoice_no();
}

if($action=="Create Invoices"){
	$group = $_POST["efield"];
	
	save_group($group);
	
	if($check){
	
		if(!$date) $date = date("t/m/Y");
		else $date = date("d/m/Y",strtotime($date));
		
		
		
		$now = date("Y_m_d_i_s");
		//$fn = tempnam("proc_invoice/temp","invoive_");
		$fn = "proc_invoice/temp/invoive_$now.csv";
		if(!$fp = fopen($fn,"w")){
			$ERROR = "Could not load temp file for invoice processing.";
		}
		
		//redirect($fn,true);
		
		write_header($fp);
		$count=1;
		$first=true;
		$num_check = count($check);
		$job_list = "(0";
		foreach($check as $job_id=>$item){
			if($item){
				$job_list.= ",".$job_id;
			}
		}
		$job_list .= ")";
		
		
		
		$qry = "SELECT * 
				FROM job 
				LEFT JOIN client
				ON job.client_id=client.client_id
				WHERE job_id IN $job_list
				ORDER BY invoice_no,client.name,publication,delivery_date";
		
		
		$res = query($qry);
		while($job = mysql_fetch_object($res)){
			$job->invoice_no = "";
		}
		$res = query($qry);
		$last_item = mysql_num_rows($res);
		$counter=1;
		$prev_invoice_no="";
		while($job = mysql_fetch_object($res)){
			//$gst = 1.00+$job->gst/100;
			$gst = 1+$GST_CIRCULAR;
			
			if($is_null_job){
				$qry = "UPDATE job SET invoice_no='No Charge' WHERE job_id=".$job->job_id;
				query($qry);
				continue;
			}
			//if(!$fuel_surcharge) $fuel_surcharge = $job->fuel_surcharge;
			
			//echo $job->rate." / ".$job->invoice_qty;
			
			$cur_job_id = $job->job_id;
			$prev_job_id = $prev_job->job_id;
			
			$g = get_group($job->job_id);
			$invoiceno = check_invoice_no($g);
			
			
			/*if($group[$cur_job_id] != $group[$prev_job_id]){
				if(!$fuel_surcharge)  $fuel_sc = $prev_job->fuel_surcharge;
				else $fuel_sc = $fuel_surcharge;
				//if($invoiceno) {
					//write_fuel_surcharge($fp,$prev_job,$invoiceno,$fuel_sc,$gst,$date);
				//}
				
				$invoiceno 	= create_invoice_no();
				//echo $cur_job_id."/".$prev_job_id."/".$invoiceno ."<br />";
				$count=1;
				//if(!$first) fwrite($fp, "\n");
				
			}
			else{
				$invoiceno = $invoiceno;
				$count++;
			}*/

			update_invoice_no($job->job_id,$invoiceno);
			
			$discount = 1-$job->discount/100;
			
			$cust_po			= $job->purchase_no;
			$delivery_status 	= 'I';
			$item_no			= create_item_no($count);
			$qty				= number_format($job->invoice_qty,3,'.','')/1000;

			$add = 0.0;
			$add+=$job->premium_sell;
			if($job->add_folding_to_invoice == 'Y'){
				$add+=$job->folding_fee;
			}
			
			$contact = "";
			//echo $prev_invoice_no.'/'.$invoiceno; 
			if($invoiceno != $prev_invoice_no) $contact = get("client","card_id","WHERE client_id= {$job->client_id}");
			//echo $contact.'/'.get("client","card_id","WHERE client_id= {$job->client_id}"); die();
			$add = number_format($add,4);
			//$contact            = get("client","card_id","WHERE client_id= {$job->client_id}");
			$price				= number_format(($job->rate+$add)*$discount,4,'.','')*1000;
			$price_gst			= number_format($price*$gst,4,'.','');
			$total 				= number_format($job->invoice_qty*$price,2,'.','');
			$total_gst			= number_format($total*$gst,2,'.','');
			$gst_val			= number_format($total_gst - $total,2,'.','');
			$gst_code			= 'S';
			$journal			= $job->publication;
			$sale_stat			= 'I';
			//$terms				= 4;
			//$balance_dd			= 20;
			$card_id			= $job->card_id;
			$description		= create_description($job);
			$inv_date_test		= date('Y-m-20',strtotime($date_show));
			$due_date			= date('Y-m-20',strtotime($inv_date_test.' + 1 month'));
			$inv_date			= date('Y-m-d',strtotime($date_show));
			
			//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
			//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
			fwrite($fp, "$contact,$invoiceno,$cust_po,$inv_date,$due_date,$description,$qty,$price,,100,15% GST on income,Division,Circulars\n");

			
			
			$count = write_bbc($fp,$job,$invoiceno,$gst,$inv_date,$count);
			
			write_bundles($fp,$job,$invoiceno,$fuel_surcharge,$gst,$inv_date);
			
			/*if($counter==$last_item){
				if(!$fuel_surcharge)  $fuel_sc = $prev_job->fuel_surcharge;
				else $fuel_sc = $fuel_surcharge;
				if($invoiceno) write_fuel_surcharge($fp,$job,$invoiceno,$fuel_sc,$gst,$date);
			}*/
			
			$prev_job = $job;
			$prev_invoice_no = $invoiceno;
			$first=false;
			$counter++;
		}
		fwrite($fp, "\n");
		fclose($fp);
		
		
		$MESSAGE = "Creating invoices finished";
		$action="download_inv";
	}
	else{
		$ERROR = "Nothing selected.";
		$action="select_jobs";
	}
}

if($action=="close_jobs"){
	switch($submit){
		case "Close":
			
			$check = $_POST["check"];
			if(!is_array($check)) break;
			foreach($check as $job_id=>$item){
				$qry = "UPDATE job SET finished='Y' WHERE job_id='$job_id'";
				query($qry);
				$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
				$qry = "UPDATE job SET finished='Y' WHERE job_id='$alt_job_id'";
				query($qry);
			}
		break;
	}
}
	
?>