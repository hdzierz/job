<?
require_once("includes/MySQLTable.php");

function split_rds(){

	$qry = "SELECT * FROM route";
	$res = query($qry);
	while($route = mysql_fetch_object($res)){
		$code = explode(" RD",$route->code);
		$code[0]=addslashes($code[0]);
		$code[1]=addslashes($code[1]);
			
		$code_rd = "";
		foreach($code AS $k=>$v){
			if($k>0) $code_rd.=$v;
		}
			
		$qry = "UPDATE route SET code_base='$code[0]',code_rd='$code_rd' WHERE route_id=$route->route_id";
		query($qry);
		//echo $qry."<br />";
	}
}




function write_bible($date,$island,$region,$mode,$home_phone,$mobile_phone){
	//if(!$mobile_phone && !$home_phone) $home_phone=true;
	/*
	 ?>
	 <div class="weekly_head">
	 <div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
	 </div>
	 <?
	 */
	$now = Date("Y-m-d");
	if(!isset($region)||$region[0]=='0')
		if(isset($island))
			$where_add = " AND route.island IN ".array_to_mysql($island);
		else
			$where_add = "";
	else{
		$where_add = " AND route.region IN ".array_to_mysql($region);
	}

	$now = date("Y-m-d");

	//echo nl2br($qry);
	$tab = new MySQLTable("find_routes.php","");
	$tab->formatLine=true;
	$tab->showRec=1;
	$tab->hasAddButton=false;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->showBlob=true;
	$tab->hasLineSeperator=true;


	$tab->cssSQLUnEvenLine = "sqltabunevenline_white";
	/*if($mode!="xls"){
	 $tab->maxLinesOnPage=19;
	 $tab->pageContinueText = "Distributor $dist_o->company continued.";
		}*/

	if($mode=="print")
		$tab->sqlTabVAlign="top";

	$tab->noRepFields["S/Dist"]=false;
	$tab->noRepFields["Latest Dep"]=false;
	/*
		$tab->noRepFields["Address"]=true;
		$tab->noRepFields["City"]=true;
		$tab->noRepFields["Phone"]=true;
		$tab->noRepFields["Latest DT"]=true;
		$tab->noRepFields["Notes"]=true;
		$tab->noRepFields["View"]=true;*/

	$tab->colWidth["RD"]=150;
	$tab->colWidth["Name"]=150;
	$tab->colWidth["Phone"]=120;
	$tab->colWidth["Parcel Drop Off"]=250;
	$tab->colWidth["Latest DT"]=10;
	//$tab->colWidth["Notes"]=10;
	//$tab->colWidth["View"]=100;

	?>

	<?

	$tab->startTable();
	
	foreach($region as $reg){
		$num_lines = 0;
	
		$subdist_start=true;
			
		$phone="";
		$phone_2="";
		if($home_phone){
			$phone_2="Phone,";
			$phone = "CONCAT_WS('<br />',
											IF(
												phone IS NOT NULL AND phone<>'',phone,NULL),
												IF(
													phone2 IS NOT NULL AND phone2<>'',phone2,NULL
												)
											)
															AS Phone,";
				
		}
		if($mobile_phone){
			$phone_2="Phone,";
			$phone = "CONCAT_WS('<br />',
											IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
											IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
															AS Phone,";
		}
	
		if($mobile_phone && $home_phone){
			$phone_2="Phone,";
			$phone = "CONCAT_WS('<br />',
									IF(phone IS NOT NULL AND phone<>'',phone, NULL),
									IF(phone2 IS NOT NULL AND phone2<>'',phone2,NULL),
									IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
									IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL)
									)
															AS Phone,";
		}
			
		$qry = "SELECT  GROUP_CONCAT(DISTINCT RD ORDER BY RD SEPARATOR ' /<br />')
								AS RD,
							Name,
							$phone_2
							IF(Notes IS NOT NULL AND Notes<>'',
								CONCAT(IF(`Parcel Drop Off`=',','',CONCAT(`Parcel Drop Off`,'<br />')),'<b>Notes</b>: ',Notes),`Parcel Drop Off`) 
									AS `Parcel Drop Off`,
							`Latest Dep`,
							Description
						FROM (
						SELECT 	  CONCAT(
									  code_base,
									  ' RD ',
									  GROUP_CONCAT(DISTINCT code_rd ORDER BY code SEPARATOR ',')
									  )
															AS RD,
								    CONCAT(name,', ',first_name) AS Name,
									CONCAT(address,', ',city) AS Address,
								   
									$phone
								   CONCAT_WS(', ',IF(do.do_address='','',do.do_address),IF(do.do_city='','',do.do_city)) 				
															AS 'Parcel Drop Off',
								   contr.latest_dep 		AS 'Latest Dep',
								   contr.env_deliv_notes 	AS Notes,
								   route.description AS Description,
								   env_dropoff_id,
								   env_contractor_id,
								   island,seq_region,seq_area,seq_code
								   $view
									
								  									  
							FROM route 
							LEFT JOIN route_aff
							ON route_aff.route_id=route.route_id
							LEFT JOIN operator contr
							ON contr.operator_id=route_aff.env_contractor_id
							LEFT JOIN address
							ON contr.operator_id=address.operator_id
							LEFT JOIN operator do
							ON do.operator_id=route_aff.env_dropoff_id
							
							
							WHERE '$date' BETWEEN route_aff.app_date AND route_aff.stop_date
								AND route.region='$reg'
								AND route.is_hidden<>'Y'
							GROUP BY route_aff.env_contractor_id,
								route.code_base
							ORDER BY island,seq_region,seq_area,seq_code,RD
						) AS tt
						GROUP BY env_contractor_id
						ORDER BY island,seq_region,seq_area,seq_code";
	
		   	$showhead=false;
		   	$page_num++;
		   	$tab->startNewLine();
		   		$tab->addLineWithStyle("<div style='float:right; font-size:20pt; font-weight:bold '>$reg</div>","sql_extra_line_text_grey",8);
			$tab->stopNewLine();
			$showhead=true;
			
			$tab->writeSQLTableElement($qry,$showhead);

	}// foreach region
	$tab->stopTable();

}

function write_dist_table($date,$dist_id,$mode,$home_phone,$mobile_phone){
	//if(!$mobile_phone && !$home_phone) $home_phone=true;
	/*
	 ?>
	 <div class="weekly_head">
	 <div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
	 </div>
	 <?
	 */

	if($dist_id==-1) $dist="";
	else $dist="AND route_aff.dist_id='$dist_id'";

	$now = Date("Y-m-d");

	$qry = "SELECT operator_id as dist_id FROM operator WHERE is_dist='Y' ";
	if($dist_id>0){
		$qry .= "AND operator_id='$dist_id'";
	}

	$res_dist = query($qry);
	$max_dist_count = mysql_num_rows($res_dist);

	$dist_count=0;

	while($dist=mysql_fetch_object($res_dist)){
		$has_aff = get("route_aff","route_id","WHERE dist_id='$dist->dist_id'");
		if(!$has_aff) continue;

		// Getting infor about distributor
		$qry = "SELECT * FROM operator
								LEFT JOIN address
								ON address.operator_id=operator.operator_id
								WHERE operator.operator_id='$dist->dist_id'";
		$res_d2 = query($qry);
			
		$dist_o = mysql_fetch_object($res_d2);
			
		$dist_phone = $dist_o->phone;
		if($dist_o->phone2) $dist_phone.='/'.$dist_o->phone2;
		$dist_mobile = $dist_o->mobile;
		if($dist_o->mobile2) $dist_mobile.='/'.$dist_o->mobile2;
			
		$dist_name = $dist_o->name.", ".$dist_o->first_name;
		if($dist_o->alias) $dist_name = $dist_o->alias;
			
		$dist_country = $dist_o->country;
			
			
		$qry = "SELECT 	operator.operator_id AS subdist_id,
							address.*
					FROM operator
					LEFT JOIN route_aff
					ON route_aff.env_subdist_id=operator.operator_id
					LEFT JOIN route
					ON route.route_id=route_aff.route_id
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					WHERE route_aff.env_dist_id='$dist->dist_id'
						AND '$date' BETWEEN route_aff.app_date AND route_aff.stop_date
						AND is_hidden<>'Y'
					GROUP BY operator_id
					ORDER BY island,seq_region,seq_area";
		$res_sd = query($qry);
		//echo nl2br($qry);
			
			
			
		if(strpos($dist_o->company,"LBC")===false){

			// Getting infor about distributor
			$qry = "SELECT * FROM operator
							LEFT JOIN address
							ON address.operator_id=operator.operator_id
							WHERE operator.operator_id='$dist->dist_id'";
			//$res_subdist = query($qry);
				

			$now = date("Y-m-d");

			//echo nl2br($qry);
			$tab = new MySQLTable("find_routes.php","");
			$tab->formatLine=true;
			$tab->showRec=1;
			$tab->hasAddButton=false;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->showBlob=true;
			$tab->hasLineSeperator=true;


			$tab->cssSQLUnEvenLine = "sqltabunevenline_white";
			/*if($mode!="xls"){
			 $tab->maxLinesOnPage=19;
			 $tab->pageContinueText = "Distributor $dist_o->company continued.";
				}*/

			if($mode=="print")
			$tab->sqlTabVAlign="top";

			$tab->noRepFields["Address"]=false;
			$tab->noRepFields["S/Dist"]=false;
			$tab->noRepFields["Latest Dep"]=false;
			/*
				$tab->noRepFields["Address"]=true;
				$tab->noRepFields["City"]=true;
				$tab->noRepFields["Phone"]=true;
				$tab->noRepFields["Latest DT"]=true;
				$tab->noRepFields["Notes"]=true;
				$tab->noRepFields["View"]=true;*/

			$tab->colWidth["RD"]=200;
			$tab->colWidth["Name"]=200;
			$tab->colWidth["Address"]=350;
			$tab->colWidth["Phone"]=150;
			$tab->colWidth["Latest DT"]=10;
			//$tab->colWidth["Notes"]=10;
			//$tab->colWidth["View"]=100;

			?>

			<?

			$tab->startTable();
			$tab->startNewLine();
			$tab->addLineWithStyle("<div style='float:right; font-size:20pt; font-weight:bold '>$dist_country</div>
														Date: $now <br />
														Distributor:
														$dist_name<br />
														  Phone: $dist_phone<br />
														  Mobile: $dist_mobile","sql_extra_line_text_grey",8);
			$tab->stopNewLine();

			$num_lines = 0;
			$page_break=true;
			while($subdist = mysql_fetch_object($res_sd)){
				$sdphone = $subdist->phone;
				if($subdist->phone2) $sdphone.='/'.$subdist->phone2;
				$sdmobile = $subdist->mobile;
				if($subdist->mobile2) $sdmobile.='/'.$subdist->mobile2;
					
				$sd_name = $subdist->name.", ".$subdist->first_name;
				if($subdist->alias) $sd_name = $subdist->alias;
					

				if($mode=="print" || $mode="screen"){
								$phone="";
								$phone_2="";
								if($home_phone){
									$phone_2="Phone,";
									$phone = "CONCAT_WS('<br />',
						IF(
										phone IS NOT NULL AND phone<>'',phone,NULL),
										IF(
											phone2 IS NOT NULL AND phone2<>'',phone2,NULL
										)
									)
													AS Phone,";
													
											}
											if($mobile_phone){
												$phone_2="Phone,";
												$phone = "CONCAT_WS('<br />',
									IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
									IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
													AS Phone,";
											}

											if($mobile_phone && $home_phone){
												$phone_2="Phone,";
												$phone = "CONCAT_WS('<br />',
									IF(phone IS NOT NULL AND phone<>'',phone,NULL),
										IF(phone2 IS NOT NULL AND phone2<>'',phone2,
												IF(mobile IS NOT NULL AND mobile<>'',mobile,
													IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
										)
									)
													AS Phone,";
											}
												
																$qry = "SELECT  GROUP_CONCAT(DISTINCT RD ORDER BY RD SEPARATOR ' /<br />')
											AS RD,
										Name,
										IF(Notes IS NOT NULL AND Notes<>'',
											CONCAT(IF(Address=',','',CONCAT(Address,'<br />')),'Notes: ',Notes),Address) 
												AS Address,
												$phone_2
										`Latest Dep`
									FROM (
									SELECT 	  CONCAT(
												  code_base,
												  ' RD ',
												  GROUP_CONCAT(DISTINCT code_rd ORDER BY code SEPARATOR ',')
												  )
																		AS RD,
											   CONCAT_WS(', ',name,first_name) 
																		AS Name,
											   CONCAT_WS(', ',IF(do_address='','',do_address),IF(do_city='','',do_city)) 				
																		AS Address,
																		$phone
											   operator.latest_dep 		AS 'Latest Dep',
											   operator.env_deliv_notes AS Notes,
											   env_dropoff_id,
											   env_contractor_id,
											   route.description		AS Description,
											   island,seq_region,seq_area,seq_code
											   $view
												
											   
											  
										FROM route 
										LEFT JOIN route_aff
										ON route_aff.route_id=route.route_id
										LEFT JOIN address
										ON address.operator_id=route_aff.env_contractor_id
										LEFT JOIN operator
										ON operator.operator_id=route_aff.env_dropoff_id
										WHERE '$date' BETWEEN route_aff.app_date AND route_aff.stop_date
											AND route_aff.env_subdist_id='$subdist->subdist_id'
											AND route_aff.env_dist_id='$dist->dist_id'
											AND route.is_hidden<>'Y'
										GROUP BY route_aff.env_contractor_id,
											route.code_base
										ORDER BY island,seq_region,seq_area,seq_code,RD
									) AS tt
									GROUP BY env_contractor_id
									ORDER BY island,seq_region,seq_area,seq_code";
															}
															//echo nl2br($qry);
															$res_test = query($qry);
															$num_rows = mysql_num_rows($res_test);

															$num_lines+=$num_rows;
															if($home_phone || $mobile_phone){
																$lb_penalty = 27;
																$tab->maxLinesOnPage=27;
																if($num_rows>=35)
																$lb_penalty2 = true;
																else
																$lb_penalty2 = false;
															}
															else{
																$lb_penalty = 35;
																$tab->maxLinesOnPage=35;
																if($num_rows>=45)
																$lb_penalty2 = true;
																else
																$lb_penalty2 = false;
															}

															if($num_lines>=$lb_penalty || $lb_penalty2){
																if($lb_penalty2)
																$num_lines=0;
																else
																$num_lines=$num_rows;

																$tab->stopTable();
																?>
<div class="pagebreak">&nbsp;</div>
																<?
																$tab->startTable();
																	
																$tab->startNewLine();
																$tab->addLineWithStyle("<div style='float:right; font-size:20pt; font-weight:bold '>$dist_country</div>
																	Date: $now <br />
																	Distributor:
																	$dist_name<br />
																	  Phone: $dist_phone<br />
																	  Mobile: $dist_mobile","sql_extra_line_text_grey",8);
																	$tab->stopNewLine();
																		



															}
															$tab->startNewLine();
															$tab->addLineWithStyle("S/Dist.: $sd_name,  Phone: $sdphone,  Mobile: $sdmobile","sql_extra_line_text_grey",8);
															//$tab->pageContinueText="S/Dist.: $sd_name continued";
															$tab->stopNewLine();
															$tab->writeSQLTableElement($qry,$page_break);
															$page_break=false;
															//$tab->writeTable();

														} // IF distributor is not MAILINGS or BBC

														if($dist_count<$max_dist_count-1){
															$tab->stopTable();
															$page_break=true;
															?>
<div class="pagebreak">&nbsp;</div>
															<?
															$tab->startTable();
														}


		} // While Subdist
		$tab->stopTable();
		$dist_count++;
	}//While dist
}


function write_geo_table($date,$regions,$islands,$target,$mode,$home_phone,$mobile_phone){
	if($target=="pc"){
		$drop_off = "env_dropoff_id";
	}
	else{
		$drop_off = "dropoff_id";
	}

	if($regions[0]=='0'){
		$regions=array();
			
		if($islands[1]) $where_add="OR island='".$islands[1]."'";

		$qry = "SELECT DISTINCT region from route
					WHERE  (island='$islands[0]' $where_add)
					AND region<>'MAILINGS'
					AND region<>'BAGS BOXES COUNTER'
					AND is_hidden<>'Y'
					ORDER BY island,seq_region";
		$res = query($qry);
		while($region = mysql_fetch_object($res)){
			$regions[] = $region->region;
		}
	}
	$reg_count=0;
	$max_reg_count=count($regions);
	$start=true;
	$page_break=false;
	foreach($regions as $region){
		$header=0;
		$qry = "SELECT DISTINCT  address.*,company,operator.operator_id
					FROM operator
					LEFT JOIN route_aff
					ON route_aff.env_dist_id=operator.operator_id
					LEFT JOIN route
					ON route.route_id=route_aff.route_id
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					WHERE '$date' BETWEEN route_aff.app_date AND route_aff.stop_date
						AND route.region='$region'
						AND is_hidden<>'Y'
					ORDER BY island,seq_region";
		$dist_res = query($qry);

		$tab = new MySQLTable("find_routes.php","");
		$tab->formatLine=true;
		$tab->showRec=1;
		$tab->hasAddButton=false;
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		//$tab->maxLinesOnPage=36;
			
		$tab->hasLineSeperator=true;

		$tab->cssSQLUnEvenLine = "sqltabunevenline_white";
		//$tab->maxLinesOnPage=20;
		/*if($mode!="xls"){
		 $tab->maxLinesOnPage=19;
		 $tab->pageContinueText = "Region $region continued.";
			}*/
		if($mode=="print")
		$tab->sqlTabVAlign="top";
			
		/*$tab->noRepFields["Name"]=true;
			$tab->noRepFields["Address"]=true;
			$tab->noRepFields["City"]=true;
			$tab->noRepFields["Phone"]=true;
			$tab->noRepFields["Latest DT"]=true;
			$tab->noRepFields["Notes"]=true;
			$tab->noRepFields["View"]=true;*/
			
		$tab->colWidth["RD"]=200;
		$tab->colWidth["Name"]=200;
		$tab->colWidth["Address"]=350;
		$tab->colWidth["Phone"]=150;
		$tab->colWidth["Latest DT"]=10;
			
		$tab->startTable();
		$now = date("Y-m-d");
		$tab->startNewLine();
		$tab->addLineWithStyle("<div style='float:right; font-size:20pt; font-weight:bold '>$region</div>
														Date: $now <br />","sql_extra_line_text_grey",8);
		$tab->stopNewLine();
			

		$num_lines=0;
		while($dist = mysql_fetch_object($dist_res)){
			$start_dist=true;
			$sphone = $dist->phone;
			if($dist->phone2) $sphone.='/'.$dist->phone2;
			$smobile = $dist->mobile;
			if($dist->mobile2) $smobile.='/'.$dist->mobile2;

			$sname = $dist->name.", ".$dist->first_name;
			if($dist->alias) $s_name = $dist->alias;
			if($mode=="xls"){
				$phone_2="";
				$phone="";
				if($home_phone){
					$phone_2="Phone,";
					$phone = "CONCAT_WS(', ',
															IF(
																phone IS NOT NULL AND phone<>'',phone,NULL),
																IF(
																	phone2 IS NOT NULL AND phone2<>'',phone2,NULL
																)
															)
																			AS Phone,";
				}
				if($mobile_phone){
					$phone_2="Phone,";
					$phone = "CONCAT_WS(', ',
															IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
															IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
																			AS Phone,";
				}
					
				if($mobile_phone && $home_phone){
					$phone_2="Phone,";
					$phone = "CONCAT_WS(', ',
															IF(phone IS NOT NULL AND phone<>'',phone,NULL),
															IF(phone2 IS NOT NULL AND phone2<>'',phone2,NULL),
															IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL)
															IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL)
													)
																			AS Phone,";
				}
				$qry2 = "SELECT 		code						AS RD,
												CONCAT_WS(', ',name,first_name) 
																		AS Name,
												IF(
													env_deliv_notes IS NOT NULL AND env_deliv_notes<>'',
													CONCAT(CONCAT_WS(', ',do_address,do_city),
														'\n <b>Notes</b>: ',
														env_deliv_notes),
													CONCAT_WS(', ',do_address,do_city)) 
																		AS Address,
																		$phone
											   
											   operator.latest_dep 		AS 'Latest Dep'
											   $view
											  
										FROM route 
										LEFT JOIN route_aff
										ON route_aff.route_id=route.route_id
										LEFT JOIN address
										ON address.operator_id=route_aff.env_contractor_id
										LEFT JOIN operator
										ON operator.operator_id=route_aff.env_dropoff_id
										WHERE '$now' BETWEEN route_aff.app_date AND route_aff.stop_date
											AND route_aff.env_dropoff_id>0
											AND region='$region'
											AND route.is_hidden<>'Y'
										ORDER BY island,seq_region,seq_area,seq_code,RD";
			}
			else if($mode=="print" || $mode=="screen"){
				$phone="";
				$phone_2="";

				if($home_phone){
					$phone_2="Phone,";
					$phone = "CONCAT_WS('<br />',
														IF(
															phone IS NOT NULL AND phone<>'',phone,NULL),
															IF(
																phone2 IS NOT NULL AND phone2<>'',phone2,NULL
															)
														)
																		AS Phone,";
						
				}
				if($mobile_phone){
					$phone_2="Phone,";
					$phone = "CONCAT_WS('<br />',
														IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
														IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
																		AS Phone,";
				}

				if($mobile_phone && $home_phone){
					$phone_2="Phone,";
					$phone = "CONCAT_WS('<br />',
														IF(phone IS NOT NULL AND phone<>'',phone,NULL),
														IF(phone2 IS NOT NULL AND phone2<>'',phone2,NULL),
														IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
														IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL)
												)
																		AS Phone,";
				}
				$qry2 = "SELECT  GROUP_CONCAT(DISTINCT RD ORDER BY RD SEPARATOR ' /<br />')
												AS RD,
											Name,
											IF(Notes IS NOT NULL AND Notes<>'',
												CONCAT(IF(Address=',','',CONCAT(Address,'<br />')),'<b>Notes</b>:',Notes),Address) 
													AS Address,
											
													$phone_2
											`Latest Dep`
										FROM (
										SELECT 	  CONCAT(
													  code_base,
													  ' RD ',
													  GROUP_CONCAT(DISTINCT code_rd ORDER BY code SEPARATOR ',')
													  )
																			AS RD,
												   CONCAT_WS(', ',name,first_name) 
																			AS Name,
												   TRIM(CONCAT_WS(', ',IF(do_address='','',do_address),IF(do_city='','',do_city))) 				
																			AS Address,
																			$phone
												   operator.latest_dep 		AS 'Latest Dep',
												   operator.env_deliv_notes AS Notes,
												   env_dropoff_id,
												   route.description		AS Description,
												   env_contractor_id,
												   island,seq_region,seq_area,seq_code
												   $view
													
												   
												  
											FROM route 
											LEFT JOIN route_aff
											ON route_aff.route_id=route.route_id
											LEFT JOIN address
											ON address.operator_id=route_aff.env_contractor_id
											LEFT JOIN operator
											ON operator.operator_id=route_aff.env_dropoff_id
											WHERE '$date' BETWEEN route_aff.app_date AND route_aff.stop_date
												AND route_aff.env_dropoff_id>0
												AND region='$region'
												AND route.is_hidden<>'Y'
											GROUP BY route_aff.env_contractor_id,
												route.code_base
											ORDER BY island,seq_region,seq_area,seq_code,RD
										) AS tt
										GROUP BY env_contractor_id
										ORDER BY island,seq_region,seq_area,seq_code";


			}
			//echo nl2br($qry2)."<br /><br /><br />";

			$res_test = query($qry2);
			$num_rows = mysql_num_rows($res_test);
			$num_lines+=$num_rows;
				
			if($num_rows>0){

				if($home_phone || $mobile_phone){
					$lb_penalty = 29;
					//$tab->maxLinesOnPage=28;
				}
				else{
					$lb_penalty = 31;
					//$tab->maxLinesOnPage=30;
				}

				/*if($num_lines>=$lb_penalty){
					$header++;
					$num_lines=$num_rows;
					$page_break=true;
					$tab->stopTable();
					?>
						<div class="pagebreak">&nbsp;</div>
					<?

					$tab->startTable();
					$tab->startNewLine();
					$tab->addLineWithStyle("<div style='float:right; font-size:20pt; font-weight:bold '>$region</div>
																		Date: $now <br />","sql_extra_line_text_grey",8);
					$tab->pageContinueText="";
					$tab->stopNewLine();


				}*/
				if($start){
					$tab->writeTableHeader($res_test);
				}
				if($start_dist){
					$header++;
					$tab->startNewLine();
					$tab->addLineWithStyle("Dist.: $sname,  Phone: $sphone,  Mobile: $smobile","sql_extra_line_text_grey_large",8);
					$tab->stopNewLine();
					$start_dist=false;
				}
				$tab->writeSQLTableElement($qry2,$page_break);
						$page_break=false;
				}// if num rows greater 0
										
					
					$start=false;		

			}// while dist
			
			$tab->stopTable();
			
			if($reg_count<$max_reg_count-1){
				$page_break=true;
				?>
<div class="pagebreak">&nbsp;</div>
<?
			}
			
			$reg_count++;
		}// while region
	}
	
	
	


?>