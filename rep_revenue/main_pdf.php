<?
	include "header_pdf.php";
	include "actions.php";
	include "functions.php";
	
	
	$today = date("Y-m-d");
	
	if($report=="rep_cirpay_by_dist"){
		
		$tab=new MySQLPDFTable($MYSQL,'p');
		
		$tab->collField["Quantity"]=true;
		$tab->collField["Qty Bdl"]=true;
		$tab->collField["Dist Amt"]=true;
		$tab->collField["S/Dist Amt"]=true;
		$tab->collField["Cont Amt"]=true;
		$tab->collField["Bdl Amt"]=true;
		$tab->collField["Total"]=true;
		$tab->collField["Total (incl. GST)"]=true;
		
		$tab->SetFont('Helvetica','B',6);
		$header=array('Job','Publication','Weight','Quantity','Qty Bdl','Dist-Rate','S/Dist-Rate','Cont-Rate','FF','Dist Amt','S/Dist Amt','Cont Amt','Bdl Amt','Total','Total (incl. GST)');
		$width = array('Job'=>10,
						'Publication'=>40,
						'Weight'=>10,
						'Quantity'=>10,
						'Qty Bdl'=>10,
						'Dist-Rate'=>10,
						'S/Dist-Rate'=>10,
						'Cont-Rate'=>10,
						'FF'=>10,
						'Dist Amt'=>10,
						'S/Dist Amt'=>10,
						'Cont Amt'=>10,
						'Bdl Amt'=>10,
						'Total'=>20,
						'Total (incl. GST)'=>20);				
		$maxw = array_sum($width);
		
		$maxw=190;
		$dist_list = array();
		if($dist_id) {
			$dist_list[]=$dist_id;
		}
		else{
			$qry = "SELECT operator.operator_id AS dist_id
					FROM operator
					LEFT JOIN route_aff
					ON route_aff.dist_id=operator.operator_id
					LEFT JOIN route
					ON route.route_id=route_aff.route_id
					WHERE is_dist='Y'
						AND '$today' BETWEEN app_date AND stop_date
					GROUP BY company
					ORDER BY seq_region,seq_area,seq_code";
			$res = query($qry);
			while($dist = mysql_fetch_object($res)){
				$dist_list[]=$dist->dist_id;
			}
		}
		
		
		
		foreach($dist_list as $dist){
			$tab->AddPage();
			
			$tab->StartLine(6);
				$tab->WriteLine("COURAL (RURAL COURIERS SOCIETY LTD)",'L',6,$maxw);
			$tab->StopLine();
			$tab->StartLine(6);
				$tab->WriteLine("PO BOX 1233",'L',6,$maxw);
			$tab->StopLine();
			/*$tab->StartLine(6);
				$tab->WriteLine("PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618",'L',6,$maxw);
			$tab->StopLine();*/
			$tab->StartLine(6);
				$tab->WriteLine($title,'L',6,$maxw);
			$tab->StopLine();
			$tab->StartLine(6);
				$tab->WriteLine($title,'L',6,$maxw);
			$tab->StopLine("GST NO: $gst_no");
			
			$tab->WriteHeader($header,$width);
		
			$dist_name = get("operator","company","WHERE operator_id='$dist'");
			$dist_email = get("address","email","WHERE operator_id='$dist'");
			$gst_no	   = get("address","gst_num","WHERE operator_id='$dist'");
			$title = "Distributor: $dist_name, $month_show/$year";
			 
			 $group 	= "job.job_no";
			 $dist_comp	= get("operator","company","WHERE operator_id=$dist");
		
			$qry = "SELECT job.job_no					AS 'Job',
						   job.publication				AS 'Publication',
						    ROUND(job.weight,0)					AS Weight,		
						   SUM(
								IF(
									job_route.dest_type<>'bundles' ,
									amount,0)
								)
														AS 'Quantity',	
						  			   
						   SUM(
								IF(
									job_route.dest_type='bundles',
									amount,0)
								)
														AS 'Qty Bdl',											
														
						   round(job.dist_rate,4)   	AS 'Dist-Rate',				   
						   round(job.subdist_rate,4)    AS 'S/Dist-Rate',
						   round(job.contr_rate+job.folding_fee,4)      AS 'Cont-Rate',
						   round(job.folding_fee,4)      AS 'FF',
						   round(
						   		SUM(
									IF( job_route.dest_type<>'bundles' ,
										job_route.amount,
										0
									)
								)*
						   		job.dist_rate,2)     		AS 'Dist Amt' ,   
						   round(	
						   		SUM(
									IF(job_route.dest_type<>'bundles' ,
										job_route.amount,
										0
									)
								)*
						   job.subdist_rate,2)     		AS 'S/Dist Amt' ,   
						   round(SUM(
									IF(job_route.dest_type<>'bundles' ,
										job_route.amount,
										0
									))*
						   (job.contr_rate+folding_fee),2)     		AS 'Cont Amt' ,   						   

						   round(SUM(
								IF(	job_route.dest_type='bundles' ,
									job_route.amount*job_route.bundle_price,
									0
								)
								)
							,2)     		
						   								AS 'Bdl Amt',   
						   
						   round(
						   		SUM(
									IF(job_route.dest_type<>'bundles' ,
										job_route.amount,
										0)
								)*
									
						   (job.dist_rate+
							job.subdist_rate+
							job.contr_rate+job.folding_fee)
							+SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)     		AS 'Total', 
							  
							ROUND(".(1+$GST_CIRCULAR)."*(
										SUM(
											IF(job_route.dest_type<>'bundles' ,
												job_route.amount,
												0
												)
											)*
						   (job.dist_rate+
							job.subdist_rate+
							job.contr_rate+job.folding_fee)
							+SUM(
								IF(job_route.dest_type='bundles' ,
									job_route.amount*job_route.bundle_price,
									0
								)
							)),2)
									AS 'Total (incl. GST)'								
					FROM job
					LEFT JOIN job_route
					ON job_route.job_id = job.job_id
					LEFT JOIN route
					ON job_route.route_id = route.route_id
					WHERE 	(
								(year(delivery_date)='$year' AND month(delivery_date)='$month')
								OR
								(year(delivery_date)='$year1' AND month(delivery_date)='$month1')
							)
						AND job_route.dist_id=$dist
						#AND job.finished<>'Y'
						AND job.cancelled<>'Y'			
						AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
					GROUP BY $group
					#ORDER BY $add_sort";
			//echo nl2br($qry);
			
				
				//$tab->noRepFields["Publication"]=$show_pub;
				
				
				$data = $tab->LoadData($qry);
							
				$tab->WriteTable($header,$data,$width);
				
				$qty = number_format($tab->getSum("Quantity"),0,'.','');
				$qty_bdl = number_format($tab->getSum("Qty Bdl"),0,'.','');
				$dist_amt = number_format($tab->getSum("Dist Amt"),2,'.','');
				$sdist_amt = number_format($tab->getSum("S/Dist Amt"),2,'.','');
				$contr_amt = number_format($tab->getSum("Cont Amt"),2,'.','');
				$bdl_amt = number_format($tab->getSum("Bdl Amt"),2,'.','');
				$tot_pay = number_format($tab->getSum("Total"),2,'.','');
				$tot_pay_gst = number_format($tab->getSum("Total (incl. GST)"),2,'.','');
				
				$tab->StartLine(6);
				
					$tab->WriteLine("",'L',6,$width["Job"]);
					$tab->WriteLine("",'L',6,$width["Publication"]);
					$tab->WriteLine("Totals.:",'L',6,$width["Weight"]);
					
					$tab->WriteLine($qty,'L',6,$width["Quantity"]);
					$tab->WriteLine($qty_bdl,'L',6,$width["Qty Bdl"]);
					
					$tab->WriteLine("",'L',6,$width["Dist-Rate"]);
					$tab->WriteLine("",'L',6,$width["S/Dist-Rate"]);
					$tab->WriteLine("",'L',6,$width["Cont-Rate"]);
					$tab->WriteLine("",'L',6,$width["FF"]);
					
					$tab->WriteLine($dist_amt,'L',6,$width["Dist Amt"]);
					$tab->WriteLine($sdist_amt,'L',6,$width["S/Dist Amt"]);
					$tab->WriteLine($contr_amt,'L',6,$width["Cont Amt"]);

					$tab->WriteLine($bdl_amt,'L',6,$width["Bdl Amt"]);
					$tab->WriteLine($tot_pay,'L',6,$width["Total"]);
					$tab->WriteLine($tot_pay_gst,'L',6,$width["Total (incl. GST)"]);
						
				$tab->StopLine();
				$tab->StartLine(6);
					$tab->WriteLine($comment2,'L',6,$maxw);
				$tab->StopLine();
				
				$tab->StartLine(6);
					$message = "BUYER GEN. INV.-IRD APPR. ";//   GST NO: 24-992-802    DATE: ".date('d/m/Y');
					$tab->WriteLine($message,'L',6,190);
				$tab->StopLine();
				
				$tab->refreshCollFields();
				
				$now=date("Y_m_d_h_i_s");
		
				$fn = "payout_".$dist_name."_$now.pdf";
				
				$tab->Output($fn);
				$dir = $SEND_OUTPUT_DIR."temp_payout";
				send_operator_mail("DELIVERY DIST_PAYOUT",$dir,$fn,$dist);
		}
	}
	
	
	if($report=="weekly"){
		//send_operator_mail("DELIVERY INSTRUCTIONS",$fn,$do->doff); die();
		if($date_start&&$date_final){
		
			$date_show_start 	= date("jS M Y",strtotime($date_start));
			$date_show_end 		= date("jS M Y",strtotime($date_final));
			$company=87;
			if($company!='All' && $company) $where_add = " AND operator_id='$company'";
			
			$qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y' $where_add";
			
			$res_dist = query($qry_dist);
			
			
			while($dist = mysql_fetch_object($res_dist)){
				
				
				$dist_id=$dist->operator_id;
				$company=get("operator","company","WHERE operator_id='$dist_id'");
				
				$qry_dos = "SELECT DISTINCT CONCAT(name,'_',first_name) AS name,company,doff 
							 FROM job 
							 LEFT JOIN job_route
							 ON job.job_id=job_route.job_id
							 LEFT JOIN route
							 ON route.route_id=job_route.route_id
							 LEFT JOIN operator
							 ON operator.operator_id=dropoff_id
							 LEFT JOIN address
							 ON address.operator_id=operator.operator_id
							 WHERE delivery_date>='$date_start'
								AND delivery_date<='$date_final'
								#AND job.finished<>'Y'
								AND job_route.dist_id='$dist_id'
							ORDER BY seq_region,seq_area,seq_code";
				//echo nl2br($qry_jobs); 
				$res_dos = query($qry_dos,0);
				/*
			?>
						<div class="weekly_head">		
							<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
							<h3>Summary Delivery Instruction Period from: <?=$date_show_start?> to: <?=$date_show_end?><br />
							Distributor: <?=$dist->company?></h3>						
						</div>								
			<?								
				}			*/
			
					
					while($do=mysql_fetch_object($res_dos)){
						$tab  = new MySQLPDFTable($MYSQL,'p');
						
						
		
						$tab->SetFont('Helvetica','B',7);
						$header=array('Job','Is Regular','Circular','Delivery Date','Quantity');
						$width=array('Job'=>20,'Is Regular'=>10,'Circular'=>40,'Delivery Date'=>40,'Quantity'=>20);
						$maxw = array_sum($width);
						
						$tab->collField["Quantity"]=true;
						
						$maxw=130;
						
						$tab->AddPage();
						
						$title = "Distributor: $dist->company";
						$tab->StartLine(8);
							$tab->WriteLine($title,'L',8,$maxw);
						$tab->StopLine();
						
						$title = "Drop off details for $do->company. Date range: $date_show_start to $date_show_end.";
						$tab->StartLine(8);
							$tab->WriteLine($title,'L',8,$maxw);
						$tab->StopLine();
					
						$tab->WriteHeader($header,$width);
					
						
						
						$qry = "SELECT 	CONCAT(job.job_no,IF(job.job_no_add IS NOT NULL,job.job_no_add,''))         AS 'Job',
										IF(job.is_regular='Y','Y','N')	 AS 'Is Regular',
										job.publication 		AS Circular,
										CONCAT(job.delivery_date,IF(cancelled IS NOT NULL AND cancelled='Y','(C)','')) 	AS 'Delivery Date',
										SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 	AS Quantity
										#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS Bundles
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								WHERE doff='$do->doff'					
									AND job_route.dist_id=$dist_id
									AND delivery_date>='$date_start'
									AND delivery_date<='$date_final'
								GROUP BY job.job_id
								ORDER BY job.is_regular DESC, seq_region,seq_area,seq_code,job.job_no";
						//echo nl2br($qry);die();
						
						
					
						$data = $tab->LoadData($qry);
									
						$tab->WriteTable($header,$data,$width);			
						
						$tab->StartLine(7);
							$width_emtpy = $maxw-$width["Delivery Date"]-$width["Quantity"];
							$tab->WriteLine("",'L',7,$width_emtpy);
							$tab->WriteLine("Total:",'L',7,$width["Delivery Date"]);
							$tab->WriteLine($tab->getSum("Quantity",0),'L',7,$width["Quantity"]);
						$tab->StopLine();
						
						$tab->StartLine(7);
							$tab->WriteLine($comment2,'L',6,$maxw);
						$tab->StopLine();
					
						
						$tab->refreshCollFields();
			
						$now=date("Y_m_d_h_i_s");
				
						$fn = "delivery_instructions_".$do->name."_$now.pdf";
						
						$tab->Output($fn);
						$dir = $SEND_OUTPUT_DIR."temp_payout";
						send_operator_mail("DELIVERY INSTRUCTIONS",$dir,$fn,$do->doff);
					}//while do
						
			}//while($dist = mysql_fetch_object($res_dist))					
			
			
			
		}//if($year && $week && $distributor)

	}
?>