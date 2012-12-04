<?
$check = $_POST["check"];

function create_invoice_no(){
	$max_inv = get_max("job","invoice_no","WHERE job_id IS NOT NULL AND LEFT(invoice_no,1) = 'C'","");
	
	$max_no = substr($max_inv,1);
	$max_no++;
	
	$new_inv = 'C'.$max_no;

	return 'C'.$max_no;
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
	
	if($job->is_ioa=='Y'){
		$delivery_date = "IOA";
	}
	
	if($job->folding_fee>0){
		$ff = number_format($job->folding_fee,4);
		$add_ff = "Includes folding fee $ff cents.";
	}
	
	if($job->discount>0){
		$discount = number_format($job->discount,1);
		$add_dc = "Includes discount of $discount %.";
	}
	
	if($job->inc_linehaul=='Y'){
		$add_linehaul = "Includes Linehaul.";
	}
	
	$result = $job->publication." - Job ".$job->job_no." - Delivery ".$delivery_date." - ".sprintf("%d",$job->weight)." grams. $add_ff $add_dc $add_linehaul";
	return $result;
}

function create_description_bbc($job){
	$delivery_date = date("d M Y",strtotime($job->delivery_date));
	
	if($job->is_ioa=='Y'){
		$delivery_date = "IOA";
	}
	
	if($job->folding_fee>0){
		$ff = number_format($job->folding_fee,4);
		$add_ff = "Includes folding fee $ff cents.";
	}
	
	if($job->discount>0){
		$discount = number_format($job->discount,1);
		$add_dc = "Includes discount of $discount %.";
	}
	
	if($job->inc_linehaul=='Y'){
		$add_linehaul = "Includes Linehaul.";
	}
	
	
	
	$result = $job->publication." - Job ".$job->job_no." - Delivery ".$delivery_date." - ".sprintf("%d",$job->weight)." grams. Bags Boxes and Counter. $add_linehaul";
	return $result;
}


function write_header($fp){
	//fwrite($fp, "Invoice  No.\tDate\tCustomer PO\tDelivery Status\tItem Number\tQuantity\tDescription\tPrice\tInc-GST Price\tTotal\tInc-GST Total\tJournal Memo\tGST Code\tGST Amount\tSale Status\tCard ID\n");
	fwrite($fp, "Invoice  No.\tDate\tCustomer PO\tDelivery Status\tItem Number\tQuantity\tDescription\tPrice\tInc-GST Price\tTotal\tTotal_GST_Inc\tJournal Memo\tGST Code\tGST Amount\tSale Status\tCard ID\n");
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
		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst\t$sale_stat\t$card_id\n");
		fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$journal\t$gst_code\t$gst\t$total_gst\t$sale_stat\t$card_id\n");
}
function write_bundles($fp,$job,$invoiceno,$fuel_surcharge,$gst,$date){
	
	$qry = "SELECT SUM(amount) AS amount,bundle_price FROM job_route WHERE job_id='$job->job_id' AND dest_type='bundles' GROUP BY bundle_price ORDER BY bundle_price";
	$res = query($qry);
	while($amt = mysql_fetch_object($res)){
		$item_add = round(100*$amt->bundle_price,0);
		
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
		
		//echo $price." ".$price_gst." ".$gst."<br />";
		//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
		fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
	}	
}
function write_bbc($fp,$job,$invoiceno,$gst,$date,$count){
	if($job->qty_bbc>0){
		$count++;
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
		
		fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
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

if($action=="Create Invoices"){
	$group = $_POST["efield"];
	
	
	if($check){
	
		if(!$date) $date = date("t/m/Y");
		else $date = date("d/m/Y",strtotime($date));
		
		
		
		$now = date("Y_m_d_i_s");
		//$fn = tempnam("proc_invoice/temp","invoive_");
		$fn = "proc_invoice/temp/invoive_$now.txt";
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
				ORDER BY client.name,publication,delivery_date";
		
		
		$res = query($qry);
		$last_item = mysql_num_rows($res);
		$counter=1;
		while($job = mysql_fetch_object($res)){
			//$gst = 1.00+$job->gst/100;
			$gst = 1+$GST_CIRCULAR;
			
			//if(!$fuel_surcharge) $fuel_surcharge = $job->fuel_surcharge;
			
			//echo $job->rate." / ".$job->invoice_qty;
			
			$cur_job_id = $job->job_id;
			$prev_job_id = $prev_job->job_id;
			//echo $cur_job_id."/".$prev_job_id."<br />";
			if($group[$cur_job_id] != $group[$prev_job_id]){
				if(!$fuel_surcharge)  $fuel_sc = $prev_job->fuel_surcharge;
				else $fuel_sc = $fuel_surcharge;
				if($invoiceno) {
					write_fuel_surcharge($fp,$prev_job,$invoiceno,$fuel_sc,$gst,$date);
				}
				
				$invoiceno 	= create_invoice_no();
				//echo $cur_job_id."/".$prev_job_id."/".$invoiceno ."<br />";
				$count=1;
				if(!$first) fwrite($fp, "\n");
				
			}
			else{
				$invoiceno = $invoiceno;
				$count++;
			}

			update_invoice_no($cur_job_id,$invoiceno);
			
			$discount = 1-$job->discount/100;
			
			$cust_po			= $job->purchase_no;
			$delivery_status 	= 'I';
			$item_no			= create_item_no($count);
			$qty				= number_format($job->invoice_qty,3,'.','');

			$price				= number_format(($job->rate+$job->folding_fee)*$discount,4,'.','');
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
			//fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");
			fwrite($fp, "$invoiceno\t$date\t$cust_po\t$delivery_status\t$item_no\t$qty\t$description\t$price\t$price_gst\t$total\t$total_gst\t$journal\t$gst_code\t$gst_val\t$sale_stat\t$card_id\n");

			
			
			$count = write_bbc($fp,$job,$invoiceno,$gst,$date,$count);
			
			write_bundles($fp,$job,$invoiceno,$fuel_surcharge,$gst,$date);
			
			if($counter==$last_item){
				if(!$fuel_surcharge)  $fuel_sc = $prev_job->fuel_surcharge;
				else $fuel_sc = $fuel_surcharge;
				if($invoiceno) write_fuel_surcharge($fp,$job,$invoiceno,$fuel_sc,$gst,$date);
			}
			
			$prev_job = $job;
			$first=false;
			$counter++;
		}
		fwrite($fp, "\n");
		//$job->publication = "Dummy Publication";
		//write_dummy($fp);
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