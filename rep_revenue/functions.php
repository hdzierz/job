<?
function write_label_a4($label,$count,$space_hor,$space_vert,$qty_per_do){

	$client = $label->Client;
	if(strlen($client)>20){
		$client = substr($client,1,20)."...";
	}
	
	$publication = $label["Publication"];
	for($i=0;$i<$qty_per_do;$i++){
?>	
	<div id="a4labels_wrapper">
		<table class="a4labels" cellpadding="0"  cellspacing="0">
			<tr>	
				<td class="a4label_head" align="center" colspan="2">
					<?=$label["FName"]?> <?=$label["Name"]?><br /><br />
				</td>			
			</tr>
			<tr>	
				<td class="a4label_head" align="center" colspan="2"><?=$label["Address"]?><br /><br /></td>			
			</tr>
			<tr>	
				<td class="a4label_head" align="center" colspan="2"><?=$label["City"]?><br /><br /></td>			
			</tr>			
			<tr>	
				<td class="a4label_job_info" align="center"  colspan="2">
					<?=$label["Notes"]?>
					<br />
					<br />
					<br />
					<br />
					<br />
					<br />
				</td>
							
			</tr>
			<tr>
				<td valign="middle" class="a4label_job_info_small">
					Client: <?=$label["Client"]?><br />
					Job #: <?=$label["Job"]?><br />
					Job Name:<?=$publication?><br />
					Vers.: <?=$label["Version"]?><br />
					<!--Vers2.: <?=$label["Version"]?><br />-->
					Delivery Date: <? if($label["Date"]=="IOA"){?><font style="font-style:italic "><?=$label["Date"]?></font><? } else{echo date("d M y",strtotime($label["Date"]));}?><br />
					D/Type: <?=$label["DType"]?><br />
					<img src="images/coural-rural-couriers.png" height="38" width="71" ><br />
				</td>
				<td valign="top" class="a4label_job_info">
					<p><strong>Bundle Qty (<?=$label["QtyPerBundle"]?>): </strong>  <?=$label["QtyBundle"]?></p>
					<p><strong>Single Qty: </strong>  <?=$label["SQuantity"]?></p>
					<p>Total Qty: <?=$label["Quantity"]?></p>
				</td>
				
				
			</tr>
			
		</table>
	</div>
	<div class="pagebreak_after"></div>	
	<span class="page_border"><hr /></span>
<?
	}
}
function write_label_a4_bu1($label,$count,$space_hor,$space_vert){

	$client = $label->Client;
	if(strlen($client)>20){
		$client = substr($client,1,20)."...";
	}
	
	$publication = $label["Publication"];
	
?>	
	<div id="a4labels">
		<table class="a4labels" cellpadding="0"  cellspacing="0">
			<tr>	
				<td class="a4label_title" align="center" colspan="2">
					<?=$label["FName"]?> <?=$label["Name"]?>
					<br />
					<br />
				</td>			
			</tr>
			<tr>	
				<td class="a4label_head" align="center" colspan="2"><?=$label["Address"]?></td>			
			</tr>
			<tr>	
				<td class="a4label_head" align="center" colspan="2"><?=$label["Notes"]?></td>			
			</tr>			
			<tr>	
				<td class="a4label_head" align="center"  colspan="2">
					<?=$label["City"]?><? if($label["Postcode"]>0){ echo ", ".$label["Postcode"];}?>
					<br />
					<br />
					<br />
					<br />
					
				</td>
							
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong>Bundle Qty: </strong>  <?=$label["QtyBundle"]?></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">
					<strong>Single Qty: </strong>  <?=$label["SQuantity"]?>
					<br />
					<br />
				</td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong><?=$label["Client"]?></strong></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong>Job #: <?=$label["Job"]?></strong><br /><br /></td>
			</tr>
			<tr>
				<td colspan="2" class="a4label_job_info"><strong><?=$publication?></strong><br /><br /></td>
			</tr>			
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">Vers.: <?=$label["Version"]?><br /><br /></td>
			</tr>						
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">Delivery Date: <? if($label["Date"]=="IOA"){?><font style="font-style:italic "><?=$label["Date"]?></font><? } else{echo date("d M y",strtotime($label["Date"]));}?></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">D/Type: <?=$label["DType"]?><br /></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">Qty:<?=$label["Quantity"]?><br /></td>
			</tr>
			<tr>
				<td class="a4label_coural_address"></td>
			</tr>	
			<tr>
				<td rowspan="3"><img src="images/coural-rural-couriers.png" height="38" width="71" ></td>
				<td></td>
			</tr>
				
		</table>
	</div>
	<div class="pagebreak_after"></div>	
	<span class="page_border"><hr /></span>
<?
}
function write_label_a4_bu($label,$count,$space_hor,$space_vert){

	$client = $label->Client;
	if(strlen($client)>20){
		$client = substr($client,1,20)."...";
	}
	
	$publication = $label["Publication"];
	
?>	
	<div id="a4labels">
		<table class="a4labels" cellpadding="0"  cellspacing="0">
			<tr>	
				<td class="a4label_title" align="center" colspan="2">
					<br />
					<br />
					<?=$label["FName"]?> <?=$label["Name"]?>
					<br />
					<br />
				</td>			
			</tr>
			<tr>	
				<td class="a4label_head" align="center" colspan="2"><?=$label["Address"]?></td>			
			</tr>
			<tr>	
				<td class="a4label_head" align="center" colspan="2"><?=$label["Notes"]?></td>			
			</tr>			
			<tr>	
				<td class="a4label_head" align="center"  colspan="2">
					<?=$label["City"]?><? if($label["Postcode"]>0){ echo ", ".$label["Postcode"];}?>
					<br />
					<br />
					<br />
					<br />
					
				</td>
							
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong>Job #: <?=$label["Job"]?></strong></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong>Bundle Qty: </strong>  <?=$label["QtyBundle"]?></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">
					<strong>Single Qty: </strong>  <?=$label["QtySingle"]?>
					<br />
					<br />
				</td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong><?=$label["Client"]?></strong><br /><br /></td>
			</tr>
			<tr>
				<td colspan="2" class="a4label_job_pub"><strong><?=$publication?></strong><br /><br /></td>
			</tr>			
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info">Vers.: <?=$label["Version"]?><br /><br /></td>
			</tr>						
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong>Delivery Date: </strong> <? if($label["Date"]=="IOA"){?><font style="font-style:italic "><?=$label["Date"]?></font><? } else{echo date("d M y",strtotime($label["Date"]));}?></td>
			</tr>
			<tr>
				<td class="a4label_job_info">&nbsp;</td>
				<td class="a4label_job_info"><strong>D/Type:</strong> <?=$label["DType"]?> <strong> - Qty: </strong><?=$label["Quantity"]?><br />
					<br /></td>
			</tr>
			<tr>
				<td class="a4label_coural_address"> </td>
			</tr>			
			<tr>
				<td class="a4label_coural_address"></td>
			</tr>	
			<tr>
				<td rowspan="3"><img src="images/coural_logo.jpg" height="38" width="71" ></td>
				<td class="a4label_coural_address">Coural Rural Couriers, 41 Havill Street,Palmerston North, 06 357 3129</td>
			</tr>
				
		</table>
	</div>
	<div class="pagebreak_after"></div>	
	<span class="page_border"><hr /></span>
<?
}

function write_label($label,$count,$space_hor,$space_vert){
	/*if(!$label->Name) $label->Name="unknown";
	if(!$label->Address) $label->Address="unknown";
	if(!$label->City) $label->City="unknown";
	if(!$label->Area) $label->Area="unknown";
	if(!$label->Publication) $label->Publication="unknown";
	if(!$label->Date) $label->Date="unknown";
	if(!$label->Quantity) $label->Quantity="unknown";*/
	
	$client = $label->Client;
	if(strlen($client)>20){
		$client = substr($client,1,20)."...";
	}
	
	$publication = $label["Publication"];
	//if(strlen($publication)>20){
	//	$publication = substr($publication,1,20)."...";
	//}	
	
	
?>
	<td width="350">
		<table width="100%" class="labels" align="right" cellpadding="0"  cellspacing="0">
			<tr>	
				<td class="label_title" align="center" colspan="2"><?=$label["FName"]?> <?=$label["Name"]?></td>			
			</tr>
			<tr>	
				<td class="label_head" align="center" colspan="2"><?=$label["Address"]?></td>			
			</tr>
			<tr>	
				<td class="label_head" align="center" colspan="2"><?=$label["Notes"]?></td>			
			</tr>			
			<tr>	
				<td class="label_head" align="center"  colspan="2"><?=$label["City"]?><? if($label["Postcode"]>0){ echo ", ".$label["Postcode"];}?></td>			
			</tr>
<!--			<tr>	
				<td class="label_head" align="center" colspan="2"><?=$label["Area"]?></td>			
			</tr>-->
			<tr>
				<td class="label_job_info">&nbsp;</td>
				<td class="label_job_info"><strong>Job #: <?=$label["Job"]?></strong></td>
			</tr>
			<tr>
				<td class="label_job_info">&nbsp;</td>
				<td class="label_job_info"><strong><?=$label["Client"]?></strong></td>
			</tr>
			<tr>
				<td colspan="2" class="label_job_pub"><strong><?=$publication?></strong></td>
			</tr>			
			<tr>
				<td class="label_job_info">&nbsp;</td>
				<td class="label_job_info">Vers.: <?=$label["Version"]?></td>
			</tr>						
			<tr>
				<td class="label_job_info">&nbsp;</td>
				<td class="label_job_info"><strong>Delivery Date: <? if($label["Date"]=="IOA"){?><font style="font-style:italic "><?=$label["Date"]?></font><? } else{echo date("d M y",strtotime($label["Date"]));}?></strong></td>
			</tr>
			<tr>
				<td class="label_job_info">&nbsp;</td>
				<td class="label_job_info"><strong>D/Type: <?=$label["DType"]?> - Qty: <?=$label["Quantity"]?></strong></td>
			</tr>
			<tr>
				<td class="label_coural_address"> </td>
			</tr>			
			<tr>
				<td class="label_coural_address"></td>
			</tr>	
			<tr>
				<td rowspan="3"><img src="images/coural_logo.jpg" height="38" width="71" ></td>
				<td class="label_coural_address">Coural Rural Couriers, 41 Havill Street,Palmerston North, 06 357 3129</td>
			</tr>
				
		</table>
	</td>
<?
	if(fmod($count,2)!=0){
		$color="blue";
?>
		</tr>
<?
		if(fmod($count+1,8)!=0){
?>		
			<tr>
				<td colspan="3" height="<?=$space_vert?>">&nbsp;</td>
			</tr>
<?
		}
?>			
		<tr>
<?
	}
	else{
?>
			<td width="<?=$space_hor?>">&nbsp;</td>
<?		
	}
}

function get_invoice_no(){
	$id = get("payout_invoice_no","max_id","WHERE invoice_id=1");
	$id++;
	$qry = "UPDATE payout_invoice_no SET max_id=$id";
	query($qry);
	return sprintf('%06d',$id);
}

function get_add_rates_qry(){
	return "(IF(job.add_folding_to_invoice='Y',job.folding_fee,0) + IF(job.add_premium_to_invoice='Y',job.premium,0))";
}

function load_circ_con($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  Date,
						Job,
						Pub,
						Qty AS 'Circ Qty',
						Qty_Bdls AS 'Bdl Qty',
						Rate AS 'Circ Rate',
						Bdl_Price AS 'Bdl Rate',
						ROUND(Amt+`Amt Bdls`,2) AS Total,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(Amt+`Amt Bdls`),2) AS 'Total (incl. GST)'
				FROM (
				SELECT  delivery_date AS Date,
						job.job_no AS Job,
						job.publication AS Pub,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						
						SUM(IF(job_route.dest_type<>'bundles',(job.folding_fee + job.premium)*amount+contr_rate*amount,0)) AS Amt,
						SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',
						ROUND(job.folding_fee + job.premium+contr_rate,4) AS Rate,
						GROUP_CONCAT(IF(bundle_price>0,bundle_price,NULL) SEPARATOR ',') AS 'Bdl_Price'
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE contractor_id='$op'
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
					# AND route_id=$route_id
				GROUP BY job.job_no
				) AS sum
				GROUP BY Job
				ORDER BY Date,Job,Pub";
			//echo nl2br($qry);
	return $m_table->LoadData($qry);
}

function load_circ_sdist($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  Date,
						Job,
						Pub,
						Qty AS 'Circ Qty',
						Qty_Bdls AS 'Bdl Qty',
						Rate AS 'Circ Rate',
						RateRed AS 'Circ RateRed',
						'' AS 'Bdl Rate',
						ROUND(Amt,2) AS Total,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(Amt),2) AS 'Total (incl. GST)'
				FROM (
				SELECT  delivery_date AS Date,
						job.job_no AS Job,
						job.publication AS Pub,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						
						SUM(IF(job_route.dest_type<>'bundles',(subdist_rate_red)*subdist_rate*amount,0)) AS Amt,
						SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',
						ROUND((subdist_rate_red)*subdist_rate,4) AS Rate,
						ROUND((subdist_rate_red),4) AS RateRed,
						bundle_price AS 'Bdl_Price'
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE subdist_id='$op'
					# AND subdist_id!=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
					# AND route_id=$route_id
				GROUP BY job.job_id
				) AS sum
				GROUP BY Job
				# HAVING Total>0
				ORDER BY Date,Job,Pub";
	return $m_table->LoadData($qry);
}


function load_circ_dist($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  Date,
						Job,
						Pub,
						Qty AS 'Circ Qty',
						'' AS 'Bdl Qty',
						Rate AS 'Circ Rate',
						'' AS 'Bdl Rate',
						ROUND(Amt,2) AS Total,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(Amt),2) AS 'Total (incl. GST)'
				FROM (
				SELECT  delivery_date AS Date,
						job.job_no AS Job,
						job.publication AS Pub,
						SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						
						SUM(IF(job_route.dest_type<>'bundles',dist_rate*amount,0)) AS Amt,
						/*SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',*/
						0 AS 'Amt Bdls',
						ROUND(dist_rate,4) AS Rate,
						bundle_price AS 'Bdl_Price'
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				WHERE dist_id='$op'
					# AND subdist_id=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
					# AND route_id=$route_id
				GROUP BY job.job_id
				) AS sum
				GROUP BY Job
				HAVING Total > 0
				ORDER BY Date,Job,Pub
				";
	return $m_table->LoadData($qry);
}

function load_circ_dist_contr_summary($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  	company AS Contractor,
						Qty AS 'Circ Qty',
						Qty_Bdls AS 'Bdl Qty',
						Rate AS 'Circ Rate',
						Bdl_Price AS 'Bdl Rate',
						ROUND(Amt+`Amt Bdls`,2) AS Total,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(Amt+`Amt Bdls`),2) AS 'Total (incl. GST)'
				FROM (
				SELECT  SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Qty,
						SUM(IF(job_route.dest_type='bundles',amount,0)) AS Qty_Bdls,
						
						SUM(IF(job_route.dest_type<>'bundles',(job.folding_fee + job.premium+contr_rate)*amount,0)) AS Amt,
						SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',
						ROUND(job.folding_fee + job.premium+contr_rate,4) AS Rate,
						bundle_price AS 'Bdl_Price',
						company,contractor_id,name
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN operator
				ON contractor_id=operator_id
				LEFT JOIN address
				ON address.operator_id=operator.operator_id
				WHERE dist_id='$op'
					# AND subdist_id=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
					# AND route_id=$route_id
				GROUP BY contractor_id
				) AS sum
				GROUP BY contractor_id
				ORDER BY name";
				//echo nl2br($qry);
	return $m_table->LoadData($qry);
}

function load_circ_dist_sdist_summary($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  	company AS 'S/Dist',
						ROUND(Amt,2) AS Total,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(Amt),2) AS 'Total (incl. GST)'
				FROM (
				SELECT  
						SUM(IF(job_route.dest_type<>'bundles',(subdist_rate_red)*subdist_rate*amount,0)) AS Amt,
						company,subdist_id
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN operator
				ON subdist_id=operator_id
				WHERE dist_id='$op'
					# AND dist_id=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
					# AND route_id=$route_id
				GROUP BY subdist_id
				) AS sum
				GROUP BY subdist_id
				ORDER BY `S/Dist`";
				//echo nl2br($qry);
	return $m_table->LoadData($qry);
}

function load_circ_dist_summary($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  	company AS Distributor,
						ROUND(Amt,2) AS Total,
						ROUND(".(1+$GST_CIRCULAR)."*SUM(Amt),2) AS 'Total (incl. GST)'
				FROM (
				SELECT  
						SUM(IF(job_route.dest_type<>'bundles',dist_rate*amount,0)) AS Amt,
						company,dist_id
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN operator
				ON dist_id=operator_id
				WHERE dist_id='$op'
					# AND subdist_id=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
					AND job.cancelled<>'Y'
					# AND route_id=$route_id
				GROUP BY dist_id
				) AS sum
				GROUP BY dist_id
				ORDER BY Distributor";
				//echo nl2br($qry);
	return $m_table->LoadData($qry);
}

function load_parc_dist_con_summary($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT 
				Contractor,
				SUM(Total) AS Total,
				SUM(`Total (incl. GST)`) AS 'Total (incl. GST)'
				FROM
				(
				SELECT 
					name,
					parcel_job_route.dist_id,
					operator.company AS Contractor,
									
					ROUND(red_rate_deliv * SUM(is_redeemed_D) + red_rate_pickup * SUM(is_redeemed_P),2) AS Total,
					ROUND((red_rate_deliv * SUM(is_redeemed_D) + red_rate_pickup * SUM(is_redeemed_P)) * (1 + $GST_CIRCULAR),2)
						AS 'Total (incl. GST)'
					
				FROM parcel_job_route
				LEFT JOIN operator
				ON contractor_id=operator.operator_id
				LEFT JOIN address
					ON address.operator_id=operator.operator_id
				LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_route.job_id
				LEFT JOIN `parcel_run` 
						   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
				LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
				WHERE parcel_job_route.dist_id=$op
					AND month(date) = '$month'
					AND year(date) = '$year'
					#AND parcel_job_route.route_id=$route_id
				GROUP BY parcel_job_route.dist_id,parcel_job_route.contractor_id,parcel_job_route.type
				ORDER BY company
				) AS val
				GROUP BY Contractor
				ORDER BY name
			";
	//echo nl2br($qry);
	return $m_table->LoadData($qry);
}


function load_parc_dist_summary($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT 
				Distributor,
				SUM(Total) AS Total,
				SUM(`Total (incl. GST)`) AS 'Total (incl. GST)'
				FROM
				(
				SELECT 
					parcel_job_route.dist_id,
					operator.company AS Distributor,
					
					ROUND(distr_payment_deliv * SUM(is_redeemed_D) + distr_payment_pickup * SUM(is_redeemed_P),2) AS Total,
					ROUND((distr_payment_deliv * SUM(is_redeemed_D) + distr_payment_pickup * SUM(is_redeemed_P)) * (1 + $GST_CIRCULAR),2)
						AS 'Total (incl. GST)'
					
				FROM parcel_job_route
				LEFT JOIN operator
				ON dist_id=operator_id
				LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_route.job_id
				LEFT JOIN `parcel_run` 
						   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
				LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
				WHERE parcel_job_route.dist_id=$op
					AND month(date) = '$month'
					AND year(date) = '$year'
					#AND parcel_job_route.route_id=$route_id
				GROUP BY parcel_job_route.dist_id,type
				ORDER BY company
				) AS val
				GROUP BY Distributor
				ORDER BY Distributor
			";
	//echo nl2br($qry);
	return $m_table->LoadData($qry);
}


function load_parc_dist($m_table,$op,$route_id,$month,$year){
global $GST_CIRCULAR;
	$qry = "	SELECT 
					parcel_job_route.dist_id,
					IF(type='CD','Documents',
						IF(type='CP','Parcels',
							IF(type='SR','Signature',type)
						)
					) AS 'Type',
					SUM(is_redeemed_P) As 'Quant/P',
					distr_payment_pickup AS 'Each/P',
					ROUND(distr_payment_pickup * SUM(is_redeemed_P),2) AS 'Value/P',
					SUM(is_redeemed_D) As 'Quant/D',
					distr_payment_deliv AS 'Each/D',
					ROUND(distr_payment_deliv * SUM(is_redeemed_D),2) AS 'Value/D',
					ROUND(distr_payment_deliv * SUM(is_redeemed_D) + distr_payment_pickup * SUM(is_redeemed_P),2) AS Total,
					ROUND((distr_payment_deliv * SUM(is_redeemed_D) + distr_payment_pickup * SUM(is_redeemed_P)) * (1 + $GST_CIRCULAR),2)
						AS 'Total (incl. GST)'
					
				FROM parcel_job_route
				LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_route.job_id
				LEFT JOIN `parcel_run` 
						   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
				LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
				WHERE parcel_job_route.dist_id=$op
					AND month(date) = '$month'
					AND year(date) = '$year'
					#AND parcel_job_route.route_id=$route_id
				GROUP BY parcel_job_route.dist_id,type
			";
	return $m_table->LoadData($qry);
}

function load_parc($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "
				SELECT 
					parcel_job_route.contractor_id,
					IF(type='CD','Documents',
						IF(type='CP','Parcels',
							IF(type='SR','Signature',type)
						)
					) AS 'Type',
					SUM(is_redeemed_P) As 'Quant/P',
					red_rate_pickup AS 'Each/P',
					ROUND(red_rate_pickup * SUM(is_redeemed_P),2) AS 'Value/P',
					SUM(is_redeemed_D) As 'Quant/D',
					red_rate_deliv AS 'Each/D',
					ROUND(red_rate_deliv * SUM(is_redeemed_D),2) AS 'Value/D',
					ROUND(red_rate_deliv * SUM(is_redeemed_D) + red_rate_pickup * SUM(is_redeemed_P),2) AS Total,
					ROUND((red_rate_deliv * SUM(is_redeemed_D) + red_rate_pickup * SUM(is_redeemed_P)) * (1 + $GST_CIRCULAR),2)
						AS 'Total (incl. GST)'
					
				FROM parcel_job_route
				LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_route.job_id
				LEFT JOIN `parcel_run` 
						   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
				LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
				WHERE parcel_job_route.contractor_id=$op
					AND month(date) = '$month'
					AND year(date) = '$year'
					#AND parcel_job_route.route_id=$route_id
				GROUP BY parcel_job_route.contractor_id,type
				/*UNION
				SELECT 
					parcel_job_route.dist_id,
					IF(type='CD','Documents',
						IF(type='CP','Parcels',
							IF(type='SR','Signature',type)
						)
					) AS 'Type',
					SUM(is_redeemed_P) As 'Quant/P',
					distr_payment_pickup AS 'Each/P',
					ROUND(distr_payment_pickup * SUM(is_redeemed_P),2) AS 'Value/P',
					SUM(is_redeemed_D) As 'Quant/D',
					distr_payment_deliv AS 'Each/D',
					ROUND(distr_payment_deliv * SUM(is_redeemed_D),2) AS 'Value/D',
					ROUND(distr_payment_deliv * SUM(is_redeemed_D) + distr_payment_pickup * SUM(is_redeemed_P),2) AS Total,
					ROUND((distr_payment_deliv * SUM(is_redeemed_D) + distr_payment_pickup * SUM(is_redeemed_P)) * (1 + $GST_CIRCULAR),2)
						AS 'Total (incl. GST)'
					
				FROM parcel_job_route
				LEFT JOIN parcel_job
					ON parcel_job.job_id=parcel_job_route.job_id
				LEFT JOIN `parcel_run` 
						   ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id` 
				LEFT JOIN route
							  ON route.route_id=parcel_job_route.route_id
				WHERE parcel_job_route.dist_id=$op
					AND month(date) = '$month'
					AND year(date) = '$year'
					#AND parcel_job_route.route_id=$route_id
				GROUP BY parcel_job_route.dist_id,type*/
			";
	return $m_table->LoadData($qry);
}

function getConRDs($op,$date){
	$qry = "SELECT group_concat(code SEPARATOR ',') AS rds
			FROM route_aff 
			LEFT JOIN route
			ON route.route_id=route_aff.route_id
			WHERE contractor_id=$op AND '$date' BETWEEN app_date and stop_date
			GROUP BY contractor_id ";
	$res = query($qry,0);
	$rds =  mysql_fetch_object($res);
	return $rds->rds;
}

function getConRoutes($op,$date){
	$qry = "SELECT route.route_id
			FROM route_aff 
			LEFT JOIN route
			ON route.route_id=route_aff.route_id
			WHERE contractor_id=$op AND '$date' BETWEEN app_date and stop_date
			GROUP BY contractor_id ";
	$res = query($qry,0);
	$routes = array();
	while($rds =  mysql_fetch_object($res)){
		$routes[$rds->route_id] = $op; 
	}
	
	return $routes;
}


// Return all routes of a contractor
function get_ops_for_dist_from_job($dist_id,$year,$month){
	$date = "$year-$month-15";
	$qry = "SELECT DISTINCT contractor_id FROM route_aff WHERE dist_id=$dist_id AND '$date' BETWEEN app_date AND stop_date GROUP BY route_id";


	$qry = "SELECT DISTINCT op FROM
			(
				SELECT DISTINCT contractor_id AS op, name
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN address
				ON address.operator_id=contractor_id
				WHERE dist_id='$dist_id'
					# AND contractor_id!=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
				UNION
				SELECT DISTINCT subdist_id AS op, name
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN address
				ON address.operator_id=subdist_id
				WHERE dist_id='$dist_id'
					# AND subdist_id!=dist_id
					AND month(delivery_date) = '$month'
					AND year(delivery_date) = '$year'
				UNION
				SELECT DISTINCT parcel_job_route.contractor_id AS op, name
				FROM parcel_job
				LEFT JOIN parcel_job_route
				ON parcel_job.job_id=parcel_job_route.job_id
				LEFT JOIN `parcel_run`
				ON `parcel_job_route`.`parcel_run_id` = `parcel_run`.`parcel_run_id`
				LEFT JOIN address
				ON address.operator_id=parcel_job_route.contractor_id
				WHERE parcel_job_route.dist_id='$dist_id'
					# AND contractor_id!=dist_id
					AND month(date) = '$month'
					AND year(date) = '$year'
				) ops
			ORDER BY name
			";
	
	$res = query($qry,0);
	
	
	$ops = array();
	while($c = mysql_fetch_object($res)){
		$ops[] = $c->op;
	}
	
	
	$ops[] = $dist_id;
	//print_r($ops);
	
	$ops = array_unique($ops);
	
	return $ops;
}

function print_op2($dist_id,$ops,$month,$year,$comment2="Comment"){
	global $GST_CIRCULAR;
	global $MYSQL;
	
	$font_size=7;
	//$ops = array();
	//$ops[] = 107;
	//print_r($ops);
	$date_show = date("F Y",mktime(0,0,0,$month,1,$year));
	$date_file = date("Y_m",mktime(0,0,0,$month,1,$year));
	$dist = get("address","name","WHERE operator_id=$dist_id");
	$dist_full = get("operator","company","WHERE operator_id=$dist_id");
	
	
	$title = "Payout Breakdown ".$tit." ".get("operator","company","WHERE operator_id='$dist_id'")." ($date_show)";
	//$file = $SEND_OUTPUT_DIR."/temp_payout2/payout_".$dist."_".$date_file.".pdf";
	$vfile = "/temp_payout2/payout_".$dist."_".$date_file.".pdf";
	$file = "E:/ProgramData/JobSys/temp_payout2/payout_".$dist."_".$date_file.".pdf";
	?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>	
			<h3><?=$title?></h3>
			<a href='<?=$vfile?>'>Download</a>
		</div>				
	
	<?php

	$new_page = true;
	//@mkdir($SEND_OUTPUT_DIR.'/temp_payout2');
	$tab  = new MySQLPDFTable($MYSQL,'p');
	$tab->footer=false;
	$tab->SetTopMargin(5);
	$tab->SetAutoPageBreak(true,1);
		//$tab->AliasNbPages();
		
	$tab->fontSize=7;
		
	$tab->AddPage();
	$tab->collField["Total"] = true;
	$tab->collField["Total (incl. GST)"] = true;
	$tab->collField["Circ Qty"] = true;
	$tab->collField["Bdl Qty"] = true;
	$start=true;
	
	// $oprs = array();
	// foreach($ops as $op){
		// $routes = getConRoutes($op, $year.'-'.$month.'-15');
		// foreach($routes as $route=>$op){
			// $oprs[$route] = $op;
		// }
	// }
	
	foreach($ops as $route=>$op){
		//$buffer = explode('.',$op_arr);
		//$op = $buffer[0];
		//$route = $buffer[1];
		$route  = 0;
		$ddata  = load_circ_dist($tab,$op,$route,$month,$year);
		$sdata  = load_circ_sdist($tab,$op,$route,$month,$year);
		$cdata  = load_circ_con($tab,$op,$route,$month,$year);
		$pdata  = load_parc($tab,$op,$route,$month,$year);
		$pddata = load_parc_dist($tab,$op,$route,$month,$year);
		$rds    = getConRDs($op,$year.'-'.$month.'-15');
		
		$invoice_no = get_invoice_no();
		$total=0;
		$total_gst=0;
		
		if(!$start) $tab->PageBreak();
		$start=false;
		//echo $op;
		$d_address = get("address","address","WHERE operator_id=$dist_id");
		$d_city = get("address","city","WHERE operator_id=$dist_id");
		$d_postcode = get("address","postcode","WHERE operator_id=$dist_id");
		$d_gst_num = get("address","gst_num","WHERE operator_id=$dist_id");
		$contr = get("operator","company","WHERE operator_id=$op");
		$c_address = get("address","address","WHERE operator_id=$op");
		$c_name = get("address","CONCAT(name,', ',first_name)","WHERE operator_id=$op");
		$c_last_name = get("address","name","WHERE operator_id=$op");
		$c_first_name = get("address","first_name","WHERE operator_id=$op");
		$c_company = get("operator","company","WHERE operator_id=$op");
		$c_city = get("address","city","WHERE operator_id=$op");
		$c_postcode = get("address","postcode","WHERE operator_id=$op");
		$c_gst_num = get("address","gst_num","WHERE operator_id=$op");
		

		if(trim($c_last_name)."-".trim($c_first_name) != $c_company)
			$contr = $c_name." / ".$c_company;
		else
			$contr = $c_name;
		
		$tab->Image('images/coural-rural-couriers.jpg',180,5,25);
		$tab->Ln(20);
		
		$tab->StartLine(6);
			$tab->WriteLine("Buyer created tax invoice - IRD approved - for:",'L',5,120);
			$tab->WriteLine("Supplier:",'L',5,50);
		$tab->StopLine();
		$tab->Ln(5);
		
		$tab->StartLine(10);
			$tab->WriteLine("",'L',5,10);	
			$tab->Cell(80,5,$contr,'LTR','L',false);
			$tab->Cell(30,5,'',false,'L',false);
			$tab->Cell(40,5,$dist_full,false,'L',false);
		$tab->StopLine();
		$tab->StartLine(10,255,255,255,'');
			$tab->WriteLine("",'L',5,10);	
			$tab->Cell(80,5,$c_address,'LR','L',false);
			$tab->Cell(30,5,'',false,'L',false);
			$tab->Cell(40,5,$d_address,false,'L',false);
		$tab->StopLine();
		$tab->StartLine(10,255,255,255,'');
			$tab->WriteLine("",'L',5,10);	
			$tab->Cell(80,5,$c_city.' '.$c_postcode,'LR','L',false);
			$tab->Cell(30,5,'',false,'L',false);
			$tab->Cell(40,5,$d_city.' '.$d_postcode,false,'L',false);
		$tab->StopLine();
		$tab->StartLine(10,255,255,255,'');
			$tab->WriteLine("",'L',5,10);	
			$tab->Cell(80,5,'','LRB','L',false);
			$tab->Cell(30,5,'',false,'L',false);
			$tab->Cell(40,5,'GST # '.$d_gst_num,false,'L',false);
		$tab->StopLine();
		$tab->Ln(5);
		
		$tab->StartLine(8);
			$tab->WriteLine("",'L',5,10);	
			$tab->WriteLine($rds,'L',8,20);
		$tab->StopLine();
		
		$tab->StartLine(8);
			$tab->WriteLine("",'L',5,10);	
			$tab->WriteLine("GST # $c_gst_num",'L',5,90);
			$tab->WriteLine("Invoice No: $invoice_no",'L',5,50);
		$tab->StopLine();
		//$tab->Ln(5);
	
	
			
		
		$header=array('Date','Job','Pub','Circ Qty','Bdl Qty','Circ Rate','Bdl Rate','Total', 'Total (incl. GST)');
		$width=array('Date'=>20,'Job'=>15,'Pub'=>25,'Circ Qty'=>15,'Bdl Qty'=>15,'Circ Rate'=>15,'Circ RateRed' => 15, 'Bdl Rate'=>15,'Total'=>20,'Total (incl. GST)'=>20);
		$maxw=get_maxw($width);
			
		// As Contractor
			
		
			
		

			
		if(count($cdata)>0){
			$new_page = true;
			
			$title = "Contractor payment advice for: Circulars ".$date_show;
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',8,20);
			$tab->StopLine();
			
			$tab->WriteHeader($header,$width);
			$tab->WriteTable($header,$cdata,$width,4,1);	
			$tab->StartLine($font_size);
				//$tab->WriteLine("",'R',5,10);
				$tab->WriteLine("Total:",'R',5,60);
				$tab->WriteLine($tab->getSum("Circ Qty",0),'R',5,$width["Circ Qty"]);
				$tab->WriteLine($tab->getSum("Bdl Qty",0),'R',5,$width["Bdl Qty"]);
				$tab->WriteLine("",'R',5,30);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
			$tab->collFieldVal["Bdl Qty"] = array();
			$tab->collFieldVal["Circ Qty"] = array();
		}		
			
	
		// As Sub dist
			
		//$header2=array('Date','Job','Pub','Circ Qty','Circ Rate','Total', 'Total (incl. GST)');
		//$width2=array('Date'=>20,'Job'=>15,'Pub'=>25,'Circ Qty'=>15,'Circ Rate'=>15,'Total'=>20,'Total (incl. GST)'=>20);
		$header2=$header;
		$width2=$width;
	
		if(count($sdata)>0){
			$title = "Sub distributor payment advice for: Circulars - ".$date_show;
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',8,$maxw-20);
			$tab->StopLine();
			$new_page = true;
			$tab->WriteHeader($header2,$width2);
			$tab->WriteTable($header2,$sdata,$width2,4,1);	
			$tab->StartLine($font_size);
				//$tab->WriteLine("",'R',5,110);
				$tab->WriteLine("Total:",'R',5,60);
				$tab->WriteLine($tab->getSum("Circ Qty",0),'R',5,$width2["Circ Qty"]);
				$tab->WriteLine($tab->getSum("Bdl Qty",0),'R',5,$width["Bdl Qty"]);
				$tab->WriteLine("",'R',5,30);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width2["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width2["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
			$tab->collFieldVal["Bdl Qty"] = array();
			$tab->collFieldVal["Circ Qty"] = array();
		}
		
		if(count($ddata)>0){
			$title = "Distributor payment advice for: Circulars ".$date_show;
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',8,20);
			$tab->StopLine();
			
			$tab->WriteHeader($header2,$width2);
			$tab->WriteTable($header2,$ddata,$width2,4,1);	
			$tab->StartLine($font_size);
				//$tab->WriteLine("",'R',5,10);
				$tab->WriteLine("Total:",'R',5,60);
				$tab->WriteLine($tab->getSum("Circ Qty",0),'R',5,$width2["Circ Qty"]);
				$tab->WriteLine($tab->getSum("Bdl Qty",0),'R',5,$width["Bdl Qty"]);
				$tab->WriteLine("",'R',5,30);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width2["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width2["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
			$tab->collFieldVal["Bdl Qty"] = array();
			$tab->collFieldVal["Circ Qty"] = array();
		}
		//else{
			//$tab->StartLine($font_size);
				//$tab->WriteLine("No circulars",'L',5,110);
			//$tab->StopLine();
		//}
	
				
		// Parcel
		
		$header=array('Type','Quant/P','Each/P','Value/P','Quant/D','Each/D','Value/D','Total', 'Total (incl. GST)');
		$width=array('Type'=>20,'Quant/P'=>15,'Each/P'=>15,'Value/P'=>15,'Quant/D'=>15,'Each/D'=>15,'Value/D'=>15,'Total'=>20,'Total (incl. GST)'=>20);
		$maxw=get_maxw($width);
		$width_empty = $maxw-40;
		
		if(count($pdata)>0){
			$title = "Contractor payment advice for: Tickets - ".$date_show;
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',8,$maxw-20);
			$tab->StopLine();
			$tab->WriteHeader($header,$width);
			$tab->WriteTable($header,$pdata,$width,4,1);	
			$tab->StartLine($font_size);
				//$tab->WriteLine("",'R',5,110);
				$tab->WriteLine("Total:",'R',5,110);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
		}
		
		
		
		
		if(count($pddata)>0){
			$title = "Distributor payment advice for: Tickets - ".$date_show;
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',8,$maxw-20);
			$tab->StopLine();
		
			$tab->WriteHeader($header,$width);
			$tab->WriteTable($header,$pddata,$width,4,1);	
			$tab->StartLine($font_size);
				//$tab->WriteLine("",'R',5,110);
				$tab->WriteLine("Total:",'R',5,110);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
		}
		
		//$tab->Ln(10);
		$tab->StartLine($font_size);
			//$tab->WriteLine("",'R',5,110);
			if(count($pdata)>0 || count($pddata)>0)
				$tab->WriteLine("Total Payout:",'R',5,110);
			else 
				$tab->WriteLine("Total Payout:",'R',5,120);
			$tab->WriteLine(number_format($total,2),'R',5,$width["Total"]);
			$tab->WriteLine(number_format($total_gst,2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
		$tab->Ln();
		$tab->SetFont('Helvetica','',9);
		$tab->MultiCell($maxw,4,$comment2,false,'L');
		//echo "Hello";
			
			
	}//foreach ops	
	
	$tot_tot=0;
	$tot_tot_gst=0;
	$tab->PageBreak();
	//$tab->norepField['Contractor'] = true;
	$distpdata = load_parc_dist_con_summary($tab,$dist_id,0,$month,$year);
	$header=array('Contractor','Total', 'Total (incl. GST)');
	$width=array('Contractor'=>25,'Total'=>20,'Total (incl. GST)'=>20);
	$maxw=get_maxw($width);
	$width_empty = $maxw-40;
	$title = "Contractor payment summary for: Tickets - ".$date_show;
	$tab->StartLine(10);
		$tab->WriteLine($title,'L',8,$maxw-20);
	$tab->StopLine();
	//$tab->Ln();
	if(count($distpdata)>0){
		$tab->collFieldVal["Total"] = array();
		$tab->collFieldVal["Total (incl. GST)"] = array();
		$tab->WriteHeader($header,$width);
		$tab->WriteTable($header,$distpdata,$width,4,1);	
		$tab->StartLine($font_size);
			$tot_tot = $tab->getSum("Total",2);
			$tot_tot_gst = $tab->getSum("Total (incl. GST)",2);
			$tab->WriteLine("Total:",'R',5,25);
			$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
			$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
	}
	else{
		$tab->StartLine($font_size);
			$tab->WriteLine("No tickets",'L',5,110);
		$tab->StopLine();
	}
	
	$distpdata = load_parc_dist_summary($tab,$dist_id,0,$month,$year);
	$header=array('Distributor','Total', 'Total (incl. GST)');
	$width=array('Distributor'=>25,'Total'=>20,'Total (incl. GST)'=>20);
	$maxw=get_maxw($width);
	$width_empty = $maxw-40;
	$title = "Distributor payment summary for: Tickets - ".$date_show;
	$tab->StartLine(10);
		$tab->WriteLine($title,'L',8,$maxw-20);
	$tab->StopLine();
	//$tab->Ln();
	if(count($distpdata)>0){
		$tab->collFieldVal["Total"] = array();
		$tab->collFieldVal["Total (incl. GST)"] = array();
		$tab->WriteHeader($header,$width);
		$tab->WriteTable($header,$distpdata,$width,4,1);	
		$tab->StartLine($font_size);
			$tot_tot += $tab->getSum("Total",2);
			$tot_tot_gst += $tab->getSum("Total (incl. GST)",2);
			$tab->WriteLine("Total:",'R',5,25);
			$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
			$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
	}
	else{
		$tab->StartLine($font_size);
			$tab->WriteLine("No tickets",'L',5,110);
		$tab->StopLine();
	}
	
	
	//$tab->PageBreak();
	$tab->Ln();
	$distpdata = load_circ_dist_contr_summary($tab,$dist_id,0,$month,$year);
	$header=array('Contractor','Total', 'Total (incl. GST)');
	$width=array('Contractor'=>25,'Total'=>20,'Total (incl. GST)'=>20);
	$maxw=get_maxw($width);
	$width_empty = $maxw-40;
	$title = "Contractor payment summary for: Circulars - ".$date_show;
	$tab->StartLine(10);
		$tab->WriteLine($title,'L',8,$maxw-20);
	$tab->StopLine();
	//$tab->Ln();
	if(count($distpdata)>0){
		$tab->collFieldVal["Total"] = array();
		$tab->collFieldVal["Total (incl. GST)"] = array();
		$tab->WriteHeader($header,$width);
		$tab->WriteTable($header,$distpdata,$width,4,1);	
		$tab->StartLine($font_size);
			$tot_tot += $tab->getSum("Total",2);
			$tot_tot_gst += $tab->getSum("Total (incl. GST)",2);
			$tab->WriteLine("Total:",'R',5,25);
			$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
			$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
	}
	$tab->Ln();
	
	$distpdata = load_circ_dist_sdist_summary($tab,$dist_id,0,$month,$year);
	$header=array('S/Dist','Total', 'Total (incl. GST)');
	$width=array('S/Dist'=>25,'Total'=>20,'Total (incl. GST)'=>20);
	$maxw=get_maxw($width);
	$width_empty = $maxw-40;
	$title = "S/Dist payment summary for: Circulars - ".$date_show;
	$tab->StartLine(10);
		$tab->WriteLine($title,'L',8,$maxw-20);
	$tab->StopLine();
	//$tab->Ln();
	if(count($distpdata)>0){
		$tab->collFieldVal["Total"] = array();
		$tab->collFieldVal["Total (incl. GST)"] = array();
		$tab->WriteHeader($header,$width);
		$tab->WriteTable($header,$distpdata,$width,4,1);	
		$tab->StartLine($font_size);
			$tot_tot += $tab->getSum("Total",2);
			$tot_tot_gst += $tab->getSum("Total (incl. GST)",2);
			$tab->WriteLine("Total:",'R',5,25);
			$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
			$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
	}
	$tab->Ln();
	
	$distpdata = load_circ_dist_summary($tab,$dist_id,0,$month,$year);
	$header=array('Distributor','Total', 'Total (incl. GST)');
	$width=array('Distributor'=>25,'Total'=>20,'Total (incl. GST)'=>20);
	$maxw=get_maxw($width);
	$width_empty = $maxw-40;
	$title = "Distributor payment summary for: Circulars - ".$date_show;
	$tab->StartLine(10);
		$tab->WriteLine($title,'L',8,$maxw-20);
	$tab->StopLine();
	//$tab->Ln();
	if(count($distpdata)>0){
		$tab->collFieldVal["Total"] = array();
		$tab->collFieldVal["Total (incl. GST)"] = array();
		$tab->WriteHeader($header,$width);
		$tab->WriteTable($header,$distpdata,$width,4,1);	
		$tab->StartLine($font_size);
			$tot_tot += $tab->getSum("Total",2);
			$tot_tot_gst += $tab->getSum("Total (incl. GST)",2);
			$tab->WriteLine("Total:",'R',5,25);
			$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
			$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
	}
	$tab->Ln();
	
	
	$header=array('','Total', 'Total (incl. GST)');
	$width=array(''=>25,'Total'=>20,'Total (incl. GST)'=>20);
	$title = "Total sums - ".$date_show;
	$tab->StartLine(10);
		$tab->WriteLine($title,'L',8,$maxw-20);
	$tab->StopLine();
	//$tab->Ln();
	$tab->WriteHeader($header,$width);
	$tab->StartLine($font_size);
			$tab->WriteLine("Total:",'R',5,25);
			$tab->WriteLine($tot_tot,'R',5,$width["Total"]);
			$tab->WriteLine($tot_tot_gst,'R',5,$width["Total (incl. GST)"]);
	$tab->StopLine();
	$tab->Output($file);
}

function write_labels_eight($dist_id,$is_current, $is_shareholder, $op_type, $format,$margin_top,$margin_bottom,$margin_left,$cell_height,$cell_width,$num_vert,$num_hor,$space_vert,$space_hor){
	global $MYSQL;
	$tab  = new MySQLPDFTable($MYSQL,'p');
	$tab->SetTopMargin($margin_top);
	$tab->SetLeftMargin($margin_left);
	$tab->SetAutoPageBreak($margin_bottom);
	$tab->AddPage();
	//$cell_height = 7;
	$cell_height /= 5;
	
	$where_add = "";
	if($is_current !== "All"){	
		$where_add.= " AND is_current='$is_current'";
	}
	if($is_shareholder !== "All"){
		$where_add.= " AND is_shareholder='$is_shareholder'";
	}
	if(is_array($op_type) && !in_array("All",$op_type)){
		$where_add.=" AND (";
		$start = true;
		foreach($op_type as $type){
			if($start)
				$where_add.= " $type='Y'";
			else
				$where_add.= " OR $type='Y'";
			$start=false;
		}
		$where_add.=")";
	}
	//echo "Hello".$dist_id[0];
	if(!$dist_id[0]){
		$res = query("SELECT operator_id FROM operator WHERE is_dist='Y'");
		$dist_id = array();
		while($d = mysql_fetch_object($res)){
			$dist_id[] = $d->operator_id;
		}
	}
	$dists = implode($dist_id,',');
	$ops = "SELECT * 
			FROM address 
			LEFT JOIN operator 
				ON operator.operator_id=address.operator_id
			RIGHT JOIN route_aff
				ON route_aff.contractor_id=operator.operator_id
			WHERE route_aff.dist_id IN ($dists)
				$where_add
				AND now() BETWEEN app_date AND stop_date
			GROUP BY operator.operator_id
			ORDER BY address.name
			
			";
	$ct_row = 1;
	$ct_col = 1;
	$res = query($ops,0);
	$addresses = array();
	$start = true;
	//echo $cell_height;
	$num_add = mysql_num_rows($res);
	$cur_add = 0;
	while($op = mysql_fetch_object($res)){
		$cur_add++;
		if($ct_col%($num_vert+1)==0 && !$start){
			$ct_col=1;
			$tab->PageBreak();
		}
		$start=false;
		$addresses[] = $op;
		//echo "H: ".$ct_row."/".$num_hor."/".$cur_add."/".$num_add."<br />";
		if($ct_row%$num_hor==0 || $cur_add == $num_add){
			$tab->StartLine(8);
				foreach($addresses as $address){
					$tab->Cell($cell_width,$cell_height,$address->first_name.' '.$address->name,"");
					$tab->Cell($space_hor,$cell_height,'');
				}
				$tab->Ln();
				foreach($addresses as $address){
					if($address->alias)
						$tab->Cell($cell_width,$cell_height,$address->alias,"");
					else
						$tab->Cell($cell_width,$cell_height,"","");
					$tab->Cell($space_hor,$cell_height,'');
				}
				$tab->Ln();
				foreach($addresses as $address){
					$tab->Cell($cell_width,$cell_height,$address->address,"");
					$tab->Cell($space_hor,$cell_height,'');
				}
				$tab->Ln();
				foreach($addresses as $address){
					$tab->Cell($cell_width,$cell_height,$address->address2,"");
					$tab->Cell($space_hor,$cell_height,'');
				}
				$tab->Ln();
				foreach($addresses as $address){
					$tab->Cell($cell_width,$cell_height,$address->city.' '.$address->postcode,"");
					$tab->Cell($space_hor,$cell_height,'');
				}
				
				$tab->Ln($space_vert);
				$tab->StopLine();
				$ct_col++;
				$ct_row=0;
				$addresses = array();
		}
		$ct_row++;
		
	}
	$tab->Output("tmp/tt.pdf");
	?><a href="tmp/tt.pdf">download</a><?
}

?>