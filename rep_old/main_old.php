<?
if($report=="ticket_sold"){
	if($submit){
		$qry = "SELECT  job_no AS 'Delivery #',
						order_date AS Date,
						type AS Type,
						start AS Start,
						end AS End,
						end-start+1 AS Qty
				FROM parcel_job_ticket
				LEFT JOIN parcel_job
				ON parcel_job.job_id=parcel_job_ticket.job_id
				WHERE parcel_job.order_date>='$start_date' AND parcel_job.order_date<='$final_date' 
				ORDER BY type,job_no";
		$tab = new MySQLTable("rep_parcels.php",$qry,$nameI="report");
		
		$tab->formatLine = true;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->showRec=1;
			
		$tab->startTable();
			$tab->writeTable();
		$tab->stopTable();
	}
}
if($report=="ticket_unsold"){
	//$date = date("Y-m-d");
	//$type = "CD";
	if($action == "remove")
	{
		$qry = "SELECT * FROM parcel_ticket_th WHERE parcel_ticket_th_id = ".$id;
		$res_th = query($qry,0);
		$th = mysql_fetch_object($res_th);
		$curr_start = $th->start;
		$curr_end = $th->end;
		if($start == $curr_start || $end == $curr_end)
		{
			if($start == $curr_start)
			{
				$qry = "UPDATE parcel_ticket_th SET start=".($end+1)." WHERE parcel_ticket_th_id = ".$id;
			}
			else
			{
				$qry = "UPDATE parcel_ticket_th SET end=".($start-1)." WHERE parcel_ticket_th_id = ".$id;
			}
			$res_up = query($qry,0);
		}
		else 
		{
			$qry = "UPDATE parcel_ticket_th SET end=".($start-1)." WHERE parcel_ticket_th_id = ".$id;
			$qry2 = "INSERT INTO parcel_ticket_th 
							SET parcel_th_receipt_id=".$th->parcel_th_receipt_id
							.", start=".($end+1)
							.", end=".$curr_end
							.", type='".$th->type
							."', qty=".$th->qty;
			
			$res_up = query($qry,0);
			$res_up = query($qry2,0);
		}		
	}
	
	if($submit){
		$qry = "SELECT 		parcel_ticket_th_id,
							type,
							start,
							end,
							date,
							CONCAT('<a href=\'rep_parcels.php?report=ticket_unsold&action=remove&id=', parcel_ticket_th_id,'&start=')
								AS Action
					FROM parcel_th_receipt
					LEFT JOIN parcel_ticket_th
					ON parcel_ticket_th.parcel_th_receipt_id=parcel_th_receipt.parcel_th_receipt_id
					WHERE parcel_th_receipt.date BETWEEN '$start_date' AND '$final_date'
						AND type = '$type'
						#AND parcel_th_receipt.parcel_th_receipt_id=14
						AND date>='2008-10-01'
					ORDER BY date DESC,parcel_th_receipt.parcel_th_receipt_id";
					
		$res_th = query($qry,0);
		
		$tot_qty = 0;
		$counter = 0;
		$tickets = array();
		
	
		
		$tab = new MySQLTable("rep_parcels.php",$qry,$nameI="report");
		
		$tab->formatLine = true;
		$tab->hasEditButton=true;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=1;
		$tab->startTable();
			$tab->startNewLine();
					$tab->addLineWithStyle("Unsold tickets for ".$type.".","sql_extra_line_text_grey",7);
			$tab->stopNewLine();
			$tab->startNewHeaderLine();
					$tab->addHeaderLine("Date Received");
					$tab->addHeaderLine("Received Start");
					$tab->addHeaderLine("Received End");
					$tab->addHeaderLine("Start");
					$tab->addHeaderLine("End");
					$tab->addHeaderLine("Qty");
					$tab->addHeaderLine("Action");
			$tab->stopNewLine();
			while($th = mysql_fetch_object($res_th)){
				$qry = "SELECT * 
						FROM parcel_job
						LEFT JOIN parcel_job_ticket
						ON parcel_job.job_id=parcel_job_ticket.job_id
						WHERE start BETWEEN $th->start AND $th->end
							OR end BETWEEN $th->start AND $th->end
						ORDER BY start";
				$res_sold = query($qry);
				$start = $th->start;
				
				
				while($sold = mysql_fetch_object($res_sold)){
					if($sold->end > $th->end) 
					{
						$start = $th->end+1;
						break;	
					}
					if($sold->start < $start)
					{
						$end = $start-1;
					}
					else
					{
						$end = $sold->start-1;
					}
					//echo $start."/".$end."<br />";
					$qty = $end-$start+1;
					$act = $th->Action.$start."&end=".$end."'>Remove</a>";
					if($qty>1){
						$tab->startNewLine();
							$tab->addLineWithStyle($th->date,"sql_extra_line_number");
							$tab->addLineWithStyle($th->start,"sql_extra_line_number");
							$tab->addLineWithStyle($th->end,"sql_extra_line_number");
							$tab->addLineWithStyle($start,"sql_extra_line_number");
							$tab->addLineWithStyle($end,"sql_extra_line_number");
							$tab->addLineWithStyle($qty,"sql_extra_line_number");
							$tab->addLineWithStyle($act,"sql_extra_line_number");
						$tab->stopNewLine();
						$tot_qty += $qty;
					}
					

					$start=$sold->end+1;
					
					$counter++;
				}
				$end = $th->end;
				//echo $start."/".$end."<br />";
				$qty = $end-$start+1;
				$act = $th->Action.$start."&end=".$end."'>Remove</a>";
				if($qty>1){
					$tab->startNewLine();
						$tab->addLineWithStyle($th->date,"sql_extra_line_number");
						$tab->addLineWithStyle($th->start,"sql_extra_line_number");
						$tab->addLineWithStyle($th->end,"sql_extra_line_number");
						$tab->addLineWithStyle($start,"sql_extra_line_number");
						$tab->addLineWithStyle($end,"sql_extra_line_number");
						$tab->addLineWithStyle($qty,"sql_extra_line_number");
						$tab->addLineWithStyle($act,"sql_extra_line_number");
					$tab->stopNewLine();
					$tot_qty += $qty;
				}
				

				//$start=$sold->end+1;
				
				$counter++;
			}
			
			$tab->startNewLine();
					$tab->addLineWithStyle("&nbsp;","sql_extra_line_number",4);
					$tab->addLineWithStyle("Total:","sql_extra_line_number");
					$tab->addLineWithStyle($tot_qty,"sql_extra_line_number");
			$tab->stopNewLine();
		$tab->stopTable();
	}
}


if($report=="ticket_unredeemed" && $submit){

	
	$date0 = date("Y-m-15",strtotime($date));
	$start_date0 = date("Y-m-15",strtotime($date0." -11 months")); 
	$final_date0 = date("Y-m-15",strtotime($date0));
	
	$start_date = date("Y-m-01",strtotime($start_date0));
	$final_date = date("Y-m-t",strtotime($final_date0));
	
	$month_list = array();
	for($d = $start_date0;$d<=$final_date0;$d=date("Y-m-15",strtotime($d." +1 months"))){
		
		$month_str = "";
		if(strpos($d,"-01")!==false){
			$month_str = "January";
		}
		else if(strpos($d,"-02")!==false){
			$month_str = "February";
		}
		else if(strpos($d,"-03")!==false){
			$month_str = "March";
		}
		else if(strpos($d,"-04")!==false){
			$month_str = "April";
		}
		else if(strpos($d,"-05")!==false){
			$month_str = "May";
		}
		else if(strpos($d,"-06")!==false){
			$month_str = "June";
		}
		else if(strpos($d,"-07")!==false){
			$month_str = "July";
		}
		else if(strpos($d,"-08")!==false){
			$month_str = "August";
		}
		else if(strpos($d,"-09")!==false){
			$month_str = "September";
		}
		else if(strpos($d,"-10")!==false){
			$month_str = "October";
		}
		else if(strpos($d,"-11")!==false){
			$month_str = "November";
		}
		else if(strpos($d,"-12")!==false){
			$month_str = "December";
		}
		$month_list[$month_str] = date("Y-m",strtotime($d));
	}
	
	//print_r($month_list);
	//die();
	$types = array("CP"=>"Parcels","CD"=>"Documents","SR"=>"Signature");
	$result = array();
	foreach($types as $key=>$type){
		$qry_red = "SELECT 	type,
							COUNT(ticket_id) AS amount,
							DATE_FORMAT(parcel_job.order_date,'%Y-%m') AS month
					FROM parcel_job_route
					LEFT JOIN parcel_run
					ON parcel_run.parcel_run_id=parcel_job_route.parcel_run_id
					LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_route.job_id
					WHERE parcel_job.order_date>='$start_date' AND parcel_job.order_date<='$final_date' 
						AND type='$key'
						AND parcel_job_route.is_redeemed_D='1'
						AND parcel_run.date<='$final_date'
					GROUP BY type,month
					HAVING month IS NOT NULL
					ORDER BY month";
					
		$res_red = query($qry_red,0);

		$qry_sold = "SELECT 	type,
							SUM(end-start+1) AS amount,
							DATE_FORMAT(parcel_job.order_date,'%Y-%m') AS month
					FROM parcel_job_ticket
					LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_ticket.job_id
					WHERE parcel_job.order_date>='$start_date' AND parcel_job.order_date<='$final_date' 
						AND type='$key'
						AND (id <> -1 OR id IS NULL)
					GROUP BY type,month
					HAVING month IS NOT NULL
					ORDER BY month";
		$res_sold = query($qry_sold,0);


		while($redeemed = mysql_fetch_object($res_red)){
			
			$sold = mysql_fetch_object($res_sold);
			
			$result[$key][$redeemed->month]["Redeemed"] = $redeemed->amount;
			$result[$key][$sold->month]["Sold"] = $sold->amount;
			$result[$key][$redeemed->month]["Unredeemed"] = $sold->amount-$redeemed->amount;
			
			if($sold->amount>0)
				$result[$key][$sold->month]["%"] = number_format(($sold->amount-$redeemed->amount)*100/$sold->amount,1);
			else
				$result[$key][$sold->month]["%"] = number_format(0,1);
				
			$red_tot+=$redeemed->amount;
			$sold_tot+=$sold->amount;
			
		}
		
		/*while($sold = mysql_fetch_object($res_sold)){
			$result[$key][$sold->month]["Sold"] = $sold->amount;
			$result[$key][$redeemed->month]["Unredeemed"] = $sold->amount-$result[$key][$sold->month]["Redeemed"];
			if($sold->amount>0)
				$result[$key][$sold->month]["%"] = number_format(($sold->amount-$result[$key][$sold->month]["Redeemed"])*100/$sold->amount,1);
			else
				$result[$key][$sold->month]["%"] = number_format(0,1);
			$sold_tot+=$sold->amount;
		}*/
		
		
		$result[$key]["Total"]["Redeemed"] += $red_tot;
		$result[$key]["Total"]["Sold"] += $sold_tot;
		$result[$key]["Total"]["Unredeemed"] = $sold_tot-$red_tot;
		$red_tot=0;
		$sold_tot=0;
		
		
		if($result[$key]["Total"]["Unredeemed"]>0)
			$result[$key]["Total"]["%"] = number_format($result[$key]["Total"]["Unredeemed"]*100/$result[$key]["Total"]["Sold"],1);
		else
			$result[$key]["Total"]["%"] = number_format(0,1);
	}
	
	
	$tab = new MySQLTable("rep_parcels.php",$qry,$nameI="report");
	
	$tab->formatLine = true;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasAddButton=false;
	$tab->showRec=1;
	$tab->startTable();
		foreach($types as $key=>$type){
			if(count($result[$key])>0){
				$tab->startNewLine();
					$tab->addLineWithStyle($type,"sql_extra_line_text_grey",14);
				$tab->stopNewLine();
				$tab->startNewLine();
					$tab->addLineWithStyle("&nbsp;","sql_extra_line_number");
					
					foreach($month_list as $m_str=>$month){
						$tab->addLineWithStyle($m_str,"sql_extra_line_number");
					}
					$tab->addLineWithStyle("Total","sql_extra_line_number");
				$tab->stopNewLine();
				$tab->startNewLine();
					$tab->addLineWithStyle("Sold","sql_extra_line_number");
					foreach($month_list as $month){
						$tab->addLineWithStyle($result[$key][$month]["Sold"],"sql_extra_line_number");
					}
					$tab->addLineWithStyle($result[$key]["Total"]["Sold"],"sql_extra_line_number");
				$tab->stopNewLine();	
				$tab->startNewLine();
					$tab->addLineWithStyle("Unredeemed","sql_extra_line_number");
					foreach($month_list as $month){
						$tab->addLineWithStyle($result[$key][$month]["Unredeemed"],"sql_extra_line_number");
					}
					$tab->addLineWithStyle($result[$key]["Total"]["Unredeemed"],"sql_extra_line_number");
				$tab->stopNewLine();	
				$tab->startNewLine();
					$tab->addLineWithStyle("%","sql_extra_line_number");
					foreach($month_list as $month){
						$tab->addLineWithStyle($result[$key][$month]["%"],"sql_extra_line_number");
					}
					$tab->addLineWithStyle($result[$key]["Total"]["%"],"sql_extra_line_number");
				$tab->stopNewLine();	
			}
			
		}
	$tab->stopTable();
}

if($report=="ticket_redeemed_by_contractor" && $submit){
	$dist = get("operator","company","WHERE operator_id='$dist_id'");
	
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
		}
?>
	<h3>Parcel Runsheet Report</h3>
	<ul>
		<li>Distributor Name: <?=$dist?></li>
		<li><?=$target?>: <?=$red_month?></li>
		<li>Printed: <?=date("Y-m-d")?></li>
	</ul>
<?	
		$tab = new MySQLTable("rep_parcels.php",$qry,$nameI="report");
		$tab->collField["Pickup_CD"]=true;
		$tab->collField["Delivery_CD"]=true;
		$tab->collField["Total_CD"]=true;
		$tab->collField["Pickup_CP"]=true;
		$tab->collField["Delivery_CP"]=true;
		$tab->collField["Total_CP"]=true;
		$tab->collField["Total_tot"]=true;
		$tab->collField["tt"]=true;
		
		$tab->hiddenFields["Pickup_CD"]=true;
		$tab->hiddenFields["Delivery_CD"]=true;
		$tab->hiddenFields["Total_CD"]=true;
		$tab->hiddenFields["Pickup_CP"]=true;
		$tab->hiddenFields["Delivery_CP"]=true;
		$tab->hiddenFields["Total_CP"]=true;
		$tab->hiddenFields["Total_tot"]=true;
		$tab->hiddenFields["tt"]=true;
		
		$tab->formatLine = true;
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=1;

	$qry = "SELECT DISTINCT parcel_job_route.contractor_id
					FROM parcel_job_route
					LEFT JOIN operator
					ON operator_id=parcel_job_route.contractor_id
					LEFT JOIN parcel_run
					ON parcel_run.parcel_run_id = parcel_job_route.parcel_run_id
					WHERE parcel_job_route.dist_id='$dist_id'
					AND parcel_run.$date_field BETWEEN '$start_date' AND '$final_date'	
					ORDER BY company";
	$res = query($qry,0);
	$tab->startTable();
	$start=true;
	$tot_pcd = 0;
	$tot_dcd = 0;
	$tot_tcd = 0;
	$tot_pcp = 0;
	$tot_pcd = 0;
	$tot_tcp = 0;
	$tot_tt = 0;
	$tot_sheets=0;
	while($contr = mysql_fetch_object($res)){
		$qry="	SELECT 	company AS Contractor,
						#operator_id,
						#parcel_run_id,
						code AS Route,
						date AS Date,
						run AS 'Sheet#',
						SUM(IF(type='CD',pickup,0)) AS Pickup,
						SUM(IF(type='CD',pickup,0)) AS Pickup_CD,
						SUM(IF(type='CD',delivery,0)) AS Delivery,
						SUM(IF(type='CD',delivery,0)) AS Delivery_CD,
						SUM(IF(type='CD',total,0)) AS Total,
						SUM(IF(type='CD',total,0)) AS Total_CD,
						SUM(IF(type='CP',pickup,0)) AS Pickup,
						SUM(IF(type='CP',pickup,0)) AS Pickup_CP,
						SUM(IF(type='CP',delivery,0)) AS Delivery,
						SUM(IF(type='CP',delivery,0)) AS Delivery_CP,
						SUM(IF(type='CP',total,0)) AS Total,
						SUM(IF(type='CP',total,0)) AS Total_CP,
						SUM(total) Total,
						SUM(total) Total_tot,
						1 AS tt
						
				FROM(
					SELECT  parcel_run.parcel_run_id,	
							company,
							date,
							run,
							code,
							parcel_run.contractor_id,
							route.route_id,
							parcel_job_route.type,
							SUM(is_redeemed_P) AS pickup,
							SUM(is_redeemed_D) AS delivery,
							SUM(is_redeemed_P)+ SUM(is_redeemed_D) AS total,
							operator.operator_id
					FROM parcel_run
					LEFT JOIN parcel_job_route
					ON parcel_job_route.parcel_run_id=parcel_run.parcel_run_id
					LEFT JOIN operator
					ON parcel_job_route.contractor_id=operator.operator_id
					LEFT JOIN route
					ON parcel_job_route.route_id=route.route_id
					WHERE parcel_run.date BETWEEN '$start_date' AND '$final_date'	
						AND parcel_job_route.contractor_id='$contr->contractor_id'
						AND parcel_job_route.dist_id='$dist_id'
						
					GROUP BY parcel_run.parcel_run_id, parcel_job_route.type
				) AS run
				GROUP BY parcel_run_id,route_id
				ORDeR BY company,date,run";
			//if($contr->contractor_id==21) echo nl2br($qry);
			if($start){
				$tab->startNewLine();
					$tab->addLineWithStyle("Sheet","sql_extra_head",3);
					$tab->addLineWithStyle("Documents","sql_extra_head",3);
					$tab->addLineWithStyle("Parcels","sql_extra_head",3);
					$tab->addLineWithStyle("Totals","sql_extra_head",2);
				$tab->stopNewLine();	
			}
			$tab->writeTableWithRes(query($qry),$start);
			$start=false;
			
			$tot_sheets += $tab->getSum("tt");
			$tot_pcd += $tab->getSum("Pickup_CD");
			$tot_dcd += $tab->getSum("Delivery_CD");
			$tot_tcd += $tab->getSum("Total_CD");
			$tot_pcp += $tab->getSum("Pickup_CP");
			$tot_pcd += $tab->getSum("Delivery_CP");
			$tot_tcp += $tab->getSum("Total_CP");
			$tot_tt += $tab->getSum("Total_tot");
			
			$tab->startNewLine();
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number",2);
				$tab->addLineWithStyle("Totals:","sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("tt",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Pickup_CD",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Delivery_CD",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Total_CD",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Pickup_CP",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Delivery_CP",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Total_CP",0,1),"sql_extra_line_number");
				$tab->addLineWithStyle($tab->getSum("Total_tot",0,1),"sql_extra_line_number");
			$tab->stopNewLine();		
	} //while contractor
	$tab->startNewLine();
		$tab->addLineWithStyle("&nbsp;","sql_extra_line_number",2);
		$tab->addLineWithStyle("Grand Totals:","sql_extra_line_number");
		$tab->addLineWithStyle($tot_sheets,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_pcd,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_dcd,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_tcd,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_pcp,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_pcd,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_tcp,"sql_extra_line_number");
		$tab->addLineWithStyle($tot_tt,"sql_extra_line_number");
	$tab->stopNewLine();		
	$tab->stopTable();
}
if($report=="ticket_redeemed"){
?>
	<h3>Tickets Redeemed from <? echo date("d F Y",strtotime($start_date))?> to <? echo date("d F Y",strtotime($final_date))?></h3>
<?

	$qry = "SELECT 	ID,
					rates.company 		AS Company,
					ROUND(rates.pickup+rates.delivery,2)	
										AS Contractor,
					ROUND(rates.distributor_p+rates.distributor_d,2)		AS 'Distributor',
					ROUND(rates.pickup+rates.delivery+rates.delivery,2)	
										AS 'Sub Total',
					ROUND(0.125*(rates.pickup+rates.delivery+rates.delivery),2)	
										AS GST,
					ROUND(1.125*(rates.pickup+rates.delivery+rates.delivery),2)	
										AS Total
					
			FROM (
				SELECT  
						parcel_job_route.dist_id		AS ID,
						company,
						SUM(red_rate_pickup*is_redeemed_P) AS pickup,
						SUM(is_redeemed_D*red_rate_deliv) AS delivery,
						SUM(is_redeemed_P* distr_payment_pickup) AS distributor_p,
						SUM(is_redeemed_D* distr_payment_deliv) AS distributor_d
				
				FROM parcel_run
				LEFT JOIN parcel_job_route
					ON parcel_run.parcel_run_id=parcel_job_route.parcel_run_id
				LEFT JOIN operator
					ON operator.operator_id=parcel_job_route.dist_id
				WHERE parcel_run.date BETWEEN '$start_date' AND '$final_date'
				%s
			) AS rates
			WHERE company IS NOT NULL
			";
			
		$qry_n = sprintf($qry," GROUP BY parcel_job_route.dist_id" );
		
		//echo nl2br($qry_n);
		$sub_total_field = 'Sub Total';
		
		$tab = new MySQLTable("rep_parcels.php",$qry_n,$nameI="report");
		$tab->collField["Contractor"]=true;
		$tab->collField["Distributor"]=true;
		$tab->collField["Sub Total"]=true;
		$tab->collField["GST"]=true;
		$tab->collField["Total"]=true;
		
		$tab->hiddenFields["ID"] = true;
		
		$tab->formatLine = true;
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=1;
		$tab->startTable();
			$tab->writeTable();
			$contr = $tab->getSum("Contractor");
			$dist = $tab->getSum("Distributor");
			$sub_total = $tab->getSum("Sub Total");
			$gst = $tab->getSum("GST");
			$total = $tab->getSum("Total");
			
			$tab->startNewLine();
				$tab->addLineWithStyle("Totals:","sql_extra_line_number");
				$tab->addLineWithStyle($contr,"sql_extra_line_number");
				$tab->addLineWithStyle($dist,"sql_extra_line_number");
				$tab->addLineWithStyle($sub_total,"sql_extra_line_number");
				$tab->addLineWithStyle($gst,"sql_extra_line_number");
				$tab->addLineWithStyle($total,"sql_extra_line_number");
			$tab->stopNewLine();		
		
		$tab->stopTable();
}
if($report=="ticket_redeemed2"){
?>
	<h3>Tickets Redeemed for <?=$year?></h3>
<?
	if($submit){
		$next_year = $year+1;
		$prev_year = $year-1;

		$qry = "
				SELECT 	ID,
						rates.company 		AS Company,
						SUM(IF(month='$year-04',amount,0)) AS April,
						SUM(IF(month='$year-05',amount,0)) AS May,
						SUM(IF(month='$year-06',amount,0)) AS June,
						SUM(IF(month='$year-07',amount,0)) AS July,
						SUM(IF(month='$year-08',amount,0)) AS August,
						SUM(IF(month='$year-09',amount,0)) AS September,
						SUM(IF(month='$year-10',amount,0)) AS October,
						SUM(IF(month='$year-11',amount,0)) AS November,
						SUM(IF(month='$year-12',amount,0)) AS December,
						SUM(IF(month='$year-01',amount,0)) AS January,
						SUM(IF(month='$year-02',amount,0)) AS Februrary,
						SUM(IF(month='$year-03',amount,0)) AS March,
						ROUND(SUM(IF(year='$year',amount,0)),2)
															  AS YTD,
										
						SUM(IF(year='$prev_year',amount,0)) AS 'YTD last yr',
						ROUND(SUM(IF(year='$prev_year',amount,0))*100/SUM(IF(year='$year',amount,0)),1) AS Difference
						
				FROM (
					SELECT  parcel_job_route.dist_id 		AS ID,
							DATE_FORMAT(date,'%Y-%m') AS month,
							YEAR(date) 				AS year,
							company,
							
							ROUND(
								SUM(
										red_rate_pickup*is_redeemed_P+is_redeemed_D*red_rate_deliv+
										is_redeemed_D* distr_payment_deliv+is_redeemed_P*distr_payment_pickup
								)
							,2)
										AS amount
					
					FROM parcel_run
					LEFT JOIN parcel_job_route
						ON parcel_run.parcel_run_id=parcel_job_route.parcel_run_id
					LEFT JOIN operator
						ON operator.operator_id=parcel_job_route.dist_id
					WHERE YEAR(date) ='$year'
					GROUP BY parcel_job_route.dist_id, month
				) AS rates
				WHERE company IS NOT NULL
				GROUP BY company
				";				
				
			//echo nl2br($qry);die();
			$qry_n = $qry;
			$YTD_field = 'YTD last yr';
			
			$tab = new MySQLTable("rep_parcels.php",$qry_n,$nameI="report");
			$tab->collField["April"]=true;
			$tab->collField["May"]=true;
			$tab->collField["June"]=true;
			$tab->collField["July"]=true;
			$tab->collField["August"]=true;
			$tab->collField["September"]=true;
			$tab->collField["October"]=true;
			$tab->collField["November"]=true;
			$tab->collField["December"]=true;
			$tab->collField["January"]=true;
			$tab->collField["February"]=true;
			$tab->collField["March"]=true;
			$tab->collField["YTD"]=true;
			$tab->collField["YTD last yr"]=true;
			$tab->collField["Difference"]=true;
			
					
			$tab->hiddenFields["ID"] = true;
			$tab->formatLine = true;
			
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->showRec=1;
			$tab->startTable();
				$tab->writeTable();
				
				$tab->startNewLine();
					$tab->addLineWithStyle("Totals:","sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("April"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("May"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("June"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("July"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("August"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("September"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("October"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("November"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("December"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("January"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("February"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("March"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("YTD"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("YTD last yr"),"sql_extra_line_number");
					$tab->addLineWithStyle($tab->getSum("Difference"),"sql_extra_line_number");
				$tab->stopNewLine();		
			$tab->stopTable();
		}
}

if($report=="invoice"){
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
			$qry = "SELECT DISTINCT dist_id FROM parcel_run WHERE $date_field BETWEEN '$start_date' AND '$final_date'";
			$res = query($qry);
			
			while($d = mysql_fetch_object($res)){
				$dist_ids[] = $d->dist_id;
			}
			
		}
	}

	foreach($dist_ids as $dist_id){
		$name = get("address","name","WHERE operator_id='$dist_id'");
		$first_name = get("address","first_name","WHERE operator_id='$dist_id'");
		$company = get("operator","company","WHERE operator_id='$dist_id'");
		$address = get("address","address","WHERE operator_id='$dist_id'");
		$city = get("address","city","WHERE operator_id='$dist_id'");
		$gst_num = get("address","gst_num","WHERE operator_id='$dist_id'");
		
		
		
		
		$invoice_no=get("parcel_invoice","invoice_no","WHERE date LIKE '$month%' AND dist_id='$dist_id'");
		if(!$invoice_no){
			$invoice_no = "TBA";
		}
		
		
	?>
		<h1 style="text-align:left">
			<img style="float:right;" src="images/coural_logo.jpg" />
			TICKET REDEMPTION SUMMARY REPORT <br />
			AND BUYER CREATED TAX INVOICE (IRD approved)
		</h1>
			
		
		<table style="margin-top:1em;margin-bottom:1em;">
			<tr>
				<td><?=$target?>:</td>
				<td><?=$red_month?></td>
			</tr>
			
			<tr>
				<td>Printed:</td>
				<td><?=date("Y-m-d")?></td>
			</tr>
			<tr>
				<td>Invoice #:</td>
				<td><strong><?=$invoice_no?></strong></td>
			</tr>
		</table>
		
		<div style="margin-bottom:4em;">
			<table style="position: absolute; left: 30em; border: solid 3px; width: 20em;"> 
				<tr>
					<td>
						Rural Couriers Society Limited<br />
						P O Box 1233<br />
						Palmerston North<br />
						<br />
						<br />
						<br />
						GST#:&nbsp; 24 992 802
					</td>
				</tr>
			</table>
			<table style="border: solid 3px; width: 20em;">
				<tr>
					<td>
						<?=$name?>, <?=$first_name?><br />
						<?=$company?><br />
						<?=$address?><br />
						<?=$city?><br />
						<br />
						<br />				
						GST#:&nbsp; <?=$gst_num?><br />
					</td>
			</table>
		</div>
	<?	
	
	
		$qry = "SELECT parcel_job_route.contractor_id,
						CONCAT(name,', ',first_name) AS name,
						name AS last_name,
						first_name,
						company
				FROM parcel_job_route
				LEFT JOIN parcel_run
				ON parcel_job_route.parcel_run_id=parcel_run.parcel_run_id
				LEFT JOIN operator
				ON operator.operator_id=parcel_job_route.contractor_id
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				WHERE parcel_job_route.dist_id='$dist_id'
					AND  $date_field BETWEEN '$start_date' AND '$final_date'
				GROUP BY parcel_job_route.contractor_id 
				ORDER BY name";
		$res_contr = query($qry,0);
		
		$tab = new MySQLTable("rep_parcels.php","",$nameI="report");
		$tab->cssSQLUnEvenLine 	= "sqltabunevenline_white";
		$tab->cssSQLTable = "sqltable_9";
		//$tab->sumField = "ID";
		//$tab->sumGTField = "Company";
		//$tab->hasSum = true;
		$tab->hiddenFields["ID"] = true;
		$tab->hiddenFields["Qty_Pickup"] = true;
		$tab->hiddenFields["Qty_Deliv"] = true;
		
		$tab->formatLine = true;
		
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=1;
		
		$tab->collField["Qty_Pickup"]=true;
		
		$tab->collField["Total"]=true;
		$tab->collField["Total incl. GST"]=true;
		
		
		$tab->startTable();
		
		$tab->startNewLine();
			$tab->addLineWithStyle("&nbsp;","sql_extra_head",1);
			$tab->addLineWithStyle("Pickup","sql_extra_head",3);
			$tab->addLineWithStyle("Delivery","sql_extra_head",3);
			$tab->addLineWithStyle("Totals","sql_extra_head",2);
		$tab->stopNewLine();	
		
		
		
		$start=true;
		
		$tot_c_qty = 0;
		$tot_c_total = 0;
		$tot_c_total_gst  = 0;
		
		while($contractor = mysql_fetch_object($res_contr)){
	
			$qry = "SELECT IF(Type='CD',CONCAT('<strong>Documents</strong>',' ',code),
								IF(Type='CP', CONCAT('<strong>Parcels</strong>',' ',code),CONCAT('<strong>Signature</strong>',' ',code))
							)
								AS 'Ticket Type',
								
						   Qty_Pickup AS Qty,
						   ROUND(red_rate_pickup,4) AS 'Each',
						   @pu := ROUND(Qty_Pickup*red_rate_pickup,2) AS Value,
						   Qty_Deliv AS Qty,
						   ROUND(red_rate_deliv,4) AS 'Each',
						   @de := ROUND(Qty_Deliv*red_rate_deliv,2) AS Value,
						   ROUND(@pu+@de,2) AS Total,
						   ROUND((@pu+@de)*1.125,2) AS 'Total incl. GST',
						   Qty_Pickup,
						   Qty_Deliv
						   
					FROM (
					
						SELECT
						  	parcel_job_route.type AS type,
							route.code,
						  	COUNT(DISTINCT `parcel_job_route`.`ticket_id`) AS Qty, 
						  	COUNT(DISTINCT IF(is_redeemed_P=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Pickup, 
						  	COUNT(DISTINCT IF(is_redeemed_D=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Deliv, 
						 	parcel_job_route.red_rate_pickup  AS Pickup,
						 	parcel_job_route.red_rate_deliv   AS Delivery,
						 	parcel_job_route.distr_payment_pickup  AS dist_pickup,
						 	parcel_job_route.distr_payment_deliv AS dist_delivery,
						  	parcel_job_route.red_rate_pickup,
							parcel_job_route.red_rate_deliv
						  
						FROM
						  `parcel_job_route` 
						  LEFT JOIN `parcel_run` 
							   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
						  LEFT JOIN `parcel_job` 
							   ON `parcel_job_route`.`job_id` = `parcel_job`.`job_id` 
						  LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
						  WHERE parcel_job_route.contractor_id='$contractor->contractor_id'
								AND  parcel_job_route.dist_id='$dist_id'
								AND  $date_field BETWEEN '$start_date' AND '$final_date'
								AND route.is_hidden<>'Y'
						  GROUP BY parcel_job_route.contractor_id,parcel_job_route.route_id,parcel_job_route.type
					) AS job";
				//echo nl2br($qry);
			
			$res = query($qry);
			if($start){
				$tab->writeTableHeader($res,false);
			}
			$start=false;	
			if(mysql_num_rows($res)>0){
			
				$tab->startNewLine();
					if(trim($contractor->last_name)."-".trim($contractor->first_name) != $contractor->company)
						$tab->addLineWithStyle($contractor->name." / ".$contractor->company,"sql_extra_head_white");
					else
						$tab->addLineWithStyle($contractor->name,"sql_extra_head_white");
				$tab->stopNewLine();
				
				$tab->writeTableWithRes($res,false,false);
				
				$qty = $tab->getSum("Qty_Pickup",2);
				$total = $tab->getSum("Total",2);
				$total_gst = $tab->getSum("Total incl. GST",2);
				
				//$qty = array_sum($tab->collFieldVal["Qty_Pickup"]);
				//$total = array_sum($tab->collFieldVal["Total"]);
				//$total_gst = array_sum($tab->collFieldVal["Total incl. GST"]);
				
				$tot_qty += $qty;
				$tot_total += $total;
				$tot_total_gst += $total_gst;
				
				
				$tot_c_qty += $qty;
				$tot_c_total += $total;
				$tot_c_total_gst += $total_gst;
				
				$tab->collFieldVal["Qty_Pickup"]=array();
				$tab->collFieldVal["Total"]=array();
				$tab->collFieldVal["Total incl. GST"]=array();
				
				$tab->startNewLine();
					
					$tab->addLineWithStyle("Sub Totals:","sql_extra_line_number_grey");
					$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey",6);
					$tab->addLineWithStyle(number_format($total,2),"sql_extra_line_number_grey");
					$tab->addLineWithStyle(number_format($total_gst,2),"sql_extra_line_number_grey");
				$tab->stopNewLine();
				
				
			}
		} // while contractor
		
		if($tot_qty){
			$tab->startNewLine();
				
				$tab->addLineWithStyle("Total Contractors:","sql_extra_line_number_grey");
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey",6);
				$tab->addLineWithStyle(number_format($tot_c_total,2),"sql_extra_line_number_grey");
				$tab->addLineWithStyle(number_format($tot_c_total_gst,2),"sql_extra_line_number_grey");
			$tab->stopNewLine();
		}
		
		$qry = "SELECT IF(Type='CD','<strong>Documents</strong>',
								IF(Type='CP', '<strong>Parcels</strong>','<strong>Signature</strong>')
							)
								AS 'Ticket Type',
						   Qty_Pickup AS Qty,
						   ROUND(distr_payment_pickup,4) AS 'Each',
						   @pu := ROUND(Qty_Pickup*distr_payment_pickup,2) AS Value,
						   Qty_Deliv AS Qty,
						   ROUND(distr_payment_deliv,4) AS 'Each',
						   @de := ROUND(Qty_Deliv*distr_payment_deliv,2) AS Value,
						   ROUND(@pu+@de,2) AS Total,
						   ROUND((@pu+@de)*1.125,2) AS 'Total incl. GST',
						   Qty_Pickup,
						   Qty_Deliv
						   
						   
					FROM (
					
						SELECT
						  parcel_job_route.type AS type,
						  	COUNT(DISTINCT `parcel_job_route`.`ticket_id`) AS Qty, 
						  	COUNT(DISTINCT IF(is_redeemed_P=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Pickup, 
						  	COUNT(DISTINCT IF(is_redeemed_D=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Deliv, 
						 	parcel_job_route.red_rate_pickup  AS Pickup,
						 	parcel_job_route.red_rate_deliv   AS Delivery,
						 	parcel_job_route.distr_payment_pickup  AS dist_pickup,
						 	parcel_job_route.distr_payment_deliv AS dist_delivery,
						  	parcel_job_route.distr_payment_pickup,
							parcel_job_route.distr_payment_deliv
						  
						FROM
						  `parcel_job_route` 
						  LEFT JOIN `parcel_run` 
							   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
						  LEFT JOIN `parcel_job` 
							   ON `parcel_job_route`.`job_id` = `parcel_job`.`job_id` 
						   LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
						  WHERE parcel_job_route.dist_id='$dist_id'
							  AND  date BETWEEN '$start_date' AND '$final_date'
							  AND is_hidden<>'Y'
						  GROUP BY parcel_job_route.dist_id,parcel_job_route.type
					) AS job";
		
		$tab->startNewLine();
			$tab->addLineWithStyle("Distributor","sql_extra_head_white");
		$tab->stopNewLine();
		$tab->writeSQLTableElement($qry,$start);
		
		if(is_array($tab->collFieldVal["Qty_Pickup"]))
			$qty = array_sum($tab->collFieldVal["Qty_Pickup"]);
			
		if(is_array($tab->collFieldVal["Total"]))
			$total = array_sum($tab->collFieldVal["Total"]);
		if(is_array($tab->collFieldVal["Total incl. GST"]))
			$total_gst = array_sum($tab->collFieldVal["Total incl. GST"]);
		if($total_gst>0){
			$tab->startNewLine();
				
				$tab->addLineWithStyle("Total Distributor:","sql_extra_line_number_grey");
				
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey",6);
				$tab->addLineWithStyle(number_format($total,2),"sql_extra_line_number_grey");
				$tab->addLineWithStyle(number_format($total_gst,2),"sql_extra_line_number_grey");
			$tab->stopNewLine();
			
			$tab->startNewLine();
				
				$tab->addLineWithStyle("Grand Total:","sql_extra_line_number_grey");
				
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey",6);
				$tab->addLineWithStyle(number_format($total+$tot_total,2),"sql_extra_line_number_grey");
				$tab->addLineWithStyle(number_format($total_gst+$tot_total_gst,2),"sql_extra_line_number_grey");
			$tab->stopNewLine();
		}
		$tab->stopTable();
		/*?>
			<div class="pagebreak"> &nbsp;</div>
		<?*/
	} // foreach dist
}

include $dir."includes/mysql.class.php";
include $dir."includes/fpdf/fpdf.php";
define('FPDF_FONTPATH',$dir.'includes/fpdf/font/');
require_once $dir."includes/MySQLPDFTable.php";

if($report=="invoice_send"){
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
			$qry = "SELECT DISTINCT dist_id FROM parcel_run WHERE $date_field BETWEEN '$start_date' AND '$final_date'";
			$res = query($qry);
			
			while($d = mysql_fetch_object($res)){
				$dist_ids[] = $d->dist_id;
			}
			
		}
	}


	foreach($dist_ids as $dist_id){
		$name = get("address","name","WHERE operator_id='$dist_id'");
		$first_name = get("address","first_name","WHERE operator_id='$dist_id'");
		$company = get("operator","company","WHERE operator_id='$dist_id'");
		$address = get("address","address","WHERE operator_id='$dist_id'");
		$city = get("address","city","WHERE operator_id='$dist_id'");
		$gst_num = get("address","gst_num","WHERE operator_id='$dist_id'");
		
		$tab = new MySQLPDFTable($MYSQL,"p");
		$tab->hasFieldAliases = true;
		$tab->hasDivider=false;
		$tab->AddPage();
		
		$header = array();
		$header["Ticket Type"] = "Ticket Type";
		$header["Qty_Pickup"] = "Qty";
		$header["Each_Pickup"] = "Each";
		$header["Value_Pickup"] = "Value";
		$header["Qty_Delivery"] = "Qty";
		$header["Each_Delivery"] = "Each";
		$header["Value_Delivery"] = "Value";
		$header["Total"] = "Total";
		$header["Total incl. GST"] = "Total incl. GST";

		$width = array();
		$width["Ticket Type"] = 60;
		$width["Qty_Pickup"] = 10;
		$width["Each_Pickup"] = 10;
		$width["Value_Pickup"] = 10;
		$width["Qty_Delivery"] = 10;
		$width["Each_Delivery"] = 10;
		$width["Value_Delivery"] = 10;
		$width["Total"] = 20;
		$width["Total incl. GST"] = 20;



		$txt = "TICKET REDEMPTION SUMMARY REPORT\nAND BUYER CREATED TAX INVOICE (IRD approved)";
		$tab->SetDrawColor(0,80,180);
		$tab->SetFillColor(230,230,0);
		$tab->SetTextColor(0,0,0);
		$tab->SetFont('Times','',12);
		
		$tab->MultiCell(0,5,$txt);
		
		//Logo
	    $tab->Image('images/coural_logo.jpg',160,8,33,71,38);
		//$tab->Image('images/coural_logo_large.jpg',160,8,33,71,38);
	
		$tab->Ln();
		$invoice_no=get("parcel_invoice","invoice_no","WHERE date LIKE '$month%' AND dist_id='$dist_id'");
		if(!$invoice_no){
			$invoice_no = "TBA";
		}
		$font_size=7;
		$tab->StartLine(7,255,255,255);
			$tab->WriteLine($target,'L',$font_size,40);
			$tab->WriteLine($red_month,'L',$font_size,40);
		$tab->StopLine();
		
		$tab->StartLine(7,255,255,255);
			$tab->WriteLine("Printed:",'L',$font_size,40);
			$tab->WriteLine(date("Y-m-d"),'L',$font_size,40);
		$tab->StopLine();
		
		$tab->StartLine(7,255,255,255);
			$tab->WriteLine("Invoice #:",'L',$font_size,40);
			$tab->WriteLine($invoice_no,'L',$font_size,40);
		$tab->StopLine();
		
		$tab->Ln();
		
		$y = $tab->GetY();
		$tab->setLineWidth(.3);
		$tab->setDrawColor(0,0,0);
		$txt = "$name, $first_name\n$company\n$address\n$city\nGST#: $gst_num";
		$tab->MultiCell(50,7,$txt,1,'L',true);

		
		$tab->SetXY(80,$y);
		$txt = "Rural Couriers Society Limited\nP O Box 1233\nPalmerston North\n\nGST#: 24 992 802";
		$tab->MultiCell(50,7,$txt,1);
		$tab->Ln();
		$tab->Ln();

		$qry = "SELECT parcel_job_route.contractor_id,
						CONCAT(name,', ',first_name) AS name,
						name AS last_name,
						first_name,
						company
				FROM parcel_job_route
				LEFT JOIN parcel_run
				ON parcel_job_route.parcel_run_id=parcel_run.parcel_run_id
				LEFT JOIN operator
				ON operator.operator_id=parcel_job_route.contractor_id
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				WHERE parcel_run.dist_id='$dist_id'
					AND  $date_field BETWEEN '$start_date' AND '$final_date'
				GROUP BY parcel_job_route.contractor_id 
				ORDER BY name";
		//echo nl2br($qry);
		$res_contr = query($qry,0);
		
		//$tab->hiddenFields["ID"] = true;
		//$tab->hiddenFields["Qty_Pickup"] = true;
		//$tab->hiddenFields["Qty_Deliv"] = true;
		
		$tab->collField["Qty_Pickup"]=true;		
		$tab->collField["Total"]=true;
		$tab->collField["Total incl. GST"]=true;
		
		$tab->StartLine(7,255,0,0);
			$tab->WriteLine("",'C',$font_size,$width["Ticket Type"]);
			$tab->WriteLine("Pickup",'C',$font_size,30);
			$tab->WriteLine("Delivery",'C',$font_size,30);
			$tab->WriteLine("Totals",'C',$font_size,40);
		$tab->StopLine();	
		
		
		
		$start=true;
		
		$tot_c_qty = 0;
		$tot_c_total = 0;
		$tot_c_total_gst  = 0;
		
		while($contractor = mysql_fetch_object($res_contr)){
	
			$qry = "SELECT IF(Type='CD',CONCAT('Documents',' / ',code),
								IF(Type='CP', CONCAT('Parcels',' / ',code),CONCAT('Signature',' / ',code))
							)
								AS 'Ticket Type',
								
						   Qty_Pickup AS Qty_Pickup,
						   ROUND(red_rate_pickup,4) AS 'Each_Pickup',
						   @pu := ROUND(Qty_Pickup*red_rate_pickup,2) AS Value_Pickup,
						   Qty_Deliv AS Qty_Delivery,
						   ROUND(red_rate_deliv,4) AS 'Each_Delivery',
						   @de := ROUND(Qty_Deliv*red_rate_deliv,2) AS Value_Delivery,
						   ROUND(@pu+@de,2) AS Total,
						   ROUND((@pu+@de)*1.125,2) AS 'Total incl. GST'
						   
					FROM (
					
						SELECT
						  	parcel_job_route.type AS type,
							route.code,
						  	COUNT(DISTINCT `parcel_job_route`.`ticket_id`) AS Qty, 
						  	COUNT(DISTINCT IF(is_redeemed_P=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Pickup, 
						  	COUNT(DISTINCT IF(is_redeemed_D=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Deliv, 
						 	parcel_job_route.red_rate_pickup  AS Pickup,
						 	parcel_job_route.red_rate_deliv   AS Delivery,
						 	parcel_job_route.distr_payment_pickup  AS dist_pickup,
						 	parcel_job_route.distr_payment_deliv AS dist_delivery,
						  	parcel_job_route.red_rate_pickup,
							parcel_job_route.red_rate_deliv
						  
						FROM
						  `parcel_job_route` 
						  LEFT JOIN `parcel_run` 
							   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
						  LEFT JOIN `parcel_job` 
							   ON `parcel_job_route`.`job_id` = `parcel_job`.`job_id` 
						   LEFT JOIN route
							   ON route.route_id=parcel_job_route.route_id
						  WHERE parcel_job_route.contractor_id='$contractor->contractor_id'
								AND  $date_field BETWEEN '$start_date' AND '$final_date'
								AND is_hidden<>'Y'
						  GROUP BY parcel_job_route.contractor_id,parcel_job_route.type
					) AS job";
				//echo nl2br($qry);die();
			
			$res = query($qry);
			if($start){
				$tab->WriteHeader($header,$width);
			}
			$start=false;	
			if(mysql_num_rows($res)>0){
			
				$tab->StartLine(7,255,255,255);
					if(trim($contractor->last_name)."-".trim($contractor->first_name) != $contractor->company)
						$tab->WriteLine($contractor->name." / ".$contractor->company,'L',$font_size,40);
					else
						$tab->WriteLine($contractor->name,'L',$font_size,40);
				$tab->StopLine();
				
				$data = $tab->LoadData($qry);
				
				$tab->WriteTable($header,$data,$width);
								
				$qty = $tab->getSum("Qty_Pickup",2);
				$total = $tab->getSum("Total",2);
				$total_gst = $tab->getSum("Total incl. GST",2);
				
				$tot_qty += $qty;
				$tot_total += $total;
				$tot_total_gst += $total_gst;
				
				
				$tot_c_qty += $qty;
				$tot_c_total += $total;
				$tot_c_total_gst += $total_gst;
				
				$tab->collFieldVal["Qty_Pickup"]=array();
				$tab->collFieldVal["Total"]=array();
				$tab->collFieldVal["Total incl. GST"]=array();
				
				$tab->StartLine(7,200,200,200);
					$tab->WriteLine("Sub Totals:",'L',$font_size,120);
					//$tab->WriteLine("",'L',$font_size,40);
					$tab->WriteLine(number_format($total,2),'R',$font_size,20);
					$tab->WriteLine(number_format($total_gst,2),'R',$font_size,20);
				$tab->StopLine();	
				
			}
		} // while contractor
		
		if($tot_qty){
			$tab->StartLine(7);
					$tab->WriteLine("Total Contractors:",'L',$font_size,120,200,200,200);
					//$tab->WriteLine("",'L',$font_size,40);
					$tab->WriteLine(number_format($tot_c_total,2),'R',$font_size,20);
					$tab->WriteLine(number_format($tot_c_total_gst,2),'R',$font_size,20);
			$tab->StopLine();	
		}
		
		$qry = "SELECT IF(Type='CD','Documents',
								IF(Type='CP', 'Parcels','Signature')
							)
								AS 'Ticket Type',
						   ROUND(distr_payment_pickup,4) AS 'Each_Pickup',
						   @pu := ROUND(Qty_Pickup*distr_payment_pickup,2) AS Value_Pickup,
						   Qty_Pickup AS Qty_Pickup,
						   ROUND(distr_payment_deliv,4) AS 'Each_Deliv',
						   @de := ROUND(Qty_Deliv*distr_payment_deliv,2) AS Value_Deliv,
						   Qty_Deliv AS Qty_Deliv,
						   ROUND(@pu+@de,2) AS Total,
						   ROUND((@pu+@de)*1.125,2) AS 'Total incl. GST'
						   
						   
					FROM (
					
						SELECT
						  parcel_job_route.type AS type,
						  	COUNT(DISTINCT `parcel_job_route`.`ticket_id`) AS Qty, 
						  	COUNT(DISTINCT IF(is_redeemed_P=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Pickup, 
						  	COUNT(DISTINCT IF(is_redeemed_D=1,`parcel_job_route`.`ticket_id`,NULL)) AS Qty_Deliv, 
						 	parcel_job_route.red_rate_pickup  AS Pickup,
						 	parcel_job_route.red_rate_deliv   AS Delivery,
						 	parcel_job_route.distr_payment_pickup  AS dist_pickup,
						 	parcel_job_route.distr_payment_deliv AS dist_delivery,
						  	parcel_job_route.distr_payment_pickup,
							parcel_job_route.distr_payment_deliv
						  
						FROM
						  `parcel_job_route` 
						  LEFT JOIN `parcel_run` 
							   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
						  LEFT JOIN `parcel_job` 
							   ON `parcel_job_route`.`job_id` = `parcel_job`.`job_id` 
						  LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
						  WHERE parcel_job_route.dist_id='$dist_id'
							  AND  date BETWEEN '$start_date' AND '$final_date'
							  AND is_hidden<>'Y'
						  GROUP BY parcel_job_route.dist_id,parcel_job_route.type
					) AS job";
		
		$tab->StartLine(7,255,255,255);
				$tab->WriteLine("Distributor",'L',$font_size,40);
		$tab->StopLine();	
		
		$data = $tab->LoadData($qry);
				
		$tab->WriteTable($header,$data,$width);
				
		if(is_array($tab->collFieldVal["Qty_Pickup"]))
			$qty = array_sum($tab->collFieldVal["Qty_Pickup"]);
			
		if(is_array($tab->collFieldVal["Total"]))
			$total = array_sum($tab->collFieldVal["Total"]);
		if(is_array($tab->collFieldVal["Total incl. GST"]))
			$total_gst = array_sum($tab->collFieldVal["Total incl. GST"]);
		if($total_gst>0){
			$tab->StartLine(7,200,200,200);
					$tab->WriteLine("Total Distributor:",'L',$font_size,120);
					//$tab->WriteLine("",'L',$font_size,40);
					$tab->WriteLine(number_format($total,2),'R',$font_size,20);
					$tab->WriteLine(number_format($total_gst,2),'R',$font_size,20);
			$tab->StopLine();	
		
			$tab->StartLine(7,200,200,200);
					$tab->WriteLine("Grand Total:",'L',$font_size,120);
					//$tab->WriteLine("",'L',$font_size,40);
					$tab->WriteLine(number_format($total+$tot_total,2),'R',$font_size,20);
					$tab->WriteLine(number_format($total_gst+$tot_total_gst,2),'R',$font_size,20);
			$tab->StopLine();	
		}
		
		$now=date("Y_m_d_h_i_s");
		$dir = "temp_payout";
		$fn = "parcel_payout_".$company."_$now.pdf";

		$tab->Output($dir.'/'.$fn);
		$send_operator_mail("PARCEL PAYOUT","temp_payout",$fn,$dist_id);
		
		//$qry = "UPDATE parcel_job SET is_pay_sent=1 WHERE job_no IN ($jobs)";
		//query($qry);
		
		$qry = "INSERT INTO send_report
			SET jobs='$jobs',
			dist_id='$dist_id',
			start_date = '$start_date',
			final_date = '$final_date',
			type='$report'";
		query($qry);

	} // foreach dist
}

if($report=="tickets_received"){
	$qry = "SELECT 	name AS Branch,
					supplier AS  Supplier,
					date AS Date,
					start AS Start,
					end	AS End,
					type AS Type,
					end-start+1 AS Qty
			 
			FROM parcel_th_receipt
			LEFT JOIN parcel_ticket_th
			ON parcel_th_receipt.parcel_th_receipt_id = parcel_ticket_th.parcel_th_receipt_id
			LEFT JOIN branch
			ON branch.branch_id = parcel_th_receipt.branch_id
			WHERE date BETWEEN '$start_date' AND '$final_date'
			ORDER BY date,type";
			
	$tab = new MySQLTable("rep_parcels.php",$qry,$nameI="report");
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasAddButton=false;
	$tab->showRec=1;
	
	$tab->startTable();
		$tab->writeTable();
	$tab->stopTable();
	
}
?>
