<?

include $dir."includes/mysql.class.php";
include $dir."includes/fpdf/fpdf.php";
define('FPDF_FONTPATH',$dir.'includes/fpdf/font/');
require_once $dir."includes/MySQLPDFTable.php";




function show_job_details_send($job_id,$choice){
	global $MYSQL;

	$margin=false;
	
	
	if($choice=="bbh") $where_add = "AND region='BAGS BOXES COUNTER'";
	else if($choice=="mail") $where_add = "AND region='MAILINGS'";
	
	else $where_add = "";

	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
	$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));
	
	
	$qry = "SELECT DISTINCT dist_id,company,address.country 
			FROM job_route
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=job_route.dist_id
			LEFT JOIN address
			ON address.operator_id=operator.operator_id
			WHERE job_route.job_id='$job_id'
			AND dist_id IS NOT NULL 
            AND company IS NOT NULL
            ORDER BY island,seq_region,seq_area";
	$res_dist = query($qry,0);
	
	
	
	$start_region=false;
	
	$now=date("Y_m_d_h_i_s");
			
	$dir = "temp_job_det/".$now;
	if(!@mkdir($dir)) die("ERROR: The system was not able to create a directory in http://192.168.100.2:81/temp_job_det. Either that directory does not exist or the system has no prvileges to create wrute into that directory.");
	
	while($dist = mysql_fetch_object($res_dist)){
		$tab=new MySQLPDFTable($MYSQL,'p');
		//$tab->AddPage();
		$start=true;
		$tot_alt_sum = 0;
		$tot_sum = 0;
		$tot_bdl_sum = 0;
			
		$tab->norepField["Name"]=true;
		$tab->hasDivider = false;
		$tab->collField[$dest_type] = true;
		$tab->collField[$alt_dest_type] = true;
		$tab->collField['Bdls'] = true;
		
		$header=array('Name',"RD",'Version',$dest_type);
		$width=array('Name'=>40,"RD"=>60,'Version'=>30,$dest_type=>20);
		
		if($alt_job_id){
			$header[] = $alt_dest_type;
			$width[$alt_dest_type] = 20;
		}
		if($has_bdls){
			$header[] = 'Bdls';
			$width['Bdls'] = 20;
		}
		
		$maxw=0;
		foreach($width as $w){
			$maxw +=  $w;
		}
		$tab->AddPage();
		
		$comments = br2nl(collect_comment($dist->dist_id,$job_id));
		
		$qry = "SELECT DISTINCT region 
				FROM route
				LEFT JOIN job_route
				ON job_route.route_id=route.route_id
				WHERE job_route.dist_id=$dist->dist_id 
					AND job_id='$job_id'
				ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry);
		$num_regions = mysql_num_rows($res_reg);
		$count_reg = 0;
		while($region = mysql_fetch_object($res_reg)){
			$has_lines=false;
			$qry = "SELECT doff,region,dist_id 
					FROM job_route 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE region='$region->region' 
						AND dist_id='$dist->dist_id'
						AND job_route.job_id='$job_id'
					GROUP BY doff
					ORDER BY island,seq_region,seq_area";
			$res_do = query($qry,0);
			
			$start=true;
			while($do = mysql_fetch_object($res_do)){
				$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles' AND job_route.doff = '$do->doff' $where_add GROUP BY job_id,dist_id ";
				$res_sum = query($qry);
				$sum = mysql_fetch_object($res_sum);
				
				$has_bdls = false;
				if($sum->sum>0) {
					$has_bdls = true;
					$sel_add = "IF(job_route.dest_type='bundles',job_route.amount,0) 		AS Bdls,";
				}
			
				
				
				
				
				if($alt_job_id){
					$add_sel = ",IF(jra.dest_type<>'bundles',jra.amount,0)			AS '$alt_dest_type'";
					$add = "LEFT JOIN job_route jra
							ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
				}					
			
				$qry = "SELECT  CONCAT(IF(operator.alias<>'',operator.alias,
									IF(address.name2 <> '',
										CONCAT(address.name,', ',address.first_name,' and ',address.name2,', ',address.first_name2)	,
										IF(address.first_name <>'',
											CONCAT(address.name,', ',address.first_name),
											CONCAT(address.name)
										)
									)
								),
								IF(mail_type<>'' AND mail_type IS NOT NULL,CONCAT(' (',mail_type,')'),'') )
														AS Name,
								/*CONCAT(operator.do_address,IF(operator.do_city<>'',', ',''),operator.do_city,IF(operator.deliv_notes<>'',', Notes:',''),operator.deliv_notes)
														AS 'Delivery Point',*/
								route.code 	AS RD,
								job_route.version AS Version,
								$sel_add
							    IF(job_route.dest_type<>'bundles',job_route.amount,0) 	AS $dest_type
							    $add_sel
						FROM job_route
						$add 
						LEFT JOIN route
						ON route.route_id=job_route.route_id
						LEFT JOIN operator
						ON operator.operator_id=job_route.doff
						LEFT JOIN address
						ON operator.operator_id=address.operator_id
						WHERE job_route.job_id='$job_id'
								AND job_route.dist_id='$dist->dist_id'
								AND route.region = '$region->region'
								AND job_route.doff = '$do->doff'
								$where_add
						ORDER BY island,route.seq_region,route.seq_area,route.seq_code,job_route.dest_type,company";
				//echo nl2br($qry)."<br />";
				$res_t = query($qry);
				//echo ";<br />";
				
				if(mysql_num_rows($res_t)>0){
					$has_lines=true;
					if($start){
						write_header_without_contact_pdf($tab,$job_id,"COURAL JOB DETAILS",$margin->margin_percent,$maxw);
						
						
						
						$tab->StartLine(5);
							$tab->WriteLine("Distributor: ".$dist->company." / Region: ".$dist->country,'L',5,$maxw);
						$tab->StopLine();
						
						$tab->WriteHeader($header,$width);
						
						$start=false;
						
					}
					
					$data = $tab->LoadData($qry);
					
				
					$tab->WriteTable($header,$data,$width,4);
					
					$num_beef_sum = $tab->getSum($dest_type,0);
					$num_beef_alt_sum = $tab->getSum($alt_dest_type,0);
					
					
					
					
					
					$tab->StartLine(5);
						$tab->WriteLine("",'L',5,$width['Name']);
						$tab->WriteLine("",'L',5,$width['RD']);
						$tab->WriteLine("Total",'R',5,$width['Version']);
					
						if(!$total) $total=0;
						if($has_bdls) {
							$bundles_sum = $tab->getSum('Bdls',0); 
							$total = $bundles_sum;
							$tab->WriteLine($total,'R',5,$width['Bdls']);
							$tab->collFieldVal['Bdls'] = array();
						}
										
						$total = $num_beef_sum;
						if(!$total) $total=0;

						$tab->WriteLine($total,'R',5,$width[$dest_type]);	
						
						if($alt_job_id){
							$total = $num_beef_alt_sum;
							$tot_alt_sum += $total;
							if(!$total) $total=0;
							$tab->WriteLine("$total",'R',5,$width[$alt_dest_type]);		
						}				
					$tab->StopLine();							
					$start=false;
					$tab->collFieldVal[$dest_type] = array();
					$tab->collFieldVal[$alt_dest_type] = array();
					$tab->collFieldVal['Bdls'] = array();
				}
			}
			if($has_lines && $num_regions>1 && $count_reg<($num_regions-1)){
				$tab->AddPage();
			}
			$count_reg++;
		}//while($region = mysql_fetch_object($res_reg))
		if($has_lines){
			$tab->StartLine(5);
				$tab->WriteLine("",'L',5,$width['Name']);
				$tab->WriteLine("",'L',5,$width['RD']);
				$tab->WriteLine("Grand Total (Distr.):",'R',5,$width['Version']);
				
				$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles'  $where_add GROUP BY job_id,dist_id ";
				$res_sum = query($qry);
				$sum = mysql_fetch_object($res_sum);
				$total = $sum->sum;
				if(!$total) $total=0;
				
				if($has_bdls) $tab->WriteLine("$total",'R',5,$width['Bdls']);					
				
				
				$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type<>'bundles'  $where_add GROUP BY job_id,dist_id ";
				$res_sum = query($qry);
				$sum = mysql_fetch_object($res_sum);
				$total = $sum->sum;
				if(!$total) $total=0;
				$tab->WriteLine("$total",'R',5,$width[$dest_type]);	
				if($alt_job_id){
					$total_2 = $tot_alt_sum;
					$tot_alt_sum=0;
					if(!$total_2) $total_2=0;
					$tab->WriteLine("$total_2",'R',5,$width[$alt_dest_type]);		
				}				
			$tab->StopLine();		
			
			if($alt_job_id){
				$tab->StartLine(5);
					$tab->WriteLine("",'L',5,$width['Name']);
					$tab->WriteLine("",'L',5,$width['RD']);
					$tab->WriteLine("Sum:",'L',5,$width['Version']);
					$tab->WriteLine($total_2+$total,'R',5,$width[$dest_type]);
				$tab->StopLine();	
			}
			$tab->WriteLine($comments,'L',5,$maxw);
			
			
			$fn = "job_details_".$dist->company.".pdf";
			
			$tab->Output($dir.'/'.$fn);
			
			//send_operator_mail("JOB DETAILS","temp_deliv",$fn,$hauler);
			send_operator_mail("JOB DETAILS",$dir,$fn,$dist->dist_id,"helge.dzierzon@computercare.co.nz");
			//send_operator_mail("JOB DETAILS",$dir,$fn,$dist->dist_id,"howard@coural.co.nz");
		}
		
		
	}// while($dist = mysql_fetch_object($res_dist))
	
	echo "<font color='red'>Sending Job details finished.</font><br />";
}

function show_do_details_send($job_id,$choice){

	global $MYSQL;
	$haulers = array();
	
	$hauler_ni = get("job","hauler_ni_id","WHERE job_id='$job_id'");
	$hauler_si = get("job","hauler_si_id","WHERE job_id='$job_id'");
	
	if($hauler_ni==$hauler_si){
		$haulers["'SI','NI'"] = $hauler_ni;
	}
	else{
		$haulers["'NI'"] = $hauler_ni;
		$haulers["'SI'"] = $hauler_si;
	}
	
	


	$margin=false;
	
	$now=date("Y_m_d_h_i_s");
			
	$dir = "temp_do_det/".$now;
	if(!@mkdir($dir)) die("ERROR: The system was not able to create a directory in http://192.168.100.2:81/temp_do_det. Either that directory does not exist or the system has no prvileges to create wrute into that directory.");

	foreach($haulers as $island=>$hauler){
	
		if($choice=="bbh") $where_add = "AND region='BAGS BOXES COUNTER'";
		else if($choice=="mail") $where_add = "AND region='MAILINGS'";
		
		else $where_add = "";
	
		$comments = get("job","comments","WHERE job_id='$job_id'");
		
		
		
		
		$qry = "SELECT DISTINCT region 
				FROM route
				LEFT JOIN job_route
				ON job_route.route_id=route.route_id
				WHERE job_id='$job_id'
					AND island IN($island)
				ORDER BY island,seq_region,seq_area";
		$res_reg = query($qry,0);
		
		$num_regions = mysql_num_rows($res_reg);
		if($num_regions==0) continue;
		
		$tab=new MySQLPDFTable($MYSQL,'p');
		$tab->hasDivider = false;
		$tab->AddPage();
		$tab->maxchar = 40;
		$start=true;
		$tot_alt_sum = 0;
		$tot_sum = 0;
		$tot_bdl_sum = 0;
		$start_region=false;
		
		
		$count_reg = 0;
		$start_region = true;
		while($region = mysql_fetch_object($res_reg)){
			$has_lines=false;
			
			$qry = "SELECT SUM(amount) AS sum FROM job_route LEFT JOIN route ON route.route_id=job_route.route_id WHERE dist_id='$dist->dist_id' AND job_route.job_id='$job_id' and dest_type='bundles'  $where_add GROUP BY job_id,dist_id ";
			$res_sum = query($qry);
			$sum = mysql_fetch_object($res_sum);
			
			$has_bdls = false;
			if($sum->sum>0) {
				$has_bdls = true;
				
				$sel_add = "SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS Bdls,";
			}
		
			$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
			
			$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
			$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));
			$tab->collField[$dest_type] = true;
			$tab->collField[$alt_dest_type] = true;
			$tab->collField['Bdls'] = true;
			
			$header=array('Name',"Delivery Point",'ALT DO','Version',$dest_type);
			$width=array('Name'=>40,"Delivery Point"=>60,'ALT DO'=>15,'Version'=>30,$dest_type=>20);
			
			if($alt_job_id){
				$header[] = $alt_dest_type;
				$width[$alt_dest_type] = 20;
			}
			if($has_bdls){
				$header[] = 'Bdls';
				$width['Bdls'] = 20;
			}
			$maxw=0;
			foreach($width as $w){
				$maxw +=  $w;
			}
			
			if($alt_job_id){
				$add_sel = ",SUM(IF(job_route.dest_type<>'bundles',jra.amount,0))			AS '$alt_dest_type'";
				$add = "LEFT JOIN job_route jra
						ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
			}					

			$qry = "SELECT  IF(
								operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
								IF(
									address.first_name2 IS NOT NULL AND address.first_name2<>'',
										CONCAT(address.first_name2,' & ',address.first_name),
										address.first_name
								)
							)		AS Name,
							/*CONCAT(operator.do_address,IF(operator.do_city<>'',', ',''),operator.do_city,IF(operator.deliv_notes<>'',', Notes:',''),operator.deliv_notes)
													AS 'Delivery Point',*/
							CONCAT(operator.do_address,', ',operator.do_city,',',operator.deliv_notes)
													AS 'Delivery Point',
							IF(job_route.alt_dropoff_id>0,'Y','N') 
													AS 'ALT DO',
							job_route.version AS Version,
							$sel_add
							SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) AS '$dest_type'
							$add_sel
					FROM job_route
					$add 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					LEFT JOIN operator
					ON operator.operator_id=job_route.doff
					LEFT JOIN address
					ON operator.operator_id=address.operator_id
					WHERE 	job_route.job_id='$job_id'
							AND route.region = '$region->region'
							$where_add
							AND island IN($island)
					GROUP BY route.region,job_route.doff,job_route.version
					ORDER BY island,route.seq_region,route.seq_area,route.seq_code,job_route.dest_type,company";
			//echo nl2br($qry)."<br />";
			
			$res_t = query($qry);
			//echo "hello<br />";
			
			
			if(mysql_num_rows($res_t)>0){
				
				$has_lines=true;
				if($start){
					write_header_without_contact_pdf($tab,$job_id,"COURAL DROP OFF DETAILS",$margin->margin_percent,$maxw);
					$tab->WriteHeader($header,$width);
					$start=false;
				}
				$tab->StartLine(6,200,200,200);
					$tab->SetTextColor(255,255,255);
					$tab->WriteLine("REGION: ".$region->region,'L',6,$maxw);
					$tab->SetTextColor(0,0,0);
				$tab->StopLine();	
				
				//$tab->StartLine(5);
				//	$tab->WriteLine("Distributor: ".$dist->company." / Region: ".$region->region,'L',5,$maxw);
				//$tab->StopLine();	
				
				
				
				
				$data = $tab->LoadData($qry);
				
				$tab->WriteTable($header,$data,$width,4);			
				
				$num_beef_sum = $tab->getSum($dest_type,0);
				$num_beef_alt_sum = $tab->getSum($alt_dest_type,0);
				
				
				$tab->collFieldVal[$dest_type] = array();
				$tab->collFieldVal[$alt_dest_type] = array();
				
				//$tab->writeDivider($maxw);			
				
				$tab->StartLine(6);
					$tab->WriteLine("",'R',5,$width["Name"]);
					$tab->WriteLine("",'R',5,$width["Delivery Point"]);
					$tab->WriteLine("",'R',5,$width["ALT DO"]);
					
					$tab->WriteLine("Total Region:",'R',5,$width["Version"]);
					
					if(!$total) $total=0;
					
									
					$total = $num_beef_sum;
					$tot_sum+=$total;
					
					if(!$total) $total=0;
					$tab->WriteLine($total,'R',5,$width[$dest_type]);
					if($alt_job_id){
						$header[] = $alt_dest_type;
						$total = $num_beef_alt_sum;
						$tot_alt_sum += $total;
						if(!$total) $total=0;
						$tab->WriteLine($total,'R',5,$width[$alt_dest_type]);
					}				
					if($has_bdls) {
						$header[] = 'Bdls';
						$bundles_sum = array_sum($tab->collFieldVal['Bdls']); 
						$total = $bundles_sum;
						$tot_bdl_sum += $total;
						$tab->WriteLine($total,'R',5,$width['Bdls']);
						$tab->collFieldVal['Bdls'] = array();
					}
				$tab->StopLine();			
				//$tab->writeDivider($maxw);
				//$tab->writeDivider($maxw);				
			}
			/*if($has_lines && $num_regions>1 && $count_reg<($num_regions-1)){
				$tab->AddPage();
			}*/
			
			$count_reg++;
		}//while($region = mysql_fetch_object($res_reg))
		
		if($has_lines){
			$tab->StartLine(6);
				$tab->WriteLine("",'R',5,$width["Name"]);
				$tab->WriteLine("",'R',5,$width["Delivery Point"]);
				$tab->WriteLine("",'R',5,$width["ALT DO"]);
				
				$tab->WriteLine("Total:",'R',5,$width["Version"]);

				$total = $tot_bdl_sum;
				if(!$total) $total=0;
						
				
				$total = $tot_sum;
				$tot_sum = 0;
				if(!$total) $total=0;
				$tab->WriteLine($total,'R',5,$width[$dest_type]);		
				if($alt_job_id){
					$total_2 = $tot_alt_sum;
					$tot_alt_sum=0;
					if(!$total_2) $total_2=0;
					$tab->WriteLine($total_2,'R',5,$width[$alt_dest_type]);		
				}				
				if($has_bdls) $tab->WriteLine($total,'R',5,$width["Bdl"]);		
			$tab->StopLine();		
			
			if($alt_job_id){
				$tab->StartLine(6);
					$tab->WriteLine("",'R',5,$width["Name"]);
					$tab->WriteLine("",'R',5,$width["Delivery Point"]);
					$tab->WriteLine("",'R',5,$width["ALT DO"]);
					
					$tab->WriteLine("Sum:",'R',5,$width["Version"]);
					$tab->WriteLine($total_2+$total,'R',5,$width[$dest_type]);
				$tab->StopLine();		
			}
		}
		
		
			/*if($has_lines){
				$tab->MultiCell($maxw,5,$comments,0,'L');
			}*/
			
		$tab->refreshCollFields();
		
		$hauler_name = get("client","name","WHERE client_id='$hauler'");

		$fn = "dropoff_details_".$hauler_name."_$now.pdf";
		
		
		$tab->Output($dir.'/'.$fn);
		
		// Send PDF to hauler Leightons
		if($hauler==206){
			echo "<font color='blue'>Sending PDF to Leightons.</font><br />";
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler);
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler,"hdzierz@gmail.com");
			send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler);
			echo "<font color='blue'>Finished.</font><br />";
		}
		
		// Send Spreaqdsheet to hauler Lyndsays 
		else if($hauler==162){
			echo "<font color='blue'>Sending CSV to Lyndsays.</font><br />";
			$fn_xls = "dropoff_details_".$hauler_name."_$now.csv";
			show_do_details_line_haul_export($job_id,$island,$dir,$fn_xls);
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler);
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn_xls,$hauler,"helge.dzierzon@computercare.co.nz");
			send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn_xls,$hauler);
			echo "<font color='blue'>Finished.</font><br />";
			
			/*echo "<font color='blue'>Sending XLS.</font><br />";
			$fn_xls = "dropoff_details_".$hauler_name."_$now.xls";
			show_do_details_line_haul_export($job_id,$island,$dir,$fn_xls,"HTML");
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler);
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn_xls,$hauler,"helge.dzierzon@computercare.co.nz");
			send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn_xls,$hauler);*/
		}
		else{
			echo "<font color='blue'>Sending PDF.</font><br />";
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler);
			//send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler,"helge.dzierzon@computercare.co.nz");
			send_operator_mail("JOB DROP OFF DETAILS",$dir,$fn,$hauler);
			echo "<font color='blue'>Finished.</font><br />";
		}
		echo "<font color='blue'>Finished.</font><br />";
		
		
	} // Foreach hauler
	echo "<font color='red'>Sending DO details finished.</font><br />";
	$qry = "UPDATE job SET is_job_details_sent=1 WHERE job_id ='$job_id'";
	query($qry); 
	
	//$job_no = get("job","job_no","WHERE job_id ='$job_id'");
	$jobs = "$job_id";
	$qry = "INSERT INTO send_report 
			SET comment='$comment',
			jobs='$jobs',
			dist_id='$hauler',
			type='do_details'";
	query($qry);
	
}

function show_do_details_line_haul_export($job_id,$island,$dir,$file,$mode="CSV"){
	set_alt_do_contractors($job_id);
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");

	$dest_type = ucfirst(get("job","dest_type","WHERE job_id='$job_id'"));
	$alt_dest_type = ucfirst(get("job","dest_type","WHERE job_id='$alt_job_id'"));

	$dest_type_short = explode('_',$dest_type);
	$dest_type_short = ucfirst($dest_type_short[1]);
	
	$alt_dest_type_short = explode('_',$alt_dest_type);
	$alt_dest_type_short = ucfirst($alt_dest_type_short[1]);

	$grand_tot_bund=0;
	$grand_tot_circ=0;
	$grand_tot_circ_alt=0;
	
	$tab = new MySQLExport($dir.'/'.$file,"",$mode);
		
	$tab->showRec				= 1;
	$tab->collField[$dest_type] = true;
	$tab->collField[$alt_dest_type] = true;
	$tab->hiddenFields["dummy1"]=true;
	$tab->hiddenFields["dummy2"]=true;
	
	//$tab->sumFields["Circ"] 	= 1;
	//$tab->sumGroupField["Name"] = 1;
	$tab->colWidth["Name"]		= 180;
	$tab->startTable();
	
	
	$start=true;
	if($alt_job_id){
		$add_sel = ",'$alt_dest_type_short' AS 'Type 2',
					SUM(IF(job_route.dest_type<>'bundles',jra.amount,0))			AS 'Num 2'";
		$add = "LEFT JOIN job_route jra
				ON jra.job_id = $alt_job_id AND jra.route_id = job_route.route_id";
			
	}		
	
	$qry = "SELECT  client.name AS Client,
					job.publication	AS Publication,
					job.job_no	AS 'Job No',
					job.delivery_date	AS 'Date',
					job_route.version AS Version,
					operator.operator_id AS 'Dropoff ID',
					IF(
						operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,
						IF(
							address.first_name2 IS NOT NULL AND address.first_name2<>'',
								CONCAT(address.first_name2,' ',IF(address.name2 IS NOT NULL,address.name2,''),' & ',address.first_name,' ',address.name),
								CONCAT(address.first_name,' ',address.name)
						)
					)		AS Dropoff,
					@comma_num_add := wordcount(operator.do_address,',') AS dummy1,
					@comma_num_city := wordcount(operator.do_city,',')  AS dummy2,
					SUBSTRING_INDEX(operator.do_address,',',1)  AS Address1,
					TRIM(IF(@comma_num_add >0,SUBSTRING_INDEX(SUBSTRING_INDEX(operator.do_address,',',2),',',-1),''))  AS Address2,
					TRIM(IF(@comma_num_add >1,SUBSTRING_INDEX(operator.do_address,',',-1),''))  AS Address3,
					
					TRIM(SUBSTRING_INDEX(operator.do_city,',',1))  AS City1,
					TRIM(IF(@comma_num_city >0,SUBSTRING_INDEX(operator.do_city,',',-1),''))  AS City2,
					operator.deliv_notes AS Notes,
				   SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS Bdls,
				   '$dest_type_short' AS Type,
				   SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) AS Num
				   $add_sel	
			FROM job_route
			$add
			LEFT JOIN job
			ON job.job_id=job_route.job_id
			LEFT JOIN client
			ON client.client_id=job.client_id
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=job_route.doff
			LEFT JOIN address
			ON operator.operator_id=address.operator_id
			LEFT JOIN operator AS dist
			ON dist.operator_id = job_route.dist_id
			WHERE job_route.job_id='$job_id'
				AND island IN($island)
			GROUP BY route.region,job_route.doff,job_route.version
			ORDER BY island,route.seq_region,seq_area,operator.company";
	//echo nl2br($qry)."<br /><br />";
	$res_t = query($qry);
	if(mysql_num_rows($res_t)>0){
		$tab->writeSQLTableElement($qry,1);
		$start_doff=false;
	}

	$tab->startNewLine();
		$tab->addLines($comments,6);
	$tab->stopNewLine();
	
	
	
	$tab->stopTable();		
}
?>