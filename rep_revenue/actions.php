<?
require_once $dir."includes/MySQLTable.php";
if($submit=="Export!" && $report=="envelopes"){
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="export.xls"');
	if($company){
	
		if($is_shareholder=='Y') 	$where_sh  = "AND operator.is_shareholder='Y'";
		if($is_current=='Y') 		$where_ic  = "AND operator.is_current='Y'";
		if($contract=='Y') 			$where_ct  = "AND operator.contract='Y'";
		if($agency=='Y') 			$where_ag  = "AND operator.agency='Y'";
		
		if($is_dist=='Y') 			$where_di  = "OR operator.is_dist='Y'";
		if($is_subdist=='Y') 		$where_sd  = "OR operator.is_subdist='Y'";
		if($is_contractor=='Y') 	$where_co  = "OR operator.is_contr='Y'";
		
		if($company != "All") 		$where_dist  = "AND route_aff.env_dist_id='$company'";
		
		$date = date("Y-m-d");
		
		if($company != "All"){
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
						
						AND(
							operator.is_dist='p'
							$where_co
							
						)
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
						
						AND(
							operator.is_dist='p'
							$where_sd
						)
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
						
						AND(
							operator.is_dist='p'
							$where_di
						)
						AND app_date<='$date'
						AND stop_date>'$date'
						
					GROUP BY ID			
					ORDER BY Dist,`Order`) AS result
					GROUP BY ID";
		}
		else{
			$qry = "SELECT address.*,
							operator.company AS Distributor
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
							$where_dist
							$where_sh
							$where_ic
							$where_ct
							$where_ag
					)
					AS address
					LEFT JOIN operator
					ON operator.operator_id=dist_id
					ORDER BY 'Name'";		
		}
		//echo nl2br($qry);die();
		$tab = new MySQLTable("report.php",$qry,$nameI="report");
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->cssSQLTable = "sqltable_8";
		$tab->cssSQLTable = "sqltable_8";
		$tab->showRec=0;
		$tab->startTable();
			$tab->writeTable();
		$tab->stopTable();
		die();
	}
}



?>