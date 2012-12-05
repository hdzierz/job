<?
if($report=="envelopes"){
	if($company){
		/*$qry = "SELECT * FROM route_aff";
		$res = query($qry);
		while($aff = mysql_fetch_object($res)){
			$qry = "SELECT route_id FROM route WHERE route_id='$aff->route_id'";
			$res_r = query($qry);
			if(mysql_num_rows($res_r)==0){
				$qry = "DELETE FROM route_aff WHERE route_id='$aff->route_id'";
				query($qry);
			}
		}*/
	
	
		if($is_shareholder=='Y') 	$where_sh  = "AND operator.is_shareholder='Y'";
		if($is_current=='Y') 		$where_ic  = "AND operator.is_current='Y'";
		if($contract=='Y') 			$where_ct  = "AND operator.contract='Y'";
		if($agency=='Y') 			$where_ag  = "AND operator.agency='Y'";
		
		if($is_dist=='Y') 			$where_di  = "AND operator.is_dist='Y'";
		if($is_subdist=='Y') 		$where_sd  = "AND operator.is_subdist='Y'";
		if($is_contractor=='Y') 	$where_co  = "AND operator.is_contr='Y'";
		
		if($is_shareholder=='N') 	$where_sh  = "AND operator.is_shareholder<>'Y'";
		if($is_current=='N') 		$where_ic  = "AND operator.is_current<>'Y'";
		if($contract=='N') 			$where_ct  = "AND operator.contract<>'Y'";
		if($agency=='N') 			$where_ag  = "AND operator.agency<>'Y'";
		
		if($is_dist=='N') 			$where_di  = "AND operator.is_dist<>'Y'";
		if($is_subdist=='N') 		$where_sd  = "AND operator.is_subdist<>'Y'";
		if($is_contractor=='N') 	$where_co  = "AND operator.is_contr<>'Y'";
		
		if($company != "All") 		$where_dist  = "HAVING dist_id='$company'";
		
		$date = date("Y-m-d");
		if($company!="All"){
			
		
			$qry = "SELECT * FROM(
					SELECT operator.operator_id AS ID, 
			
						   IF(
								operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
								IF(
									address.first_name2 IS NOT NULL AND address.first_name2<>'',
										CONCAT(address.first_name2,' ',IF(address.name2 IS NOT NULL,address.name2,''),' &amp ',address.first_name,' ',address.name),
										CONCAT(address.first_name,' ',address.name)
								)
						   )		
												AS Name,
						   IF(operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,address.name)		
												AS `Order`,									
						   address.address    	AS Address,
						   address.address2    	AS Address2,
						   address.city       	AS City,
						   address.postcode   	AS Postcode,
						   operator.alias   	AS Alias,
						   'contr'				AS Type,
						   o.company			AS Dist
					FROM operator
					LEFT JOIN route_aff
					ON operator.operator_id=route_aff.env_contractor_id
					LEFT JOIN route
					ON route.route_id=route_aff.route_id
					LEFT JOIN operator o
					ON o.operator_id=route_aff.env_dist_id
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					
					WHERE operator.operator_id IS NOT NULL
						AND is_hidden = 'N'
						$where_dist
						$where_sh
						$where_ic
						$where_ct
						$where_ag
						$where_co
						
						AND app_date<='$date'
						AND stop_date>'$date'
					GROUP BY operator.operator_id
					UNION
					SELECT operator.operator_id AS ID, 
			
						   IF(
								operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
								IF(
									address.first_name2 IS NOT NULL AND address.first_name2<>'',
									IF(
										address.name2 IS NOT NULL AND address.name2<>'',
										CONCAT(address.first_name,' ',address.name,' &amp ',address.first_name2,' ',address.name2),
										CONCAT(address.first_name,' &amp ',address.first_name2,' ',address.name)
									),
									CONCAT(address.first_name,' ',address.name)
								)
						   )		
												AS Name,
						   IF(operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,address.name)		
												AS `Order`,									
						   address.address    	AS Address,
						   address.address2    	AS Address2,
						   address.city       	AS City,
						   address.postcode   	AS Postcode,
						   operator.alias   	AS Alias,
						   'subdist'			AS Type,
						   o.company			AS Dist
					FROM operator
					LEFT JOIN route_aff
					ON operator.operator_id=route_aff.env_subdist_id
					LEFT JOIN route
					ON route.route_id=route_aff.route_id
					LEFT JOIN operator o
					ON o.operator_id=route_aff.env_dist_id
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					
					WHERE operator.operator_id IS NOT NULL
						AND is_hidden = 'N'
						$where_dist
						$where_sh
						$where_ic
						$where_ct
						$where_ag
						$where_sd
						AND app_date<='$date'
						AND stop_date>'$date'
					UNION
					SELECT operator.operator_id AS ID, 
			
						   IF(
								operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
								IF(
									address.first_name2 IS NOT NULL AND address.first_name2<>'',
									IF(
										address.name2 IS NOT NULL AND address.name2<>'',
										CONCAT(address.first_name,' ',address.name,' &amp ',address.first_name2,' ',address.name2),
										CONCAT(address.first_name,' &amp ',address.first_name2,' ',address.name)
									),
									CONCAT(address.first_name,' ',address.name)
								)
						   )		
												AS Name,
						   IF(operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,address.name)		
												AS `Order`,									
						   address.address    	AS Address,
						   address.address2    	AS Address2,
						   address.city       	AS City,
						   address.postcode   	AS Postcode,
						   operator.alias   	AS Alias,
						   'subdist'			AS Type,
						   o.company			AS Dist
					FROM operator
					LEFT JOIN route_aff
					ON operator.operator_id=route_aff.env_dist_id
					LEFT JOIN route
					ON route.route_id=route_aff.route_id
					LEFT JOIN operator o
					ON o.operator_id=route_aff.env_dist_id
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					
					WHERE operator.operator_id IS NOT NULL
						AND is_hidden = 'N'
						$where_dist
						$where_sh
						$where_ic
						$where_ct
						$where_ag
						$where_di
						AND app_date<='$date'
						AND stop_date>'$date'
						
					GROUP BY ID			
					ORDER BY Dist,`Order`) AS result
					GROUP BY ID";
		}
		else{
			$qry = "SELECT address.*,
							operator.company
					FROM 
					(
						SELECT operator.operator_id AS ID, 
								IF(
								operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
								IF(
									address.first_name2 IS NOT NULL AND address.first_name2<>'',
									IF(
										address.name2 IS NOT NULL AND address.name2<>'',
										CONCAT(address.first_name,' ',address.name,' &amp ',address.first_name2,' ',address.name2),
										CONCAT(address.first_name,' &amp ',address.first_name2,' ',address.name)
									),
									CONCAT(address.first_name,' ',address.name)
								)
						   )		
												AS Name,
						   IF(operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,address.name)		
												AS `Order`,									
						   address.address    	AS Address,
						   address.address2    	AS Address2,
						   address.city       	AS City,
						   address.postcode   	AS Postcode,
						   operator.alias   	AS Alias,
						   IF(is_contr='Y','contr',
								IF(is_subdist='Y',
									'subdistr' ,
									'dist'
								)
							)
												AS Type,
						   IF(is_contr='Y',
										(
											SELECT dist_id FROM route_aff 
											WHERE route_aff.contractor_id=operator.operator_id 
												AND '$date' BETWEEN app_date AND stop_date
											GROUP BY operator.operator_id
										),
										IF(is_subdist='Y',
											(
												SELECT dist_id FROM route_aff 
												WHERE route_aff.subdist_id=operator.operator_id 
													AND '$date' BETWEEN app_date AND stop_date
												GROUP BY operator.operator_id
											) ,
											operator.operator_id
										)
										
									)
										
												AS dist_id
						FROM address
						LEFT JOIN operator
						ON operator.operator_id=address.operator_id
						WHERE operator.operator_id IS NOT NULL
							$where_dist
							$where_di
							$where_sh
							$where_ic
							$where_ct
							$where_ag
							$where_co
					)
					AS address
					LEFT JOIN operator
					ON operator.operator_id=dist_id
					
					ORDER BY 'Name'";		
		}
		
		$qry = "SELECT address.*,
						operator.company
				FROM 
				(
					SELECT operator.operator_id AS ID, 
							IF(
							operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
							IF(
								address.first_name2 IS NOT NULL AND address.first_name2<>'',
								IF(
									address.name2 IS NOT NULL AND address.name2<>'',
									CONCAT(address.first_name,' ',address.name,' &amp ',address.first_name2,' ',address.name2),
									CONCAT(address.first_name,' &amp ',address.first_name2,' ',address.name)
								),
								CONCAT(address.first_name,' ',address.name)
							)
					   )		
											AS Name,
					   IF(operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,address.name)		
											AS `Order`,									
					   address.address    	AS Address,
					   address.address2    	AS Address2,
					   address.city       	AS City,
					   address.postcode   	AS Postcode,
					   operator.alias   	AS Alias,
					   IF(is_contr='Y','contr',
							IF(is_subdist='Y',
								'subdistr' ,
								'dist'
							)
						)
											AS Type,
					   IF(is_contr='Y',
									(
										SELECT dist_id FROM route_aff WHERE route_aff.contractor_id=operator.operator_id GROUP BY operator.operator_id
									),
									IF(is_subdist='Y',
										(
											SELECT dist_id FROM route_aff WHERE route_aff.subdist_id=operator.operator_id GROUP BY operator.operator_id
										) ,
										operator.operator_id
									)
									
								)
									
											AS dist_id
					FROM address
					LEFT JOIN operator
					ON operator.operator_id=address.operator_id
					WHERE operator.operator_id IS NOT NULL
						$where_di
						$where_sh
						$where_ic
						$where_ct
						$where_ag
						$where_co
				)
				AS address
				LEFT JOIN operator
				ON operator.operator_id=dist_id
				$where_dist
				ORDER BY 'Name'";		
		//echo nl2br($qry);
		$res = query($qry,0);
		$num_rows = mysql_num_rows($res);
		$count=0;
		if(mysql_num_rows($res)>0){
			while($env = mysql_fetch_object($res)){
				$count++;
	?>
				<div id="envelope">
					<div style="float:left "><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
					<table border="0" style="font-size:12pt; margin-left:50mm; margin-top: 60mm;  ">
	
						<tr>
							<td><?=$env->Name?></td>
						</tr>
						<tr>
							<td><?=$env->Address?></td>
						</tr>
<?
						if($env->Address2){
?>						
							<tr>
								<td><?=$env->Address2?></td>
							</tr>
<?
						}
?>						
						<tr>
							<td><?=$env->City?><? if($env->Postcode>0){ echo ", ".sprintf("%04d",$env->Postcode);}?></td>
						</tr>
					</table>
				</div>
<?
				if($num_rows>$count){
?>				
					<div class="pagebreak_after">&nbsp;</div>
<?		
				}
			}
		}//if(mysql_num_rows($res)>0){
	}//if job_id
}

if($report=="rep_send_out"){
	$qry = "SELECT send_report.date 	AS Date,
					REPLACE(type,'_send_out','') AS Type,
					CASE type
						WHEN 'invoice_send_out' THEN 
							CONCAT('<a href=\'rep_parcels.php?report=',REPLACE(type,'_send_out',''),'&send_report_id=',send_report_id,'\'>Show</a>')
						WHEN 'job_delivery_send_out' THEN 
							CONCAT('<a href=\'rep_parcels.php?report=',REPLACE(type,'_send_out',''),'&send_report_id=',send_report_id,'\'>Show</a>')
						WHEN 'do_details' THEN 
							CONCAT('<a href=\'proc_job.php?action=show_do_details&job_id=',jobs,'&send_report_id=',send_report_id,'\'>Show</a>')
						ELSE  
							CONCAT('<a href=\'rep_revenue.php?report=',REPLACE(type,'_send_out',''),'&send_report_id=',send_report_id,'\'>Show</a>')
					END
							
										AS Action
						
			FROM send_report
			WHERE DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '$date_start' AND '$date_final'";
	//echo nl2br($qry);
	$tab  = new MySQLTable("rep_revenue.php",$qry);
	$tab->showRec=1;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasAddButton=false;
	$tab->hasForm = false;
	
	$tab->startTable();
		$tab->writeTable();
	$tab->stopTable();
	
}
if($report=="rep_cirpay_by_payee"){
	if($month&&$year){
?>
				<div class="weekly_head">
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>		
					<p>COURAL (RURAL COURIERS SOCIETY LTD)<br />
						PO BOX 1233<br />
						PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618<br /></p>		
					<h3>Circular Payout</h3>
				</div>				
			
<?		
		$month_show = date('F',mktime(0,0,0,$month,1,$year));
		$dist = get("operator","company","WHERE operator_id=$dist_id");
		if($dist_id) {
			$dist_list[]=$dist_id;
			 $title = "Distributor: $dist, $month_show/$year";
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
			$title = "All, $month_show/$year";
		}
		
		foreach($dist_list as $dist){
			$dist_comp	= get("operator","company","WHERE operator_id=$dist");
			$title = "Distributor: $dist_comp, $month_show/$year";	
			$qry_start = "SELECT Name,
								 #SUM(Circ) AS Circ,
							   	 SUM(Bdls)	AS Bdls,
								 SUM(Amount) AS Amount,
								 SUM(BdlAmount) AS 'BdlAmount',
								 SUM(Total) AS Total,
								 SUM(Total_GST) AS Total_GST,
								 '1' AS TT
						FROM 
						(
							SELECT 'Distributor' AS Highlight,
									'Distributor' AS Categorie,
									operator.company
									AS Name,
									SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0))
									AS 'Circ',
									SUM(IF(job_route.dest_type='bundles',job_route.amount,0))
									AS Bdls,
									ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*job.dist_rate,0)),2)
									AS 'Amount',
									ROUND(SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
									AS 'BdlAmount',
									ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*job.dist_rate,0))+
									SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
									AS 'Total',
									ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*job.dist_rate,0))+
									SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0))),2)
									AS 'Total_GST'
							FROM job
							LEFT JOIN job_route
							ON job_route.job_id = job.job_id
							LEFT JOIN route
							ON job_route.route_id = route.route_id
							LEFT JOIN operator
							ON job_route.dist_id=operator.operator_id
								WHERE year(delivery_date)='$year'
								AND month(delivery_date)='$month'
								AND job_route.dist_id='$dist'
								#AND job.finished<>'Y'
								AND job.cancelled<>'Y'
								AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
								AND job_route.dist_id='$dist' 
							GROUP BY job_route.dist_id
							UNION
							SELECT 'Sub-Distributor' AS Highlight,
									'Sub-Distributor' AS Categorie,
									operator.company AS Name,
									'0'				AS 'Circ',
									'0' AS Bdls,
									round(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*subdist_rate_red*job.subdist_rate,0)),2)
									AS 'Amount' ,
									'0'	AS 'BdlAmount',
									ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*subdist_rate_red*job.subdist_rate,0))+
									SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
									AS 'Total',
									ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*subdist_rate_red*job.subdist_rate,0))+
									SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0))),2)
									AS 'Total_GST'
							FROM job
							LEFT JOIN job_route
							ON job_route.job_id = job.job_id
							LEFT JOIN route
							ON job_route.route_id = route.route_id
							LEFT JOIN operator
							ON job_route.subdist_id=operator.operator_id
							WHERE year(delivery_date)='$year'
								AND month(delivery_date)='$month'
								AND job_route.dist_id='$dist'
								#AND job.finished<>'Y'
								AND job.cancelled<>'Y'
								AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
								AND job_route.dist_id='$dist' GROUP BY job_route.subdist_id
							UNION
							SELECT 'Contractor' AS Highlight,
									'Contractor' AS Categorie,
									operator.company AS Name,
									'0'				AS 'Circ',
									'0'				AS 'Bdls',
									round(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*(job.contr_rate+folding_fee),0)),2)
									AS 'Amount',
									'0'	AS 'BdlAmount' ,
									ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*(job.contr_rate+folding_fee),0))+
									SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
									AS 'Total',
									ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*(job.contr_rate+folding_fee),0))+
									SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0))),2)
									AS 'Total_GST'
							FROM job
							LEFT JOIN job_route
							ON job_route.job_id = job.job_id
							LEFT JOIN route
							ON job_route.route_id = route.route_id
							LEFT JOIN operator
							ON job_route.contractor_id=operator.operator_id
							WHERE year(delivery_date)='$year'
								AND month(delivery_date)='$month'
								AND job_route.dist_id='$dist'
								#AND job.finished<>'Y'
								AND job.cancelled<>'Y'
								AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
								AND job_route.dist_id='$dist' GROUP BY job_route.contractor_id
						) Res
						";
						
					$qry_dist = $qry_start." GROUP BY Name";
					
					$qry_dist_sum = "$qry_start GROUP BY TT";
					
					//echo nl2br($qry_dist);
					$res_dist_sum = query($qry_dist_sum);
					$obj = mysql_fetch_object($res_dist_sum);
					$dist_qty_sum = $obj->Circ;
					$dist_bdl_sum = $obj->Bdls;
					$dist_amt_sum = $obj->Amount;
					$dist_bat_sum = $obj->BdlAmount;
					$dist_tot_sum = $obj->Total;
					$dist_tog_sum = $obj->Total_GST;
			
			if(count($dist_list)>0){
?>
				<div class="weekly_head">
					<h3 class="weekly_head_h2"><?=$title?></h3>
				</div>				
			
<?						
			
				$tab  = new MySQLTable("rep_old.php","");
				$tab->highlightSpecifierField 		= "Highlight";
				$tab->cssSQLHighlightedEvenLine		= "sqltabevenline_high_bold";
				$tab->cssSQLHighlightedUnEvenLine	= "sqltabunevenline_high_bold";
				$tab->highlightSpecifierValue		= 'Distributor';
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->fieldNames["BdlAmount"]="Bdl Amt";
				$tab->fieldNames["Total_GST"]="Total (incl. GST)";
				$tab->hiddenFields["TT"]=1;
				$tab->startTable();
				
				$tab->writeSQLTableElement($qry_dist);

				$tab->startNewLine();
					$tab->addLineWithStyle("Grand Total","sql_extra_line_text_grey");
					//$tab->addLineWithStyle($dist_qty_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_bdl_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_amt_sum+$subdist_amt_sum+$contr_amt_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_bat_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_tot_sum+$subdist_tot_sum+$contr_tot_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_tog_sum+$subdist_tog_sum+$contr_tog_sum,"sql_extra_line_number_grey");
				$tab->stopNewLine();			
							
				$tab->stopTable();
				?>
					<hr class="pagebreak_after">
				<?
			}//if mysql_num_rows>0
		}//	if($year && $month && $distributor)					
	}//foreach dist_list as dist	
	
			
}

if($report=="rep_cirpay_by_payee_subsum"){
	if($month&&$year){
?>
				<div class="weekly_head">
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>		
					<p>COURAL (RURAL COURIERS SOCIETY LTD)<br />
						PO BOX 1233<br />
						PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618<br /></p>		
					<h3>Circular Payout</h3>
				</div>				
			
<?		
	
		$month_show = date('F',mktime(0,0,0,$month,1,$year));
		$dist = get("operator","company","WHERE operator_id=$dist_id");
		if($dist_id) {
			$dist_list[]=$dist_id;
			 $title = "Distributor: $dist, $month_show/$year";
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
			$title = "All, $month_show/$year";
		}
		
		foreach($dist_list as $dist){
			$dist_comp	= get("operator","company","WHERE operator_id=$dist");
			$title = "Distributor: $dist_comp, $month_show/$year";	
			
			 $add_and 	= "AND job_route.dist_id=$dist";
			 $group 	= "job_route.dist_id";
			 
			 $query_start_dist = "SELECT 'Distributor' 			AS Highlight,
										 'Distributor' 			AS Categorie,
										operator.company
										 						AS Name,
										SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0))
																 AS 'Circ',
										SUM(IF(job_route.dest_type='bundles',job_route.amount,0))
																AS Bdls,
										ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*job.dist_rate,0)),2)
																AS 'Amount',																
										ROUND(SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
																AS 'BdlAmount',
										ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*job.dist_rate,0))+
										SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
																AS 'Total',
										ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*job.dist_rate,0))+
										SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0))),2)
																AS 'Total_GST'														
									FROM job
									LEFT JOIN job_route
									ON job_route.job_id = job.job_id
									LEFT JOIN route
									ON job_route.route_id = route.route_id
									LEFT JOIN operator
									ON job_route.dist_id=operator.operator_id
									WHERE year(delivery_date)='$year' 
										AND month(delivery_date)='$month'
										AND job_route.dist_id=$dist
										#AND job.finished<>'Y'
										AND job.cancelled<>'Y'
										AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
										AND job_route.dist_id=$dist	";																
																
																						
			 $query_start_subdist = "SELECT 'Sub-Distributor' 			AS Highlight,
											 'Sub-Distributor' 			AS Categorie,
										operator.company				AS Name,
										SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0))
																 AS 'Circ',
										0						AS Bdls,
										round(SUM(IF(job_route.dest_type<>'bundles',subdist_rate_red*job_route.amount*job.subdist_rate,0)),2)
																AS 'Amount'  ,
										0						AS 'BdlAmount',
										ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*subdist_rate_red*job.subdist_rate,0))+
										SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
																AS 'Total',
										ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*subdist_rate_red*job.subdist_rate,0))+
										SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0))),2)
																AS 'Total_GST'	
										FROM job
										LEFT JOIN job_route
										ON job_route.job_id = job.job_id
										LEFT JOIN route
										ON job_route.route_id = route.route_id
										LEFT JOIN operator
										ON job_route.subdist_id=operator.operator_id
										WHERE year(delivery_date)='$year' 
											AND month(delivery_date)='$month'
											AND job_route.dist_id=$dist
											#AND job.finished<>'Y'
											AND job.cancelled<>'Y'
											AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
											AND job_route.dist_id=$dist	";		
																
			 $query_start_contr = "SELECT 'Contractor' 			AS Highlight,
										 'Contractor' 			AS Categorie,
										operator.company		AS Name,
										SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0))
																 AS 'Circ',
										0						 AS 'Bdls',																 
										round(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*(job.contr_rate+folding_fee),0)),2)
																AS 'Amount',
										0						AS 'BdlAmount'	,
										ROUND(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*(job.contr_rate+folding_fee),0))+
										SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0)),2)
																AS 'Total',
										ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',job_route.amount*(job.contr_rate+folding_fee),0))+
										SUM(IF(job_route.dest_type='bundles',job_route.amount*job_route.bundle_price,0))),2)
																AS 'Total_GST'																 
										FROM job
										LEFT JOIN job_route
										ON job_route.job_id = job.job_id
										LEFT JOIN route
										ON job_route.route_id = route.route_id
										LEFT JOIN operator
										ON job_route.contractor_id=operator.operator_id
										WHERE year(delivery_date)='$year' 
											AND month(delivery_date)='$month'
											AND job_route.dist_id=$dist
											#AND job.finished<>'Y'
											AND job.cancelled<>'Y'
											AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
											AND job_route.dist_id=$dist	";																																

			$qry_dist = "$query_start_dist GROUP BY job_route.dist_id";
			$qry_subdist = "$query_start_subdist GROUP BY job_route.subdist_id";
			$qry_contr = "$query_start_contr GROUP BY job_route.contractor_id";
			$qry_dist_sum = "$query_start_dist GROUP BY job_route.dist_id";
			
			$res = query($qry_dist_sum);
			
			if(mysql_num_rows($res)>0){
?>
				<div class="weekly_head">
					<h3 class="weekly_head_h2"><?=$title?></h3>
				</div>				
			
<?						
				$obj = mysql_fetch_object($res);
				$dist_qty_sum = $obj->Circ;
				$dist_bdl_sum = $obj->Bdls;
				$dist_amt_sum = $obj->Amount;
				$dist_bat_sum = $obj->BdlAmount;
				$dist_tot_sum = $obj->Total;
				$dist_tog_sum = $obj->Total_GST;
							
				$qry_subdist_sum = "$query_start_subdist GROUP BY job_route.dist_id";
				$res = query($qry_subdist_sum);
				$obj = mysql_fetch_object($res);
				$subdist_qty_sum = $obj->Circ;
				$subdist_bdl_sum = $obj->Bdls;
				$subdist_amt_sum = $obj->Amount;
				$subdist_bat_sum = $obj->BdlAmount;
				$subdist_tot_sum = $obj->Total;
				$subdist_tog_sum = $obj->Total_GST;
				
				$qry_contr_sum = "$query_start_contr GROUP BY job_route.dist_id";
				$res = query($qry_contr_sum);
				$obj = mysql_fetch_object($res);
				
				$contr_qty_sum = $obj->Circ;
				$contr_bdl_sum = $obj->Bdls;
				$contr_amt_sum = $obj->Amount;
				$contr_bat_sum = $obj->BdlAmount;
				$contr_tot_sum = $obj->Total;
				$contr_tog_sum = $obj->Total_GST;				
			
				$tab  = new MySQLTable("rep_revenue.php",$qry);
				$tab->highlightSpecifierField 		= "Highlight";
				$tab->cssSQLHighlightedEvenLine		= "sqltabevenline_high_bold";
				$tab->cssSQLHighlightedUnEvenLine	= "sqltabunevenline_high_bold";
				$tab->highlightSpecifierValue		= 'Distributor';
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->fieldNames["BdlAmount"]="Bdl Amt";
				$tab->fieldNames["Total_GST"]="Total (incl. GST)";
				$tab->startTable();
				
				$tab->writeSQLTableElement($qry_dist);
				
				$tab->startNewLine();
					$tab->addLines("",4);
				$tab->stopNewLine();
				
				$tab->writeSQLTableElement($qry_subdist,false);
				//$tab->writeDivider(4);
				$tab->startNewLine();
					$tab->addLines("",0);
					$tab->addLineWithStyle("Total","sql_extra_line_text_grey");
					$tab->addLineWithStyle($subdist_qty_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($subdist_bdl_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($subdist_amt_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($subdist_bat_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($subdist_tot_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($subdist_tog_sum,"sql_extra_line_number_grey");
				$tab->stopNewLine();
				
				$tab->writeSQLTableElement($qry_contr,false);
				
				$tab->startNewLine();
					$tab->addLines("",0);
					$tab->addLineWithStyle("Total","sql_extra_line_text_grey");
					$tab->addLineWithStyle($contr_qty_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($contr_bdl_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($contr_amt_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($contr_bat_sum ,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($contr_tot_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($contr_tog_sum,"sql_extra_line_number_grey");
				$tab->stopNewLine();
				
				$tab->startNewLine();
					$tab->addLines("",0);
					$tab->addLineWithStyle("Grand Total","sql_extra_line_text_grey");
					$tab->addLineWithStyle($dist_qty_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_bdl_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_amt_sum+$subdist_amt_sum+$contr_amt_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_bat_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_tot_sum+$subdist_tot_sum+$contr_tot_sum,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($dist_tog_sum+$subdist_tog_sum+$contr_tog_sum,"sql_extra_line_number_grey");
				$tab->stopNewLine();			
							
				$tab->stopTable();
				?>
					<hr class="pagebreak_after">
				<?
			}//if mysql_num_rows>0
		}//	if($year && $month && $distributor)					
	}//foreach dist_list as dist	
	
			
}

if($report=="rep_payout_breakdown"){
	if($level){
		
		if($level=="dist"){
			 $target = "dist_rate";
			 $tgt_op = "dist_id";
			 $tit = "Distributor";
		}
		else if($level=="subdist"){
			 $target = "subdist_rate";
			 $tgt_op = "subdist_id";
			 $tit = "S/Distributor";
		}
		else if($level=="contr"){
			 $target = "(job.contr_rate+folding_fee)";
			 $tgt_op = "contractor_id";
			 $tit = "Contractor";
		}
		
		
		$date_show = date("F Y",mktime(0,0,0,$month,1,$year));
		
		$title = "Payout Breakdown ".$tit." ".get("operator","company","WHERE operator_id='$operator_id'")." ($date_show)";
	?>
					<div class="weekly_head">
						<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
						<p>COURAL (RURAL COURIERS SOCIETY LTD)<br />
							PO BOX 1233<br />
							PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618<br /></p>		
						<h3><?=$title?></h3>
					</div>				
				
	<?		
		$qry = "SELECT  Date,
						Job,
						Pub,
						Qty AS 'Circ Qty',
						Qty_Bdls AS 'Bdl Qty',
						Rate AS 'Circ Rate',
						Bdl_Price AS 'Bdl Rate',
						Amt+`Amt Bdls` AS Total,
						".(1+$GST_CIRCULAR)."*SUM(Amt+`Amt Bdls`) AS 'Total (incl. GST)'
				FROM (
				SELECT  delivery_date AS Date,
						job.job_no AS Job,
						job.publication AS Pub,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						
						SUM(IF(job_route.dest_type<>'bundles',$target*amount,0)) AS Amt,
						SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',
						$target AS Rate,
						bundle_price AS 'Bdl_Price'
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE $tgt_op='$operator_id'
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
				GROUP BY job.job_id
				) AS sum
				GROUP BY Job
				ORDER BY Date,Job,Pub";
			
			
			$qry_sum = "SELECT job.job_no AS Job,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						ROUND(SUM(IF(job_route.dest_type<>'bundles',$target*amount,0)),2) AS Amt,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(IF(job_route.dest_type<>'bundles',$target*amount,0)),2) AS Amount_gst
						
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE $tgt_op='$operator_id'
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
				GROUP BY IF(1,1,1)";
			
			
			//echo nl2br($qry);
			$res_sum = query($qry_sum);
			$sum = mysql_fetch_object($res_sum);
			
			$tab  = new MySQLTable("rep_revenue.php",$qry);
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			
			$tab->roundField["Total"] = 2;
			$tab->roundField["Total (incl. GST)"] = 2;
			
			$tab->colWidth["Job"]=50;
			$tab->colWidth["Pub"]=250;
			$tab->colWidth["Circ Qty"]=100;
			$tab->colWidth["Bdl Qty"]=100;
			$tab->colWidth["Circ Rate"]=100;
			$tab->colWidth["Bdl Rate"]=100;
			$tab->colWidth["Total"]=100;
			$tab->colWidth["Total (incl. GST)"]=150;
			
			$tab->startTable();		
			$tab->writeTable();		
			$tab->startNewLine();
				$tab->addLineWithStyle("","sql_extra_line_text_grey");
				$tab->addLineWithStyle("Total","sql_extra_line_text_grey");
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey");
				$tab->addLineWithStyle($sum->Qty,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($sum->Qty_Bdls,"sql_extra_line_number_grey");
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey");
				$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey");
				$tab->addLineWithStyle($sum->Amt,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($sum->Amount_gst,"sql_extra_line_number_grey");
			$tab->stopNewLine();
			$tab->stopTable();							
	}
}
if($report=="rep_payout_breakdown_by_dist"){
	//$qry = "SELECT * FROM operator WHERE operator_id=";
	if($mode=="contr")
		print_op(get_contr_from_dist($dist_id),$mode,$month,$year);
	else if($mode=="subdist")
		print_op(get_subdist_from_dist($dist_id),$mode,$month,$year);
	else{
		print_op(get_subdist_from_dist($dist_id),'subdist',$month,$year);
		print_op(get_contr_from_dist($dist_id),'contr',$month,$year);
	}
	
}

function print_op($ops,$mode,$month,$year){
	global $GST_CIRCULAR;
	foreach($ops as $operator_id){
		if($mode=='contr'){
			 $target = "(job.contr_rate+folding_fee)";
			 $tgt_op = "contractor_id";
			 $tit = "Contractor";
		}
		else if($mode=='subdist'){
			 $target = "subdist_rate";
			 $tgt_op = "subdist_id";
			 $tit = "S/Distributor";
		}
		
		
		$date_show = date("F Y",mktime(0,0,0,$month,1,$year));
		
		$title = "Payout Breakdown ".$tit." ".get("operator","company","WHERE operator_id='$operator_id'")." ($date_show)";
	
		$qry = "SELECT  Date,
						Job,
						Pub,
						Qty AS 'Circ Qty',
						Qty_Bdls AS 'Bdl Qty',
						Rate AS 'Circ Rate',
						Bdl_Price AS 'Bdl Rate',
						Amt+`Amt Bdls` AS Total,
						".(1+$GST_CIRCULAR)."*SUM(Amt+`Amt Bdls`) AS 'Total (incl. GST)'
				FROM (
				SELECT  delivery_date AS Date,
						job.job_no AS Job,
						job.publication AS Pub,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						
						SUM(IF(job_route.dest_type<>'bundles',$target*amount,0)) AS Amt,
						SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',
						$target AS Rate,
						bundle_price AS 'Bdl_Price'
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE $tgt_op='$operator_id'
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
				GROUP BY job.job_id
				) AS sum
				GROUP BY Job
				ORDER BY Date,Job,Pub";
			
			
			$qry_sum = "SELECT job.job_no AS Job,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						ROUND(SUM(IF(job_route.dest_type<>'bundles',$target*amount,0)),2) AS Amt,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(IF(job_route.dest_type<>'bundles',$target*amount,0)),2) AS Amount_gst
						
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE $tgt_op='$operator_id'
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
				GROUP BY IF(1,1,1)";
			
			
			//echo nl2br($qry);
			$res_sum = query($qry_sum);
			$sum = mysql_fetch_object($res_sum);
			if($sum){
				?>
					<div class="weekly_head">
						<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
						<p>COURAL (RURAL COURIERS SOCIETY LTD)<br />
							PO BOX 1233<br />
							PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618<br /></p>		
						<h3><?=$title?></h3>
					</div>				
				
	<?		
			
			
				$tab  = new MySQLTable("rep_revenue.php",$qry);
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				
				$tab->roundField["Total"] = 2;
				$tab->roundField["Total (incl. GST)"] = 2;
				
				$tab->colWidth["Job"]=50;
				$tab->colWidth["Pub"]=250;
				$tab->colWidth["Circ Qty"]=100;
				$tab->colWidth["Bdl Qty"]=100;
				$tab->colWidth["Circ Rate"]=100;
				$tab->colWidth["Bdl Rate"]=100;
				$tab->colWidth["Total"]=100;
				$tab->colWidth["Total (incl. GST)"]=150;
				
				$tab->startTable();		
				$tab->writeTable();		
				$tab->startNewLine();
					$tab->addLineWithStyle("","sql_extra_line_text_grey");
					$tab->addLineWithStyle("Total","sql_extra_line_text_grey");
					$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey");
					$tab->addLineWithStyle($sum->Qty,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($sum->Qty_Bdls,"sql_extra_line_number_grey");
					$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey");
					$tab->addLineWithStyle("&nbsp;","sql_extra_line_number_grey");
					$tab->addLineWithStyle($sum->Amt,"sql_extra_line_number_grey");
					$tab->addLineWithStyle($sum->Amount_gst,"sql_extra_line_number_grey");
				$tab->stopNewLine();
				$tab->stopTable();		
				?>
					<div class="pagebreak"> &nbsp;</div>
				<?		
			} // if sum			
	}
	
}
if($report=="delivery_details"){
	if($type){
		if($type=="num_hort") $stype = "Hort";
		if($type=="num_total") $stype = "Total";
		if($type=="num_farmers") $stype = "Farmer";
		if($type=="num_lifestyle") $stype = "Lifestyle";
		if($type=="num_dairies") $stype = "Dairy";
		if($type=="num_sheep") $stype = "Sheep";
		if($type=="num_beef") $stype = "Beef";
		if($type=="num_sheepbeef") $stype = "Sheep/Beef";
		if($type=="num_dairybeef") $stype = "Dairy/Beef";
		if($type=="num_hort") $stype = "Hort";
		if($type=="num_nzfw") $stype = "F@90%";
		
		if($type=="num_total") $type = "num_farmers+num_lifestyle";
	?>
			<div class="weekly_head">
				<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
				<h3 class="weekly_head_h2">Delivery Details for <?=$stype?></h3>
			</div>							
	<?
	
		
		if($region){
			$where_reg=" AND (";
			$first=true;
			foreach($region as $reg){
				if($first){
					$where_reg.="region='$reg'";
					$first=false;
				}
				else
					$where_reg.=" OR region='$reg'";
			}
			$where_reg.=") ";
			if($area){
				$where_area=" AND (";
				$first=true;
				foreach($area as $a){
					if($first){
						$where_area.="area='$a'";
						$first=false;
					}
					else
						$where_area.=" OR area='$a'";
				}
				$where_area.=") ";			
			}
		}
		
		if($area[0]=='0'){
			$where_area="";
		}
		if($region[0]=='0'){
			$region = array();
			$qry = "SELECT DISTINCT region FROM route";
			$res_reg  = query($qry);
			while($regi = mysql_fetch_object($res_reg)){
				$region[] = $regi->region;
			}
		}
		
		if($submit2=="Export")
			$tab  = new MySQLExport("export.html",$qry);
		else
			$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->startTable();
		
		//North Island
		$tot_sum_ni=0;
		$start=true;
		if($island[0]=='NI'){
			foreach($region as $reg){
				$group = "area";
				$qry = "SELECT 	island AS Island,
								region 	AS Region,				
								area AS Area,
								SUM($type) AS '$stype'
						FROM route
						WHERE region = '$reg'
						$where_area
						AND island='NI'
						AND is_hidden<>'Y'
						GROUP BY $group
						ORDER BY island,seq_region,seq_area";
				$res = query($qry);
				
				$qry_sum = "SELECT 	island AS Island,
								region 	AS Region,				
								area AS Area,
								SUM($type) AS '$stype'
						FROM route
						WHERE region = '$reg'
						$where_area
						AND island='NI'
						AND is_hidden<>'Y'
						GROUP BY region
						ORDER BY island,seq_region,seq_area";
				$res_sum = query($qry_sum);
				$sum = mysql_fetch_object($res_sum);	
				if(mysql_num_rows($res)>0){
					$has_rows=true;
					$tab->writeSQLTableElement($qry,$start);
					$start=false;
					$tab->startNewLine();
					$tab->addLines("",1);
					$tab->addLine("Total");
					$tab->addLine($sum->$stype);
					$tab->stopNewLine();
					$tab->startNewLine();
					$tab->addLines("",5);
					$tab->stopNewLine();
				}
				$tot_sum_ni += $sum->$stype;
			}
			if($has_rows){
				$tab->startNewLine();
				$tab->addLines("",1);
				$tab->addLine("Grand Total NI");
				$tab->addLine($tot_sum_ni);		
				$tab->stopNewLine();
			}	
		}
		// South Island
		$tot_sum_si=0;
		$start=true;
		if($island[0]=='SI'||$island[1]=='SI'){
			foreach($region as $reg){
				$group = "area";
				$qry = "SELECT 	island AS Island,
								region 	AS Region,				
								area AS Area,
								SUM($type) AS '$stype'
						FROM route
						WHERE region = '$reg'
						$where_area
						AND island='SI'
						AND is_hidden<>'Y'
						GROUP BY $group
						ORDER BY island,seq_region,seq_area";
				$res = query($qry);
				
				$qry_sum = "SELECT 	island AS Island,
								region 	AS Region,				
								area AS Area,
								SUM($type) AS '$stype'
						FROM route
						WHERE region = '$reg'
						$where_area
						AND island='SI'
						AND is_hidden<>'Y'
						GROUP BY region
						ORDER BY island,seq_region,seq_area";
				$res_sum = query($qry_sum);
				$sum = mysql_fetch_object($res_sum);	
				if(mysql_num_rows($res)>0){
					$has_rows=true;
					$tab->writeSQLTableElement($qry,$start);
					$start=false;
					$tab->startNewLine();
					$tab->addLines("",1);
					$tab->addLine("Total");
					$tab->addLine($sum->$stype);
					$tab->stopNewLine();
					$tab->startNewLine();
					$tab->addLines("",5);
					$tab->stopNewLine();
					$tot_sum_si += $sum->$stype;
				}
			}
			if($has_rows){
				$tab->startNewLine();
				$tab->addLines("",1);
				$tab->addLine("Grand Total SI");
				$tab->addLine($tot_sum_si);		
				$tab->stopNewLine();
			}
		}
		
		$tab->startNewLine();
		$tab->addLines("",1);
		$tab->addLine("Grand Total NZ");
		$tab->addLine($tot_sum_si+$tot_sum_ni);		
		$tab->stopNewLine();
		
		$tab->stopTable();			
		if($submit2=="Export"){
	?>
			<a href="export.html">Right Click for Download</a>
	<?	
		}		
	}//if type
}
if($report=="dropoff_details"){
	$date=date("Y-m-d");
	if($date && $region && $type){
		if($type=="num_total") $descr_type="Total";
		if($type=="num_farmers") $descr_type="Farmer";
		if($type=="num_lifestyle") $descr_type="Lifestyle";
		if($type=="num_dairies") $descr_type="Dairy";
		if($type=="num_sheep") $descr_type="Sheep";
		if($type=="num_beef") $descr_type="Beef";
		if($type=="num_sheepbeef") $descr_type="Sheep/Beef";
		if($type=="num_dairybeef") $descr_type="Dairy/Beef";
		if($type=="num_hort") $descr_type="Hort";
		if($type=="num_nzfw") $descr_type="F@90%";
		
		if($type=="num_total") $type="(num_lifestyle+num_farmers)";		
		if(!$region[0]){
			$region=array();
			if($island[1]) $where_add="AND island='".$islands[1]."'";
			 $qry = "SELECT DISTINCT region from route WHERE 
					island='".$island[0]."' $where_add 
					AND region<>'MAILINGS'
					AND region<>'BAGS BOXES COUNTER'
					AND is_hidden<>'Y'
					ORDER BY island,seq_region";
			$res = query($qry);
			while($reg = mysql_fetch_object($res)){
				$region[] = $reg->region;
			}
		}
		$in = "IN('0'";
		
		foreach($region as $reg){
			$in.=",'$reg'";
		}
		$in.=")";
		
		$qry = "SELECT 	route_aff.dropoff_id AS Record,
						city	AS 'Delivery point',
						SUM($type) AS $descr_type

				FROM route
				LEFT JOIN route_aff
				ON route_aff.route_id=route.route_id
				LEFT JOIN operator
				ON operator.operator_id=route_aff.dropoff_id
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				WHERE app_date<='$date'
					AND stop_date>='$date'
					AND route.region $in
					AND is_hidden<>'Y'
				/*GROUP BY route_aff.dropoff_id*/
					%s
				ORDER BY route.island,route.seq_region,route.seq_area,route.seq_code";
		//echo nl2br($qry);		
		$qry_n = sprintf($qry,"GROUP BY route_aff.dropoff_id");
		$qry_sum = sprintf($qry,"GROUP BY IF(1,1,1)");
		$res_sum = query($qry_sum);
		$sum = mysql_fetch_object($res_sum);
		
		$tab = new MySQLTable("report.php",$qry_n,$nameI="report");
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		//$tab->cssSQLTable = "sqltable_8";
		//$tab->cssSQLTable = "sqltable_8";
		$tab->showRec=0;
		$tab->startTable();
		$tab->writeTable();
		$tab->startNewLine();
				$tab->addLine("Grand Total:");
				$tab->addLine($sum->$descr_type);		
			$tab->stopNewLine();
		$tab->stopTable();
		
		//$tab->colWidth["Description"]=180;
		//$tab->colWidth["Phone"]=120;
		//$tab->colWidth["RD"]=120;
		//$tab->colWidth["Contractor"]=120;
		//$tab->colWidth["Address"]=120;
		//$tab->colWidth["City"]=120;
		//$tab->colWidth["Mobile"]=80;
		//$tab->cssSQLUnEvenCol="sqltabunevencol_white";
		//$tab->hasLineSeperator=true;
	}//if date
}


?>