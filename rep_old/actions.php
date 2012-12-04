<?
if($report=="invoice"){
	
	switch($submit){
		case "Create Invoice #":
			if($date_year && $date_month){
				$start_date = $date_year."-".$date_month."-01";
				$last_day = date("t",strtotime($start_date));
				$final_date = $date_year."-".$date_month."-".$last_day;
				$date_field = "date";
				$target = "Redemption Month";
				$red_month = date("M Y",strtotime($start_date));
			}
			else{
				$target = "Date Range";
				$date_field = "real_date";
				$red_month = "$start_date to $final_date";
				
				$start_date .= " 00:00:00";
				$final_date .= " 23:59:59";
			}
		
			$month = date("Y-m",strtotime($start_date));
			
			$dist_ids = array();
			if($dist_id){
				$dist_ids[] = $dist_id;
			}
			else{
				if($submit){
					$qry = "SELECT DISTINCT dist_id FROM parcel_run WHERE date LIKE '$month%'";
					$res = query($qry);
					
					while($d = mysql_fetch_object($res)){
						$dist_ids[] = $d->dist_id;
					}
					
				}
			}
			foreach($dist_ids as $d_id){
				$invoice_no=get("parcel_invoice","invoice_no","WHERE date LIKE '$month%' AND dist_id='$d_id'");
				if(!$invoice_no){
					$invoice_no=get("parcel_invoice","MAX(invoice_no)","");
					$invoice_no++;
					
					$qry = "INSERT INTO parcel_invoice SET date='$start_date',invoice_no='$invoice_no',dist_id='$d_id'";
					query($qry);
				}
				
				$qry = "UPDATE parcel_run SET invoice_no='$invoice_no' WHERE date LIKE '$month%' AND dist_id='$d_id'";
				query($qry);
			}
		break;
	}
}
?>