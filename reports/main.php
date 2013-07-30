<?

function analyse_aff_dates($aff){
	$result = array();
	
	$prev_app_date  = "";
	$prev_stop_date = "";
	
	if(count($aff)==0 || !$aff){
		return false;
	}
	$start=true;
	foreach($aff as $k=>$a){
		//echo $k." ".$a."<br />";
		if($a["app_date"]==$a["stop_date"]){
			$result = $a;
			$result["reason"] = "Zero Length Period";
			return $result;
		}
		// Gap
		else if(date("Y-m-d",strtotime("-1 day",strtotime($a["app_date"]))) > $prev_stop_date && !$start){
			$result = $a;
			$result["reason"] = "Gap";
			return $result;
		}
		// Overlap
		else if(date("Y-m-d",strtotime("-1 day",strtotime($a["app_date"]))) < $prev_stop_date && !$start){
			$result = $a;
			$result["reason"] = "Overlap";
			return $result;
		}
		else if($start && count($aff)==1 && $a["app_date"]>"1999-01-01"){
			$result = $a;
			$result["reason"] = "Gap";
			return $result;
		}
		else if($start && count($aff)==1 && $a["stop_date"]<"2030-01-01"){
			$result = $a;
			$result["reason"] = "Gap";
			return $result;
		}
		
		$start=false;
		$prev_app_date = $a["app_date"];
		$prev_stop_date = $a["stop_date"];
	}
	return false;
}

function analyse_ids($aff){
	$result = array();
	
	foreach($aff as $a){
		if(!$a["dist_id"]){
			$result = $a;
			$result["reason"] = "No distributor";
			return $result;
		}
	}
	return false;
}

if($report=="error"){
	$qry = "SELECT * FROM route";
	$res = query($qry);
	while($route = mysql_fetch_object($res)){
		$qry = "SELECT *
				FROM route_aff 
				LEFT JOIN route
				ON route.route_id=route_aff.route_id
				WHERE route.route_id=$route->route_id
				ORDER BY app_date";
		
		$res_aff = query($qry);
		$a=array();
		while($aff = mysql_fetch_assoc($res_aff)){
			$a[]=$aff;
		}
		
		$result = analyse_aff_dates($a);
		
		$result = analyse_ids($a);
		if($result)
			$error[] = $result;
	}
?>
	<table class="SQLTable">
<?	
	foreach($error as $e){
	
?>
		<tr>
			<td><?=$e["area"]?></td>
			<td><?=$e["code"]?></td>
			<td><?=$e["reason"]?></td>
			<td><a href="admin_route.php?action=edit&record=<?=$e["route_id"]?>&dest=default">Go to</a></td>
		</tr>
<?		
	}

?>
	</table>	
<?
}

if($report=="`off"){
	split_rds();
	if($mode=="geo"){
		$island = $_POST["island"];
		$region = $_POST["region"];
		$target = "pc";
		if(is_array($region))
			write_geo_table($date,$region,$island,$target,"print",$home_phone,$mobile_phone);				
		
	}
	else{
		$dist_id = $_POST["dist_id"];
		if($dist_id)
			write_dist_table($date,$dist_id,"print",$home_phone,$mobile_phone);
	}
}

if($report=="bible"){
	if(is_array($region))
		write_bible($date,$island,$region,"print",$home_phone,$mobile_phone);
}


if($report=="by_region_dist"){
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
					AND is_hidden<>'Y'
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
						AND is_hidden<>'Y'
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
								AND is_hidden<>'Y'
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
				$tab->hasLineSeperator=true;
				
				$done=false;
				
				$start=true;
				while($area=mysql_fetch_object($res_area)){
						if(!$first_page){
							
			?>
							<div class="pagebreak">&nbsp;</div>
			<?					
							
						}
			?>
						<div style="float:right; font-weight:bold; font-size:10px ">Page <?=$page?></div>
			<?									
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
								AND is_hidden<>'Y'
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
										CONCAT(
												first_name,' ',
												name,
												IF(first_name2 IS NOT NULL AND first_name2 <>'',CONCAT('<br />',first_name2,' '),''),
												IF(name2 IS NOT NULL AND name2 <>'',CONCAT(name2),'')
											   ) 
											AS Contractor,
										CONCAT(
												address,'<br />',
												IF(address2 IS NOT NULL AND address2 <> '',CONCAT(address2),'')
											)
												
											AS Address,
										city	AS City,
										CONCAT(
												phone,
												IF(phone2 IS NOT NULL AND phone2 <> '',CONCAT('<br />',phone2),''),
												IF(mobile2 IS NOT NULL AND mobile <> '',CONCAT('<br />',mobile),''),
												IF(mobile2 IS NOT NULL AND mobile2 <> '',CONCAT('<br />',mobile2),'')
											)
												
											AS Phone,				
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
									AND is_hidden<>'Y'
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
}

if($report=="address_details"){
		/*if($choice=="dist") 		{$where = "WHERE operator.is_dist='Y'"; $choice_t = "Distributors";}
		if($choice=="subdist") 		{$where = "WHERE operator.is_subdist='Y'";$choice_t = "Sub-Distributors";}
		if($choice=="dropoff") 		{$where = "WHERE operator.is_dropoff='Y'";$choice_t = "Dropoffs";}
		if($choice=="altdo") 		{$where = "WHERE operator.is_alt_dropoff='Y'";$choice_t = "Alt. Dropoffs";}
		if($choice=="contr") 		{$where = "WHERE operator.is_contr='Y'";$choice_t = "Contractors";}
		if($choice=="share") 		{$where = "WHERE operator.is_shareholder='Y'";$choice_t = "Shareholders";}
		if($choice=="distshare") 	{$where = "WHERE operator.is_dist='Y' AND operator.is_shareholder='Y'";$choice_t = "Distributors and Shareholders";}
		if($choice=="subdistshare") {$where = "WHERE operator.is_subdist='Y' AND operator.is_shareholder='Y'";$choice_t = "Sub-Distributors and Shareholders";}
		if($choice=="contrshare") 	{$where = "WHERE operator.is_contr='Y' AND operator.is_shareholder='Y'";$choice_t = "Contractors and Shareholders";}*/
		
		$where = "WHERE operator.operator_id IS NOT NULL ";
		
		if($is_shareholder=='Y') $where.=" AND operator.is_shareholder='Y' ";
		if($is_current=='Y') $where.=" AND operator.is_current='Y' ";
		if($contract=='Y') $where.=" AND operator.contract='Y' ";
		if($agency=='Y') $where.=" AND operator.agency='Y' ";
		
		if($is_dist=='Y') $where.=" AND operator.is_dist='Y' ";
		if($is_subdist=='Y') $where.=" AND operator.is_subdist='Y' ";
		if($is_contractor=='Y') $where.=" AND operator.is_contr='Y' ";
		
		
		if($is_shareholder=='N') $where.=" AND operator.is_shareholder<>'Y' ";
		if($is_current=='N') $where.=" AND operator.is_current<>'Y' ";
		if($contract=='N') $where.=" AND operator.contract<>'Y' ";
		if($agency=='N') $where.=" AND operator.agency<>'Y' ";
		
		if($is_dist=='N') $where.=" AND operator.is_dist<>'Y' ";
		if($is_subdist=='N') $where.=" AND operator.is_subdist<>'Y' ";
		if($is_contractor=='N') $where.=" AND operator.is_contr<>'Y' ";
		
		$where_tot.="WHERE ID IS NOT NULL";		
		if($company != "All") $where_tot.=" AND dist_id='$company'";		
		
		if($submit=="Export!"){
			
				$qry = "SELECT	 ID,
								Salutation,
								PName AS 'Print Name',
								`First Name`, 
								`Last Name`,
								Salutation2,
								`First Name2`, 
								`Last Name2`,
								`Trading As`,
								operator.company AS Distributor,
								`Address 1`,
								`Address 2`,
								`Postal Addr.`,
								City,
								Postcode,
								Region,
								Phone1,
								Phone2,
								Fax,
								Mobile1,
								Mobile2,
								Email,
								Bank,
								GST,
								`Mail Type`,
								`Card ID`,
								`Is Dist`,
								`Is S/Dist`,
								`Is Contr`,
								`Is DO`,
								`Is Alt. DO`,
								
								address.`Contract`,
								address.Agency,
								`Date started`,
								`Date left`,
								`DO Address`,
								`DO City`,
								`DO Notes`,
								`Current`,
								`Is Shareholder`,
								`Num Shares`,
								`Shares Bought`,
								`Shares Sold`,
								`Share Notes`
						FROM 
						(
							SELECT address_id AS ID,
								operator.company		AS Name,
								
							   IF(
									operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
									IF(
										address.first_name2 IS NOT NULL AND address.first_name2<>'',
											CONCAT(address.first_name2,' ',IF(address.name2 IS NOT NULL,address.name2,''),' &amp ',address.first_name,' ',address.name),
											CONCAT(address.first_name,' ',address.name)
									)
							   )		
													AS PName,
								salutation	AS Salutation,
								first_name 	AS 'First Name', 
								name 		AS 'Last Name',
								salutation2	AS Salutation2,
								first_name2 AS 'First Name2', 
								name2 		AS 'Last Name2',
								operator.alias		AS 'Trading As',
								address		AS 'Address 1',
								address2	AS 'Address 2',
								postal_addr AS 'Postal Addr.',
								city		AS City,
								postcode	AS Postcode,
								country 	AS Region,
								phone		AS Phone1,
								phone2		AS Phone2,
								fax			AS Fax,
								mobile		AS Mobile1,
								mobile2		AS Mobile2,
								email		AS Email,
								bank_num	AS Bank,
								gst_num		AS GST,
								card_id		AS `Card ID`,
								mail_type	AS 'Mail Type',
								operator.is_dist	AS 'Is Dist',
								operator.is_subdist	AS 'Is S/Dist',
								operator.is_dropoff	AS 'Is DO',
								operator.is_alt_dropoff	AS 'Is Alt. DO',
								operator.is_contr	AS 'Is Contr',
								operator.contract	AS 'Contract',
								operator.agency		AS Agency,
								IF(operator.date_started<'1990-01-01','',operator.date_started)	
											AS 'Date started',
								IF(operator.date_left<'1990-01-01','',operator.date_left)	
											AS 'Date left',
								operator.do_address	AS 'DO Address',
								operator.do_city		AS 'DO City',
								operator.deliv_notes	AS 'DO Notes',
								operator.is_current	AS 'Current',
								operator.is_shareholder 	
											AS 'Is Shareholder',
								operator.shares		AS 'Num Shares',
								IF(operator.share_bought<'1990-01-01','',operator.share_bought)
											AS 'Shares Bought',
								IF(operator.share_sold<'1990-01-01','',operator.share_sold)
											AS 'Shares Sold',
								operator.share_notes	AS 'Share Notes',
								
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
						
						
						$where
						) AS address
					LEFT JOIN operator
					ON operator.operator_id=dist_id
					ORDER BY 'Last Name'";		
			$filename = "addr_".$choice."_".date("Y-m-d").".htm";
			$tab  = new MySQLExport($filename,$qry);
			$tab->showRec=1;
		}
		else{			
		?>
			<div class="weekly_head">
				<h3>Address Details</h3>
			</div>
		<?
		
		$qry = "SELECT	 ID,
						type AS Type,
						
						`Last Name`,
						`First Name`, 
						`Trading As`,
						`Address 1`,
						`Address 2`,
						operator.company AS Distributor,
						City,
						Phone1,
						Fax,
						Mobile1,
						Email
				FROM 
				(
					SELECT address_id	AS ID,
							name 		AS 'Last Name',
							first_name 	AS 'First Name', 
							operator.alias		AS 'Trading As',
							address		AS 'Address 1',
							address2	AS 'Address 2',
							IF(is_contr='Y',
								(
									SELECT dist_id FROM route_aff WHERE route_aff.contractor_id=operator.operator_id AND '$date' BETWEEN app_date AND stop_date GROUP BY route_aff.contractor_id
								),
								IF(is_subdist='Y',
									(
										SELECT dist_id FROM route_aff WHERE route_aff.subdist_id=operator.operator_id AND '$date' BETWEEN app_date AND stop_date GROUP BY route_aff.contractor_id
									) ,
									operator.operator_id
								)
								
							)
								
							AS dist_id,
							IF(is_dist='Y',
								(
									'DIST'
								),
								IF(is_subdist='Y',
									(
										'S/DIST'
									) ,
									'CONTR'
								)
								
							)
								
							AS type,
							IF(postcode IS NOT NULL AND postcode <> '',CONCAT(city,', ',postcode),city)		AS City,
							phone		AS Phone1,
							#phone2		AS Phone2,
							fax			AS Fax,
							mobile		AS Mobile1,
							#mobile2	AS Mobile2,
							email		AS Email
					FROM address
					LEFT JOIN operator
					ON operator.operator_id=address.operator_id
					
					$where
				) AS address
				LEFT JOIN operator
				ON operator.operator_id=dist_id
				$where_tot
				ORDER BY 'Last Name'";				
		//echo nl2br($qry);
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=0;
		$tab->colWidth["Last Name"]=10;
		}
		$tab->startTable();
		$tab->writeTable();
		$tab->stopTable();					
		if($submit=="Export!"){	
		?>
			<a href="<?=$filename?>">Right Click to Download</a>
		<?		
		}
}




if($report=="pmp_updated"){
?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
			<h3 class="weekly_head_h2">RURAL DELIVERY NUMBERS (<? echo date("j F Y");?>)</h3>
		</div>							
<?	 
	if($choice){
		if($region[0]){
			$where = "AND region IN ('0'";
			foreach($region as $r){
				$where.=",'$r'";
			}
			$where.=")";
		}
		
		if($ni && $si){
			$where_add = "";
		}
		else if($ni) $where_add = "AND island='NI' AND is_hidden<>'Y' ";
		else if($si) $where_add = "AND island='SI' AND is_hidden<>'Y' ";
		
		/*if($dist_ids[0]!=''){
			$where = "AND dist_id IN ('0'";
			foreach($dist_ids as $dist_id){
				$where.=",'$dist_id'";
			}
			$where.=")";
		}
		*/
		$qry = "SELECT DISTINCT island,region,area 
				FROM route 
				
				WHERE island IS NOT NULL
				AND is_hidden<>'Y'
				$where
				$where_add
				ORDER BY island,seq_region,seq_area";
		//echo nl2br($qry);
		$res = query($qry);
		
		if($submit=="Export")
			$tab  = new MySQLExport("export.html",$qry);
		else
			$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->hiddenFields["TT"]=1;
		//$tab->colWidth["Description"]=400;
		$tab->maxLinesOnPage=60;
		$tab->startTable();
		
		
		
		$tab->hiddenFields["Total"]=1;
		$tab->hiddenFields["Farmers"]=1;
		$tab->hiddenFields["L/style"]=1;
		$tab->hiddenFields["Dairy"]=1;
		$tab->hiddenFields["Sheep"]=1;
		$tab->hiddenFields["Beef"]=1;
		$tab->hiddenFields["S/B"]=1;
		$tab->hiddenFields["D/B"]=1;
		$tab->hiddenFields["Hort"]=1;
		$tab->hiddenFields["F@90%"]=1;
		
		$tab->hiddenFields["Total #"]=1;
		$tab->hiddenFields["RM RR"]=1;
		$tab->hiddenFields["RM F"]=1;
		$tab->hiddenFields["RM D"]=1;


		if($type){
			foreach($type as $t){
				if($t=="RMT")
					$tab->hiddenFields["Total #"]=0;
				else
					$tab->hiddenFields[$t]=0;
			}
		}

		if(!$show_mailings) $add_where1 = " AND route.region <> 'MAILINGS'";		
		if(!$show_bbc) 	    $add_where2 = " AND route.region <> 'BAGS BOXES COUNTER'";		
		$test_num_lines=0;
		$num_lines=0;
		$page=1;
		
		$num_total_tot 		= 0;
		$num_farmers_tot  	= 0;
		$num_lifestyle_tot 	= 0;
		$num_dairies_tot  	= 0;
		$num_sheep_tot  	= 0;
		$num_beef_tot  		= 0;
		$num_sheepbeef_tot  = 0;
		$num_dairybeef_tot  = 0;
		$num_hort_tot  		= 0;
		$num_nzfw_tot  		= 0;
		$num_rmt_tot  		= 0;
		$num_rm_rr_tot  	= 0;
		$num_rm_f_tot 		= 0;
		$num_rm_d_tot  		= 0;
		$start=true;
		while($area = mysql_fetch_object($res)){
			$region = get("route","region","WHERE region='$area->region'");
			$island = get("route","island","WHERE area='$area->area'");
			
			if($choice==1){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								pmp_areacode AS PMP_AREA,
								pmp_runcode AS PMP_RUN,
								num_farmers+num_lifestyle AS Total,
								num_farmers AS Farmers,
								num_lifestyle AS 'L/style',
								num_dairies AS Dairy,
								num_sheep AS Sheep,
								num_beef AS Beef,
								num_sheepbeef AS 'S/B',
								num_dairybeef AS 'D/B',
								num_hort AS Hort,
								num_nzfw AS 'F@90%',
								rmt AS 'Total #',
								rm_rr AS 'RM RR',
								rm_f AS 'RM F',
								rm_d AS 'RM D'
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							AND island='$area->island'
							AND route.is_hidden<>'Y'
							AND (num_farmers+num_lifestyle)>0
							$add_where1
							$add_where2
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==2){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								num_farmers+num_lifestyle AS Total,
								num_farmers AS Farmers,
								num_lifestyle AS 'L/style',
								num_dairies AS Dairy,
								num_sheep AS Sheep,
								num_beef AS Beef,
								num_sheepbeef AS 'S/B',
								num_dairybeef AS 'D/B',
								num_hort AS Hort,
								num_nzfw AS 'F@90%',
								rmt AS 'Total #',
								rm_rr AS 'RM RR',
								rm_f AS 'RM F',
								rm_d AS 'RM D'
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							AND island='$area->island'
							AND (num_farmers+num_lifestyle)>0
							$add_where1
							$add_where2
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==3){
				if($submit=="Export") $region_q = "region	AS Region";
				else $region_q = "IF(LENGTH(region)>10,CONCAT(LEFT(region,10),'...'),region) 	AS Region";
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#$region_q,		
								#area AS Area,
								num_farmers+num_lifestyle AS Total,
								num_farmers AS Farmers,
								num_lifestyle AS 'L/style',
								num_dairies AS Dairy,
								num_sheep AS Sheep,
								num_beef AS Beef,
								num_sheepbeef AS 'S/B',
								num_dairybeef AS 'D/B',
								num_hort AS Hort,
								num_nzfw AS 'F@90%',
								rmt AS 'Total #',
								rm_rr AS 'RM RR',
								rm_f AS 'RM F',
								rm_d AS 'RM D',
								description AS Description
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							AND island='$area->island'
							AND (num_farmers+num_lifestyle)>0
							$add_where1
							$add_where2
						ORDER BY island,seq_region,seq_area,seq_code";
			}	
			
			if($choice==4){
				$qry = "SELECT  island AS Island,
								region 	AS Region,				
								area AS Area,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw) AS 'F@90%',
								SUM(rmt) AS 'Total #',
								SUM(rm_rr) AS 'RM RR',
								SUM(rm_f) AS 'RM F',
								SUM(rm_d) AS 'RM D'
						FROM route
						WHERE region='$area->region'
							AND area='$area->area'
							AND island='$area->island'
							AND route.is_hidden<>'Y'
							AND (num_farmers+num_lifestyle)>0
							$add_where1
							$add_where2
						GROUP BY island,region,area
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			
			//echo nl2br($qry); echo "<br /><br />";
			$res_tt = query($qry);
			$num_lines = mysql_num_rows($res_tt);
			
			if(($test_num_lines+($num_lines+3))>60){
				$tab->stopTable();
				$tab->startTable();
				$test_num_lines=0;
				$page++;
				
				?>
					<div class="pagebreak_right"><strong>Page <?=$page?></strong></div>
					<p>&nbsp;<br /></p>
				<?
				if($num_lines>=60) {
					$page++;
					$tab->page_num=$page;
				}
			}		
			$test_num_lines+=($num_lines+3);
			
			if($num_lines>0 ){
				if($choice!=4){
					$tab->startNewLine();
						$tab->addLineWithStyle("Island: $area->island Region: ".$region." Area: ".$area->area,"sql_extra_head_huge_left",3);
					$tab->stopNewLine();		
				}
				if($choice==4)
					$tab->writeSQLTableElement($qry,$start);
				else
					$tab->writeSQLTableElement($qry,1);
				$start=false;
				$num_total 		= get_sum_as("route","num_farmers+num_lifestyle","num_total","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_farmers 	= get_sum("route","num_farmers","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area ");
				$num_lifestyle 	= get_sum("route","num_lifestyle","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_dairies 	= get_sum("route","num_dairies","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_sheep 		= get_sum("route","num_sheep","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_beef 		= get_sum("route","num_beef","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_sheepbeef 	= get_sum("route","num_sheepbeef","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_dairybeef 	= get_sum("route","num_dairybeef","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_hort 		= get_sum("route","num_hort","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_nzfw 	= get_sum("route","num_nzfw","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				
				$num_rmt 	= get_sum("route","rmt","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_rm_rr 	= get_sum("route","rm_rr","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_rm_f 	= get_sum("route","rm_f","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				$num_rm_d 	= get_sum("route","rm_d","WHERE region='$area->region' AND area='$area->area' $add_where1 $add_where2","GROUP BY island,region,area");
				
				$num_total_tot 		+= $num_total;
				$num_farmers_tot  	+= $num_farmers;
				$num_lifestyle_tot 	+= $num_lifestyle;
				$num_dairies_tot  	+= $num_dairies;
				$num_sheep_tot  	+= $num_sheep;
				$num_beef_tot  		+= $num_beef;
				$num_sheepbeef_tot  += $num_sheepbeef;
				$num_dairybeef_tot  += $num_dairybeef;
				$num_hort_tot  		+= $num_hort;
				$num_nzfw_tot  		+= $num_nzfw;
				$num_rmt_tot  		+= $num_rmt;
				$num_rm_rr_tot  	+= $num_rm_rr;
				$num_rm_f_tot 		+= $num_rm_f;
				$num_rm_d_tot  		+= $num_rm_d;
				if($choice!=4){
					$tab->startNewLine();
						if($choice==1){
							$tab->addLine("");
							$tab->addLine("");
						}
						else if($choice==2){
							//$tab->addLine("");
						}
						else if($choice==3){
							//$tab->addLine("");
						}
						$tab->addLine("Total:");
						
						
						if(!$tab->hiddenFields["Total"])
							$tab->addLine("$num_total");	
						if(!$tab->hiddenFields["Farmers"])
							$tab->addLine("$num_farmers");				
						if(!$tab->hiddenFields["L/style"])
							$tab->addLine("$num_lifestyle");				
						if(!$tab->hiddenFields["Dairy"])
							$tab->addLine("$num_dairies");				
						if(!$tab->hiddenFields["Sheep"])
							$tab->addLine("$num_sheep");				
						if(!$tab->hiddenFields["Beef"])
							$tab->addLine("$num_beef");				
						if(!$tab->hiddenFields["S/B"])
							$tab->addLine("$num_sheepbeef");				
						if(!$tab->hiddenFields["D/B"])
							$tab->addLine("$num_dairybeef");				
						if(!$tab->hiddenFields["Hort"])
							$tab->addLine("$num_hort");				
						if(!$tab->hiddenFields["F@90%"])
							$tab->addLine("$num_nzfw");			
						if(!$tab->hiddenFields["Total #"])
							$tab->addLine("$num_rmt");			
						if(!$tab->hiddenFields["RM RR"])
							$tab->addLine("$num_rm_rr");			
						if(!$tab->hiddenFields["RM F"])
							$tab->addLine("$num_rm_f");			
						if(!$tab->hiddenFields["RM D"])
							$tab->addLine("$num_rm_d");				
					$tab->stopNewLine();
				}
			}
			
			
		}//while($area = mysql_fetch_object($res)){
		
		if(!$show_mailings) $add_where1 = " AND route.region <> 'MAILINGS'";		
		if(!$show_bbc) 	    $add_where2 = " AND route.region <> 'BAGS BOXES COUNTER'";		
		
		$tab->startNewLine();
			if($choice==1){
				$tab->addLine("");
				$tab->addLine("");
			}
			else if($choice==2){
				//$tab->addLine("");
			}
			else if($choice==3){
				//$tab->addLine("");
			}
			else if($choice==4){
				$tab->addLine("");
				$tab->addLine("");
			}
			$tab->addLine(" Grand Total:");
			
			
			if(!$tab->hiddenFields["Total"])
				$tab->addLine("$num_total_tot");	
			if(!$tab->hiddenFields["Farmers"])
				$tab->addLine("$num_farmers_tot");				
			if(!$tab->hiddenFields["L/style"])
				$tab->addLine("$num_lifestyle_tot");				
			if(!$tab->hiddenFields["Dairy"])
				$tab->addLine("$num_dairies_tot");				
			if(!$tab->hiddenFields["Sheep"])
				$tab->addLine("$num_sheep_tot");				
			if(!$tab->hiddenFields["Beef"])
				$tab->addLine("$num_beef_tot");				
			if(!$tab->hiddenFields["S/B"])
				$tab->addLine("$num_sheepbeef_tot");				
			if(!$tab->hiddenFields["D/B"])
				$tab->addLine("$num_dairybeef_tot");				
			if(!$tab->hiddenFields["Hort"])
				$tab->addLine("$num_hort_tot");				
			if(!$tab->hiddenFields["F@90%"])
				$tab->addLine("$num_nzfw_tot");			
			if(!$tab->hiddenFields["Total #"])
				$tab->addLine("$num_rmt_tot");			
			if(!$tab->hiddenFields["RM RR"])
				$tab->addLine("$num_rm_rr_tot");			
			if(!$tab->hiddenFields["RM F"])
				$tab->addLine("$num_rm_f_tot");			
			if(!$tab->hiddenFields["RM D"])
				$tab->addLine("$num_rm_d_tot");				
			
				
		$tab->stopNewLine();		
		$tab->stopTable();			
		if($submit=="Export"){
	?>
			<a href="export.html">Right Click for Download</a>
	<?	
		}
	}
}

if($report=="pmp_updated_dist_bu"){
?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
			<h3 class="weekly_head_h2">RURAL DELIVERY NUMBERS (<? echo date("j F Y");?>)</h3>
		</div>							
<?	 
	if($choice){
		
		if($dist_ids[0]){
			$where = "AND dist_id IN ('0'";
			foreach($dist_ids as $dist_id){
				$where.=",'$dist_id'";
			}
			$where.=")";
		}
		
		$qry = "SELECT operator.operator_id AS dist_id,operator.company,address.country
				FROM operator
				LEFT JOIN address 
				ON address.operator_id=operator.operator_id
				LEFT JOIN route_aff
				ON route_aff.dist_id = operator.operator_id 
				LEFT JOIN route
				ON route.route_id=route_aff.route_id
				WHERE island IS NOT NULL
					AND app_date <= now()
					AND stop_date > now()
					AND is_hidden<>'Y'
					$where
				GROUP BY operator.operator_id
				ORDER BY island,seq_region,seq_area";
		//echo nl2br($qry);
		$res = query($qry);
		
		if($submit=="Export")
			$tab  = new MySQLExport("export.html",$qry);
		else
			$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->hiddenFields["TT"]=1;
		//$tab->colWidth["Description"]=400;
		$tab->maxLinesOnPage=40;
		$tab->fieldNames["num_nzfw"] = 'F@90%';
		
		$tab->startTable();
				
		$tab->hiddenFields["Total"]=1;
		$tab->hiddenFields["Farmers"]=1;
		$tab->hiddenFields["L/style"]=1;
		$tab->hiddenFields["Dairy"]=1;
		$tab->hiddenFields["Sheep"]=1;
		$tab->hiddenFields["Beef"]=1;
		$tab->hiddenFields["S/B"]=1;
		$tab->hiddenFields["D/B"]=1;
		$tab->hiddenFields["Hort"]=1;
		$tab->hiddenFields["num_nzfw"]=1;
		
		$tab->hiddenFields["RMT"]=1;
		$tab->hiddenFields["RM RR"]=1;
		$tab->hiddenFields["RM F"]=1;
		$tab->hiddenFields["RM D"]=1;
		
		foreach($type as $t){
			if($t == "F@90%")
			$tab->hiddenFields["num_nzfw"]=0;
			else 
			$tab->hiddenFields[$t]=0;
		}

		if(!$show_mailings) $add_where1 = " AND route.region <> 'MAILINGS'";		
		if(!$show_bbc) 	    $add_where2 = " AND route.region <> 'BAGS BOXES COUNTER'";		
		$test_num_lines=0;
		$num_lines=0;
		$page=1;
		
		$num_total_tot 		= 0;
		$num_farmers_tot  	= 0;
		$num_lifestyle_tot 	= 0;
		$num_dairies_tot  	= 0;
		$num_sheep_tot  	= 0;
		$num_beef_tot  		= 0;
		$num_sheepbeef_tot  = 0;
		$num_dairybeef_tot  = 0;
		$num_hort_tot  		= 0;
		$num_nzfw_tot  		= 0;
		$start=true;
		while($dist = mysql_fetch_object($res)){	
			
			if($choice==1){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								pmp_areacode AS PMP_AREA,
								pmp_runcode AS PMP_RUN,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw)  AS num_nzfw
						FROM route
						LEFT JOIN route_aff
							ON route_aff.route_id = route.route_id 
						WHERE island IS NOT NULL
							AND app_date <= now()
							AND stop_date > now()
							AND sudist_id='$dist->dist_id'
							AND route.is_hidden<>'Y'
							AND (num_farmers+num_lifestyle)>0
						%s
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==2){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw) AS num_nzfw
						FROM route
						LEFT JOIN route_aff
							ON route_aff.route_id = route.route_id 
						WHERE (num_farmers+num_lifestyle)>0
							AND app_date <= now()
							AND stop_date > now()
							AND is_hidden<>'Y'
							AND dist_id='$dist->dist_id'
						%s
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==3){
				if($submit=="Export") $region_q = "region	AS Region";
				else $region_q = "IF(LENGTH(region)>10,CONCAT(LEFT(region,10),'...'),region) 	AS Region";
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#$region_q,		
								#area AS Area,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw)  AS num_nzfw,
								description AS Description
						FROM route
						LEFT JOIN route_aff
							ON route_aff.route_id = route.route_id 
						WHERE (num_farmers+num_lifestyle)>0
							AND app_date <= now()
							AND stop_date > now()
							AND is_hidden<>'Y'
							AND dist_id='$dist->dist_id'
						%s
						ORDER BY island,seq_region,seq_area,seq_code";
			}	
			
			$qry_sum = sprintf($qry,"GROUP BY dist_id");

			$qry = sprintf($qry,"GROUP BY RD");
			
			//echo nl2br($qry);
			
			$res_sums = query($qry_sum);
			$sums = mysql_fetch_object($res_sums);
			$sums->Total;
			
			$res_tt = query($qry);
			$num_lines = mysql_num_rows($res_tt);
			
			if(($test_num_lines+($num_lines+3))>60 && !$start){
			
				$tab->stopTable();
				$tab->startTable();
				$test_num_lines=0;
				$page++;
				
				?>
					<div class="pagebreak_right"><strong>Page <?=$page?></strong></div>
					<p>&nbsp;<br /></p>
				<?
				if($num_lines>=60) {
					$page++;
					$tab->page_num=$page;
				}
			}		
			$test_num_lines+=($num_lines+3);
			
			if($num_lines>0){
				$tab->startNewLine();
					$tab->addLineWithStyle("Distributor: $dist->company Region: ".$dist->country,"sql_extra_head_huge_left",3);
				$tab->stopNewLine();		
				$tab->writeSQLTableElement($qry,1);
				$tab->startNewLine();
					$lab_lfs = 'L/style';
					$lab_sb = 'S/B';
					$lab_db = 'D/B';
					$num_total 		= $sums->Total;
					$num_farmers	= $sums->Farmers;
					$num_lifestyle 		= $sums->$lab_lfs;
					$num_dairies 		= $sums->Dairy;
					$num_sheep 		= $sums->Sheep;
					$num_beef 		= $sums->Beef;
					$num_sheepbeef 		= $sums->$lab_sb;
					$num_dairybeef 		= $sums->$lab_db;
					$num_hort 		= $sums->Hort;
					$num_nzfw 		= $sums->num_nzfw;
					
					$num_total_tot 		+= $num_total;
					$num_farmers_tot  	+= $num_farmers;
					$num_lifestyle_tot 	+= $num_lifestyle;
					$num_dairies_tot  	+= $num_dairies;
					$num_sheep_tot  	+= $num_sheep;
					$num_beef_tot  		+= $num_beef;
					$num_sheepbeef_tot  += $num_sheepbeef;
					$num_dairybeef_tot  += $num_dairybeef;
					$num_hort_tot  		+= $num_hort;
					$num_nzfw_tot  		+= $num_nzfw;

					if($choice==1){
						$tab->addLine("");
						$tab->addLine("");
					}
					else if($choice==2){
						//$tab->addLine("");
					}
					else if($choice==3){
						//$tab->addLine("");
					}
					$tab->addLine("Total:");
					if(!$tab->hiddenFields["Total"])
						$tab->addLine("$num_total");	
					if(!$tab->hiddenFields["Farmers"])
						$tab->addLine("$num_farmers");				
					if(!$tab->hiddenFields["L/style"])
						$tab->addLine("$num_lifestyle");				
					if(!$tab->hiddenFields["Dairy"])
						$tab->addLine("$num_dairies");				
					if(!$tab->hiddenFields["Sheep"])
						$tab->addLine("$num_sheep");				
					if(!$tab->hiddenFields["Beef"])
						$tab->addLine("$num_beef");				
					if(!$tab->hiddenFields["S/B"])
						$tab->addLine("$num_sheepbeef");				
					if(!$tab->hiddenFields["D/B"])
						$tab->addLine("$num_dairybeef");				
					if(!$tab->hiddenFields["Hort"])
						$tab->addLine("$num_hort");				
					if(!$tab->hiddenFields["num_nzfw"])
						$tab->addLine("$num_nzfw");			
					if(!$tab->hiddenFields["RMT"])
						$tab->addLine("$num_rmt");			
					if(!$tab->hiddenFields["RM RR"])
						$tab->addLine("$num_rm_rr");			
					if(!$tab->hiddenFields["RM F"])
						$tab->addLine("$num_rm_f");			
					if(!$tab->hiddenFields["RM D"])
						$tab->addLine("$num_rm_d");				
				$tab->stopNewLine();
			}
			
			$start=false;
		}//while($area = mysql_fetch_object($res)){
		
		if(!$show_mailings) $add_where1 = " AND route.region <> 'MAILINGS'";		
		if(!$show_bbc) 	    $add_where2 = " AND route.region <> 'BAGS BOXES COUNTER'";		
		
		$tab->startNewLine();
			if($choice==1){
				$tab->addLine("");
				$tab->addLine("");
			}
			else if($choice==2){
				//$tab->addLine("");
			}
			else if($choice==3){
				//$tab->addLine("");
			}
			$tab->addLine(" Grand Total:");
			if(!$tab->hiddenFields["Total"])
				$tab->addLine("$num_total_tot");	
			if(!$tab->hiddenFields["Farmers"])
				$tab->addLine("$num_farmers_tot");				
			if(!$tab->hiddenFields["L/style"])
				$tab->addLine("$num_lifestyle_tot");				
			if(!$tab->hiddenFields["Dairy"])
				$tab->addLine("$num_dairies_tot");				
			if(!$tab->hiddenFields["Sheep"])
				$tab->addLine("$num_sheep_tot");				
			if(!$tab->hiddenFields["Beef"])
				$tab->addLine("$num_beef_tot");				
			if(!$tab->hiddenFields["S/B"])
				$tab->addLine("$num_sheepbeef_tot");				
			if(!$tab->hiddenFields["D/B"])
				$tab->addLine("$num_dairybeef_tot");				
			if(!$tab->hiddenFields["Hort"])
				$tab->addLine("$num_hort_tot");				
			if(!$tab->hiddenFields["num_nzfw"])
				$tab->addLine("$num_nzfw_tot");			
			if(!$tab->hiddenFields["RMT"])
				$tab->addLine("$num_rmt_tot");			
			if(!$tab->hiddenFields["RM RR"])
				$tab->addLine("$num_rm_rr_tot");			
			if(!$tab->hiddenFields["RM F"])
				$tab->addLine("$num_rm_f_tot");			
			if(!$tab->hiddenFields["RM D"])
				$tab->addLine("$num_rm_d_tot");				
			
		$tab->stopNewLine();		
		$tab->stopTable();			
		if($submit=="Export"){
	?>
			<a href="export.html">Right Click for Download</a>
	<?	
		}
	}
}

if($report=="pmp_updated_dist"){
?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
			<h3 class="weekly_head_h2">RURAL DELIVERY NUMBERS (<? echo date("j F Y");?>)</h3>
		</div>							
<?	 
	if($choice){
		
		if($dist_ids[0]){
			$where = "AND dist_id IN ('0'";
			foreach($dist_ids as $dist_id){
				$where.=",'$dist_id'";
			}
			$where.=")";
		}
		
		$qry = "SELECT route_aff.dist_id, operator.operator_id AS subdist_id,operator.company,address.country
				FROM operator
				LEFT JOIN address 
				ON address.operator_id=operator.operator_id
				LEFT JOIN route_aff
				ON route_aff.subdist_id = operator.operator_id 
				LEFT JOIN route
				ON route.route_id=route_aff.route_id
				WHERE island IS NOT NULL
					AND app_date <= now()
					AND stop_date > now()
					AND is_hidden<>'Y'
					$where
				GROUP BY operator.operator_id
				ORDER BY island,seq_region,seq_area";
		//echo nl2br($qry);
		$res = query($qry);
		
		if($submit=="Export")
			$tab  = new MySQLExport("export.html",$qry);
		else
			$tab  = new MySQLTable("reports.php",$qry);
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->hiddenFields["TT"]=1;
		//$tab->colWidth["Description"]=400;
		$tab->maxLinesOnPage=40;
		$tab->fieldNames["num_nzfw"] = 'F@90%';
		
		$tab->startTable();
				
		$tab->hiddenFields["Total"]=1;
		$tab->hiddenFields["Farmers"]=1;
		$tab->hiddenFields["L/style"]=1;
		$tab->hiddenFields["Dairy"]=1;
		$tab->hiddenFields["Sheep"]=1;
		$tab->hiddenFields["Beef"]=1;
		$tab->hiddenFields["S/B"]=1;
		$tab->hiddenFields["D/B"]=1;
		$tab->hiddenFields["Hort"]=1;
		$tab->hiddenFields["num_nzfw"]=1;
		
		$tab->hiddenFields["RMT"]=1;
		$tab->hiddenFields["RM RR"]=1;
		$tab->hiddenFields["RM F"]=1;
		$tab->hiddenFields["RM D"]=1;
		
		foreach($type as $t){
			if($t == "F@90%")
			$tab->hiddenFields["num_nzfw"]=0;
			else 
			$tab->hiddenFields[$t]=0;
		}

		if(!$show_mailings) $add_where1 = " AND route.region <> 'MAILINGS'";		
		if(!$show_bbc) 	    $add_where2 = " AND route.region <> 'BAGS BOXES COUNTER'";		
		$test_num_lines=0;
		$num_lines=0;
		$page=1;
		
		$num_total_tot 		= 0;
		$num_farmers_tot  	= 0;
		$num_lifestyle_tot 	= 0;
		$num_dairies_tot  	= 0;
		$num_sheep_tot  	= 0;
		$num_beef_tot  		= 0;
		$num_sheepbeef_tot  = 0;
		$num_dairybeef_tot  = 0;
		$num_hort_tot  		= 0;
		$num_nzfw_tot  		= 0;
		
		$num_total_tot_dist 		= 0;
		$num_farmers_tot_dist  		= 0;
		$num_lifestyle_tot_dist 	= 0;
		$num_dairies_tot_dist  		= 0;
		$num_sheep_tot_dist  		= 0;
		$num_beef_tot_dist  		= 0;
		$num_sheepbeef_tot_dist  	= 0;
		$num_dairybeef_tot_dist  	= 0;
		$num_hort_tot_dist  		= 0;
		$num_nzfw_tot_dist  		= 0;
		
					
		$start=true;
		$count=0;
		$dist_curr = false;
		$dist_prev = false;
		while($sdist = mysql_fetch_object($res)){
			$dist_curr = $sdist->dist_id;
			
			if($choice==1){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								pmp_areacode AS PMP_AREA,
								pmp_runcode AS PMP_RUN,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw)  AS num_nzfw
						FROM route
						LEFT JOIN route_aff
							ON route_aff.route_id = route.route_id 
						WHERE island IS NOT NULL
							AND app_date <= now()
							AND stop_date > now()
							AND subdist_id='$sdist->subdist_id'
							AND route.is_hidden<>'Y'
							AND (num_farmers+num_lifestyle)>0
						%s
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==2){
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#region 	AS Region,				
								#area AS Area,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw) AS num_nzfw
						FROM route
						LEFT JOIN route_aff
							ON route_aff.route_id = route.route_id 
						WHERE (num_farmers+num_lifestyle)>0
							AND app_date <= now()
							AND stop_date > now()
							AND is_hidden<>'Y'
							AND subdist_id='$sdist->subdist_id'
						%s
						ORDER BY island,seq_region,seq_area,seq_code";
			}
			if($choice==3){
				if($submit=="Export") $region_q = "region	AS Region";
				else $region_q = "IF(LENGTH(region)>10,CONCAT(LEFT(region,10),'...'),region) 	AS Region";
				$qry = "SELECT 	code AS RD,
								#island AS Island,
								#$region_q,		
								#area AS Area,
								SUM(num_farmers+num_lifestyle) AS Total,
								SUM(num_farmers) AS Farmers,
								SUM(num_lifestyle) AS 'L/style',
								SUM(num_dairies) AS Dairy,
								SUM(num_sheep) AS Sheep,
								SUM(num_beef) AS Beef,
								SUM(num_sheepbeef) AS 'S/B',
								SUM(num_dairybeef) AS 'D/B',
								SUM(num_hort) AS Hort,
								SUM(num_nzfw)  AS num_nzfw,
								description AS Description
						FROM route
						LEFT JOIN route_aff
							ON route_aff.route_id = route.route_id 
						WHERE (num_farmers+num_lifestyle)>0
							AND app_date <= now()
							AND stop_date > now()
							AND is_hidden<>'Y'
							AND subdist_id='$sdist->subdist_id'
						%s
						ORDER BY island,seq_region,seq_area,seq_code";
			}	
			
			$qry_sum = sprintf($qry,"GROUP BY subdist_id");

			$qry = sprintf($qry,"GROUP BY RD");
			
			//echo nl2br($qry);
			
			$res_sums = query($qry_sum);
			$sums = mysql_fetch_object($res_sums);

			if(($dist_curr<>$dist_prev && $count>0)){
				$tab->startNewLine();
					if($choice==1){
						$tab->addLine("");
						$tab->addLine("");
					}
					else if($choice==2){
						//$tab->addLine("");
					}
					else if($choice==3){
						//$tab->addLine("");
					}
					$tab->addLine("Total Dist.:");
					if(!$tab->hiddenFields["Total"])
						$tab->addLine("$num_total_tot_dist");	
					if(!$tab->hiddenFields["Farmers"])
						$tab->addLine("$num_farmers_tot_dist");				
					if(!$tab->hiddenFields["L/style"])
						$tab->addLine("$num_lifestyle_tot_dist");				
					if(!$tab->hiddenFields["Dairy"])
						$tab->addLine("$num_dairies_tot_dist");				
					if(!$tab->hiddenFields["Sheep"])
						$tab->addLine("$num_sheep_tot_dist");				
					if(!$tab->hiddenFields["Beef"])
						$tab->addLine("$num_beef_tot_dist");				
					if(!$tab->hiddenFields["S/B"])
						$tab->addLine("$num_sheepbeef_tot_dist");				
					if(!$tab->hiddenFields["D/B"])
						$tab->addLine("$num_dairybeef_tot_dist");				
					if(!$tab->hiddenFields["Hort"])
						$tab->addLine("$num_hort_tot_dist");				
					if(!$tab->hiddenFields["num_nzfw"])
						$tab->addLine("$num_nzfw_tot_dist");			
					if(!$tab->hiddenFields["RMT"])
						$tab->addLine("$num_rmt_tot_dist");			
					if(!$tab->hiddenFields["RM RR"])
						$tab->addLine("$num_rm_rr_tot_dist");			
					if(!$tab->hiddenFields["RM F"])
						$tab->addLine("$num_rm_f_tot_dist");			
					if(!$tab->hiddenFields["RM D"])
						$tab->addLine("$num_rm_d_tot_dist");				
					$tab->stopNewLine();
					
					$num_total_tot_dist 		= 0;
					$num_farmers_tot_dist  		= 0;
					$num_lifestyle_tot_dist 	= 0;
					$num_dairies_tot_dist  		= 0;
					$num_sheep_tot_dist  		= 0;
					$num_beef_tot_dist  		= 0;
					$num_sheepbeef_tot_dist  	= 0;
					$num_dairybeef_tot_dist  	= 0;
					$num_hort_tot_dist  		= 0;
					$num_nzfw_tot_dist  		= 0;
			}
			$res_tt = query($qry);
			$num_lines = mysql_num_rows($res_tt);
			
			if($breaksdist && !$start){
				$tab->stopTable();
				$tab->startTable();
				$test_num_lines=0;
				$page++;
				?>
					<div class="pagebreak_right"><strong>Page <?=$page?></strong></div>
					<p>&nbsp;<br /></p>
				<?
				$tab->page_num=$page;
			}
			else{
				if(($test_num_lines+($num_lines+3))>60 && !$start){
			
					$tab->stopTable();
					$tab->startTable();
					$test_num_lines=0;
					$page++;
					
					?>
						<div class="pagebreak_right"><strong>Page <?=$page?></strong></div>
						<p>&nbsp;<br /></p>
					<?
					if($num_lines>=60) {
						$page++;
						$tab->page_num=$page;
					}
				}
			}		
			$test_num_lines+=($num_lines+3);
			
			
			$dist_company = get("operator","company","WHERE operator_id=$dist_curr");
			$dist_country = get("address","country","WHERE operator_id=$dist_curr");
			$write_head=false;
			if($dist_curr<>$dist_prev){
				$tab->startNewLine();
					$tab->addLineWithStyle("Distributor: $dist_company Region: ".$dist_country,"sql_extra_head_huge_left",3);
				$tab->stopNewLine();
				$write_head=true;
			}
			
			if($num_lines>0){
				if($showsdist){
					$tab->startNewLine();
						$tab->addLineWithStyle("S/Distributor: $sdist->company","sql_extra_head_huge_left",3);
					$tab->stopNewLine();
					$tab->writeSQLTableElement($qry,1);
				}
				else{
					$tab->writeSQLTableElement($qry,$write_head);
				}
				$lab_lfs = 'L/style';
				$lab_sb = 'S/B';
				$lab_db = 'D/B';
				$num_total 		= $sums->Total;
				$num_farmers	= $sums->Farmers;
				$num_lifestyle 		= $sums->$lab_lfs;
				$num_dairies 		= $sums->Dairy;
				$num_sheep 		= $sums->Sheep;
				$num_beef 		= $sums->Beef;
				$num_sheepbeef 		= $sums->$lab_sb;
				$num_dairybeef 		= $sums->$lab_db;
				$num_hort 		= $sums->Hort;
				$num_nzfw 		= $sums->num_nzfw;
				
				$num_total_tot 		+= $num_total;
				$num_farmers_tot  	+= $num_farmers;
				$num_lifestyle_tot 	+= $num_lifestyle;
				$num_dairies_tot  	+= $num_dairies;
				$num_sheep_tot  	+= $num_sheep;
				$num_beef_tot  		+= $num_beef;
				$num_sheepbeef_tot  += $num_sheepbeef;
				$num_dairybeef_tot  += $num_dairybeef;
				$num_hort_tot  		+= $num_hort;
				$num_nzfw_tot  		+= $num_nzfw;
				
				
				$num_total_tot_dist 	 += $num_total;
				$num_farmers_tot_dist  	 += $num_farmers;
				$num_lifestyle_tot_dist  += $num_lifestyle;
				$num_dairies_tot_dist  	 += $num_dairies;
				$num_sheep_tot_dist  	 += $num_sheep;
				$num_beef_tot_dist 		 += $num_beef;
				$num_sheepbeef_tot_dist  += $num_sheepbeef;
				$num_dairybeef_tot_dist  += $num_dairybeef;
				$num_hort_tot_dist 		 += $num_hort;
				$num_nzfw_tot_dist 		 += $num_nzfw;
				
				if($showsdist){
					$tab->startNewLine();
						
						if($choice==1){
							$tab->addLine("");
							$tab->addLine("");
						}
						else if($choice==2){
							//$tab->addLine("");
						}
						else if($choice==3){
							//$tab->addLine("");
						}
						$tab->addLine("Total:");
						if(!$tab->hiddenFields["Total"])
							$tab->addLine("$num_total");	
						if(!$tab->hiddenFields["Farmers"])
							$tab->addLine("$num_farmers");				
						if(!$tab->hiddenFields["L/style"])
							$tab->addLine("$num_lifestyle");				
						if(!$tab->hiddenFields["Dairy"])
							$tab->addLine("$num_dairies");				
						if(!$tab->hiddenFields["Sheep"])
							$tab->addLine("$num_sheep");				
						if(!$tab->hiddenFields["Beef"])
							$tab->addLine("$num_beef");				
						if(!$tab->hiddenFields["S/B"])
							$tab->addLine("$num_sheepbeef");				
						if(!$tab->hiddenFields["D/B"])
							$tab->addLine("$num_dairybeef");				
						if(!$tab->hiddenFields["Hort"])
							$tab->addLine("$num_hort");				
						if(!$tab->hiddenFields["num_nzfw"])
							$tab->addLine("$num_nzfw");			
						if(!$tab->hiddenFields["RMT"])
							$tab->addLine("$num_rmt");			
						if(!$tab->hiddenFields["RM RR"])
							$tab->addLine("$num_rm_rr");			
						if(!$tab->hiddenFields["RM F"])
							$tab->addLine("$num_rm_f");			
						if(!$tab->hiddenFields["RM D"])
							$tab->addLine("$num_rm_d");				
					$tab->stopNewLine();
				}
				

			}
			$dist_prev = $sdist->dist_id;
			$start=false;
			$count++;
			if($count==mysql_num_rows($res)){
				
				$tab->startNewLine();
					if($choice==1){
						$tab->addLine("");
						$tab->addLine("");
					}
					else if($choice==2){
						//$tab->addLine("");
					}
					else if($choice==3){
						//$tab->addLine("");
					}
					$tab->addLine("Total Dist.:");
					if(!$tab->hiddenFields["Total"])
						$tab->addLine("$num_total_tot_dist");	
					if(!$tab->hiddenFields["Farmers"])
						$tab->addLine("$num_farmers_tot_dist");				
					if(!$tab->hiddenFields["L/style"])
						$tab->addLine("$num_lifestyle_tot_dist");				
					if(!$tab->hiddenFields["Dairy"])
						$tab->addLine("$num_dairies_tot_dist");				
					if(!$tab->hiddenFields["Sheep"])
						$tab->addLine("$num_sheep_tot_dist");				
					if(!$tab->hiddenFields["Beef"])
						$tab->addLine("$num_beef_tot_dist");				
					if(!$tab->hiddenFields["S/B"])
						$tab->addLine("$num_sheepbeef_tot_dist");				
					if(!$tab->hiddenFields["D/B"])
						$tab->addLine("$num_dairybeef_tot_dist");				
					if(!$tab->hiddenFields["Hort"])
						$tab->addLine("$num_hort_tot_dist");				
					if(!$tab->hiddenFields["num_nzfw"])
						$tab->addLine("$num_nzfw_tot_dist");			
					if(!$tab->hiddenFields["RMT"])
						$tab->addLine("$num_rmt_tot_dist");			
					if(!$tab->hiddenFields["RM RR"])
						$tab->addLine("$num_rm_rr_tot_dist");			
					if(!$tab->hiddenFields["RM F"])
						$tab->addLine("$num_rm_f_tot_dist");			
					if(!$tab->hiddenFields["RM D"])
						$tab->addLine("$num_rm_d_tot_dist");				
					$tab->stopNewLine();
					
					$num_total_tot_dist 		= 0;
					$num_farmers_tot_dist  		= 0;
					$num_lifestyle_tot_dist 	= 0;
					$num_dairies_tot_dist  		= 0;
					$num_sheep_tot_dist  		= 0;
					$num_beef_tot_dist  		= 0;
					$num_sheepbeef_tot_dist  	= 0;
					$num_dairybeef_tot_dist  	= 0;
					$num_hort_tot_dist  		= 0;
					$num_nzfw_tot_dist  		= 0;
				}
			
		}//while($area = mysql_fetch_object($res)){
		
		if(!$show_mailings) $add_where1 = " AND route.region <> 'MAILINGS'";		
		if(!$show_bbc) 	    $add_where2 = " AND route.region <> 'BAGS BOXES COUNTER'";		
		
		$tab->startNewLine();
			if($choice==1){
				$tab->addLine("");
				$tab->addLine("");
			}
			else if($choice==2){
				//$tab->addLine("");
			}
			else if($choice==3){
				//$tab->addLine("");
			}
			$tab->addLine(" Grand Total:");
			if(!$tab->hiddenFields["Total"])
				$tab->addLine("$num_total_tot");	
			if(!$tab->hiddenFields["Farmers"])
				$tab->addLine("$num_farmers_tot");				
			if(!$tab->hiddenFields["L/style"])
				$tab->addLine("$num_lifestyle_tot");				
			if(!$tab->hiddenFields["Dairy"])
				$tab->addLine("$num_dairies_tot");				
			if(!$tab->hiddenFields["Sheep"])
				$tab->addLine("$num_sheep_tot");				
			if(!$tab->hiddenFields["Beef"])
				$tab->addLine("$num_beef_tot");				
			if(!$tab->hiddenFields["S/B"])
				$tab->addLine("$num_sheepbeef_tot");				
			if(!$tab->hiddenFields["D/B"])
				$tab->addLine("$num_dairybeef_tot");				
			if(!$tab->hiddenFields["Hort"])
				$tab->addLine("$num_hort_tot");				
			if(!$tab->hiddenFields["num_nzfw"])
				$tab->addLine("$num_nzfw_tot");			
			if(!$tab->hiddenFields["RMT"])
				$tab->addLine("$num_rmt_tot");			
			if(!$tab->hiddenFields["RM RR"])
				$tab->addLine("$num_rm_rr_tot");			
			if(!$tab->hiddenFields["RM F"])
				$tab->addLine("$num_rm_f_tot");			
			if(!$tab->hiddenFields["RM D"])
				$tab->addLine("$num_rm_d_tot");				
			
		$tab->stopNewLine();		
		$tab->stopTable();			
		if($submit=="Export"){
	?>
			<a href="export.html">Right Click for Download</a>
	<?	
		}
	}
}


if($report=="total_box_holder"){
	if($region){
		$qry = "SELECT route.dist_id 						   AS Record,
					   operator.company						   AS Company,
					   route.area							   AS Area,
					   (route.num_lifestyle+route.num_farmers) AS Total
				FROM route
				LEFT JOIN operator
				ON operator.operator_id=route.dist_id
				WHERE route.region='$region'
					AND is_hidden<>'Y'
				GROUP BY area,route.dist_id
				ORDER BY operator.company,seq_area";
				
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->showRec=0;
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->startTable();
		$tab->writeTable();
		$tab->stopTable();			
	}
}

if($report=="by_region"){
	if($region){
		 $title = "Region: $region";
	?>
			<div class="weekly_head">
				<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
				<h3 class="weekly_head_h2"><?=$title?></h3>
			</div>							
	<?	 
		$qry = "SELECT DISTINCT area,region FROM route WHERE region='$region'";
		$res = query($qry);
		if($action=="export"){
			$tab  = new MySQLExport("export.html","");
		}
		else{
			$tab  = new MySQLTable("reports.php","");
			$tab->hasAddButton=false;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;	
		}
		//$tab->colWidth["RD"]=20;
		//$tab->colWidth["Contractor"]=50;
		//$tab->colWidth["City"]=30;
		
		$tab->colWidth["Description"]=280;
		$tab->cssSQLUnEvenCol="sqltabunevencol_white";
		$tab->hasLineSeperator=true;
		$tab->showRec=0;
		
		while($obj_area=mysql_fetch_object($res)){
			$tab->startTable();
			$area = $obj_area->area;
			$region = $obj_area->region;
			$qry = "SELECT 	r.route_id AS Record,
							code AS RD,
							CONCAT(
									first_name,' ',
									name,
									IF(first_name2 IS NOT NULL AND first_name2 <>'',CONCAT('<br />',first_name2,' '),''),
									IF(name2 IS NOT NULL AND name2 <>'',CONCAT(name2),'')
								   ) 
								AS Contractor,
							CONCAT(
									address,'<br />',
									IF(address2 IS NOT NULL AND address2 <> '',CONCAT(address2),'')
								)
									
								AS Address,
							city	AS City,
							CONCAT(
									phone,'<br />',
									IF(phone2 IS NOT NULL AND phone2 <> '',CONCAT(phone2),'')
								)
									
								AS Phone,				
							CONCAT(
									mobile,'<br />',
									IF(mobile2 IS NOT NULL AND mobile2 <> '',CONCAT(mobile2),'')
								)
									
								AS Mobile,																					
							r.description AS Description		
					   FROM route r
					   LEFT JOIN
					   route_aff ra
					   ON  ra.route_id=r.route_id
					   LEFT JOIN
					   operator o
					   ON o.operator_id=ra.contractor_id
					   LEFT JOIN address a
					   ON a.operator_id=ra.contractor_id		   
					   WHERE region='$region' 
					   		AND area='$area'
							AND '$date' >= app_date
							AND '$date' < stop_date
							AND is_hidden<>'Y'
					   ORDER BY island,seq_region,seq_area,seq_code;";
			//echo nl2br($qry)."<br />";
			$tab->startNewLine();
			if($action=="export")
				$tab->addBoldLine($region."/".$area,5);
			else
				$tab->addLineWithStyle($region."/".$area,"sql_extra_line_text",5);
			$tab->stopNewLine();
			$tab->writeSQLTableElement($qry);
			$tab->stopTable();
?>
			<div class="pagebreak_after">&nbsp;</div>
<?				
		}

		
		if($action=="export"){
	?>
			<a href="export.html">Right Click for Download</a>
	<?	
		}
	} // if region
}



if($report=="by_dist_num"){
	if($dist_id){
		 $dist  = get("operator","company","WHERE operator_id='$dist_id'");
		 $title = "Distributor: $dist";
		?>
			<div class="weekly_head">
				<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
				<h3 class="weekly_head_h2"><?=$title?></h3>
			</div>							
		<?	 
		$qry = "SELECT DISTINCT area,region FROM route WHERE dist_id='$dist_id'";
		$res = query($qry);
		if($action=="export"){
			$tab  = new MySQLExport("test.htm","");
		}
		else{
			$tab  = new MySQLTable("reports.php","");
			$tab->hasAddButton=false;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;	
		}
		$tab->colWidth["RD"]=20;
		$tab->colWidth["Contractor"]=50;
		$tab->colWidth["City"]=30;
		$tab->startTable();
		
		while($obj_area=mysql_fetch_object($res)){
			$area = $obj_area->area;
			$region = $obj_area->region;
			$qry = "SELECT 	r.code 			AS RD,
							(SELECT company FROM operator WHERE operator.operator_id=r.subdist_id) AS 'Sub-Dist',
							o.company		AS Contractor,
							a.address       AS Address,
							a.city          AS City,
							IF(a.phone IS NOT NULL,IF(a.phone2 IS NOT NULL,CONCAT(a.phone,'<br />',a.phone2),a.phone),'') AS Phone,
							IF(a.mobile IS NOT NULL,IF(a.mobile2 IS NOT NULL,CONCAT(a.mobile,'<br />',a.mobile2),a.phone),'') AS Mobile,
							(r.num_farmers+r.num_lifestyle) AS Total,
							r.num_farmers 	AS Farmer,
							r.num_lifestyle	AS 'L/style',
							r.num_dairies	AS Dairy,
							r.num_sheep		AS Sheep,
							r.num_beef 		AS Beef,
							r.num_sheepbeef	AS 'S/B',
							r.num_dairybeef	AS 'D/B',
							r.num_hort		AS 'Hort',
							r.num_nzfw 		AS 'F@90%'
		
					   FROM route r
					   LEFT JOIN
					   operator o
					   ON o.operator_id=r.contractor_id
					   LEFT JOIN address a
					   ON a.operator_id=r.contractor_id		   
					   WHERE r.region='$region' AND area='$area' AND r.dist_id='$dist_id'
					   AND is_hidden<>'Y'
					   ORDER BY island,seq_region,seq_area,seq_code;";
			//echo nl2br($qry);
			$tab->startNewLine();
			if($action=="export")
				$tab->addBoldLine($region."/".$area,4);
			else
				$tab->addLineWithStyle($region."/".$area,"sql_extra_line_text",4);
			$tab->stopNewLine();
			$tab->writeSQLTableElement($qry);
			$tab->startNewLine();
			$tab->addLines("",6);
			$tab->addLine(get_sum_as("route","num_farmers+num_lifestyle","Total","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));
			$tab->addLine(get_sum("route","num_farmers","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));
			$tab->addLine(get_sum("route","num_lifestyle","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));
			$tab->addLine(get_sum("route","num_dairies","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));
			$tab->addLine(get_sum("route","num_sheep","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));	
			$tab->addLine(get_sum("route","num_beef","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));		
			$tab->addLine(get_sum("route","num_sheepbeef","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));		
			$tab->addLine(get_sum("route","num_dairybeef","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));		
			$tab->addLine(get_sum("route","num_hort","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));			
			$tab->addLine(get_sum("route","num_nzfw","WHERE region='$region' AND area='$area' AND dist_id='$dist_id'","GROUP BY '$region'"));			
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
		$tab->addLine(get_sum("route","num_hort","WHERE dist_id='$dist_id'","GROUP BY '$region'"));			
		$tab->addLine(get_sum("route","num_nzfw","WHERE dist_id='$dist_id'","GROUP BY '$region'"));			
		$tab->stopNewLine();		
		$tab->stopTable();
		
		if($action=="export"){
		?>
			<a href="test.htm">Download</a>
		<?	
		}
	}//if dist_id
}


if($report=="by_dist"){
	if(isset($dist_id)){
	?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
		</div>							
	<?	 	 
		$qry = "SELECT DISTINCT dist_id FROM route
				LEFT JOIN route_aff
				ON route.route_id=route_aff.route_id";
		if($dist_id) 
			$where = " WHERE dist_id=$dist_id";
		$qry .= $where;
		
		$qry .= " ORDER BY seq_region, seq_area";
		$res_dist = query($qry);
		if($action=="export"){
			$tab  = new MySQLExport("test.htm","");
		}
		else{
			$tab  = new MySQLTable("reports.php","");
			$tab->hasAddButton=false;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;	
		}
		//$tab->colWidth["DropOff"]=50;
		//$tab->colWidth["RD"]=20;
		//$tab->colWidth["Contractor"]=50;
		//$tab->colWidth["City"]=30;
		//$tab->colWidth["Address"]=30;
		//$tab->colWidth["Address2"]=30;
		$tab->colWidth["Contact Details"]=150;
		//$tab->colWidth["Description"]=100;
					
		while($distr = mysql_fetch_object($res_dist)){
			$tab->startTable();
			$dist_id = $distr->dist_id;
			$dist  = get("operator","company","WHERE operator_id='$dist_id'");
			$title = "Distributor: $dist";		
			
			$qry = "SELECT	(SELECT company FROM operator WHERE operator.operator_id=ra.dropoff_id) AS 'DropOff',
							r.area		AS Area,
							r.code 			AS RD,
							o.company		AS Contractor,
							a.address       AS Address,
							a.address2       AS Address2,
							a.city          AS City,
							CONCAT(
									IF(a.phone IS NOT NULL AND a.phone <> '',CONCAT('Phone1: ',a.phone,'<br />'),''),
									IF(a.phone2 IS NOT NULL AND a.phone2 <> '',CONCAT('Phone2: ',a.phone2,'<br />'),''),
									IF(a.mobile IS NOT NULL AND a.mobile <> '',CONCAT('Mobile: ',a.mobile,'<br />'),''),
									IF(a.mobile2 IS NOT NULL AND a.mobile2 <> '',CONCAT('Mobile2: ',a.mobile2),'')
							)		
											AS 'Contact Details',
							#IF(a.phone IS NOT NULL,IF(a.phone2 IS NOT NULL,CONCAT(a.phone,'<br />',a.phone2),a.phone),'') AS Phone,
							#IF(a.mobile IS NOT NULL,IF(a.mobile2 IS NOT NULL,CONCAT(a.mobile,'<br />',a.mobile2),a.phone),'') AS Mobile,
							r.description  	AS Description
		
					   FROM route r
					   LEFT JOIN
					   route_aff ra
					   ON  ra.route_id=r.route_id
					   LEFT JOIN  operator o
					   ON o.operator_id=ra.contractor_id
					   LEFT JOIN address a
					   ON a.operator_id=ra.contractor_id		   
					   WHERE ra.dist_id='$dist_id'
						   	AND '$date' >= app_date
							AND '$date' < stop_date
							AND is_hidden<>'Y'
					   ORDER BY DropOff,Region,Area,RD";
			//echo nl2br($qry);
			$tab->startNewLine();
				$tab->addLines("",9);
			$tab->startNewLine();
			if($action=="export")
				$tab->addBoldLine("Distributor:".$dist,4);
			else
				$tab->addLineWithStyle("Distributor:".$dist,"sql_extra_line_text",4);
			$tab->stopNewLine();
			$tab->writeSQLTableElement($qry);
			$tab->stopTable();
?>
				<div class="pagebreak">&nbsp;</div>
<?
		}
		
		if($action=="export"){
		?>
			<a href="test.htm">Download</a>
		<?	
		}
	}//if dist_id
}




?>