<?
if($report=="by_region"){
	$qry = "SELECT DISTINCT area FROM route WHERE region='$region'";
	$res = query($qry);
	$tab  = new MySQLTable("reports.php","");
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;	
	$tab->startTable();
	
	while($obj_area=mysql_fetch_object($res)){
		$area = $obj_area->area;
		$qry = "SELECT 	r.code 			AS RD,
						o.name 			AS Contractor,
						a.address       AS Address,
						a.city          AS City,
						CONCAT(a.phone,'/',a.phone)     AS Phone,
						CONCAT(a.mobile,'/',a.mobile2)  AS Mobile,					
						(r.num_farmers+r.num_lifestyle) AS Total,
						r.num_farmers 	AS Farmers,
						r.num_lifestyle	AS Lifestyle,
						r.num_dairies	AS Dairies,
						r.num_sheep		AS Sheep,
						r.num_beef 		AS Beef,
						r.num_sheepbeef	AS 'Sheep/Beef',
						r.num_dairybeef	AS 'Dairy/Beef',
						r.num_hort		AS 'Hort'
	
				   FROM route r
				   LEFT JOIN
				   operator o
				   ON o.operator_id=r.operator_id
				   LEFT JOIN address a
				   ON a.operator_id=r.operator_id 			   
				   WHERE region='$region' AND area='$area'
				   ORDER BY region,area,code;";
		
		$tab->startNewLine();
		$tab->addLineWithStyle($area,"sql_extra_line_text");
		$tab->stopNewLine();
		$tab->writeSQLTableElement($qry);
		$tab->startNewLine();
		$tab->addLines("",6);
		$tab->addLine(get_sum_as("route","num_farmers+num_lifestyle","Total","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));
		$tab->addLine(get_sum("route","num_farmers","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));
		$tab->addLine(get_sum("route","num_lifestyle","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));
		$tab->addLine(get_sum("route","num_dairies","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));
		$tab->addLine(get_sum("route","num_sheep","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));	
		$tab->addLine(get_sum("route","num_beef","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));		
		$tab->addLine(get_sum("route","num_sheepbeef","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));		
		$tab->addLine(get_sum("route","num_dairybeef","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));		
		$tab->addLine(get_sum("route","num_hort","WHERE region='$region' AND area='$area'","GROUP BY '$region'"));			
		$tab->stopNewLine();	
	}
	$tab->startNewLine();
	$tab->addLines("",6);
	$tab->addLine(get_sum_as("route","num_farmers+num_lifestyle","Total","WHERE region='$region'","GROUP BY '$region'"));
	$tab->addLine(get_sum("route","num_farmers","WHERE region='$region'","GROUP BY '$region'"));
	$tab->addLine(get_sum("route","num_lifestyle","WHERE region='$region'","GROUP BY '$region'"));
	$tab->addLine(get_sum("route","num_dairies","WHERE region='$region'","GROUP BY '$region'"));
	$tab->addLine(get_sum("route","num_sheep","WHERE region='$region'","GROUP BY '$region'"));	
	$tab->addLine(get_sum("route","num_beef","WHERE region='$region'","GROUP BY '$region'"));		
	$tab->addLine(get_sum("route","num_sheepbeef","WHERE region='$region'","GROUP BY '$region'"));		
	$tab->addLine(get_sum("route","num_dairybeef","WHERE region='$region'","GROUP BY '$region'"));		
	$tab->addLine(get_sum("route","num_hort","WHERE region='$region'","GROUP BY '$region'"));			
	$tab->stopNewLine();		
	$tab->stopTable();
}

if($report=="month_job"){
	if($month && $year){
		$qry = "SELECT job.job_id             AS 'Job #',
					   invoice.delivery_date  AS 'Delivery Date',
					   client.name            AS 'Client',
					   client_pub.publication AS 'Publication',
					   route.area             AS 'Area',
					   route.island           AS 'Island',
					   SUM(job_route.amount)  AS 'Quantity'       
				FROM job
				LEFT JOIN invoice
				ON invoice.invoice_id=job.invoice_id
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN client
				ON client.client_id=job.client_id
				LEFT JOIN client_pub
				ON client_pub.client_pub_id=job.client_pub_id
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				WHERE month(invoice.delivery_date)='$month' 
					AND year(invoice.delivery_date)='$year'				
				GROUP BY job.job_id,client.name,client_pub.publication,route.area";
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->startTable();
		$tab->writeTable();
		$tab->stopTable();
	}//$if($month && $year)
}

function write_label($label,$count){
	if(!$label->FName) $label->FName="unknown";
	if(!$label->Name) $label->Name="unknown";
	if(!$label->Address) $label->Address="unknown";
	if(!$label->City) $label->City="unknown";
	if(!$label->Area) $label->Area="unknown";
	if(!$label->Publication) $label->Publication="unknown";
	if(!$label->Date) $label->Date="unknown";
	if(!$label->Quantity) $label->Quantity="unknown";
?>
		<table class="labels" align="right">
			<tr>	
				<td class="label_title" align="center" colspan="2"><?=$label->FName?> <?=$label->Name?></td>			
			</tr>
			<tr>	
				<td class="label_head" align="center" colspan="2"><?=$label->Address?></td>			
			</tr>
			<tr>	
				<td class="label_head" align="center"  colspan="2"><?=$label->City?></td>			
			</tr>
			<tr>	
				<td class="label_head" align="center" colspan="2"><?=$label->Area?></td>			
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="label_job_info">Job No.: <?=$label->Job?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="label_job_info">Job: <?=$label->Publication?>/<?=$label->Version?></td>
			</tr>			
			<tr>
				<td>&nbsp;</td>
				<td class="label_job_info">Delivery Date: <?=$label->Date?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="label_job_info">Quantity: <?=$label->Quantity?></td>
			</tr>
			<tr>
				<td rowspan="3"><img src="images/coural_logo.jpg" height="38" width="71" ></td>
				<td class="label_coural_address">Coural Rural Couriers</td>
			</tr>
			<tr>
				<td class="label_coural_address">41 Havill Strees,Palmerston North </td>
			</tr>			
			<tr>
				<td class="label_coural_address">06 357 3129</td>
			</tr>		
		</table>
<?
	if($count==8){
?>
		<span class="pagebreak">.</span>
<?
	}
}
if($report=="label"){
	$qry = "SELECT address.name       AS Name,
				   address.first_name AS FName,
				   address.address    AS Address,
				   address.city       AS City,
				   address.postcode   AS Postcode,
				   route.area         AS Area,
				   job.job_id         AS Job,
				   invoice.delivery_date AS Date,
				   job_route.amount      AS Quantity,
				   client_pub.version    AS Version,
				   client_pub.publication   AS Publication
			FROM job
			LEFT JOIN job_route
			ON job.job_id=job_route.job_id
			LEFT JOIN route
			ON job_route.route_id=route.route_id
			LEFT JOIN invoice
			ON invoice.invoice_id=job.invoice_id
			LEFT JOIN client_pub 
			ON job.client_pub_id=client_pub.client_pub_id
			LEFT JOIN address
			ON address.operator_id=route.operator_id
			WHERE job.job_id='$job_id'";
	$res = query($qry);
	$count=0;
	while($label=mysql_fetch_object	($res)){
		write_label($label,$count++);
	}
}

if($report=="weekly_by_dist"){
	$qry = "";
}
?>