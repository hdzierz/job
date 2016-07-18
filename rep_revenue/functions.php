<?
include_once $dir."includes/fpdf/fpdf.php";
define('FPDF_FONTPATH',$dir.'includes/fpdf/font/');

class a5label extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        //$this->Image('images/coural-rural-couriers.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        //$this->Cell(30,10,'Title',1,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}



function write_a5($data){
    $pdf = new a5label('L', 'mm', 'A5');
    $pdf->AliasNbPages(); 
    $pdf->AddPage();
    $pdf->Cell(0,10,'Printing line number ',0,1);
    
    $pdf->Output('temp_deliv/tt'.$data['Dropoff'].'.pdf','F');
}

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
            <col width="130">
            <col width="80">
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
				<td class="a4label_head_small" style="font-weight:bold; font-size: 14pt"  align="center" colspan="2">
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
					<? if($label["ShowNotes"] == 'Y'){ echo  "Comments: ".$label["Comments"]."<br />"; } ?>
					Delivery Date: <? if($label["Date"]=="IOA"){?><font style="font-style:italic "><?=$label["Date"]?></font><? } else{echo date("d M y",strtotime($label["Date"]));}?><br />
					D/Type: <?=$label["DType"]?><br />
                    Version: <?=$label["Version"]?><br />
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
				<td class="label_job_info"><? if($label["ShowNotes"] == 'Y'){ echo  "Comments: ".$label["Comments"]."<br />"; } ?></td>
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
                <td class="label_job_info">&nbsp;</td>
                <td class="label_job_info"><strong>Version: <?=$label["Version"]?></strong></td>
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
	return "(IF(job.add_folding_to_invoice='Y',job.folding_fee,0) + job.premium_sell)";
}

function load_circ_con($m_table,$op,$route_id,$month,$year){
	global $GST_CIRCULAR;
	$qry = "SELECT  Date,
						Job,
						Pub,
                        Weight,
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
                        round(job.weight,0) AS Weight,
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
						
						SUM(IF(job_route.dest_type<>'bundles',(1-subdist_rate_red/100)*subdist_rate*amount,0)) AS Amt,
						SUM(IF(job_route.dest_type='bundles',bundle_price*amount,0)) AS 'Amt Bdls',
						ROUND((1-subdist_rate_red/100)*subdist_rate,4) AS Rate,
						ROUND((1-subdist_rate_red/100),4) AS RateRed,
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
						SUM(IF(job_route.dest_type<>'bundles',(1-subdist_rate_red/100)*subdist_rate*amount,0)) AS Amt,
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
					IF(type='CD' AND org=3,'Documents mobile',
                        IF(type='CD' AND org<3,'Documents',
                            IF(type='CP' AND org=3,'Parcels mobile',
						        IF(type='CP' AND org<3,'Parcels',
							        IF(type='SR','Signature',type)
                                )
                            )
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
				GROUP BY parcel_job_route.dist_id,type,parcel_job_route.org
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

function print_op2($submit, $dist_id,$ops,$month,$year,$comment2="Comment"){
	global $GST_CIRCULAR;
	global $MYSQL;
	
    $send_email = false;
    if($submit == "Send Out"){
        $send_email=true;
    }
    
	$font_size=7;
	//$ops = array();
	//$ops[] = 107;
	//print_r($ops);
	$date_show = date("F Y",mktime(0,0,0,$month,1,$year));
	$date_file = date("Y_m",mktime(0,0,0,$month,1,$year));

    $date_csv = date("Y-m-t",mktime(0,0,0,$month,15,$year));
    $date_csv_due = date("Y-m-d",strtotime("+ 20 days", strtotime($date_csv)));


	$dist = get("address","name","WHERE operator_id=$dist_id");
	$dist_full = get("operator","company","WHERE operator_id=$dist_id");
	
	
	$title = "Payout Breakdown ".$tit." ".get("operator","company","WHERE operator_id='$dist_id'")." ($date_show)";
	//$file = $SEND_OUTPUT_DIR."/temp_payout2/payout_".$dist."_".$date_file.".pdf";
	$vfile = "/job_test/temp_payout2/";
    $dir_base = "/var/www/html/job_test/temp_payout2/$date_file/";
    $file_base = $dir_base."payout_".$dist."_".$date_file;
	$file = $file_base.".pdf";
    $vfile_csv = "/job_test/temp_payout2/payout_".$dist."_".$date_file.".csv";
    $file_csv = "/var/www/html/job_test/temp_payout2/payout_".$dist."_".$date_file.".csv";

    @mkdir($dir_base);

	?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>	
			<h3><?=$title?></h3>
			<a href='<?=$vfile?>'>Download PDF</a><br />
            <a href='<?=$vfile_csv?>'>Download CSV</a>
		</div>				
	
	<?php

    $csv = "*ContactName,EmailAddress,POAddressLine1,POAddressLine2,POAddressLine3,POAddressLine4,POCity,PORegion,POPostalCode,POCountry,*InvoiceNumber,*InvoiceDate,*DueDate,InventoryItemCode,Description,*Quantity,*UnitAmount,*AccountCode,*TaxType,TrackingName1,TrackingOption1,TrackingName2,TrackingOption2,Currency\n";

	$new_page = true;
	//@mkdir($SEND_OUTPUT_DIR.'/temp_payout2');
	$start=true;
	
    $csv_line_cont_lbm = "\"%s\",,,,,,,,,,%d,%s,%s,,Contractor LBM,1,%.2f,312,15%% GST on Expenses,,,,,\n";
    $csv_line_cont_tic = "\"%s\",,,,,,,,,,%d,%s,%s,,Contractor Tickets,1,%.2f,313,15%% GST on Expenses,,,,,\n";
    $csv_line_scanner_charge = "\"%s\",,,,,,,,,,%d,%s,%s,,Contractor Scanner Charge,1,%.2f,190,15%% GST on Expenses,,,,,\n";
    $csv_line_mobile_pay = "\"%s\",,,,,,,,,,%d,%s,%s,,Contractor Mobile Pay,1,%.2f,190,15%% GST on Expenses,,,,,\n";
    $csv_line_depot_rent = "\"%s\",,,,,,,,,,%d,%s,%s,,Contractor Depot Rent,1,%.2f,368,15%% GST on Expenses,,,,,\n";
    $csv_line_sdist_lbm = "\"%s\",,,,,,,,,,%d,%s,%s,,Sub Distributor LBM,1,%.2f,303,15%% GST on Expenses,,,,,\n";
    $csv_line_dist_lbm = "\"%s\",,,,,,,,,,%d,%s,%s,,Distributor LBM,1,%.2f,302,15%% GST on Expenses,,,,,\n";
    $csv_line_dist_tic = "\"%s\",,,,,,,,,,%d,%s,%s,,Distributor Tickets,1,%.2f,304,15%% GST on Expenses,,,,,\n";
    	

    $name_printed=false;
	foreach($ops as $route=>$op){
        $tab  = new MySQLPDFTable($MYSQL,'p');
        $name_printed=false;
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
        $d_address2 = get("address","address2","WHERE operator_id=$dist_id");
		$d_city = get("address","city","WHERE operator_id=$dist_id");
		$d_postcode = get("address","postcode","WHERE operator_id=$dist_id");
		$d_gst_num = get("address","gst_num","WHERE operator_id=$dist_id");
        $d_scanner_charge = get("operator", "scanner_charge", "WHERE operator_id=$dist_id");
        $d_mobile_pay = get("operator", "mobile_pay", "WHERE operator_id=$dist_id");
		$contr = get("operator","company","WHERE operator_id=$op");
		$c_address = get("address","address","WHERE operator_id=$op");
        $c_address2 = get("address","address2","WHERE operator_id=$op");
		$c_name = get("address","CONCAT(name,', ',first_name)","WHERE operator_id=$op");
		$c_last_name = get("address","name","WHERE operator_id=$op");
		$c_first_name = get("address","first_name","WHERE operator_id=$op");
		$c_company = get("operator","company","WHERE operator_id=$op");
		$c_city = get("address","city","WHERE operator_id=$op");
		$c_postcode = get("address","postcode","WHERE operator_id=$op");
		$c_gst_num = get("address","gst_num","WHERE operator_id=$op");
        $c_scanner_charge = -1 * get("operator", "scanner_charge", "WHERE operator_id=$op");
		$c_mobile_pay = get("operator", "mobile_pay", "WHERE operator_id=$op");
        $c_depot_rent = -1 * get("operator", "depot_rent", "WHERE operator_id=$op");

        $contr_f = $c_name;
		if(trim($c_last_name)."-".trim($c_first_name) != $c_company)
			$contr = $c_name." / ".$c_company;
		else
			$contr = $c_name;
	
        $tab->footer=false;
        $tab->SetTopMargin(17);
        $tab->SetAutoPageBreak(true,15);
        //$tab->AliasNbPages();

        $tab->fontSize=7;

        //$tab->AddPage();
        $tab->collField["Total"] = true;
        $tab->collField["Total (incl. GST)"] = true;
        $tab->collField["Circ Qty"] = true;
        $tab->collField["Bdl Qty"] = true;
	
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
            $tab->Cell(80,5,$c_address2,'LR','L',false);
            $tab->Cell(30,5,'',false,'L',false);
            $tab->Cell(40,5,$d_address2,false,'L',false);
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
	
	
			
		
		$header=array('Date','Job','Pub','Weight', 'Circ Qty','Bdl Qty','Circ Rate','Bdl Rate','Total', 'Total (incl. GST)');
		$width=array('Date'=>20,'Job'=>10,'Pub'=>35,'Weight'=>10, 'Circ Qty'=>15,'Bdl Qty'=>15,'Circ Rate'=>15,'Circ RateRed' => 15, 'Bdl Rate'=>15,'Total'=>20,'Total (incl. GST)'=>20);
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
				$tab->WriteLine("Total:",'R',5,75);
				$tab->WriteLine($tab->getSum("Circ Qty",0),'R',5,$width["Circ Qty"]);
				$tab->WriteLine($tab->getSum("Bdl Qty",0),'R',5,$width["Bdl Qty"]);
				$tab->WriteLine("",'R',5,30);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
			$tab->StopLine();
            $csv_sum = $tab->getSum("Total",2);
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
			$tab->collFieldVal["Bdl Qty"] = array();
			$tab->collFieldVal["Circ Qty"] = array();
		}		
		

        if($csv_sum>0){	
            $csv_line = sprintf($csv_line_cont_lbm, $contr, $invoice_no, $date_csv, $date_csv_due, $csv_sum);
            $csv .= $csv_line;
            $csv_sum = 0;
            $name_printed=true;
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
				$tab->WriteLine("Total:",'R',5,75);
				$tab->WriteLine($tab->getSum("Circ Qty",0),'R',5,$width2["Circ Qty"]);
				$tab->WriteLine($tab->getSum("Bdl Qty",0),'R',5,$width["Bdl Qty"]);
				$tab->WriteLine("",'R',5,30);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width2["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width2["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
            $csv_sum = $tab->getSum("Total",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
			$tab->collFieldVal["Bdl Qty"] = array();
			$tab->collFieldVal["Circ Qty"] = array();

            
            if($name_printed) $n = "";
            else $n = $contr;
            
            if($csv_sum>0){
                $csv_line = sprintf($csv_line_sdist_lbm, $n, $invoice_no, $date_csv, $date_csv_due, $csv_sum);
                $csv .= $csv_line;
                $csv_sum = 0;
                $name_printed=true;
            }
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
				$tab->WriteLine("Total:",'R',5,75);
				$tab->WriteLine($tab->getSum("Circ Qty",0),'R',5,$width2["Circ Qty"]);
				$tab->WriteLine($tab->getSum("Bdl Qty",0),'R',5,$width["Bdl Qty"]);
				$tab->WriteLine("",'R',5,30);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width2["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width2["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
            $csv_sum = $tab->getSum("Total",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
			$tab->collFieldVal["Bdl Qty"] = array();
			$tab->collFieldVal["Circ Qty"] = array();

            if($name_printed) $n = "";
            else $n = $contr;

            if($csv_sum>0){
                $csv_line = sprintf($csv_line_dist_lbm, $n, $invoice_no, $date_csv, $date_csv_due, $csv_sum);
                $csv .= $csv_line;
                $csv_sum = 0;
                $name_printed=true;
            }
		}
				
		// Parcel
		
		$header=array('Type','','Quant/P','Each/P','Value/P','Quant/D','Each/D','Value/D','Total', 'Total (incl. GST)');
		$width=array('Type'=>20,''=>25,'Quant/P'=>15,'Each/P'=>15,'Value/P'=>15,'Quant/D'=>15,'Each/D'=>15,'Value/D'=>15,'Total'=>20,'Total (incl. GST)'=>20);
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
				$tab->WriteLine("Total:",'R',5,135);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
            $csv_sum = $tab->getSum("Total",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();

            $headerp=array('Type','','Total', 'Total (incl. GST)');
            $widthp=array('Type'=>20,''=>115,'Total'=>20,'Total (incl. GST)'=>20);
            $title = "Contractor other: ".$date_show;
            $tab->StartLine(10);
                $tab->WriteLine($title,'L',8,$maxw-20);
            $tab->StopLine();
            $tab->WriteHeader($headerp,$widthp);
            $GST = 1.15;
            $tab->StartLine($font_size);
                $tab->WriteLine("Scanner",'L',5,135);
                $tab->WriteLine(number_format($c_scanner_charge,2),'R',5,20);
                $tab->WriteLine(number_format($c_scanner_charge * $GST,2),'R',5,20);
            $tab->StopLine();
            $tab->StartLine($font_size);
                $tab->WriteLine("Mobile",'L',5,135);
                $tab->WriteLine(number_format($c_mobile_pay,2),'R',5,20);
                $tab->WriteLine(number_format($c_mobile_pay * $GST,2),'R',5,20);
            $tab->StopLine();
            $tab->StartLine($font_size);
                $tab->WriteLine("Depot",'L',5,135);
                $tab->WriteLine(number_format($c_depot_rent,2),'R',5,20);
                $tab->WriteLine(number_format($c_depot_rent * $GST,2),'R',5,20);
            $tab->StopLine();
            $total += $c_scanner_charge;
            $total += $c_mobile_pay;
            $total += $c_depot_rent;
            $total_gst += $c_scanner_charge * $GST;
            $total_gst += $c_mobile_pay * $GST;
            $total_gst += $c_depot_rent * $GST;
            $tab->StartLine($font_size);
                $tab->WriteLine("Total:",'R',5,135);
                $tab->WriteLine(number_format($c_depot_rent + $c_scanner_charge + $c_mobile_pay,2),'R',5,$width["Total"]);
                $tab->WriteLine(number_format($GST*($c_depot_rent + $c_scanner_charge + $c_mobile_pay),2),'R',5,$width["Total (incl. GST)"]);
            $tab->StopLine();
        

            if($name_printed) $n = "";
            else $n = $contr;

            if($csv_sum>0){
                $csv_line = sprintf($csv_line_cont_tic, $n, $invoice_no, $date_csv, $date_csv_due, $csv_sum);
                $csv .= $csv_line;
                $csv_sum = 0;
                $name_printed=true;
            }

            if($name_printed) $n = "";
            else $n = $contr;

            $csv_line = sprintf($csv_line_scanner_charge, $n, $invoice_no, $date_csv, $date_csv_due, $c_scanner_charge);
            $csv .= $csv_line;
            $csv_line = sprintf($csv_line_mobile_pay, $n, $invoice_no, $date_csv, $date_csv_due, $c_mobile_pay);
            $csv .= $csv_line;
            $csv_line = sprintf($csv_line_depot_rent, $n, $invoice_no, $date_csv, $date_csv_due, $c_depot_rent);
            $csv .= $csv_line;
            $name_printed=true;
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
				$tab->WriteLine("Total:",'R',5,135);
				$tab->WriteLine($tab->getSum("Total",2),'R',5,$width["Total"]);
				$tab->WriteLine($tab->getSum("Total (incl. GST)",2),'R',5,$width["Total (incl. GST)"]);
			$tab->StopLine();
			$total+=$tab->getSum("Total",2);
			$total_gst+=$tab->getSum("Total (incl. GST)",2);
            $csv_sum = $tab->getSum("Total",2);
			$tab->collFieldVal["Total"] = array();
			$tab->collFieldVal["Total (incl. GST)"] = array();
            if($name_printed) $n = "";
            else $n = $contr;

            if($csv_sum>0){
                $csv_line = sprintf($csv_line_dist_tic, $n, $invoice_no, $date_csv, $date_csv_due, $csv_sum);
                $csv .= $csv_line; 
            }  
            $csv_sum = 0; 
            $name_printed=true;

		}
		
		//$tab->Ln(10);
		$tab->StartLine($font_size);
			//$tab->WriteLine("",'R',5,110);
			if(count($pdata)>0 || count($pddata)>0)
				$tab->WriteLine("Total Payout:",'R',5,135);
			else 
				$tab->WriteLine("Total Payout:",'R',5,135);
			$tab->WriteLine(number_format($total,2),'R',5,$width["Total"]);
			$tab->WriteLine(number_format($total_gst,2),'R',5,$width["Total (incl. GST)"]);
		$tab->StopLine();
		
		$tab->Ln();
		$tab->SetFont('Helvetica','',9);
		$tab->MultiCell($maxw,4,$comment2,false,'L');
		//echo "Hello";
	
        $c_file = $file_base."_".$contr_f.".pdf";
        $tab->Output($c_file);
        if($send_email){
            send_operator_mail("payout2","",$c_file,$op,false, true);
        }		
	}//foreach ops	
    
    $csv_f = fopen($file_csv, "w");
    fwrite($csv_f, $csv);
    fclose($csv_f); 
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
