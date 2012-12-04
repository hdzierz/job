<?
if($submit=="Export" && $report=="by_region_dist"){
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="export.xls"');
	if($date){
		if($company!='All') $where_add = "AND operator.operator_id='$company'";
		
		$qry = "SELECT DISTINCT operator.operator_id AS dist_id,
						company,
						phone,
						phone2,
						mobile,
						mobile2
				FROM operator 
				LEFT JOIN address
				ON address.operator_id = operator.operator_id
				LEFT JOIN route_aff
				ON route_aff.env_dist_id=operator.operator_id				
				LEFT JOIN route
				ON route.route_id=route_aff.route_id	
				WHERE is_dist='Y' $where_add
					AND app_date<='$date'
					AND stop_date>'$date'
					AND operator.operator_id>0
				ORDER BY route.island,route.seq_region,route.seq_area,route.seq_code";
		$res = query($qry);
		$start=true;
		$first_page=true;
		$page=1;
		while($dist = mysql_fetch_object($res)){
			$qry = "SELECT DISTINCT region 
					FROM route
					LEFT JOIN route_aff
					ON route_aff.route_id=route.route_id
					WHERE route_aff.env_dist_id='$dist->dist_id'
						AND app_date<='$date'
						AND stop_date>'$date'
					ORDER BY route.island,route.seq_region";
			
			$res_reg = query($qry);
			while($reg = mysql_fetch_object($res_reg)){
				$tab = new MySQLTable("report.php",$qry,$nameI="report");
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->cssSQLTable = "sqltable_8";
				$tab->cssSQLTable = "sqltable_8";
				$tab->showRec=0;
			
				$qry_area = "SELECT DISTINCT area FROM route 
							LEFT JOIN route_aff
								ON route_aff.route_id=route.route_id
							WHERE route_aff.env_dist_id='$dist->dist_id'
								AND route.region='$reg->region'
								AND app_date<='$date'
								AND stop_date>'$date' 
							ORDER BY route.island,route.seq_region,route.seq_area,route.seq_code";
							//echo nl2br($qry_area)."<br />";
				$res_area = query($qry_area);
				
				//$tab->colWidth["Description"]=180;
				//$tab->colWidth["Phone"]=120;
				//$tab->colWidth["RD"]=120;
				$tab->colWidth["Contractor"]=120;
				//$tab->colWidth["Address"]=120;
				//$tab->colWidth["City"]=120;
				//$tab->colWidth["Mobile"]=80;
				$tab->cssSQLUnEvenCol="sqltabunevencol_white";
				$tab->hasLineSeperator=false;
				
				$done=false;
				
				$start=true;
				while($area=mysql_fetch_object($res_area)){
					$qry = "SELECT DISTINCT operator.operator_id AS subdist_id ,
								company,
								phone,
								phone2,
								mobile,
								mobile2
							FROM operator
							LEFT JOIN address
							ON address.operator_id = operator.operator_id					
							LEFT JOIN route_aff
							ON route_aff.env_subdist_id=operator.operator_id
							LEFT JOIN route
							ON route.route_id=route_aff.route_id	
							WHERE route_aff.env_dist_id='$dist->dist_id'
								AND route.region='$reg->region'
								AND route.area='$area->area'
								AND app_date<='$date'
								AND stop_date>'$date'
								AND operator.operator_id>0
							ORDER BY route.island,route.seq_region,route.seq_area,route.seq_code";
					//echo nl2br($qry);					
					$res_sd = query($qry);
					
					while($subdist=mysql_fetch_object($res_sd)){
						$tab->startTable();
						$page++;
						$first_page=false;
						$start=false;
						$qry_rd = "SELECT 	route.route_id AS record,
										code AS RD,
										first_name AS 'First Name',
										name	AS Name,
										first_name2 AS 'First Name2',
										name2 AS Name2,
										address AS Address,
										address2 AS Address2,
										city	AS City,
										phone AS Phone,
										phone2 AS Phone2,
										mobile AS Mobile,
										mobile2 AS Mobile2,
										route.description AS Description
								FROM route
								LEFT JOIN route_aff
								ON route_aff.route_id=route.route_id
								LEFT JOIN operator
								ON operator.operator_id=route_aff.env_contractor_id
								LEFT JOIN address
								ON operator.operator_id=address.operator_id
								WHERE route_aff.env_dist_id='$dist->dist_id'
									AND route_aff.env_subdist_id='$subdist->subdist_id'
									AND route.region = '$reg->region'
									AND area='$area->area'
									AND app_date<='$date'
									AND stop_date>'$date'
									AND operator.operator_id>0
								ORDER BY route.island,route.seq_region,route.seq_area,route.seq_code";
						//echo nl2br($qry_rd);		
						$ph_str = ", Phone(s): ".$dist->phone;
						if($dist->phone2) $ph_str .= ", ".$dist->phone2;
						if($dist->mobile) $ph_str .= ", ".$dist->mobile;
						if($dist->mobile2) $ph_str .= ", ".$dist->mobile2;
						
						$tab->startNewLine();
							$tab->addLineWithStyle("Distr.: $dist->company $ph_str","sql_extra_head_small_left",13);
						$tab->stopNewLine();	
						
						$ph_str = ", Phone(s): ".$subdist->phone;
						if($subdist->phone2) $ph_str .= ", ".$dist->phone2;
						if($subdist->mobile) $ph_str .= ", ".$dist->mobile;
						if($subdist->mobile2) $ph_str .= ", ".$dist->mobile2;
	
						$tab->startNewLine();
							$tab->addLineWithStyle("Region: ".$reg->region." / Area: ".$area->area,"sql_extra_head_small_left",13);
						$tab->stopNewLine();			
											
						$tab->startNewLine();
							$tab->addLineWithStyle("S/Distr.: $subdist->company $ph_str","sql_extra_head_small_left",13);
						$tab->stopNewLine();	
						$done=true;
				
						
						$tab->writeSQLTableElement($qry_rd,1);
						$tab->stopTable();	
					}
				}//while($subdist=mysql_fetch_object($res_sd)){
			}//while($reg = mysql_fetch_object($res_reg)){	
		}//while($dist = mysql_fetch_object($res)){
	}//if date
	
	
	
	die();
}
if($submit=="Export" && $report=="dropoff_details"){
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="export.xls"');
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
	die();
}


	
	if($submit=="Export" && $mode){
		
		split_rds();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="export.xls"');
		
		if($mode=="geo"){
			
			$target = "pc";
			write_geo_table($date,$region,$island,$target,"xls",$home_phone,$mobile_phone);
		}
		else{
			$dist_id = $_POST["dist_id"];
			write_dist_table($date,$dist_id,"xls",$home_phone,$mobile_phone);
		}
		die();
	}



?>