<?

function test_pdf($a){
	echo "test_function valid - ".$a."<br />";
}



function bible_region_qry($date,$island,$region,$home_phone,$mobile_phone){
	if(!isset($region)||$region[0]=='0'){
		$qry = "SELECT DISTINCT region FROM route ORDER BY seq_region";
		$res = query($qry);
		$region=array();
		while($reg = mysql_fetch_object($res)){
			$region[] = $reg->region;
		}
	}
	$result = array();

	foreach($region as $reg){
		$page_num=1;

	$now = date("Y-m-d");

	//echo nl2br($qry);

	$num_lines = 0;

		
	$phone="";
	$phone_2="";
	if($home_phone){
		$phone_2="Phone,";
		$phone = "CONCAT_WS('\n',
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
		$phone = "CONCAT_WS('\n',
											IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
											IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
															AS Phone,";
	}
		
	if($mobile_phone && $home_phone){
		$phone_2="Phone,";
		$phone = "CONCAT_WS('\n',
								IF(phone IS NOT NULL AND phone<>'',phone, NULL),
								IF(phone2 IS NOT NULL AND phone2<>'',phone2,NULL),
								IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
								IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL)
								)
														AS Phone,";
	}

	$qry = "SELECT  GROUP_CONCAT(DISTINCT RD ORDER BY RD SEPARATOR ' /\n')
								AS RD,
							Name,
							/*Address,*/
							$phone_2
							IF(Notes IS NOT NULL AND Notes<>'',
								CONCAT(IF(`Parcel Drop Off`=',','',CONCAT(`Parcel Drop Off`,'\n')),'Notes: ',Notes),`Parcel Drop Off`) 
									AS `Parcel Drop Off`,
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
								AND route.region = '$reg'
								AND route.is_hidden<>'Y'
							GROUP BY route_aff.env_contractor_id,
								route.code_base
							ORDER BY island,seq_region,seq_area,seq_code,RD
						) AS tt
						GROUP BY env_contractor_id
						ORDER BY island,seq_region,seq_area,seq_code";
								   	
		   echo nl2br($qry);exit;
		   $res = query($qry);
		   while($record = mysql_fetch_array($res)){
		   		$result[$reg][]=$record;
		   }
	}//foreach region
	return $result;
}

function get_route($id,$type){
	
}

function bible_dist_qry($date,$dist_id,$home_phone,$mobile_phone){
	$result = array();
	if($dist_id==-1) $dist="";
	else $dist="AND route_aff.dist_id='$dist_id'";

	$now = Date("Y-m-d");

	$qry = "SELECT operator.operator_id as dist_id,
					address.* 
			FROM operator
			LEFT JOIN address
			ON address.operator_id=operator.operator_id 
			WHERE is_dist='Y' ";
	if($dist_id>0){
		$qry .= "AND operator.operator_id='$dist_id'";
	}

	$res_dist = query($qry);
	while($dist = mysql_fetch_object($res_dist)){
		$result[$dist->dist_id]['dist_info'] = $dist;
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
						AND route.is_hidden<>'Y'
					GROUP BY operator_id
					ORDER BY island,seq_region,seq_area";
		$res_sd = query($qry);
		
		while($subdist = mysql_fetch_object($res_sd)){
			$result[$dist->dist_id]['sub_dist'][$subdist->subdist_id]['subdist_info'] = $subdist;
			
			$phone="";
			$phone_2="";
			if($home_phone){
				$phone_2="Phone,";
				$phone = "CONCAT_WS('\n',
							IF(
								phone IS NOT NULL AND phone<>'',phone,NULL),
								IF(
									phone2 IS NOT NULL AND phone2<>'',phone2,NULL
								),
								IF(email IS NOT NULL AND email<>'',email,NULL)
							)
														AS Phone,";
													
			}
			if($mobile_phone){
				$phone_2="Phone,";
				$phone = "	CONCAT_WS('\n',
								IF(mobile IS NOT NULL AND mobile<>'',mobile,NULL),
								IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL)
							)
											AS Phone,";
			}
			if($mobile_phone && $home_phone){
				$phone_2="Phone,";
				$phone = "CONCAT_WS('\n',
							IF(phone IS NOT NULL AND phone<>'',phone,NULL),
							IF(email IS NOT NULL AND email<>'',email,NULL),
								IF(phone2 IS NOT NULL AND phone2<>'',phone2,
										IF(mobile IS NOT NULL AND mobile<>'',mobile,
											IF(mobile2 IS NOT NULL AND mobile2<>'',mobile2,NULL))
								)
							)
													AS Phone,";
			}
				
			$qry = "SELECT  GROUP_CONCAT(DISTINCT RD ORDER BY RD SEPARATOR ' \n')
																AS RD,
							Name,
							$phone_2
							IF(Notes IS NOT NULL AND Notes<>'',
								CONCAT(IF(Address=',','',CONCAT(Address,'\n')),'Notes: ',Notes),Address) 
																AS 'Parcel Drop Off',
							
							Description
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
										   operator.operator_id,
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
		
			$res = query($qry);
			while($route = mysql_fetch_array($res)){
				$result[$dist->dist_id]['sub_dist'][$subdist->subdist_id]['records'][] = $route;

			}
		}
	}
	return $result;
	
}


?>
