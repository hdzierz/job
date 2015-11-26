<?
$today=date("Y-m-d");
	
include $dir."includes/mysql.class.php";	
include_once $dir."includes/fpdf/fpdf.php";	
define('FPDF_FONTPATH',$dir.'includes/fpdf/font/');
require_once $dir."includes/MySQLPDFTable.php";

	
if($report=="job_manifest_select"){
	$where_add="";
	
	if($show_casual && $show_regular) $where_add.="";
	else if($show_regular) $where_add .= " AND is_regular='Y'";
	else if($show_casual) $where_add .= " AND (is_regular='N' OR is_regular ='')";
	
	
	if($ni_line_hauler && $si_line_hauler){
		$where_add .= " AND (hauler_ni_id = $ni_line_hauler OR hauler_si_id = $si_line_hauler)";
	}
	else if($ni_line_hauler) $where_add .= " AND hauler_ni_id = $ni_line_hauler";
	else if($si_line_hauler) $where_add .= " AND hauler_si_id = $si_line_hauler";
	
	
	$qry_jobs = "SELECT job.job_id AS Record,
						job.job_no AS 'Job',
						ni_hauler.name AS 'NI Hauler',
						si_hauler.name AS 'SI Hauler',
						publication AS Publication,
						IF(is_ioa='Y','IOA',delivery_date) AS 'Delivery Date',
						IF(is_regular='Y','Y','N') AS 'Is Regular'
						 FROM job 
						 LEFT JOIN client AS ni_hauler ON job.hauler_ni_id=ni_hauler.client_id
						 LEFT JOIN client AS si_hauler ON job.hauler_si_id=si_hauler.client_id
						 WHERE delivery_date BETWEEN '$date_start' AND '$date_final'
						  	$where_add
						ORDER BY job_no";
						//echo nl2br($qry_jobs);
?>
	<span class="set_button" onClick="return checkAll('yes'); return true;">All</span>
	<span class="set_button" onClick="return checkAll('no'); return true;">None</span>
<?php 
	$tab  = new MySQLTable("reports.php",$qry_jobs);
	$tab->showRec=0;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasAddButton=false;
	
	$tab->hasCheckBoxes=true;
	$tab->checkDefaultOn = true;
	
	$tab->hasForm=true;
	$tab->formPage="rep_revenue.php?report=job_manifest";
	$tab->hasSubmitButton = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Create Manifest";
	
	
	$tab->startTable();
		$tab->addHiddenInput("date_start",$date_start);
		$tab->addHiddenInput("date_final",$date_final);
		$tab->addHiddenInput("ni_linehaul",$ni_linehaul);
		$tab->addHiddenInput("si_linehaul",$si_linehaul);
		$tab->writeTable();
	$tab->stopTable();
	
}


if($report=="job_manifest"){
	if($check){
		$job_queries = "";
		$jobs="-1";
		$job_nos = array();
		foreach($check as $j=>$has){
			$job_no = get('job','job_no',"WHERE job_id=$j");
			$pub =  substr(get('job','publication',"WHERE job_id=$j"),0,10);
			$job_queries.="
				SUM(
					IF(job.job_id=$j,ROUND(weight*amount/1000,1),0)
				) AS `$job_no`,
				SUM(
					IF(job.job_id=$j,ROUND(weight*amount/1000,1),0)
				) AS `$job_no <br /> $pub`,
			";
			$jobs .= ",".$j;
			$job_nos[] = $job_no;
		}
		
		$qry = "SELECT 
					operator.company AS `Drop Off`,
					$job_queries
					SUM(ROUND(weight*amount/1000,1)) AS `Total Weight`,
					(	
						SELECT CONCAT(LPAD(seq_region,5,'0'),'.',LPAD(seq_area,5,'0'),'.',LPAD(seq_code,5,'0')) FROM route
						LEFT JOIN route_aff
						ON route.route_id=route_aff.route_id
						WHERE route_aff.dropoff_id=operator.operator_id  AND now() BETWEEN app_date AND stop_date
						GROUP BY route_aff.dropoff_id
					) 
						AS sequ
				FROM job
				LEFT JOIN job_route
				ON job_route.job_id=job.job_id
				LEFT JOIN operator
				ON operator.operator_id=job_route.doff
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				WHERE job.job_id IN($jobs)
				GROUP BY job_route.doff
				ORDER BY sequ";
	?>
		<a class="suba" href="rep_revenue.php?report=job_manifest_select">&lt&lt Back</a>
		<h3 class="weekly_head_h2">Manifest from <?=date('d F Y',strtotime($date_start))?> to <?=date('d F Y',strtotime($date_final))?></h3>
	<?php 	
//	echo nl2br($qry);	
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->showRec=1;
		$tab->hiddenFields['sequ']=true;
		foreach($job_nos as $job_no){
			$tab->collField[$job_no]=true;
			$tab->hiddenFields[$job_no]=true;
		}
		$tab->collField["Total Weight"]=true;
		
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		
		$tab->hasCheckBoxes=false;
		$tab->checkDefaultOn = false;
		
		$tab->hasForm=false;
		//$tab->formPage="rep_revenue.php?report=job_manifest";
		//$tab->hasSubmitButton = true;
		//$tab->submitButtonName = "submit";
		//$tab->submitButtonValue = "Process Delivery";
		
		
		$tab->startTable();
			$tab->writeTable();
			$tab->startNewLine();
				$tab->addLineWithStyle("SUM:","sql_extra_line_number_grey",1);
				foreach($job_nos as $job_no){
					$tab->addLineWithStyle(number_format($tab->getSum($job_no),1,'.',''),"sql_extra_line_number_grey",$num_cols);
				}
				$tab->addLineWithStyle(number_format($tab->getSum("Total Weight"),1,'.',''),"sql_extra_line_number_grey",$num_cols);
		$tab->stopNewLine();
		$tab->stopTable();
	}
}


if($report=="rep_cirpay_by_dist_send_out"){

	if(!$comment2)
		$comment2 = str_replace("<br />","\n",get("last_print_comment","comment2","WHERE last_print_comment_id=1"));	
		
	
	$dist_list = array();
	if($dist_id) {
		$dist_list[]=$dist_id;
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
	}
	
	
	
	foreach($dist_list as $dist){
	
		$tab=new MySQLPDFTable($MYSQL,'l');
		
		$tab->maxNumRows = 25;
		
		$tab->collField["Quantity"]=true;
		$tab->collField["Qty Bdl"]=true;
		$tab->collField["Dist Amt"]=true;
		$tab->collField["S/Dist Amt"]=true;
		$tab->collField["Cont Amt"]=true;
		$tab->collField["Bdl Amt"]=true;
		$tab->collField["Total"]=true;
		$tab->collField["Total (incl. GST)"]=true;
		$tab->maxchar = 70;
		$tab->SetFont('Helvetica','B',6);
		$header=array('Job','Publication','Weight','Quantity','Qty Bdl','Dist-Rate','S/Dist-Rate','Cont-Rate','Dist Amt','S/Dist Amt','Cont Amt','Bdl Amt','Total','Total (incl. GST)');
		$width = array('Job'=>10,
						'Publication'=>80,
						'Weight'=>10,
						'Quantity'=>10,
						'Qty Bdl'=>10,
						'Dist-Rate'=>10,
						'S/Dist-Rate'=>10,
						'Cont-Rate'=>10,
						'Dist Amt'=>10,
						'S/Dist Amt'=>10,
						'Cont Amt'=>10,
						'Bdl Amt'=>10,
						'Total'=>20,
						'Total (incl. GST)'=>20);				
		$maxw = array_sum($width);
		
		$maxw=270;
		$company = get("operator","company","WHERE operator_id='$dist'");
		$gst_no = get("address","gst_num","WHERE operator_id='$dist'");
		$jobs = '-1';
		$title = "Payout Report for $company ";
		
		$tab->AddPage();
		
		$tab->StartLine(8);
			$tab->WriteLine("COURAL (RURAL COURIERS SOCIETY LTD)",'L',6,$maxw);
		$tab->StopLine();
		$tab->StartLine(8);
			$tab->WriteLine("PO BOX 1233",'L',6,$maxw);
		$tab->StopLine();
		$tab->StartLine(8);
			$tab->WriteLine("PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618",'L',6,$maxw);
		$tab->StopLine();
		$tab->StartLine(8);
			$tab->WriteLine("GST NO: $gst_no",'L',6,$maxw);
		$tab->StopLine();
		
		$tab->StartLine(8);
			$tab->WriteLine($title,'L',8,$maxw);
		$tab->StopLine();
		
		$tab->WriteHeader($header,$width);
	
		$dist_name = get("operator","company","WHERE operator_id='$dist'");
		$dist_email = get("address","email","WHERE operator_id='$dist'");
		$gst_no	   = get("address","gst_num","WHERE operator_id='$dist'");
		$title = "Distributor: $dist_name, $month_show/$year";
		 
		 $group 	= "job.job_no";
		 $dist_comp	= get("operator","company","WHERE operator_id=$dist");
	
		$qry = "SELECT 		payout.*,
							@tot := ROUND(`Dist Amt`+ `S/Dist Amt` + `Cont Amt` + `Bdl Amt`,2) AS Total,
							ROUND(".(1+$GST_CIRCULAR)."*@tot,2) AS 'Total (incl. GST)'	
							
				FROM
				(
		
					SELECT job.job_no					AS 'Job',
						   job.publication				AS 'Publication',
							ROUND(job.weight,1)					AS Weight,		
						   SUM(
								IF(
									job_route.dest_type<>'bundles' ,
									amount,0)
								)
														AS 'Quantity',	
									   
						   SUM(
								IF(
									job_route.dest_type='bundles',
									amount,0)
								)
														AS 'Qty Bdl',											
														
						   round(job.dist_rate,4)   	AS 'Dist-Rate',				   
						   round(job.subdist_rate,4)    AS 'S/Dist-Rate',
						   round(job.contr_rate+job.folding_fee+job.premium,4)      AS 'Cont-Rate',
						   round(
								SUM(
									IF( job_route.dest_type<>'bundles' ,
										job_route.amount,
										0
									)
								)*
								job.dist_rate,2)     		AS 'Dist Amt' ,   
						   round(	
								SUM(
									IF(job_route.dest_type<>'bundles' ,
										job_route.amount * job_route.subdist_rate_red,
										0
									)
								)*
						   job.subdist_rate,2)     		AS 'S/Dist Amt' ,   
						   job.folding_fee+job.premium AS 'Add',
						   round(SUM(
									IF(job_route.dest_type<>'bundles' ,
										job_route.amount,
										0
									))*
						   (job.contr_rate+job.folding_fee+job.premium),2)     		AS 'Cont Amt' ,   						   
	
						   round(SUM(
								IF(	job_route.dest_type='bundles' ,
									job_route.amount*job_route.bundle_price,
									0
								)
								)
							,2)     		
														AS 'Bdl Amt'
						   
					FROM job
					LEFT JOIN job_route
					ON job_route.job_id = job.job_id
					LEFT JOIN route
					ON job_route.route_id = route.route_id
					WHERE 	(
								(year(delivery_date)='$year' AND month(delivery_date)='$month')
								OR
								(year(delivery_date)='$year1' AND month(delivery_date)='$month1')
							)
						AND job_route.dist_id=$dist
						#AND job.finished<>'Y'
						AND job.cancelled<>'Y'			
						AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
					GROUP BY $group
					ORDER BY Job
				) AS payout";
		//echo nl2br($qry);
			
			//$tab->noRepFields["Publication"]=$show_pub;
			
			
			$data = $tab->LoadData($qry);
			
			foreach($data as $row){
				$jobs .= ','.$row["Job"];
			}
			$tab->WriteTable($header,$data,$width);
			
			$qty = $tab->getSum("Quantity",0);
			$qty_bdl = $tab->getSum("Qty Bdl",0);
			$dist_amt = $tab->getSum("Dist Amt");
			$sdist_amt = $tab->getSum("S/Dist Amt");
			$contr_amt = $tab->getSum("Cont Amt");
			$bdl_amt = $tab->getSum("Bdl Amt");
			$tot_pay = $tab->getSum("Total");
			$tot_pay_gst = $tab->getSum("Total (incl. GST)");
			
			$tab->StartLine(7);
			
				$tab->WriteLine("",'R',7,$width["Job"]);
				$tab->WriteLine("Totals.:",'R',7,$width["Publication"]);
				$tab->WriteLine("",'R',7,$width["Weight"]);
				
				$tab->WriteLine($qty,'R',7,$width["Quantity"]);
				$tab->WriteLine($qty_bdl,'R',7,$width["Qty Bdl"]);
				
				$tab->WriteLine("",'R',7,$width["Dist-Rate"]);
				$tab->WriteLine("",'R',7,$width["S/Dist-Rate"]);
				$tab->WriteLine("",'R',7,$width["Cont-Rate"]);
				
				$tab->WriteLine($dist_amt,'R',7,$width["Dist Amt"]);
				$tab->WriteLine($sdist_amt,'R',7,$width["S/Dist Amt"]);
				$tab->WriteLine($contr_amt,'R',7,$width["Cont Amt"]);

				$tab->WriteLine($bdl_amt,'R',7,$width["Bdl Amt"]);
				$tab->WriteLine($tot_pay,'R',7,$width["Total"]);
				$tab->WriteLine($tot_pay_gst,'R',7,$width["Total (incl. GST)"]);
					
			$tab->StopLine();
			
			//$tab->WordWrap($comment2, $maxw-1,'\n');
			$tab->MultiCell($maxw,7,$comment2,0,'L');
			
			/*$tab->StartLine(7);
				$tab->WriteLine($comment2,'L',7,$maxw);
			$tab->StopLine();*/
			
			$tab->StartLine(7);
				$message = "BUYER GEN. INV.-IRD APPR. GST NO: 24-992-802    DATE: ".date('d/m/Y');
				$tab->WriteLine($message,'L',7,190);
			$tab->StopLine();
			
			$tab->refreshCollFields();
			
			$now=date("Y_m_d_h_i_s");
			$dir = $SEND_OUTPUT_DIR."temp_payout";
			$fn = "payout_".$dist_name."_$now.pdf";
			
			$tab->Output($dir.'/'.$fn);
			
			if($tot_pay_gst>0){
				$t = new mailThread("DELIVERY INSTRUCTIONS BY DATERANGE",$dir,$fn,$dist);
                $t->start();
				//send_operator_mail("DELIVERY INSTRUCTIONS BY DATERANGE",$dir,$fn,$dist,"hdzierz@gmail.com");
			}
			
		$qry = "UPDATE job SET is_pay_sent=1 WHERE job_no IN($jobs)";
		query($qry);
		
		$qry = "INSERT INTO send_report 
				SET comment='$comment2',
				jobs='$jobs',
				dist_id='$dist_id',
				year = '$year',
				month = '$month',
				show_regular = 'N',
				show_casual = 'N',
				type='$report'";
		query($qry);
		
		echo "<font color='red'>Sending payout finished.</font><br />";
	}
}

function get_maxw($width){
	$maxw=0;
	foreach($width as $f=>$w){
		$maxw += $w;
	}
	return $maxw;
}

function clean_file_name($fn){
	$fn = stripslashes($fn);
	$fn  = str_replace(" ","_",$fn);
	$fn  = str_replace("'","_",$fn);
	$fn  = str_replace("&","_",$fn);
	$fn  = str_replace("(","_",$fn);
	$fn  = str_replace(")","_",$fn);
	$fn  = str_replace("\\","_",$fn);

	
	return $fn;
}

function _exec($cmd) 
{ 
   $WshShell = new COM("WScript.Shell"); 
   $cwd = getcwd(); 
   if (strpos($cwd,' ')) 
   {  if ($pos = strpos($cmd, ' ')) 
      {  $cmd = substr($cmd, 0, $pos) . '" ' . substr($cmd, $pos); 
      } 
      else 
      {  $cmd .= '"'; 
      } 
      $cwd = '"' . $cwd; 
   }   
   $oExec = $WshShell->Run("cmd /C \" $cwd\\$cmd\"", 0,false); 
   echo "cmd /C \" $cwd\\$cmd\"";
   return $oExec == 0 ? true : false; 
} 

function execInBackground($path, $exe, $args = "") {
   global $conf;
   echo  "cmd /C bin\\pdftk\\pdftk.exe ".$args;
   //pclose(popen("start \"bla\" \"" . $path . $exe . "\" " . escapeshellarg($args), "r"));  
   $WshShell = new COM("WScript.Shell");
	$oExec = $WshShell->Run("cmd /C \"bin\\pdftk\\pdftk.exe ".$args."\"", 0, false);
	print_r($oExec);
}

function pdf_merge($now,$file){
	//$exec = "bin/pdftk/pdftk.exe ";
	$exec = "";
	
	if(file_exists($SEND_OUTPUT_DIR."temp_deliv/$now/temp.pdf"))
		unlink($SEND_OUTPUT_DIR."temp_deliv/$now/temp.pdf");
	
	if(file_exists($SEND_OUTPUT_DIR."temp_deliv/$now/delivery_summary_proof.pdf"))
		$exec .= " ".$SEND_OUTPUT_DIR."temp_deliv\\$now\\delivery_summary_proof.pdf ";
	$exec .= " ".$SEND_OUTPUT_DIR."temp_deliv\\$now\\".$file." ";
	
	
	echo "Executing PDF merge for $file.<br />";

	$exec .= " output ".$SEND_OUTPUT_DIR."temp_deliv\\$now\\temp.pdf";
	
	//echo  str_replace("\\\\","\\",$exec);
	$WshShell = new COM("WScript.Shell");
	$oExec = $WshShell->Run("cmd /C \"C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\Coural\bin\pdftk\pdftk.exe\" \"$SEND_OUTPUT_DIR/temp_deliv\2008_12_05_17_03_45\delivery_instructions_Coleman.pdf\" output \"C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\Coural\temp_deliv\2008_12_05_17_03_45\temp.pdf\"", 0, false);
	//_exec("bin\\pdftk\\pdftk.exe ".$exec);
	//execInBackground("bin\\pdftk\\", "pdftk.exe ", $exec) ;
	//exec($exec,$o);
	if(file_exists("temp_deliv/$now/delivery_summary_proof.pdf"))
		unlink("temp_deliv/$now/delivery_summary_proof.pdf");
	rename("temp_deliv/$now/temp.pdf","temp_deliv/$now/delivery_summary_proof.pdf");
	die();
}

if($report=="linehaul_send_out"){
	$pdffiles = array();
	$now=date("Y_m_d_H_i_s");
	$dir=$SEND_OUTPUT_DIR."temp_deliv/$now";
	mkdir($dir);
	
	$receiver = false;
	$mail_receiver = 'dayna@coural.co.nz';
	//$mail_receiver = 'hdzierz@gmail.com';
	
	$count_mail=0;
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");	

	if(($date_start&&$date_final)||$check){
		
		//$fp = fopen("debug.txt","w+");
		$date_show_start 	= date("jS M Y",strtotime($date_start));
		$date_show_end 		= date("jS M Y",strtotime($date_final));
		if($name!='All' && $name) $where_add = " AND client_id='$name'";
		
		$qry_dist = "SELECT DISTINCT client_id,name FROM client WHERE is_linehaul='1' $where_add";
		$res_dist = query($qry_dist);
		
		echo "Num Linehaulers:".mysql_num_rows($res_dist)."<br />";
		$jobs = array();
		
		while($haul = mysql_fetch_object($res_dist))
		{
			echo "Preparing linehaul job summary for <strong>$haul->name</strong>.<br />";
			//fwrite($fp,"Preparing linehaul job summary for <strong>$haul->name</strong>.\n");
			
			/////////////////////////////////////////////////////////
			// LINEHAUL SEND OUT
			/////////////////////////////////////////////////////////
			
			$haul_id=get("client","client_id","WHERE name='$haul->name'");
			$where_add_client = "";
			//echo "Hello".$client_id;
			if($client_id!="All") $where_add_client = " AND client_id=$client_id";
			
			$qry_jobs = "SELECT DISTINCT job_no, is_regular, hauler_ni_id, hauler_si_id ,
							IF(is_ioa='Y','1990-01-01', delivery_date) AS st
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 WHERE delivery_date>='$date_start'
						 	AND delivery_date<='$date_final'
							$where_add_client
							AND (job.hauler_ni_id='$haul_id'
							OR job.hauler_si_id='$haul_id')
						ORDER BY st, job_no";
			//echo nl2br($qry_jobs);exit;
			$res_jobs = query($qry_jobs);
			
			$num_jobs = mysql_num_rows($res_jobs);
			
			$tab  = new MySQLPDFTable($MYSQL,'l');
			$tab->collField["Total Quantity"]=true;
			$tab->hasDivider=false;
			$tab->border = "LRB";
			$header=array('Job #','PMP Job #','Circular','Recd Date','Pick Date','D/Date','Disp Qty','Weight','Rural','PO Box','Total', 'Recd Qty', 'Overs/Unders', 'Signed');
			$width=array('Job #'=>15,'PMP Job #' => 20,'Circular'=>30,'Recd Date'=>20,'D/Date'=>15,'Pick Date'=>20,'Disp Qty' => 20, 'Weight'=>20,'Rural'=>20,'PO Box'=>15,'Total'=>20, 'Recd Qty' => 20, 'Overs/Unders' => 20, 'Signed' => 20);
			$tab->fontSize = 9;
			
			//$tab->norepField["Circular"]=true;
			
			$tab->AddPage();
			$maxw=get_maxw($width);
			
			$title = "Linehaul job summary for: $haul->name.";
			$title2 = "Date range: $date_show_start to $date_show_end.";
			
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',5,$maxw);
			$tab->StopLine();
			$tab->StartLine(10);
				$tab->WriteLine($title2,'L',5,$maxw);
			$tab->StopLine();
		
			$tab->WriteHeader($header,$width);
			
			$first_job=true;
			
			$first_casual = true;
			
			if(!$show_casual && !$show_regular)
			{
				$show_regular = true;
			}
			
			if(true)
			//if($show_regular)
			{
			
				while($job=mysql_fetch_object($res_jobs))
				{
					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$group = "GROUP BY job_route.route_id";					
						$num_blank_cols = 2;
					}
					else{
						$group = "GROUP BY job.job_no";
						$num_blank_cols = 2;
					}
					if($haul_id == '206')
						$island_select = " AND route.island='NI'";
					elseif($haul_id == '162')
						$island_select = " AND route.island='SI'";
					
					$qry = "SELECT 	job.job_id 				AS Record,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							CONCAT('#',job.pmp_job_no)         AS 'PMP Job #',
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN CONCAT(LEFT(job.publication,17),'...')
								ELSE job.publication
							END
											AS Circular,
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
								AS 'D/Date',
							ROUND(job.weight,0) AS 'We',
							SUM(IF(route.region<>'PMP',job_route.amount,0)) AS 'Rural',
							SUM(IF(route.region='PMP',job_route.amount,0)) AS 'PO Boxes',
							SUM(job_route.amount) AS 'Total',
							SUM(job_route.amount) AS 'Total Quantity'
							
							FROM job
							LEFT JOIN job_route
							ON job.job_id=job_route.job_id
							LEFT JOIN route
								ON route.route_id=job_route.route_id
							
							WHERE job.job_no='$job->job_no'		
								$island_select
								AND job.cancelled<>'Y'
								/*AND job.is_regular='Y'*/
							GROUP BY 'Job #'
							ORDER BY job.job_no";
						
						$res_sum = query($qry);
						$line=mysql_fetch_object($res_sum);
						//echo print_r($line); exit;
						if($line->Record)
						{
				
							$data = $tab->LoadData($qry);
							$tab->WriteTable($header, $data, $width, 7);
							
						}
						
				}//while job
			}
			
			if(false)
			//if($show_casual)
			{
				mysql_data_seek($res_jobs, 0);
				while($job=mysql_fetch_object($res_jobs))
				{
					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$group = "GROUP BY job_route.route_id";					
						$num_blank_cols = 2;
					}
					else{
						$group = "GROUP BY job.job_no";
						$num_blank_cols = 2;
					}
					if($haul_id == '206')
						$island_select = " AND route.island='NI'";
					elseif($haul_id == '162')
						$island_select = " AND route.island='SI'";
					
					$qry = "SELECT 	job.job_id 				AS Record,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN CONCAT(LEFT(job.publication,17),'...')
								ELSE job.publication
							END
											AS Circular,
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
								AS 'Delivery Date*',
							job.weight AS 'Weight (if known)',
							SUM(job_route.amount) AS 'Total Quantity'
							
							FROM job
							LEFT JOIN job_route
							ON job.job_id=job_route.job_id
							LEFT JOIN route
								ON route.route_id=job_route.route_id
							
							WHERE job.job_no='$job->job_no'		
								$island_select
								AND job.cancelled<>'Y'
								AND job.is_regular<>'Y'
							GROUP BY 'Job #'
							ORDER BY job.job_no";
						
						$res_sum = query($qry);
						$line=mysql_fetch_object($res_sum);
						//echo print_r($line); exit;
						if($line->Record)
						{
				
							$data = $tab->LoadData($qry);
							$tab->WriteTable($header, $data, $width, 7);
							
						}
						
				}//while job
			}
			
			$tab->MultiCell($maxw,3,$comment2,1,'L');			
			$tab->refreshCollFields();
			$first_job=true;
			
			$fn = clean_file_name("linehaul_job_summary_".$haul->name.".pdf");
			
			$tab->Output($dir.'/'.$fn);
			
			echo "Linehaul job summary for <strong>$haul->name</strong> created.<br />";
			//fwrite($fp,"Linehaul job summary for <strong>$haul->name</strong> created.\n");
			if($num_jobs>0){
				if(!$pdf_only){
                     $t = new mailThread("LINEHAUL JOB SUMMARY",$dir,$fn,$haul->client_id);
                     $t->start();
				}
			}
			
		}
	}
}


$doff_prev = array();
$ct=0;
function weekly_a5($doff, $where_add, $dirp, $date_start, $date_final, $pdf_only, $receiver, $is_by_job){
    $pdffiles = array();
    GLOBAL $doff_prev;
    GLOBAL $ct;
    if(isset($doff_prev[$doff])){
        return;
    }
    $doff_prev[$doff] = true;
    $font_size = 8;
    $count_mail=0;
    if(!$comment2)
        $comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");

    if($date_start&&$date_final){
        $date_show_start    = date("jS M Y",strtotime($date_start));
        $date_show_end      = date("jS M Y",strtotime($date_final));

        $jobs = array();
            $qry = "
                    SELECT SUM(amount) AS amt,
                            job.*,
                            DATE_FORMAT(job.delivery_date,'%e %M') AS disp_date,
                            address.first_name,
                            address.name,
                             GROUP_CONCAT(DISTINCT IF(is_att<>'Y', 
                                   CASE job_route.dest_type
                                        WHEN 'num_lifestyle' THEN 'L/Style'
                                        WHEN 'num_farmers' THEN 'Farmer'
                                        WHEN 'num_dairies' THEN 'Dairy'
                                        WHEN 'num_sheep' THEN 'Sheep'
                                        WHEN 'num_beef' THEN 'Beef'
                                        WHEN 'num_sheepbeef' THEN 'S/Beef'
                                        WHEN 'num_dairybeef' THEN 'D/Beef'
                                        WHEN 'num_hort' THEN 'Hort'
                                        WHEN 'num_total' THEN 'Total'
                                        WHEN 'num_nzfw' THEN 'F@90%'
                                        WHEN 'bundles' THEN 'Bundles'
                                   END
                                   , '') SEPARATOR '') AS 'Type',
                            route.code ,
                            operator.company,
                            operator.send_contr_sheet 
                    FROM job
                    LEFT JOIN job_route
                        ON job_route.job_id=job.job_id
                    LEFT JOIN operator
                        ON job_route.contractor_id=operator.operator_id
                    LEFT JOIN address
                        ON address.operator_id=operator.operator_id
                    LEFT JOIN route
                        ON job_route.route_id=route.route_id
                    WHERE
                        doff={$doff}
                        $where_add 
                        AND print_advices = 'Y'
                    GROUP BY job.job_id, contractor_id, route.route_id
                    ORDER BY job.job_id, route.code
                ";
            $res_contr = query($qry);
            if(mysql_num_rows($res_contr)>0){
                $pdf = new a5label('P', 'mm', 'A4');
                //$pdf->AliasNbPages();
                while($contr = mysql_fetch_object($res_contr)){
                    $pdf->AddPage();
                    $pdf->SetFontSize(24);
                    $pdf->Cell(0,18,$contr->name.'-'.$contr->first_name,0,1);
                    $pdf->SetFontSize(16);
                    $pdf->Cell(0,18,$contr->code,0,1);
                    $pdf->SetFontSize(20);
                    $pdf->Cell(0,9,"Delivery Date: ".$contr->disp_date,0,1);
                    $pdf->SetFontSize(12);
                    $pdf->Cell(0,9,"Delivery Type: ".$contr->Type,0,1);
                    $pdf->SetFontSize(20);
                    $pdf->Cell(0,9,"Quantity: ".$contr->amt,0,1);
                    $pdf->SetFontSize(14);
                    $pdf->Cell(0,9,"Job Number: ".$contr->job_no,0,1);
                    $pdf->Cell(0,9,"Job Name: ".$contr->publication,0,1);
                    $pdf->Cell(0,9,"Version: ".$contr->version,0,1);
                    if($contr->show_comments == 'Y')
                        $pdf->Cell(0,9,"Special Notes: ".$contr->comments,0,1);
                }
                $do_name = get("operator","company","WHERE operator_id=$doff");
                $fn = addslashes('contractor_sheets_'.$do_name.'_'.$ct.'.pdf');
                $ct++;
                $pdf->Output($dirp.'/'.$fn,'F');
                if(!$pdf_only) {
                    $t = new mailThread("COURAL DELIVERY INSTRUCTIONS (CONTR SHEET)",$dirp,$fn,$doff, null);
                    $t->start();
                }
            }//if(mysql_num_rows($res_contr>0)
    }
}

class mailThread{
    var $i = 0;
    var $title = "";
    var $dirp = "";
    var $fn = "";
    var $dropoff_id = 0;
    var $receiver = "";
    public function __construct($title,$dirp,$fn,$dropoff_id,$receiver=null){
        $this->title = $title;
        $this->dirp = $dirp;
        $this->fn = $fn;
        $this->dropoff_id = $dropoff_id;
        $this->receiver = $receiver;
    }
    public function run(){
        $val = $this->title.','.$this->dirp.','.$this->fn.','.$this->dropoff_id.','.$this->receiver;
        $qry = "INSERT INTO schedule_mail_send_out(`config`, status) VALUES('$val', 1)";
        query($qry);
        #send_operator_mail($this->title,$this->dirp,$this->fn,$this->dropoff_id,$this->receiver);
    }
    public function start(){
        sleep(1);
        $this->run();
    }
}


if($report=="weekly_send_out"){
    if($submit=="Create PDF only"){
        $pdf_only = true;
    }
    if($company=='All'){
        $qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y'";
        $res_dist = query($qry_dist);
        $count = mysql_num_rows($res_dist);
        $c = 1;
        while($op = mysql_fetch_object($res_dist)){
            echo "Num Dist: $c / $count<br />";
            $c++;
            $qstr =  $_SERVER['QUERY_STRING'];
            $qstr = str_replace("All", "{$op->operator_id}", $qstr);
            $qry = "INSERT INTO schedule_mail_send_out(config, status) VALUES('$qstr',2)";
            query($qry);
            $company = get('operator', 'company', "WHERE operator_id={$op->operator_id}");
            echo "<b>Sendout for $company scheduled</b><br .>";
        }
    }
    else{
        weekly_send_out($company, $date_start, $date_final, $show_regular, $show_casual, $show_rd_details, $include_contr, $pdf_only, $check);
    }
}

function weekly_send_out($company, $date_start, $date_final, $show_regular, $show_casual, $show_rd_details, $include_contr, $pdf_only, $check){
    global $MYSQL;
    $threads = array();	
	//$pdf_only=true;
	$pdffiles = array();
	$now=date("Y_m_d_H");
	$dirp=$SEND_OUTPUT_DIR."temp_deliv/$now";
	@mkdir($dirp);
	
	$font_size = 8;
    if($submit=="Create PDF only"){
        $pdf_only=true;
    }
	// Helge
	//$receiver = "helge.dzierzon@computercare.co.nz";
	//$mail_receiver = "helge.dzierzon@computercare.co.nz";
	// Howard
	//$receiver = "howard@coural.co.nz";
	//$mail_receiver = 'dayna@coural.co.nz';
	// Life!!!!!!!!!
	$receiver = false;
	$mail_receiver = 'dayna@coural.co.nz';
	
	$count_mail=0;
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");	

	if(($date_start&&$date_final)||$check){
	
		//$fp = fopen("debug.txt","w+");
		$date_show_start 	= date("jS M Y",strtotime($date_start));
		$date_show_end 		= date("jS M Y",strtotime($date_final));
		if($company!='All' && $company) $where_add = " AND operator_id='$company'";
		
	
        //sleep(60);
	
		$qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y' $where_add";
		
		$res_dist = query($qry_dist);
		
		#echo "Num Dist:".mysql_num_rows($res_dist)."<br />";
		$jobs = array();
		while($dist = mysql_fetch_object($res_dist)){
			$dist_id=$dist->operator_id;
			$company=get("operator","company","WHERE operator_id='$dist_id'");
			$name = get("address","name","WHERE operator_id='$dist_id'");
			
			echo "Preparing delivery instructions for <strong>$company</strong>.<br />";
			//fwrite($fp,"Preparing delivery instructions for <strong>$company</strong>.\n");
			/////////////////////////////////////////////////////////
			// DISTRIBUTOR SEND OUT
			/////////////////////////////////////////////////////////
			if($check) {
				$is_job_report = true;
				$where_add = " AND job.job_id IN (-1";
				foreach($check as $job_id=>$has_job){
					$where_add .= " ,$job_id";
				}
				$where_add .= ")";
			}
			else{
				$is_job_report = false;
				$where_add = " AND delivery_date>='$date_start'
						 	AND delivery_date<='$date_final' ";
			}
			
			$qry_jobs = "SELECT DISTINCT job_no,is_regular,job.job_id, inc_linehaul
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 WHERE job_route.dist_id='$dist_id'
						 	AND is_att<>'Y'
							$where_add
						ORDER BY is_regular DESC,job_no,job_no_add";
			//echo nl2br($qry_jobs); die();
			$res_jobs = query($qry_jobs);
			
			$num_jobs = mysql_num_rows($res_jobs);
			
			$tab  = new MySQLPDFTable($MYSQL,'l');
			//$tab->setPersRotation(90);
			$tab->AliasNbPages();
			$tab->SetTopMargin(5);
			$tab->collField["Qty"]=true;
			//$tab->collField["Qty 2"]=true;
			$tab->collField["Tot"]=true;
			$tab->hasDivider=false;
			$header=array('Job #','Is Regular','Type','Circular','Client','D/Date*','Dropoff','RD','Qty');
			$width=array('Job #'=>15,'Is Regular'=>15,'Type'=>20,'Circular'=>45,'Client'=>40,'D/Date*'=>15,'Dropoff'=>30,'RD'=>30,'Qty'=>15,'Tot'=>15);
			$tab->fontSize = $font_size;
			
			//$tab->norepField["Type"]=true;
			$tab->norepField["Dropoff"]=true;
			$tab->norepField["Circular"]=true;
			$tab->norepField["Client"]=true;

			$tab->AddPage();
			
			$maxw=get_maxw($width);
			
			if($is_job_report)
				$title = "Drop off details for $company.";
			else
				$title = "Drop off details for $company. Date range: $date_show_start to $date_show_end.";
				
			
			$tab->StartLine(10);
				$tab->WriteLine($title,'L',10,$maxw);
				$tab->WriteLine('Dist '.$tab->PageNo().'/{nb}','R',10,30);
			$tab->StopLine();
		
			$tab->WriteHeader($header,$width);
			
			$first_job=true;
			
			$first_casual = true;
			
			while($job=mysql_fetch_object($res_jobs)){
				$jobs[] = $job->job_id;
				
				if($job->is_regular<>'Y' && $first_casual=true){
					$tab->SetFillColor(200,200,200);
					$tab->Cell(230,3,"",0,0,'C',1);
				
					$tab->Ln();
					$tab->SetFillColor(255,255,255);
					$first_casual=false;
				}
			
				$qry_dos = "SELECT DISTINCT CONCAT(name,'_',first_name) AS name,
								company,
								dropoff_id,
								mail_type,
                                inc_linehaul
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 LEFT JOIN operator
						 ON operator.operator_id=dropoff_id
						 LEFT JOIN address
						 ON address.operator_id=operator.operator_id
						 WHERE job_route.dist_id='$dist_id'
							AND job.job_id = $job->job_id
						ORDER BY company";
				//echo nl2br($qry_jobs); 
				$res_dos = query($qry_dos,0);
				$tot_qty1 = 0;
				$tot_qty2 = 0;
				while($do = mysql_fetch_object($res_dos)){
                    if(!$do->dropoff_id) continue;
                    $send_contr_sheet = get("operator", "send_contr_sheet", "WHERE operator_id=$do->dropoff_id");
                    if($send_contr_sheet == 'Y' && $job->inc_linehaul == 'Y'){
                        #$is_by_job = false;
                        weekly_a5($do->dropoff_id, $where_add, $dirp, $date_start, $date_final, $pdf_only, $receiver, $is_job_report);
                    }
					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$group = "GROUP BY job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
						$sel_rd = "route.code AS 'RD',";					
						$num_blank_cols = 5;
					}
					else{
						$group = "GROUP BY job.job_no,IF(job_route.dest_type='bundles',1,0)";
						$num_blank_cols = 5;
						$sel_rd = "'' AS RD,";
					}
					$qry = "SELECT 	job.job_id 				AS Record,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							IF(job.is_regular='Y','Y','N')	 AS 'Is Regular',
							client.name AS client,
							GROUP_CONCAT(DISTINCT IF(is_att<>'Y', 
								   CASE job_route.dest_type
										WHEN 'num_lifestyle' THEN 'L/Style'
										WHEN 'num_farmers' THEN 'Farmer'
										WHEN 'num_dairies' THEN 'Dairy'
										WHEN 'num_sheep' THEN 'Sheep'
										WHEN 'num_beef' THEN 'Beef'
										WHEN 'num_sheepbeef' THEN 'S/Beef'
										WHEN 'num_dairybeef' THEN 'D/Beef'
										WHEN 'num_hort' THEN 'Hort'
										WHEN 'num_total' THEN 'Total'
										WHEN 'num_nzfw' THEN 'F@90%'
										WHEN 'bundles' THEN 'Bundles'
								   END
								   , '') SEPARATOR '') AS 'Type',
								
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN 
									CONCAT(LEFT(job.publication,17),'...')
								ELSE 
									job.publication
							END
											AS Circular,
							CASE client.name
								WHEN LENGTH(client.name)<=20 THEN 
									CONCAT(LEFT(client.name,17),'...')
								ELSE 
									client.name
							END
											AS Client,
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
									AS 'D/Date*',
							CASE operator.company
								WHEN LENGTH(operator.company) <= 20 THEN 
									CONCAT(LEFT(operator.company,17),'... (',mail_type,')')
								ELSE CONCAT(operator.company,' (',mail_type,')')
							END
								AS Dropoff,
                            inc_linehaul,
							$sel_rd
							SUM(IF(is_att<>'Y',job_route.amount,0)) 	AS 'Qty'
					FROM job
					LEFT JOIN client
					ON job.client_id=client.client_id
					LEFT JOIN job_route
					ON job.job_id=job_route.job_id
					LEFT JOIN route
						ON route.route_id=job_route.route_id
					LEFT JOIN operator
						ON job_route.dropoff_id=operator.operator_id
					LEFT JOIN address
						ON address.operator_id=operator.operator_id
					WHERE job.job_no='$job->job_no'					
						AND job_route.dist_id=$dist_id
						AND job.cancelled<>'Y'
						AND job_route.dropoff_id = $do->dropoff_id
					$group
					ORDER BY seq_region,seq_area,seq_code,job.job_no,operator.company";				/*echo nl2br($qry);
					echo "<br />";
					echo "<br />";
					echo "<br />";*/
	
					$data = $tab->LoadData($qry);
								
					$tab->WriteTable($header,$data,$width,4,false);		

					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$tab->StartLine($font_size);
							$width_emtpy = $maxw-$width["Tot"]-$width["Qty"]-$width["RD"];
							$tab->WriteLine("",'R',5,$width_emtpy);
							$tab->WriteLine("Total:",'R',5,$width["RD"]);
							$tab->WriteLine($tab->getSum("Qty",0),'R',5,$width["Qty"]);
							//$tab->WriteLine($tab->getSum("Qty 2",0),'R',5,$width["Qty 2"]);
							
							
							
						$tab->StopLine();

					}
					$tot_qty1 += $tab->getSum("Qty",0);
					//$tot_qty2 += $tab->getSum("Qty 2",0);
					$first_job=false;

					$tab->collFieldVal["Qty"] = array();
					//$tab->collFieldVal["Qty 2"] = array();
	
				}//while dos
				$tab->StartLine($font_size);
					$width_emtpy = $maxw-$width["Tot"]-$width["Qty"]-$width["RD"];
					$tab->WriteLine("",'R',5,$width_emtpy);
					$tab->WriteLine("Total:",'R',5,$width["RD"]);
					$tab->WriteLine($tot_qty1,'R',5,$width["Qty"]);
					//$tab->WriteLine($tot_qty2,'R',5,$width["Qty 2"]);
				$tab->StopLine();
			} // while job
			$first_job=true;
			
			$comment2 = str_replace("<br />","\n",$comment2);
			$tab->StartLine($font_size);
				$tab->WriteLine("* D/Date as DD-MM",'L',$font_size,$maxw);
			$tab->StopLine();
			$tab->MultiCell($maxw-10,3,$comment2,1,'L');
		
			
			$tab->refreshCollFields();

			
		
			$fn = clean_file_name("dist_delivery_instructions_".$name.".pdf");
			
			if($num_jobs>0){
				$tab->Output($dirp.'/'.$fn);
				//$tab->Output();
				
				echo "Delivery instructions for <strong>$company</strong> created.<br />";
				
				//fwrite($fp,"Delivery instructions for <strong>$company</strong> created.\n");
		
				if(!$pdf_only) {
                    $threads[$fn] = new mailThread("COURAL DELIVERY INSTRUCTIONS",$dirp,$fn,$dist_id,$receiver);
                    $threads[$fn]->start();	
				}
				
				$pdffiles[] = $dirp.'/'.$fn;
			}
			
			
			/////////////////////////////////////////////////////////
			// DROPOFF SEND OUT
			/////////////////////////////////////////////////////////
			$where_add_contr = "";
			
			#if($include_contr) $where_add_contr = " AND parcel_send_di='N' ";
			$qry_dos = "SELECT DISTINCT CONCAT(name,'_',first_name) AS name,
								company,
								dropoff_id,
								mail_type
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 LEFT JOIN operator
						 ON operator.operator_id=dropoff_id
						 LEFT JOIN address
						 ON address.operator_id=operator.operator_id
						 WHERE job_route.dist_id='$dist_id'
							$where_add
							$where_add_contr
							#mailt_type='m'
						ORDER BY company";
			//echo nl2br($qry_dos); 
			//die();
			$res_dos = query($qry_dos,0);
		
				echo "Number of DOs:".mysql_num_rows($res_dos)."<br />";
				//fwrite($fp,"Number of DOs:".mysql_num_rows($res_dos)."\n");
				$count=0;
				while($do=mysql_fetch_object($res_dos)){
					$count++;
					echo "($count) Preparing delivery instructions for <strong>$do->company</strong>.<br />";
					//fwrite($fp,"($count) Preparing delivery instructions for <strong>$do->company</strong>.\n");
					$qry_jobs = "SELECT DISTINCT job_no,is_regular
								 FROM job 
								 LEFT JOIN job_route
								 ON job.job_id=job_route.job_id
								 LEFT JOIN route
								 ON route.route_id=job_route.route_id
								 WHERE job_route.dist_id='$dist_id'
									
									AND job_route.dropoff_id='$do->dropoff_id'
									$where_add
								ORDER BY is_regular DESC,job_no,job_no_add";
					//echo nl2br($qry_jobs); die();
					$res_jobs = query($qry_jobs);
				
					
					$tab  = new MySQLPDFTable($MYSQL,'l');
					$tab->SetTopMargin(5);
					$tab->AliasNbPages();
					$tab->fontSize = $font_size;
					//$tab->norepField["Type"]=true;
					$tab->norepField["Circular"]=true;
					$tab->norepField["Client"]=true;

	
					$tab->SetFont('Helvetica','B',$font_size);
					
					
					$tab->collField["Qty"]=true;
					//$tab->collField["Qty 2"]=true;
					$tab->collField["Tot"]=true;
					
					
					$tab->AddPage();
					
					
					$title = "Distributor: $dist->company";
					$tab->StartLine(8);
						$tab->WriteLine($title,'L',6,$maxw-20);
						$tab->WriteLine('Drop off '.$tab->PageNo().'/{nb}','R',6,30);
					$tab->StopLine();
					
					if($is_job_report)
						$title = "Drop off details and delivery report for $do->company.";
					else
						$title = "Drop off details and delivery report for $do->company. Date range: $date_show_start to $date_show_end.";
						
					
					$tab->StartLine(8);
						$tab->WriteLine($title,'L',8,$maxw);
					$tab->StopLine();
				
					
				
					if($show_rd_details){
						$group = "GROUP BY job_no,job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
						$sel_rd = "route.code AS 'RD',";					
						$num_blank_cols = 5;
						
						$header=array('Job #','Type','Circular','Client','D/Date','RD','Version', 'Qty','Weight','Date Recd.');
						$width=array('Job #'=>15,'Type'=>20,'Circular'=>55,'Client'=>40,'D/Date'=>12,'RD'=>40,'Version'=>20,'Qty'=>20,'Weight'=>12,'Date Deliv.'=>20);
						$maxw=get_maxw($width);
						$width_emtpy = $maxw-$width["Qty"]-$width["RD"];
						$width_tot_f = $width["RD"];
					}
					else{
						$group = "GROUP BY job.job_no,job_route.dropoff_id,IF(job_route.dest_type='bundles',1,0)";
						$num_blank_cols = 5;
						
						$header=array('Job #','Type','Circular','Client','D/Date','Version','Qty','Weight','Date Recd.');
						$width=array('Job #'=>20,'Type'=>20,'Circular'=>60,'Client'=>45,'D/Date'=>20,'Version'=>20,'Qty'=>20,'Weight'=>15,'Date Recd.'=>35);
						$maxw=get_maxw($width);
						$width_emtpy = $maxw-$width["Qty"]-$width["D/Date"];
						$width_tot_f = $width["D/Date"];
					}
					
					$tab->StartLine($font_size);
						$tab->WriteLine("Regular Jobs",'L',$font_size,$maxw);
					$tab->StopLine();

					
					$qry = "SELECT 	job.job_id 		AS job_id,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							GROUP_CONCAT(DISTINCT  IF(is_att<>'Y', 
							   CASE job_route.dest_type
									WHEN 'num_lifestyle' THEN 'L/Style'
									WHEN 'num_farmers' THEN 'Farmer'
									WHEN 'num_dairies' THEN 'Dairy'
									WHEN 'num_sheep' THEN 'Sheep'
									WHEN 'num_beef' THEN 'Beef'
									WHEN 'num_sheepbeef' THEN 'S/Beef'
									WHEN 'num_dairybeef' THEN 'D/Beef'
									WHEN 'num_hort' THEN 'Hort'
									WHEN 'num_total' THEN 'Total'
									WHEN 'num_nzfw' THEN 'F@90%%'
									WHEN 'bundles' THEN 'Bundles'
							   END
							   , '') SEPARATOR '') AS 'Type',
							
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN 
									CONCAT(LEFT(job.publication,17),'...')
								ELSE CONCAT(job.publication)
							END
										AS Circular,
							CASE client.name
								WHEN LENGTH(client.name)<=20 THEN 
									CONCAT(LEFT(client.name,17),'...')
								ELSE 
									client.name
							END
											AS Client,
							%s
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%%m-%%d'))
								AS 'D/Date',
							job_route.version AS Version,
							SUM(IF(is_att<>'Y',job_route.amount,0)) 	AS 'Qty'
							
					FROM job
					LEFT JOIN client
					ON client.client_id=job.client_id
					LEFT JOIN job_route
					ON job.job_id=job_route.job_id
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE dropoff_id='$do->dropoff_id'					
						AND job_route.dist_id=$dist_id
						AND job.cancelled<>'Y'
						$where_add
						%s
					%s
					ORDER BY job.is_regular DESC, job.job_no";
					
					$tab->WriteHeader($header,$width);
					
					if(mysql_num_rows($res_jobs)==0){
						$tab->StartLine($font_size);
							$tab->WriteLine("No Regular Jobs",'L',$font_size,$maxw);
						$tab->StopLine();
					}

					while($job = mysql_fetch_object($res_jobs)){
						$qry_reg = sprintf($qry,$sel_rd," AND is_regular='Y' AND job.job_no='$job->job_no'",$group);
						//echo nl2br($qry_reg);die();
						$data = $tab->LoadData($qry_reg);
						
						if(count($data)>0){
							$tab->WriteTable($header,$data,$width,4,1);			
							if($show_rd_details){
								$tab->StartLine($font_size);
									$tab->WriteLine("",'R',5,$width_emtpy);
									$tab->WriteLine("Total:",'R',5,$width_tot_f);
									$tab->WriteLine($tab->getSum("Qty",0),'R',5,$width["Qty"]);
									//$tab->WriteLine($tab->getSum("Qty 2",0),'R',5,$width["Qty 2"]);
								$tab->StopLine();
							}
							$tab->collFieldVal["Qty"] = array();
							//$tab->collFieldVal["Qty 2"] = array();
						}
					}
					
					$first_job=false;

					$tab->StartLine($font_size);
						$tab->WriteLine("* D/date as DD-MM",'L',$font_size,$maxw);
					$tab->StopLine();
					
					$tab->MultiCell($maxw,2.5,$comment2,false,'L');
					
					$header=array('Job #','Type','Circular','Client','D/Date','RD','Version','Qty','Weight','Date Recd.');
					$width=array('Job #'=>15,'Type'=>20,'Circular'=>45,'Client'=>40,'D/Date'=>12,'RD'=>40,'Version'=>30,'Qty'=>20,'Weight'=>12,'Date Recd.'=>20);
					$maxw=get_maxw($width);
					$width_emtpy = $maxw-$width["Qty"]-$width["RD"]-$width["Weight"]-$width["Date Recd."];
					$width_tot_f = $width["RD"];
					
					$res_jobs = query($qry_jobs);
					
					$tab->StartLine($font_size);
						$tab->WriteLine("Casual Jobs",'L',$font_size,$maxw);
					$tab->StopLine();

					
					$tab->WriteHeader($header,$width);
					if(mysql_num_rows($res_jobs)==0){
						$tab->StartLine($font_size);
							$tab->WriteLine("No Casual Jobs",'L',$font_size,$maxw);
						$tab->StopLine();
					}
					while($job = mysql_fetch_object($res_jobs)){
						$qry_cas = sprintf($qry,' route.code AS RD, '," AND job.job_no='$job->job_no' AND is_regular<>'Y' ","GROUP BY job.job_no,job_route.route_id,IF(job_route.dest_type='bundles',1,0)");
						$data = $tab->LoadData($qry_cas);
						
						if(count($data)>0){
							$tab->WriteTable($header,$data,$width,4,false);	//exit;		
							$tab->StartLine($font_size);
								$tab->WriteLine("",'R',5,$width_emtpy);
								$tab->WriteLine("Total:",'R',5,$width_tot_f);
								$tab->WriteLine($tab->getSum("Qty",0),'R',5,$width["Qty"]);
								//$tab->WriteLine($tab->getSum("Qty 2",0),'R',5,$width["Qty 2"]);
								$tab->Cell($width["Weight"],5,'',1,0,'L',1);
								$tab->Cell($width["Date Recd."],5,'',1,0,'L',1);
							$tab->StopLine();
							$tab->StopLine();
							$tab->collFieldVal["Qty"] = array();
						}
					}
					
					$first_job=false;

					$tab->SetFontSize(9);
					$blurb = "THIS SHOULD BE USED FOR DELIVERY CONFIRMATIONS - 'GOLDS'. PLEASE FAX TO 06 356 6618 EACH WEEK.\n";
					$blurb.= "Please enter date received in the box alongside the total drop (above) and note any shortages or other comments below.";
					$blurb.="\n\n";
					$blurb2 = "The above deliveries have been carried out in accordance with instructions.\n\n";
					$blurb2.= "Signed:______________________  Date: _______________________\n\n\n";
					$y = $tab->GetY();
					$tab->MultiCell($maxw,4,$blurb,1,'L');
					$tab->SetXY($maxw-110,$y);
			
					//$blurb2 = "The above deliveries have been carried out in accordance with instructions.\n\n";
					//$blurb2.= "Signed:______________________  Date: _______________________\n\n\n";
					//$blurb2.="\n\n\n\n";
					
					//$tab->MultiCell($maxw-154,3,$blurb2,1,'L');
					$tab->Ln();
					$tab->SetFontSize($font_size);
					
					if($num_jobs>0){
						if($do->mail_type<>'m'){
						
							$fn = clean_file_name("do_delivery_instructions_".$do->name.".pdf");
							
							$tab->Output($dirp.'/'.$fn);
							
							echo "($count) Delivery instructions for <strong>$do->company</strong> created.<br />";
							//fwrite($fp,"($count) Delivery instructions for <strong>$do->company</strong> created.\n");
							if(!$pdf_only){
                                $threads[$fn] = new mailThread("COURAL DELIVERY INSTRUCTIONS",$dirp,$fn,$do->dropoff_id,$receiver);
                                $threads[$fn]->start(); 
                            }
							$pdffiles[] = $dirp.'/'.$fn;
						}
						else{
							$fn = clean_file_name("do_MAIL_delivery_instructions_".$do->company.".pdf");
			

							$tab->Output($dirp.'/'.$fn);
							
							echo "($count) Delivery instructions for <strong>$do->company (MAIL)</strong> created.<br />";
							if(!$pdf_only){
                                $threads[$fn] = new mailThread("COURAL DELIVERY INSTRUCTIONS",$dirp,$fn,$do->dropoff_id,$mail_receiver);
                                $threads[$fn]->start();
                            }
						}
					}
				}//while do

			/////////////////////////////////////////////////////////
            // CONTRACTOR SEND OUT
            /////////////////////////////////////////////////////////
			if(1){
            $qry_dos = "SELECT DISTINCT CONCAT(name,'_',first_name) AS name,
                                company,
                                contractor_id,
                                mail_type
                         FROM job
                         LEFT JOIN job_route
                         ON job.job_id=job_route.job_id
                         LEFT JOIN route
                         ON route.route_id=job_route.route_id
                         LEFT JOIN operator
                         ON operator.operator_id=contractor_id
                         LEFT JOIN address
                         ON address.operator_id=operator.operator_id
                         WHERE job_route.dist_id='$dist_id'
                            $where_add
                        ORDER BY company";
            //echo nl2br($qry_dos);
            //die();
			$res_dos = query($qry_dos,0);

                echo "Number of CONTRs:".mysql_num_rows($res_dos)."<br />";
                //fwrite($fp,"Number of DOs:".mysql_num_rows($res_dos)."\n");
                $count=0;
                while($do=mysql_fetch_object($res_dos)){
                    $parcel_send_di = get("operator", "parcel_send_di", "WHERE operator_id={$do->contractor_id}");
                    if($parcel_send_di != 'Y'){
                        continue;
                    } 
                    $count++;
                    echo "($count) Preparing delivery instructions for <strong>$do->company</strong>.<br />";
                    //fwrite($fp,"($count) Preparing delivery instructions for <strong>$do->company</strong>.\n");
                    $qry_jobs = "SELECT DISTINCT job_no,is_regular
                                 FROM job
                                 LEFT JOIN job_route
                                 ON job.job_id=job_route.job_id
                                 LEFT JOIN route
                                 ON route.route_id=job_route.route_id
                                 WHERE job_route.dist_id='$dist_id'

                                    AND job_route.contractor_id='$do->contractor_id'

                                    $where_add
                                ORDER BY is_regular DESC,job_no,job_no_add";
                    //echo nl2br($qry_jobs); die();
                    $res_jobs = query($qry_jobs);
				    $tab  = new MySQLPDFTable($MYSQL,'l');
                    $tab->SetTopMargin(5);
                    $tab->AliasNbPages();
                    $tab->fontSize = $font_size;
                    //$tab->norepField["Type"]=true;
                    $tab->norepField["Circular"]=true;
                    $tab->norepField["Client"]=true;

                    $tab->SetFont('Helvetica','B',$font_size);

                    $tab->collField["Qty"]=true;
                    $tab->collField["Tot"]=true;

                    $tab->AddPage();

                    $title = "Distributor: $dist->company";
                    $tab->StartLine(8);
                        $tab->WriteLine($title,'L',6,$maxw-20);
                        $tab->WriteLine('Contractor '.$tab->PageNo().'/{nb}','R',6,30);
                    $tab->StopLine();

                    if($is_job_report)
                        $title = "Drop off details and delivery report for $do->company.";
                    else
                        $title = "Drop off details and delivery report for $do->company. Date range: $date_show_start to $date_show_end.";


                    $tab->StartLine(8);
                        $tab->WriteLine($title,'L',8,$maxw);
                    $tab->StopLine();

                    if($show_rd_details){
                        $group = "GROUP BY job_no,job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
                        $sel_rd = "route.code AS 'RD',";
                        $num_blank_cols = 5;

                        $header=array('Job #','Type','Circular','Client','D/Date','RD','Version', 'Qty','Weight','Date Recd.');
                        $width=array('Job #'=>15,'Type'=>20,'Circular'=>55,'Client'=>40,'D/Date'=>12,'RD'=>40,'Version'=>20,'Qty'=>20,'Weight'=>12,'Date Deliv.'=>20);
                        $maxw=get_maxw($width);
                        $width_emtpy = $maxw-$width["Qty"]-$width["RD"];
                        $width_tot_f = $width["RD"];
                    }
                    else{
                        $group = "GROUP BY job.job_no,job_route.contractor_id,IF(job_route.dest_type='bundles',1,0)";
                        $num_blank_cols = 5;

                        $header=array('Job #','Type','Circular','Client','D/Date','Version','Qty','Weight','Date Recd.');
                        $width=array('Job #'=>20,'Type'=>20,'Circular'=>60,'Client'=>45,'D/Date'=>20,'Version'=>20,'Qty'=>20,'Weight'=>15,'Date Recd.'=>35);
                        $maxw=get_maxw($width);
                        $width_emtpy = $maxw-$width["Qty"]-$width["D/Date"];
                        $width_tot_f = $width["D/Date"];
                    }

                    $tab->StartLine($font_size);
                        $tab->WriteLine("Regular Jobs",'L',$font_size,$maxw);
                    $tab->StopLine();

					                   $qry = "SELECT  job.job_id      AS job_id,
                            CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
                            GROUP_CONCAT(DISTINCT  IF(is_att<>'Y',
                               CASE job_route.dest_type
                                    WHEN 'num_lifestyle' THEN 'L/Style'
                                    WHEN 'num_farmers' THEN 'Farmer'
                                    WHEN 'num_dairies' THEN 'Dairy'
                                    WHEN 'num_sheep' THEN 'Sheep'
                                    WHEN 'num_beef' THEN 'Beef'
                                    WHEN 'num_sheepbeef' THEN 'S/Beef'
                                    WHEN 'num_dairybeef' THEN 'D/Beef'
                                    WHEN 'num_hort' THEN 'Hort'
                                    WHEN 'num_total' THEN 'Total'
                                    WHEN 'num_nzfw' THEN 'F@90%%'
                                    WHEN 'bundles' THEN 'Bundles'
                               END
                               , '') SEPARATOR '') AS 'Type',

                            CASE job.publication
                                WHEN LENGTH(job.publication)<=20 THEN
                                    CONCAT(LEFT(job.publication,17),'...')
                                ELSE CONCAT(job.publication)
                            END
                                        AS Circular,
                            CASE client.name
                                WHEN LENGTH(client.name)<=20 THEN
                                    CONCAT(LEFT(client.name,17),'...')
                                ELSE
                                    client.name
                            END
                                            AS Client,
                            %s
                            IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%%m-%%d'))
                                AS 'D/Date',
                            job_route.version AS Version,
                            SUM(IF(is_att<>'Y',job_route.amount,0))     AS 'Qty'

                    FROM job
                    LEFT JOIN client
                    ON client.client_id=job.client_id
                    LEFT JOIN job_route
                    ON job.job_id=job_route.job_id
                    LEFT JOIN route
                    ON route.route_id=job_route.route_id
                    WHERE contractor_id='$do->contractor_id'
                        AND job_route.dist_id=$dist_id
                        AND job.cancelled<>'Y'
                        $where_add
                        %s
                    %s
                    ORDER BY job.is_regular DESC, job.job_no";

                    $tab->WriteHeader($header,$width);

                    if(mysql_num_rows($res_jobs)==0){
                        $tab->StartLine($font_size);
                            $tab->WriteLine("No Regular Jobs",'L',$font_size,$maxw);
                        $tab->StopLine();
                    }

                    while($job = mysql_fetch_object($res_jobs)){
                        $qry_reg = sprintf($qry,$sel_rd," AND is_regular='Y' AND job.job_no='$job->job_no'",$group);
                        //echo nl2br($qry_reg);die();
                        $data = $tab->LoadData($qry_reg);
                        if(count($data)>0){
                            $tab->WriteTable($header,$data,$width,4,1);
                            if($show_rd_details){
                                $tab->StartLine($font_size);
                                    $tab->WriteLine("",'R',5,$width_emtpy);
                                    $tab->WriteLine("Total:",'R',5,$width_tot_f);
                                    $tab->WriteLine($tab->getSum("Qty",0),'R',5,$width["Qty"]);
                                    //$tab->WriteLine($tab->getSum("Qty 2",0),'R',5,$width["Qty 2"]);
                                $tab->StopLine();
                            }
                            $tab->collFieldVal["Qty"] = array();
                            //$tab->collFieldVal["Qty 2"] = array();
                        }
                    }

                    $first_job=false;
                    $tab->StartLine($font_size);
                        $tab->WriteLine("* D/date as DD-MM",'L',$font_size,$maxw);
                    $tab->StopLine();

                    $tab->MultiCell($maxw,2.5,$comment2,false,'L');

                    $header=array('Job #','Type','Circular','Client','D/Date','RD','Version','Qty','Weight','Date Recd.');
                    $width=array('Job #'=>15,'Type'=>20,'Circular'=>45,'Client'=>40,'D/Date'=>12,'RD'=>40,'Version'=>30,'Qty'=>20,'Weight'=>12,'Date Recd.'=>20);
                    $maxw=get_maxw($width);
                    $width_emtpy = $maxw-$width["Qty"]-$width["RD"]-$width["Weight"]-$width["Date Recd."];
                    $width_tot_f = $width["RD"];

                    $res_jobs = query($qry_jobs);

                    $tab->StartLine($font_size);
                        $tab->WriteLine("Casual Jobs",'L',$font_size,$maxw);
                    $tab->StopLine();


                    $tab->WriteHeader($header,$width);
                    if(mysql_num_rows($res_jobs)==0){
                        $tab->StartLine($font_size);
                            $tab->WriteLine("No Casual Jobs",'L',$font_size,$maxw);
                        $tab->StopLine();
                    }
                    while($job = mysql_fetch_object($res_jobs)){
                        $qry_cas = sprintf($qry,' route.code AS RD, '," AND job.job_no='$job->job_no' AND is_regular<>'Y' ","GROUP BY job.job_no,job_route.route_id,IF(job_route.dest_type='bundles',1,0)");
                        $data = $tab->LoadData($qry_cas);

                        if(count($data)>0){
                            $tab->WriteTable($header,$data,$width,4,false); //exit;
                            $tab->StartLine($font_size);
                                $tab->WriteLine("",'R',5,$width_emtpy);
                                $tab->WriteLine("Total:",'R',5,$width_tot_f);
                                $tab->WriteLine($tab->getSum("Qty",0),'R',5,$width["Qty"]);
                                //$tab->WriteLine($tab->getSum("Qty 2",0),'R',5,$width["Qty 2"]);
                                $tab->Cell($width["Weight"],5,'',1,0,'L',1);
                                $tab->Cell($width["Date Recd."],5,'',1,0,'L',1);
                            $tab->StopLine();
                            $tab->StopLine();
                            $tab->collFieldVal["Qty"] = array();
                            //$tab->collFieldVal["Qty 2"] = array();
                        }
                    }



                    $first_job=false;
                    $tab->SetFontSize(9);
                    $blurb = "THIS SHOULD BE USED FOR DELIVERY CONFIRMATIONS - 'GOLDS'. PLEASE FAX TO 06 356 6618 EACH WEEK.\n";
                    $blurb.= "Please enter date received in the box alongside the total drop (above) and note any shortages or other comments below.";
                    $blurb.="\n\n";
                    $blurb2 = "The above deliveries have been carried out in accordance with instructions.\n\n";
                    $blurb2.= "Signed:______________________  Date: _______________________\n\n\n";
                    $y = $tab->GetY();
                    $tab->MultiCell($maxw,4,$blurb,1,'L');
                    $tab->SetXY($maxw-110,$y);

                    $tab->Ln();
                    $tab->SetFontSize($font_size);

                    if($num_jobs>0){
                        if($do->mail_type<>'m'){

                            $fn = clean_file_name("contr_delivery_instructions_".$do->name.".pdf");

                            $tab->Output($dirp.'/'.$fn);

                            echo "($count) Delivery instructions for <strong>$do->company</strong> created.<br />";
                            //fwrite($fp,"($count) Delivery instructions for <strong>$do->company</strong> created.\n");
                            if(!$pdf_only){
                                $threads[$fn] = new mailThread("COURAL DELIVERY INSTRUCTIONS",$dirp,$fn,$do->contractor_id,$receiver);
                                $threads[$fn]->start();
                            }
                            $pdffiles[] = $dirp.'/'.$fn;
                            //pdf_merge($now,$fn);

                        }
                        else{
                            $fn = clean_file_name("contr_MAIL_delivery_instructions_".$do->company.".pdf");

                            $tab->Output($dirp.'/'.$fn);

                            echo "($count) Delivery instructions for <strong>$do->company (MAIL)</strong> created.<br />";
                            //fwrite($fp,"($count) Delivery instructions for <strong>$do->company</strong> created.\n");
                            if(!$pdf_only){
                                $threads[$fn] = new mailThread("COURAL DELIVERY INSTRUCTIONS",$dirp,$fn,$do->dropoff_id,$mail_receiver);
                                $threads[$fn]->start();
                            }
                        }
                    }
                }//while contr
										
			} // if include_contr;
		}//while($dist = mysql_fetch_object($res_dist))					
		
		$job_str = "-1";
		foreach($jobs as $j){
			$job_str .=  ",".$j;
		}

		
		$qry = "UPDATE job SET is_deliv_sent=1 WHERE job_id IN($job_str)";
		query($qry);
		
		$jobs = serialize($jobs);
		
		if($company=="All") $dist_id=-1;
		if($check){
			$insert_add = "jobs = '$jobs'";
		}
		else{
			$insert_add = "start_date = '$date_start',
							final_date = '$date_final',";
		}
		
		if($origin=="job_delivery") $report = "job_delivery_send_out";
		
		
		
    	$qry = "INSERT INTO send_report 
				SET comment='$comment2',
				dist_id='$dist_id',
				
				show_regular = '$show_regular',
				show_casual = '$show_casual',
				`type`='$report'";
		query($qry);
		
		echo "<font color='red'>Sending delivery instructions finished.</font><br />";
	}//if($year && $week && $distributor)
}



if($report=="month_job"){
	if($month && $year){
		$month_show = date('F',mktime(0,0,0,$month,1,$year));
		$title = "Monthly Job Report for $month_show/$year";
?>
		<div class="weekly_head">
			<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
			<h3 class="weekly_head_h2"><?=$title?></h3>
		</div>				
			
<?				
		$qry = "SELECT job.job_id 			  AS 'Record',
					   job.job_no             AS 'Job #',
					   job.delivery_date  	  AS 'Delivery Date',
					   client.name            AS 'Client',
					   job.publication 		  AS 'Publication',
							CASE job.dest_type
								WHEN 'num_lifestyle' THEN 'L/Style'
								WHEN 'num_farmers' THEN 'Farmer'
								WHEN 'num_dairies' THEN 'Dairy'
								WHEN 'num_sheep' THEN 'Sheep'
								WHEN 'num_beef' THEN 'Beef'
								WHEN 'num_sheepbeef' THEN 'S/Beef'
								WHEN 'num_dairybeef' THEN 'D/Beef'
								WHEN 'num_hort' THEN 'Hort'
								WHEN 'num_total' THEN 'Total'
								WHEN 'num_nzfw' THEN 'F@90%%'	
							END AS 'Farmer Type',
					   #job.cancelled          AS 'Cancelled',
					   #job.finished	          AS 'Closed',
                        job.print_advices      AS 'Print Adv.',     
                       IF(job.inc_linehaul ='','N',job.inc_linehaul)       AS 'Inc. Linehaul',     
                       job.rate_bbc           AS 'Rate (extra)',       
                       job.qty_bbc            AS 'Qty (extra)',        
                       job.premium_sell       AS 'Premium Cust.',      
                       job.premium            AS 'Premium Cost.',      
                       job.folding_fee        AS 'Folding Fee',        
                       job.add_folding_to_invoice AS 'Add to Inv.', 
					   SUM(IF(job_route.dest_type <>'bundles',job_route.amount,0))  AS 'Quantity' ,
					   SUM(IF(job_route.dest_type ='bundles',job_route.amount,0))  AS 'Bundles'
				FROM job
				LEFT JOIN job_route
				ON job.job_id=job_route.job_id
				LEFT JOIN client
				ON client.client_id=job.client_id
				LEFT JOIN route
				ON route.route_id=job_route.route_id
				WHERE month(job.delivery_date)='$month' 
					AND year(job.delivery_date)='$year'		
					AND job.cancelled<>'Y'
					AND job_route.dist_id NOT IN (812,590)							
				GROUP BY job.job_id
				ORDER BY job.job_no,job.delivery_date,client.name,job.publication";
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->showRec=0;
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->startTable();
		$tab->writeTable();
		$tab->startNewLine();
			$tab->addLines("",12);
			$tab->addLine("Total");
			$qry = "SELECT SUM(amount) AS amt
					FROM job_route 
					LEFT JOIN job 
					ON job.job_id=job_route.job_id 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE month(job.delivery_date)='$month' 
						AND year(job.delivery_date)='$year'	
						AND job_route.dest_type <> 'bundles'		
						#AND job.finished<>'Y'
						AND job.cancelled<>'Y'
						AND job_route.dist_id NOT IN (812,590)											
					GROUP BY IF(month(job.delivery_date)='$month' 
						AND year(job.delivery_date)='$year',1,0)";
			$res = query($qry);
			$sum = mysql_fetch_object($res);
			$tab->addLine($sum->amt);
			mysql_free_result($res);
			$qry = "SELECT SUM(amount) AS amt
					FROM job_route 	
					LEFT JOIN job 
					ON job.job_id=job_route.job_id 
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					WHERE month(job.delivery_date)='$month' 
						AND year(job.delivery_date)='$year'	
						AND job_route.dest_type = 'bundles'
						AND job.cancelled<>'Y'
						AND job_route.dist_id NOT IN (812,590)							
					GROUP BY IF(month(job.delivery_date)='$month' 
						AND year(job.delivery_date)='$year',1,0)";
			$res = query($qry);
			$sum = mysql_fetch_object($res);
			$tab->addLine($sum->amt);
		$tab->stopNewLine();		
		$tab->stopTable();
	}//$if($month && $year)
}

if($report=="label_eight"){
	write_labels_eight($dist, $is_current, $is_shareholder, $op_type, $format,$margin_top,$margin_bottom,$margin_left,$cell_height,$cell_width,$num_vert,$num_hor,$space_vert,$space_hor);
}

if($report=="label"){
	if($island == "NI"){
		$where_add = " AND island='NI'";
	}
	else if($island == "SI"){
		$where_add = " AND island='SI'";
	}
	else{
		$where_add = " ";
	}
	$qry = "SELECT IF(operator.alias IS NOT NULL AND operator.alias<>'',operator.alias,address.name) AS Name,
				   IF(operator.alias IS NOT NULL AND operator.alias<>'','',address.first_name) AS FName,
				   operator.do_address    AS Address,
				   operator.deliv_notes    AS Notes,
				   operator.do_city       AS City,
				   address.postcode   AS Postcode,
				   route.area         AS Area,
				   job.job_no         AS Job,
				   job.comments 	  AS Comments,
                   job.show_comments  AS ShowNotes,
				   CASE job_route.dest_type
						WHEN 'num_lifestyle' THEN 'L/Style'
						WHEN 'num_farmers' THEN 'Farmer'
						WHEN 'num_dairies' THEN 'Dairy'
						WHEN 'num_sheep' THEN 'Sheep'
						WHEN 'num_beef' THEN 'Beef'
						WHEN 'num_sheepbeef' THEN 'S/Beef'
						WHEN 'num_dairybeef' THEN 'D/Beef'
						WHEN 'num_hort' THEN 'Hort'
						WHEN 'num_total' THEN 'Total'
						WHEN 'num_nzfw' THEN 'F@90%%'
						WHEN 'bundles' THEN 'Bundles'
				   END 
				   	AS DType,
				   IF(is_ioa='Y','IOA',job.delivery_date)  AS Date,
				   SUM(job.weight*job_route.amount)		  AS Weight,
				   SUM(job_route.amount)	AS Amt,
				   SUM(job_route.amount)  	AS Quantity,
				   SUM(att.amount)        	AS AttQuantity,
				   job_route.version      	AS Version,
				   job.publication   AS Publication,
				   client.name   AS Client,
				   island AS island
			FROM job
			LEFT JOIN job_route
			ON job.job_id=job_route.job_id
			LEFT JOIN job_route AS att
			ON job.alt_job_id=att.job_id
				AND job_route.route_id=att.route_id
			LEFT JOIN route
			ON job_route.route_id=route.route_id
			LEFT JOIN client
			ON job.client_id=client.client_id
			LEFT JOIN operator
			ON operator.operator_id=job_route.doff
			LEFT JOIN address
			ON address.operator_id=operator.operator_id
			
			WHERE job.job_id='$job_id'
				AND is_hidden<>'Y'
				$where_add
			GROUP BY route.region,job_route.doff,job_route.dest_type
			ORDER BY island,route.seq_region,route.seq_area,route.seq_code,address.name";
	//echo nl2br($qry);
	$res = query($qry );
	
	if(mysql_num_rows($res)>0){
		if($export){
			$tab = MySQLExport("export.html",$qry);
			$tab->startTable();
			$tab->writeTable();
			$tab->stopTable();
		}
		else{
			
			if($kg_per_lab){
				$label = array();
				
				while($label=mysql_fetch_object	($res)){
					
					$weight_tot=$label->Weight/1000;
					$num_labs = ceil($weight_tot/$kg_per_lab);
					
					if($a4_only) $num_labs=1;
					for($i=0;$i<$num_labs;$i++){
						$lab["FName"] = $label->FName;
						$lab["Name"] = $label->Name;
						$lab["Address"] = $label->Address;
                        $lab["ShowNotes"] = $label->ShowNotes;
						$lab["Notes"] = $label->Notes;
						$lab["City"] = $label->City;
						$lab["Comments"] = $label->Comments;
						$lab["Postcode"] = $label->Postcode;
						$lab["Area"] = $label->Area;
						$lab["Job"] = $label->Job;
						$lab["Version"] = $label->Version;
						$lab["DType"] = $label->DType;
						$lab["Date"] = $label->Date;
						$lab["Quantity"] = $label->Quantity+$label->AttQuantity;
						$lab["Client"] = $label->Client;
						$lab["Publication"] = $label->Publication;
						$lab["QtyPerBundle"] = $qty_per_bund;
						$lab["QtyBundle"] = floor($lab["Quantity"]/$qty_per_bund);
						$lab["SQuantity"] = $lab["Quantity"] - $lab["QtyBundle"] * $qty_per_bund;
						$labels[] = $lab;
					}
				}
				$count=0;
				$start=true;
				
				if($a4_only){
					if($labels){
						foreach($labels as $lab){
							write_label_a4($lab,$count,$space_hor,$space_vert,$qty_per_do);
							$count++;
						}
					}
				}
				else{
			?>
					<table width="100%" cellspacing="0" border="0"  cellpadding="0">
						<tr>
			<?		
					
					//$res = query($qry);
					
					if($labels){
						foreach($labels as $lab){
							if(fmod($count,8)==0 && $start==false){
			?>
									</tr>
								</table>
								<div class="pagebreak_after"></div>	
								<span class="page_border"><hr /></span>
								<table width="100%" cellspacing="0" border="0"  cellpadding="0">
									<tr>
			<?				
							}
							$start=false;
							write_label($lab,$count,$space_hor,$space_vert);
							$count++;
						}
					}
					else{
	?>
						<td>ERROR: Can't print labels. The reason could be that the weight is not specified or the job does have no routes attached to it.</td>
	<?					
					}
			?>			
								</tr>
							</table>
					
		<?		
				}	
			}
		}
	}
}

if($report=="linehaul"){
	
	if($comment2){
		$comment2 = nl2br($comment2);
		$qry = "UPDATE last_print_comment SET comment2='$comment2' WHERE last_print_comment_id=1";
		query($qry);
	}
	
	if($date_start&&$date_final)
	{
		$date_show_start 	= date("jS M Y",strtotime($date_start));
		$date_show_end 		= date("jS M Y",strtotime($date_final));
		
		if($name!='All') $where_add = " AND client_id='$name'";
		
		$qry_dist = "SELECT DISTINCT client_id,name FROM client WHERE is_linehaul='1' $where_add";
		$res_dist = query($qry_dist);
		
		$num_dist = mysql_num_rows($res_dist);
		$count=0;
		
		while($haul = mysql_fetch_object($res_dist))
		{
			$start=true;
			$start_cas=true;
			$haul_id=get("client","client_id","WHERE name='$haul->name'");
			
			$where_add_client = "";
			if($client_id!='All') $where_add_client = " AND client_id='$client_id'";
			
			$qry_jobs = "SELECT DISTINCT job_no, is_regular, hauler_ni_id, hauler_si_id,
							IF(is_ioa='Y', 1990-01-01, delivery_date) AS st
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 WHERE delivery_date>='$date_start'
						 	AND delivery_date<='$date_final'
							$where_add_client
							AND (job.hauler_ni_id='$haul_id'
							OR job.hauler_si_id='$haul_id')
						ORDER BY st, job_no";
			//echo nl2br($qry_jobs);exit;
			$res_jobs = query($qry_jobs);
			
			//echo mysql_num_rows($res_jobs);
			
			?>
				<div class="weekly_head">		
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
					<h3>Summary Linehaul Period from: <?=$date_show_start?> to: <?=$date_show_end?><br />
					Linehauler: <?=$haul->name?></h3>						
				</div>								
			<?								
						
			
			//echo $show_regular;
			//echo $show_casual;
			if(!$show_casual && !$show_regular)
			{
				$show_regular = true;
			}
			
			if($show_regular)
			{
				$tab  = new MySQLTable("reports.php","");
				$tab->showRec=0;
				$tab->noRepFields["Circular"] = 1;
				$tab->noRepFields["Job #"] = 1;
				$tab->noRepFields["Delivery Date"] = 1;
				
				$tab->collField["Total"]=true;
				
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->setHiddenField("type");
				$tab->startTable();
				$first_job=true;
				
				$tot_sum_qty = 0;
				$tot_sum_bund = 0;
				$tot_sum_total = 0;
				
				while($job=mysql_fetch_object($res_jobs))
				{
					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$group = "GROUP BY job_route.route_id";					
						$num_blank_cols = 2;
					}
					else{
						$group = "GROUP BY job.job_no";
						$num_blank_cols = 2;
					}
					if($haul_id == '206')
						$island_select = " AND route.island='NI'";
					elseif($haul_id == '162')
						$island_select = " AND route.island='SI'";
					
					$qry = "SELECT 	job.job_id 				AS Record,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							CONCAT('#',job.pmp_job_no)         AS 'PMP Job #',
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN 
									CONCAT(LEFT(job.publication,17),'...',client.name)
								ELSE 
									CONCAT(job.publication,client.name)
							END
											AS Circular,
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
								AS 'D/Date',
							ROUND(job.weight,0) AS 'Weight (if known)',
							SUM(IF(route.region<>'PMP',job_route.amount,0)) AS 'Rural',
							SUM(IF(route.region='PMP',job_route.amount,0)) AS 'PO Boxes',
							SUM(job_route.amount) AS 'Total'
							
							FROM job
							LEFT JOIN client
							ON client.client_id=job.client_id
							LEFT JOIN job_route
							ON job.job_id=job_route.job_id
							LEFT JOIN route
								ON route.route_id=job_route.route_id
							
							WHERE job.job_no='$job->job_no'		
								$island_select
								AND job.cancelled<>'Y'
								AND job.is_regular='Y'
							GROUP BY 'Job #'
							ORDER BY delivery_date DESC, job.job_no";
						
						$res_sum = query($qry);
						$line=mysql_fetch_object($res_sum);
						//echo print_r($line); exit;
						if($line->Record)
						{
				
							$tab->writeSQLTableElement($qry,$first_job);
							$first_job=false;
						}
						
						
							
						
				}//while job
				
				$first_job=true;
				
				
			
			} // if regular
		
				
		if($show_casual)
		{
			$tab  = new MySQLTable("reports.php","");
			$tab->showRec=0;
			$tab->noRepFields["Circular"] = 1;
			$tab->noRepFields["Job #"] = 1;
			$tab->noRepFields["Delivery Date"] = 1;
			
			$tab->collField["Total"]=true;
			
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->setHiddenField("type");
			$tab->startTable();
			$first_job=true;
			
			$tot_sum_qty = 0;
			$tot_sum_bund = 0;
			$tot_sum_total = 0;
			
			mysql_data_seek($res_jobs, 0);
			
			while($job=mysql_fetch_object($res_jobs))
			{
				if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
					$group = "GROUP BY job_route.route_id";					
					$num_blank_cols = 2;
				}
				else{
					$group = "GROUP BY job.job_no";
					$num_blank_cols = 2;
				}
				if($haul_id == '206')
					$island_select = " AND route.island='NI'";
				else if($haul_id == '162')
					$island_select = " AND route.island='SI'";
				
				$qry = "SELECT 	job.job_id 				AS Record,
						CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
						CONCAT('#',job.pmp_job_no)         AS 'PMP Job #',
						CASE job.publication
							WHEN LENGTH(job.publication)<=20 THEN CONCAT(LEFT(job.publication,17),'...')
							ELSE job.publication
						END
										AS Circular,
						IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
							AS 'D/Date',
						ROUND(job.weight,0) AS 'Weight (if known)',
						SUM(IF(route.region<>'PMP',job_route.amount,0)) AS 'Rural',
						SUM(IF(route.region='PMP',job_route.amount,0)) AS 'PO Boxes',
						SUM(job_route.amount) AS 'Total'
						
						FROM job
						LEFT JOIN job_route
						ON job.job_id=job_route.job_id
						LEFT JOIN route
							ON route.route_id=job_route.route_id
						
						WHERE job.job_no='$job->job_no'		
							$island_select
							AND job.cancelled<>'Y'
							AND job.is_regular<>'Y'
						GROUP BY 'Job #'
						ORDER BY job.job_no";
				
						$res_sum = query($qry);
						$line=mysql_fetch_object($res_sum);
						//echo print_r($line); exit;
						if($line->Record)
						{
						$tab->writeSQLTableElement($qry,$first_job);
						
						$first_job=false;
						}
						
					
				}//while job
				
				$first_job=true;
				
				
			
			} // if regular
			
			$tab->startNewLine();
				$tab->addLineWithStyle($comment2,"sql_comment_line",$num_blank_cols+3);
			$tab->stopNewLine();
			
			$tab->stopTable();
?>
			<div class="pagebreak_after">&nbsp;</div>
<?
						
		}
	}
	
}


if($report=="weekly"){
	

	if($comment2){
		$comment2 = nl2br($comment2);
		$qry = "UPDATE last_print_comment SET comment2='$comment2' WHERE last_print_comment_id=1";
		query($qry);
	}
	
	if($date_start&&$date_final){
		$date_show_start 	= date("jS M Y",strtotime($date_start));
		$date_show_end 		= date("jS M Y",strtotime($date_final));
		
		if($company!='All') $where_add = " AND operator_id='$company'";
		
		$qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y' $where_add";
		$res_dist = query($qry_dist);
		
		$num_dist = mysql_num_rows($res_dist);
		$count=0;
		while($dist = mysql_fetch_object($res_dist)){
			$start=true;
			$start_cas=true;
			$dist_id=get("operator","operator_id","WHERE company='$dist->company'");
			
			$where_add_contr = " ";
			if($sel_contr_only=='Y') $where_add_contr = " AND operator.parcel_send_di = 'Y' ";
			
			$qry_jobs = "SELECT DISTINCT job.job_id,job_no,is_regular 
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 WHERE delivery_date>='$date_start'
						 	AND delivery_date<='$date_final'
							AND job_route.dist_id='$dist_id'
						ORDER BY seq_region,seq_area,seq_code";
			//echo nl2br($qry_jobs);
			$res_jobs = query($qry_jobs);
			
			if(mysql_num_rows($res_jobs)>0){
		?>
					<div class="weekly_head">		
						<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
						<h3>Summary Delivery Instruction Period from: <?=$date_show_start?> to: <?=$date_show_end?><br />
						Distributor: <?=$dist->company?></h3>						
					</div>								
		<?								
			}			
			$show_regular=true;
			$show_casual=false;
			
			if($show_regular){
				$tab  = new MySQLTable("reports.php","");
				$tab->showRec=0;
				$tab->noRepFields["Circular"] = 1;
				$tab->noRepFields["Job #"] = 1;
				$tab->noRepFields["Delivery Date"] = 1;
				$tab->noRepFields["Type"] = 1;
				$tab->noRepFields["Dropoff"] = 1;
				
				$tab->collField["Qty"]=true;
				//$tab->collField["Qty 2"]=true;
				
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->setHiddenField("type");
				$tab->startTable();
				$first_job=true;
				
				$tot_sum_qty = 0;
				$tot_sum_bund = 0;
				$tot_sum_total = 0;
				
				/*if($show_rd_details){
					$group = "GROUP BY job.job_no,job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
					$sel_rd = "route.code AS 'RD',";					
					$num_blank_cols = 6;
				}
				else{
					$group = "GROUP BY job.job_no,job_route.dropoff_id,IF(job_route.dest_type='bundles',1,0)";
					$num_blank_cols = 5;
				}*/

				while($job=mysql_fetch_object($res_jobs)){
					//echo "Hello:".$job->is_regular;
					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$group = "GROUP BY job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
						$sel_rd = "route.code AS 'RD',";					
						$num_blank_cols = 7;
					}
					else{
						$group = "GROUP BY job.job_no,IF(job_route.dest_type='bundles',1,0)";
						$num_blank_cols = 7;
						$sel_rd = "'N/A' AS RD,";
					}
					$qry = "SELECT 	job.job_id 				AS Record,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							IF(job.is_regular='Y','Y','N')	 AS 'Is Regular',
							GROUP_CONCAT(DISTINCT IF(is_att<>'Y', 
								   CASE job_route.dest_type
										WHEN 'num_lifestyle' THEN 'L/Style'
										WHEN 'num_farmers' THEN 'Farmer'
										WHEN 'num_dairies' THEN 'Dairy'
										WHEN 'num_sheep' THEN 'Sheep'
										WHEN 'num_beef' THEN 'Beef'
										WHEN 'num_sheepbeef' THEN 'S/Beef'
										WHEN 'num_dairybeef' THEN 'D/Beef'
										WHEN 'num_hort' THEN 'Hort'
										WHEN 'num_total' THEN 'Total'
										WHEN 'num_nzfw' THEN 'F@90%'
										WHEN 'bundles' THEN 'Bundles'
								   END
								   , '') SEPARATOR '') AS 'Type',
								
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN CONCAT(LEFT(job.publication,17),'...')
								ELSE job.publication
							END
											AS Circular,
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
								AS 'D/Date',
							CASE operator.company
								WHEN LENGTH(operator.company) <= 20 THEN CONCAT(LEFT(operator.company,17),'... (',mail_type,')')
								ELSE CONCAT(operator.company,' (',mail_type,')')
							END
								AS Dropoff,
							$sel_rd
							SUM(IF(is_att<>'Y',job_route.amount,0)) 	AS 'Qty'
				
					FROM job
					LEFT JOIN job_route
					ON job.job_id=job_route.job_id
					LEFT JOIN route
						ON route.route_id=job_route.route_id
					LEFT JOIN operator
						ON job_route.dropoff_id=operator.operator_id
					LEFT JOIN address
						ON address.operator_id=operator.operator_id
					WHERE job.job_no='$job->job_no'					
						AND job_route.dist_id=$dist_id
						AND job.cancelled<>'Y'
					$group
					ORDER BY seq_region,seq_area,seq_code,job.job_no,operator.company";

						//echo nl2br($qry);
						//$res_query = query($qry);
						$qry_sum = "SELECT 	SUM(IF(is_att<>'Y',job_route.amount,0)) 	AS tot_qty1,
											SUM(IF(is_att='Y',job_route.amount,0)) 	AS tot_qty2
											#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS tot_bund
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								LEFT JOIN operator
								ON job_route.contractor_id=operator.operator_id
								WHERE job.job_id='$job->job_id'					
									AND job_route.dist_id=$dist_id				
								GROUP BY job_route.dist_id";
						//echo nl2br($qry);
						$res_sum = query($qry_sum);
						if(mysql_num_rows($res_sum)>0){
							$has_reg=true;
							if($start){
								$start=false;
	/*?>
								<div class="weekly_head">
									<h3>Regular Jobs</h3>
								</div>								
	<?			*/								
							}
						
							$sum = mysql_fetch_object($res_sum);	
							//$tab->startNewLine();
								//$tab->addLineWithStyle("Job #:".$job->job_no,"sql_extra_head_big");
							//$tab->stopNewLine();					
							$tab->writeSQLTableElement($qry,$first_job);
							$tab->startNewLine();
								$tab->addLines("",$num_blank_cols-1);
								$tab->addLine("Total:");
								$tab->addLine($sum->tot_qty1);
								//$tab->addLine($sum->tot_qty2);
								//$tab->addLine($sum->tot_bund);
								//$tab->addLine($sum->total);
							$tab->stopNewLine();
							$tot_sum_qty += $sum->tot_qty;
							$tot_sum_bund += $sum->tot_bund;
							$tot_sum_total += $sum->total;
							$first_job=false;
						}
					}//while job
					
					$first_job=true;
					/*$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total:");
						$tab->addLine($tot_sum_qty);
						//$tab->addLine($tot_sum_bund);
						//$tab->addLine($tot_sum_total);
					$tab->stopNewLine();*/
					
					$tab->startNewLine();
						$tab->addLineWithStyle($comment2,"sql_comment_line",$num_blank_cols+2);
					$tab->stopNewLine();
					
					$tab->stopTable();	
				
				} // if regular
				
				//////////////////////////////// Casual Jobs /////////////////////////////////
				if($show_casual){
					$tab  = new MySQLTable("reports.php","");
					$tab->showRec=0;
					$tab->hasEditButton=false;
					$tab->hasDeleteButton=false;
					$tab->hasAddButton=false;
					$tab->hasForm = false;
					$tab->noRepFields["Circular"] = 1;
					$tab->noRepFields["Job #"] = 1;
					$tab->noRepFields["Delivery Date"] = 1;
					$tab->noRepFields["Type"] = 1;
					$tab->noRepFields["Dropoff"] = 1;
					$tab->setHiddenField("type");
					$tab->startTable();
					$first_job=true;
					
					$tot_sum_qty = 0;
					$tot_sum_bund = 0;
					$tot_sum_total = 0;
					$start=true;
					$res_jobs = query($qry_jobs);
					while($job=mysql_fetch_object($res_jobs)){
						$qry = "SELECT 	job.job_id 				AS Record,
										CONCAT(job.job_no,IF(job.job_no_add IS NOT NULL,job.job_no_add,''))         AS 'Job #',
										CASE job.dest_type
											WHEN 'num_lifestyle' THEN 'L/Style'
											WHEN 'num_farmers' THEN 'Farmer'
											WHEN 'num_dairies' THEN 'Dairy'
											WHEN 'num_sheep' THEN 'Sheep'
											WHEN 'num_beef' THEN 'Beef'
											WHEN 'num_sheepbeef' THEN 'S/Beef'
											WHEN 'num_dairybeef' THEN 'D/Beef'
											WHEN 'num_hort' THEN 'Hort'
											WHEN 'num_total' THEN 'Total'
											WHEN 'num_nzfw' THEN 'F@90%'
										END
																AS 'Type',
										job.publication 		AS Circular,
										IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%%m-%%d'))
											AS 'D/Date',
										@mail_type := (SELECT address.mail_type FROM address WHERE address.operator_id=job_route.dropoff_id)
																AS 'type',
										IF(@mail_type='e',(SELECT CONCAT(operator.company,' (e)') FROM operator WHERE operator.operator_id=job_route.dropoff_id),
											IF(@mail_type='f',(SELECT CONCAT(operator.company,' (f)') FROM operator WHERE operator.operator_id=job_route.dropoff_id),
												(SELECT CONCAT(operator.company,' (m)') FROM operator WHERE operator.operator_id=job_route.dropoff_id)))
																AS Dropoff,
										route.code AS 'RD',
										SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 	AS Quantity
										#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS Bundles
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								LEFT JOIN operator
								ON job_route.contractor_id=operator.operator_id
								WHERE job.job_id='$job->job_id'					
									AND job_route.dist_id=$dist_id
									AND job.is_regular<>'Y'
								#GROUP BY job.job_no,job_route.dropoff_id
								GROUP BY job.job_no,job_route.route_id
								ORDER BY job.job_no,operator.company";
						//echo nl2br($qry);
						//$res_query = query($qry);
						$qry_sum = "SELECT 	SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 
														AS tot_qty
											#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS tot_bund
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								LEFT JOIN operator
								ON job_route.contractor_id=operator.operator_id
								WHERE job.job_id='$job->job_id'					
									AND job_route.dist_id=$dist_id				
									AND job.is_regular<>'Y'
								GROUP BY job_route.dist_id";
						//echo nl2br($qry);
						$res_sum = query($qry_sum);
						if(mysql_num_rows($res_sum)>0){
							$has_reg=true;
							
							if($start_cas){
								$start_cas=false;
	?>
								<div class="weekly_head">
									<h3>Casual Jobs</h3>
								</div>								
	<?										
							}
							$sum = mysql_fetch_object($res_sum);	
							//$tab->startNewLine();
								//$tab->addLineWithStyle("Job #:".$job->job_no,"sql_extra_head_big");
							//$tab->stopNewLine();					
							$tab->writeSQLTableElement($qry,$first_job);
							$tab->startNewLine();
								$tab->addLines("",5);
								$tab->addLine("Total:");
								$tab->addLine($sum->tot_qty);
								//$tab->addLine($sum->tot_bund);
								//$tab->addLine($sum->total);
							$tab->stopNewLine();
							$tot_sum_qty += $sum->tot_qty;
							$tot_sum_bund += $sum->tot_bund;
							$tot_sum_total += $sum->total;
							$first_job=false;
						}
					}//while job
					
					$first_job=true;
					/*$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total:");
						$tab->addLine($tot_sum_qty);
						//$tab->addLine($tot_sum_bund);
						//$tab->addLine($tot_sum_total);
					$tab->stopNewLine();*/
					
					$tab->stopTable();	
				
				}
				//echo "Hello: ".$count." ".$num_dist;
				if($count<$num_dist-1){
				
?>
					<div class="pagebreak_after">&nbsp;</div>
<?				
				}
				$count++;
		}//while($dist = mysql_fetch_object($res_dist))					
		
	}//if($year && $week && $distributor)
	else{
		$ERROR = "";
	}
			
}



if($report=="weekly_contr_only"){
	

	if($comment2){
		$comment2 = nl2br($comment2);
		$qry = "UPDATE last_print_comment SET comment2='$comment2' WHERE last_print_comment_id=1";
		query($qry);
	}
	
	if($date_start&&$date_final){
		$date_show_start 	= date("jS M Y",strtotime($date_start));
		$date_show_end 		= date("jS M Y",strtotime($date_final));
		
		if($company!='All') $where_add = " AND operator_id='$company'";
		
		$qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y' $where_add";
		$res_dist = query($qry_dist);
		
		$num_dist = mysql_num_rows($res_dist);
		$count=0;
		while($dist = mysql_fetch_object($res_dist)){
			$start=true;
			$start_cas=true;
			$dist_id=get("operator","operator_id","WHERE company='$dist->company'");
			
			$qry_jobs = "SELECT DISTINCT job.job_id,job_no,is_regular 
						 FROM job 
						 LEFT JOIN job_route
						 ON job.job_id=job_route.job_id
						 LEFT JOIN route
						 ON route.route_id=job_route.route_id
						 WHERE delivery_date>='$date_start'
						 	AND delivery_date<='$date_final'
							AND job_route.dist_id='$dist_id'
						ORDER BY seq_region,seq_area,seq_code";
			//echo nl2br($qry_jobs);
			$res_jobs = query($qry_jobs);
			
			if(mysql_num_rows($res_jobs)>0){
		?>
					<div class="weekly_head">		
						<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
						<h3>Summary Delivery Instruction Period from: <?=$date_show_start?> to: <?=$date_show_end?><br />
						Distributor: <?=$dist->company?></h3>						
					</div>								
		<?								
			}			
			$show_regular=true;
			$show_casual=false;
			
			if($show_regular){
				$tab  = new MySQLTable("reports.php","");
				$tab->showRec=0;
				$tab->noRepFields["Circular"] = 1;
				$tab->noRepFields["Job #"] = 1;
				$tab->noRepFields["Delivery Date"] = 1;
				$tab->noRepFields["Type"] = 1;
				$tab->noRepFields["Dropoff"] = 1;
				
				$tab->collField["Qty"]=true;
				//$tab->collField["Qty 2"]=true;
				
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->setHiddenField("type");
				$tab->startTable();
				$first_job=true;
				
				$tot_sum_qty = 0;
				$tot_sum_bund = 0;
				$tot_sum_total = 0;
				
				/*if($show_rd_details){
					$group = "GROUP BY job.job_no,job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
					$sel_rd = "route.code AS 'RD',";					
					$num_blank_cols = 6;
				}
				else{
					$group = "GROUP BY job.job_no,job_route.dropoff_id,IF(job_route.dest_type='bundles',1,0)";
					$num_blank_cols = 5;
				}*/

				while($job=mysql_fetch_object($res_jobs)){
					//echo "Hello:".$job->is_regular;
					if(($show_rd_details && $job->is_regular=='Y') || ($job->is_regular=='N'||trim($job->is_regular=='')) ){
						$group = "GROUP BY job_route.route_id,IF(job_route.dest_type='bundles',1,0)";
						$sel_rd = "route.code AS 'RD',";					
						$num_blank_cols = 7;
					}
					else{
						$group = "GROUP BY job.job_no,IF(job_route.dest_type='bundles',1,0)";
						$num_blank_cols = 7;
						$sel_rd = "'N/A' AS RD,";
					}
					$qry = "SELECT 	job.job_id 				AS Record,
							CONCAT('#',job.job_no,IF(job.job_no_add IS NOT NULL AND job.job_no_add<>'','L',''))         AS 'Job #',
							IF(job.is_regular='Y','Y','N')	 AS 'Is Regular',
							GROUP_CONCAT(DISTINCT IF(is_att<>'Y', 
								   CASE job_route.dest_type
										WHEN 'num_lifestyle' THEN 'L/Style'
										WHEN 'num_farmers' THEN 'Farmer'
										WHEN 'num_dairies' THEN 'Dairy'
										WHEN 'num_sheep' THEN 'Sheep'
										WHEN 'num_beef' THEN 'Beef'
										WHEN 'num_sheepbeef' THEN 'S/Beef'
										WHEN 'num_dairybeef' THEN 'D/Beef'
										WHEN 'num_hort' THEN 'Hort'
										WHEN 'num_total' THEN 'Total'
										WHEN 'num_nzfw' THEN 'F@90%'
										WHEN 'bundles' THEN 'Bundles'
								   END
								   , '') SEPARATOR '') AS 'Type',
								
							CASE job.publication
								WHEN LENGTH(job.publication)<=20 THEN CONCAT(LEFT(job.publication,17),'...')
								ELSE job.publication
							END
											AS Circular,
							IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%m-%d'))
								AS 'D/Date',
							CASE operator.company
								WHEN LENGTH(operator.company) <= 20 THEN CONCAT(LEFT(operator.company,17),'... (',mail_type,')')
								ELSE CONCAT(operator.company,' (',mail_type,')')
							END
								AS Dropoff,
							$sel_rd
							SUM(IF(is_att<>'Y',job_route.amount,0)) 	AS 'Qty'
				
					FROM job
					LEFT JOIN job_route
					ON job.job_id=job_route.job_id
					LEFT JOIN route
						ON route.route_id=job_route.route_id
					LEFT JOIN operator
						ON job_route.dropoff_id=operator.operator_id
					LEFT JOIN address
						ON address.operator_id=operator.operator_id
					WHERE job.job_no='$job->job_no'					
						AND job_route.dist_id=$dist_id
						AND job.cancelled<>'Y'
					$group
					ORDER BY seq_region,seq_area,seq_code,job.job_no,operator.company";

						//echo nl2br($qry);
						//$res_query = query($qry);
						$qry_sum = "SELECT 	SUM(IF(is_att<>'Y',job_route.amount,0)) 	AS tot_qty1,
											SUM(IF(is_att='Y',job_route.amount,0)) 	AS tot_qty2
											#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS tot_bund
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								LEFT JOIN operator
								ON job_route.contractor_id=operator.operator_id
								WHERE job.job_id='$job->job_id'					
									AND job_route.dist_id=$dist_id				
								GROUP BY job_route.dist_id";
						//echo nl2br($qry);
						$res_sum = query($qry_sum);
						if(mysql_num_rows($res_sum)>0){
							$has_reg=true;
							if($start){
								$start=false;
	/*?>
								<div class="weekly_head">
									<h3>Regular Jobs</h3>
								</div>								
	<?			*/								
							}
						
							$sum = mysql_fetch_object($res_sum);	
							//$tab->startNewLine();
								//$tab->addLineWithStyle("Job #:".$job->job_no,"sql_extra_head_big");
							//$tab->stopNewLine();					
							$tab->writeSQLTableElement($qry,$first_job);
							$tab->startNewLine();
								$tab->addLines("",$num_blank_cols-1);
								$tab->addLine("Total:");
								$tab->addLine($sum->tot_qty1);
								//$tab->addLine($sum->tot_qty2);
								//$tab->addLine($sum->tot_bund);
								//$tab->addLine($sum->total);
							$tab->stopNewLine();
							$tot_sum_qty += $sum->tot_qty;
							$tot_sum_bund += $sum->tot_bund;
							$tot_sum_total += $sum->total;
							$first_job=false;
						}
					}//while job
					
					$first_job=true;
					/*$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total:");
						$tab->addLine($tot_sum_qty);
						//$tab->addLine($tot_sum_bund);
						//$tab->addLine($tot_sum_total);
					$tab->stopNewLine();*/
					
					$tab->startNewLine();
						$tab->addLineWithStyle($comment2,"sql_comment_line",$num_blank_cols+2);
					$tab->stopNewLine();
					
					$tab->stopTable();	
				
				} // if regular
				
				//////////////////////////////// Casual Jobs /////////////////////////////////
				if($show_casual){
					$tab  = new MySQLTable("reports.php","");
					$tab->showRec=0;
					$tab->hasEditButton=false;
					$tab->hasDeleteButton=false;
					$tab->hasAddButton=false;
					$tab->hasForm = false;
					$tab->noRepFields["Circular"] = 1;
					$tab->noRepFields["Job #"] = 1;
					$tab->noRepFields["Delivery Date"] = 1;
					$tab->noRepFields["Type"] = 1;
					$tab->noRepFields["Dropoff"] = 1;
					$tab->setHiddenField("type");
					$tab->startTable();
					$first_job=true;
					
					$tot_sum_qty = 0;
					$tot_sum_bund = 0;
					$tot_sum_total = 0;
					$start=true;
					$res_jobs = query($qry_jobs);
					while($job=mysql_fetch_object($res_jobs)){
						$qry = "SELECT 	job.job_id 				AS Record,
										CONCAT(job.job_no,IF(job.job_no_add IS NOT NULL,job.job_no_add,''))         AS 'Job #',
										CASE job.dest_type
											WHEN 'num_lifestyle' THEN 'L/Style'
											WHEN 'num_farmers' THEN 'Farmer'
											WHEN 'num_dairies' THEN 'Dairy'
											WHEN 'num_sheep' THEN 'Sheep'
											WHEN 'num_beef' THEN 'Beef'
											WHEN 'num_sheepbeef' THEN 'S/Beef'
											WHEN 'num_dairybeef' THEN 'D/Beef'
											WHEN 'num_hort' THEN 'Hort'
											WHEN 'num_total' THEN 'Total'
											WHEN 'num_nzfw' THEN 'F@90%'
										END
																AS 'Type',
										job.publication 		AS Circular,
										IF(is_ioa='Y','IOA',DATE_FORMAT(job.delivery_date,'%%m-%%d'))
											AS 'D/Date',
										@mail_type := (SELECT address.mail_type FROM address WHERE address.operator_id=job_route.dropoff_id)
																AS 'type',
										IF(@mail_type='e',(SELECT CONCAT(operator.company,' (e)') FROM operator WHERE operator.operator_id=job_route.dropoff_id),
											IF(@mail_type='f',(SELECT CONCAT(operator.company,' (f)') FROM operator WHERE operator.operator_id=job_route.dropoff_id),
												(SELECT CONCAT(operator.company,' (m)') FROM operator WHERE operator.operator_id=job_route.dropoff_id)))
																AS Dropoff,
										route.code AS 'RD',
										SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 	AS Quantity
										#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS Bundles
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								LEFT JOIN operator
								ON job_route.contractor_id=operator.operator_id
								WHERE job.job_id='$job->job_id'					
									AND job_route.dist_id=$dist_id
									AND job.is_regular<>'Y'
								#GROUP BY job.job_no,job_route.dropoff_id
								GROUP BY job.job_no,job_route.route_id
								ORDER BY job.job_no,operator.company";
						//echo nl2br($qry);
						//$res_query = query($qry);
						$qry_sum = "SELECT 	SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 
														AS tot_qty
											#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS tot_bund
								FROM job
								LEFT JOIN job_route
								ON job.job_id=job_route.job_id
								LEFT JOIN route
								ON route.route_id=job_route.route_id
								LEFT JOIN operator
								ON job_route.contractor_id=operator.operator_id
								WHERE job.job_id='$job->job_id'					
									AND job_route.dist_id=$dist_id				
									AND job.is_regular<>'Y'
								GROUP BY job_route.dist_id";
						//echo nl2br($qry);
						$res_sum = query($qry_sum);
						if(mysql_num_rows($res_sum)>0){
							$has_reg=true;
							
							if($start_cas){
								$start_cas=false;
	?>
								<div class="weekly_head">
									<h3>Casual Jobs</h3>
								</div>								
	<?										
							}
							$sum = mysql_fetch_object($res_sum);	
							//$tab->startNewLine();
								//$tab->addLineWithStyle("Job #:".$job->job_no,"sql_extra_head_big");
							//$tab->stopNewLine();					
							$tab->writeSQLTableElement($qry,$first_job);
							$tab->startNewLine();
								$tab->addLines("",5);
								$tab->addLine("Total:");
								$tab->addLine($sum->tot_qty);
								//$tab->addLine($sum->tot_bund);
								//$tab->addLine($sum->total);
							$tab->stopNewLine();
							$tot_sum_qty += $sum->tot_qty;
							$tot_sum_bund += $sum->tot_bund;
							$tot_sum_total += $sum->total;
							$first_job=false;
						}
					}//while job
					
					$first_job=true;
					/*$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total:");
						$tab->addLine($tot_sum_qty);
						//$tab->addLine($tot_sum_bund);
						//$tab->addLine($tot_sum_total);
					$tab->stopNewLine();*/
					
					$tab->stopTable();	
				
				}
				//echo "Hello: ".$count." ".$num_dist;
				if($count<$num_dist-1){
				
?>
					<div class="pagebreak_after">&nbsp;</div>
<?				
				}
				$count++;
		}//while($dist = mysql_fetch_object($res_dist))					
		
	}//if($year && $week && $distributor)
	else{
		$ERROR = "";
	}
			
}


if($report=="job_delivery_select"){
	$where_add="";
	if($dist_only){
		$show_regular = $show_casual = true;
		$where_add = " AND is_deliv_sent=1";
	}
	
	if($show_casual && $show_regular) $where_add.="";
	else if($show_regular) $where_add .= " AND is_regular='Y'";
	else if($show_casual) $where_add .= " AND (is_regular='N' OR is_regular ='')";
	
	
	$qry_jobs = "SELECT job.job_id AS Record,
						job.job_no AS 'Job',
						publication AS Publication,
						IF(is_ioa='Y','IOA',delivery_date) AS 'Delivery Date',
						IF(is_regular='Y','Y','N') AS 'Is Regular',
						is_deliv_sent AS 'Delivery Sent',
						is_pay_sent AS 'Payout Sent'
						 FROM job 
						 WHERE delivery_date BETWEEN '$date_start' AND '$date_final'
						  	$where_add
						ORDER BY job_no";
						//echo nl2br($qry_jobs);
	$tab  = new MySQLTable("reports.php",$qry_jobs);
	$tab->showRec=0;
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasAddButton=false;
	
	$tab->hasCheckBoxes=true;
	$tab->checkDefaultOn = false;
	
	$tab->hasForm=true;
	$tab->formPage="rep_revenue.php?report=job_delivery";
	$tab->hasSubmitButton = true;
	$tab->submitButtonName = "submit";
	$tab->submitButtonValue = "Process Delivery";
	
	
	$tab->startTable();
		$tab->addHiddenInput("date_start",$date_start);
		$tab->addHiddenInput("date_final",$date_final);
		$tab->addHiddenInput("comment2",$comment2);
		$tab->addHiddenInput("dist_only",$dist_only);
		$tab->writeTable();
	$tab->stopTable();
	
}
if($report=="job_delivery"){

	
	if(isset($comment2)){
		$comment2 = nl2br($comment2);
		$qry = "UPDATE last_print_comment SET comment1='$comment1',comment2='$comment2' WHERE last_print_comment_id=1";
		query($qry);
	}
	
	if($check){
		$jobs = "-1";
		foreach($check as $j=>$has){
			$jobs .= ",".$j;
		}
		
		$qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y'";
		$res_dist = query($qry_dist);
		
		$num_dist = mysql_num_rows($res_dist);
		$count=0;
		while($dist = mysql_fetch_object($res_dist)){
			$start=true;
			$start_cas=true;
			$dist_id=get("operator","operator_id","WHERE company='$dist->company'");
			
			$qry_jobs = "SELECT job.job_id,job_no,is_regular 
						 FROM job 
						 WHERE job_id IN ($jobs)";
			//echo nl2br($qry_jobs);
			$res_jobs = query($qry_jobs);
			
			if(mysql_num_rows($res_jobs)>0){
		?>
					<div class="weekly_head">		
						<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
						<h3>Summary Delivery Instructions<br />
						Distributor: <?=$dist->company?></h3>						
					</div>								
		<?								
			}			

			$tab  = new MySQLTable("reports.php","");
			$tab->showRec=0;
			$tab->noRepFields["Circular"] = 1;
			$tab->noRepFields["Job #"] = 1;
			$tab->noRepFields["Delivery Date"] = 1;
			$tab->noRepFields["Is Regular"] = 1;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->setHiddenField("type");
			$tab->startTable();
			$first_job=true;
			
			$tot_sum_qty = 0;
			$tot_sum_bund = 0;
			$tot_sum_total = 0;
			
			
			while($job=mysql_fetch_object($res_jobs)){
					$qry = "SELECT 	job.job_id 				AS Record,
									CONCAT(job.job_no,IF(job.job_no_add IS NOT NULL,job.job_no_add,''))         AS 'Job #',
									IF(is_regular='Y','Y','N') AS 'Is Regular',
									job.publication 		AS Circular,
									CONCAT(job.delivery_date,IF(cancelled IS NOT NULL AND cancelled='Y','(C)','')) 	AS 'Delivery Date',
									@mail_type := (SELECT address.mail_type FROM address WHERE address.operator_id=job_route.dropoff_id)
															AS 'type',
									IF(@mail_type='e',(SELECT CONCAT(operator.company,' (e)') FROM operator WHERE operator.operator_id=job_route.dropoff_id),
										IF(@mail_type='f',(SELECT CONCAT(operator.company,' (f)') FROM operator WHERE operator.operator_id=job_route.dropoff_id),
											(SELECT CONCAT(operator.company,' (m)') FROM operator WHERE operator.operator_id=job_route.dropoff_id)))
															AS Dropoff,
									SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 	AS Quantity
									#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) 	AS Bundles
							FROM job
							LEFT JOIN job_route
							ON job.job_id=job_route.job_id
							LEFT JOIN route
							ON route.route_id=job_route.route_id
							LEFT JOIN operator
							ON job_route.contractor_id=operator.operator_id
							WHERE job.job_id='$job->job_id'					
								AND job_route.dist_id=$dist_id
							GROUP BY job.job_no,job_route.dropoff_id
							ORDER BY seq_region,seq_area,seq_code,job.job_no,operator.company";
					//echo nl2br($qry);
					//$res_query = query($qry);
					$qry_sum = "SELECT 	SUM(IF(job_route.dest_type<>'bundles',job_route.amount,0)) 
													AS tot_qty
										#SUM(IF(job_route.dest_type='bundles',job_route.amount,0)) AS tot_bund
							FROM job
							LEFT JOIN job_route
							ON job.job_id=job_route.job_id
							LEFT JOIN route
							ON route.route_id=job_route.route_id
							LEFT JOIN operator
							ON job_route.contractor_id=operator.operator_id
							WHERE job.job_id='$job->job_id'					
								AND job_route.dist_id=$dist_id				
							GROUP BY job_route.dist_id";
					//echo nl2br($qry);
					$res_sum = query($qry_sum);
					if(mysql_num_rows($res_sum)>0){
					
						$sum = mysql_fetch_object($res_sum);	
						//$tab->startNewLine();
							//$tab->addLineWithStyle("Job #:".$job->job_no,"sql_extra_head_big");
						//$tab->stopNewLine();					
						$tab->writeSQLTableElement($qry,$first_job);
						$tab->startNewLine();
							$tab->addLines("",4);
							$tab->addLine("Total:");
							$tab->addLine($sum->tot_qty);
							//$tab->addLine($sum->tot_bund);
							//$tab->addLine($sum->total);
						$tab->stopNewLine();
						$tot_sum_qty += $sum->tot_qty;
						$tot_sum_bund += $sum->tot_bund;
						$tot_sum_total += $sum->total;
						$first_job=false;
					}
				}//while job
				
				if($tot_sum_qty>0){
					$first_job=true;
					$tab->startNewLine();
						$tab->addLines("",4);
						$tab->addLine("Total:");
						$tab->addLine($tot_sum_qty);
						//$tab->addLine($tot_sum_bund);
						//$tab->addLine($tot_sum_total);
					$tab->stopNewLine();
					
					$tab->startNewLine();
						$tab->addLineWithStyle($comment2,"sql_comment_line",5);
					$tab->stopNewLine();
				}
				
				$tab->stopTable();	
				
				
				//echo "Hello: ".$count." ".$num_dist;
				if($count<$num_dist-1){
				
?>
					<div class="pagebreak_after">&nbsp;</div>
<?				
				}
				$count++;
		}//while($dist = mysql_fetch_object($res_dist))					
		
	}//if($year && $week && $distributor)
	else{
		$ERROR = "";
	}
			
}


if($report=="weekly2"){
	if(isset($comment1)){
		$comment2 = addslashes($comment2);
		$comment2 = nl2br($comment2);
		$qry = "UPDATE last_print_comment SET comment1='$comment1',comment2='$comment2' WHERE last_print_comment_id=1";
		query($qry);
	}
	
	if($date_start&&$date_final){
		$date_show_start 	= date("jS M Y",strtotime($date_start));
		$date_show_end 		= date("jS M Y",strtotime($date_final));
		
		$qry_dist = "SELECT DISTINCT operator_id,company FROM operator WHERE is_dist='Y'";
		$res_dist = query($qry_dist);
		while($dist = mysql_fetch_object($res_dist)){
		?>
					<div class="weekly_head">		
						<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
						<h3>Summary Delivery Instruction Period from: <?=$date_show_start?> to: <?=$date_show_end?>
						for Distributor: <?=$dist->company?></h3>						
					</div>					
					<div class="weekly_head">
						<h3 class="weekly_head_h2">Regular Jobs</h3>
					</div>								
		<?						
			$qry = "SELECT DISTINCT company,route.dropoff_id
					FROM job
					LEFT JOIN job_route
					ON job.job_id=job_route.job_id
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					LEFT JOIN operator
					ON operator.operator_id=route.dropoff_id
					WHERE dist_id=$dist->operator_id
						AND job.is_regular='Y'
						AND job.cancelled<>'Y'
					ORDER BY island,seq_region,seq_area";
			
			$res_do = query($qry);
			$tab  = new MySQLTable("reports.php","");
			$tab->showRec=1;
			$tab->colWidth["Circ"]=50;
			$tab->colWidth["Total"]=50;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->setHiddenField("type");
			$tab->startTable();					
			$first_job=true;
			while($do = mysql_fetch_object($res_do)){
				$qry_sum = "SELECT 
								SUM(IF(job_route.dest_type='bundles',amount,0)) 	
									AS Bundles,
								SUM(IF(job_route.dest_type<>'bundles',amount,0)) 	
									AS 'Circ',
								SUM(amount)
									AS Total,
								COUNT(*) AS ct
						FROM job
						LEFT JOIN job_route
						ON job.job_id=job_route.job_id
						LEFT JOIN route
						ON route.route_id=job_route.route_id
						LEFT JOIN operator
						ON operator.operator_id=job_route.dropoff_id
						WHERE dist_id=$dist->operator_id
							AND job_route.dropoff_id = $do->dropoff_id
							AND job.is_regular='Y'
							AND job.cancelled<>'Y'
							AND job.delivery_date>='$date_start'
							AND job.delivery_date<='$date_final'
							
						GROUP BY dist_id,route.dropoff_id";						
						
				$qry = "SELECT job.job_no AS 'Job #',
								job.publication 	AS Circular,
								job.delivery_date 		AS 'Delivery Date',
								CONCAT(operator.company,' (',address.mail_type,')') 
															AS Dropoff,
								SUM(IF(job_route.dest_type='bundles',amount,0)) 	
									AS Bundles,
								SUM(IF(job_route.dest_type<>'bundles',amount,0)) 	
									AS Circ,
								SUM(amount)
									AS Total
						FROM job
						LEFT JOIN job_route
						ON job.job_id=job_route.job_id
						LEFT JOIN route
						ON route.route_id=job_route.route_id
						LEFT JOIN operator
						ON operator.operator_id=job_route.dropoff_id
						LEFT JOIN address
						ON address.operator_id=operator.operator_id
						WHERE dist_id=$dist->operator_id
							AND job_route.dropoff_id = $do->dropoff_id
							AND job.is_regular='Y'
							AND job.cancelled<>'Y'
							AND job.delivery_date>='$date_start'
							AND job.delivery_date<='$date_final'
							
						GROUP BY route.dropoff_id,job.job_id
						ORDER BY Dropoff,Circular";
				$tab->writeSQLTableElement($qry,$first_job);
				$first_job=false;
				$res_sum = query($qry_sum);
				$sum = mysql_fetch_object($res_sum);
			}//DO
			$first_job=true;
			$tab->startNewLine();
				$tab->addLineWithStyle($comment2,"sql_comment_line",5);
			$tab->stopNewLine();
			$tab->stopTable();		
?>
			<div class="weekly_head">
				<h3 class="weekly_head_h2">Casual Jobs</h3>
			</div>								
<?					
			$qry = "SELECT job.job_no AS 'Job #',
							job.publication 	AS Circular,
							job.delivery_date 		AS 'Delivery Date',
							operator.company 
														AS Dropoff,
							SUM(IF(job_route.dest_type='bundles',amount,0)) 	
								AS Bundles,
							SUM(IF(job_route.dest_type<>'bundles',amount,0)) 	
								AS Circ,
							SUM(amount)
								AS Total
					FROM job
					LEFT JOIN job_route
					ON job.job_id=job_route.job_id
					LEFT JOIN route
					ON route.route_id=job_route.route_id
					LEFT JOIN operator
					ON operator.operator_id=route.dropoff_id
					WHERE dist_id=$dist->operator_id
						AND job.is_regular<>'Y'
						AND job.cancelled<>'Y'
						AND job.delivery_date>='$date_start'
						AND job.delivery_date<='$date_final'
						
					GROUP BY route.dropoff_id
					ORDER BY Dropoff,Circular";
			$tab  = new MySQLTable("reports.php",$qry);
			$tab->showRec=0;
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->setHiddenField("type");
			$tab->startTable();				
			$tab->startTable();		
			$tab->writeTable();
			$tab->stopTable();									
?>
				<hr class="pagebreak_after" />
<?								
		}//while($dist = mysql_fetch_object($res_dist))					
	}//if($year && $week && $distributor)
	else{
		$ERROR = "";
	}
			
}


if($report=="revenue"){
	if($month&&$year){
		$month_show = date('F',mktime(0,0,0,$month,1,$year));

		//$dist  = get("operator","company","WHERE operator_id=$dist_id");
		//$title = "Distributor: $dist, $month_show/$year";
		$title = "Revenue for $month_show/$year";
?>
				<div class="weekly_head">
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
					<h3 class="weekly_head_h2"><?=$title?></h3>
				</div>				
			
<?	
	$qry = "SELECT job.Record,
				   job.invoice_no AS 'Invoice #',
				   job.job_no AS 'Job #',
			       CONCAT(LEFT(job.client,10),'...') AS 'Client',
				   CONCAT(LEFT(job.publication,10),'...') AS 'Publication',
				   job.Weight,
				   job.invoice_qty AS 'Invoiced',
				   job.qty_bbc AS 'BBC',
				   job.Coural,				   
				   job.Coural AS 'coural_qty',	
				   IF(job.Bundles IS NULL,0,job.Bundles) AS Bdls,
				   @bundles := IF(job.Bundles2 IS NULL,0,job.Bundles2) AS Bundles2,
				   round(job.Normal,4) AS Normal,
				   /*round(job.bbc_rate,4) AS 'BBC',*/
				   job.Dist,
				   job.SubDist AS 'Sub Dist',
				   job.Cont,
				   job.folding_fee + job.premium AS `Add`,
				   @cust2 := ROUND((job.invoice_qty)*job.Normal+job.qty_bbc*job.bbc_rate+@bundles*job.bundle_sell ,2)
						AS 'Cust.',
				  /* job.fuel_surcharge_fact AS 'F/Surch.',
				   job.discount_fact,*/
				   @cust := ROUND(@cust2 * (1+job.fuel_surcharge_fact-discount_fact),2) 
						AS 'Cust. (inc.)',
				   @coural := round((job.Coural-IF(job.mailings IS NOT NULL,job.mailings,0))*(job.Dist+job.SubDist+job.Cont)+@bundles*job.bundle_sell,2)
						AS 'Coural',
				   round((job.Coural-IF(job.mailings IS NOT NULL,job.mailings,0))*(job.Dist+job.SubDist+job.Cont)+@bundles*job.bundle_sell,2)
						AS 'coural_dollars',
				   @freight := round(job.lbc_charge+job.freight_charge+job.lbc_charge_bbc,2)
						  AS 'Freight',
				   @margin := round(@cust-@coural-@freight,2) AS '$',
				   IF(@cust >0,
					  round(100*@margin/@cust,1),
					  0)
					  AS '%'   
				   
			FROM
			(
			SELECT job.job_id AS 'Record', 
				   job.invoice_no,
				   job.job_no,
				   job.add_premium_to_invoice,
				   job.add_folding_to_invoice,
				   job.premium,
				   job.premium_sell,
				   job.bundle_sell,
				   route.region,
				   job.delivery_date,
				   client.name AS client,
				   job.publication,
				   round(job.weight)  AS 'Weight',
				   job.invoice_qty,
				   SUM(
						IF(	job_route.dest_type<>'bundles', 
							amount,0)
						)
						AS 'Coural',
				   SUM(
						IF(	job_route.dest_type='bundles',
							amount,0)
						)
						AS 'Bundles',
				   SUM(
						IF(	job_route.dest_type='bundles',
							amount,0)
						)
						AS 'Bundles2',						
				   job.qty_bbc,
				   round(job.rate+".get_add_rates_qry().",4) AS 'Normal',
				   round(job.rate_bbc,4) AS 'bbc_rate',
				   round(job.dist_rate,4) AS 'Dist',
				   round(job.subdist_rate,4) AS 'SubDist',
				   round(job.contr_rate+job.folding_fee+job.premium,4) AS 'Cont',
				   round(job.fuel_surcharge,1) AS fuel_surcharge,
				   round(job.fuel_surcharge/100,2) AS fuel_surcharge_fact,
				   round(job.discount/100,2) AS discount_fact,
				   job.discount,
				   ROUND(".get_add_rates_qry().",4) AS folding_fee,
				   job.lbc_charge,
				   job.lbc_charge_bbc,
				   job.freight_charge,
				   (
				   	SELECT SUM(amount)
					FROM job_route
					LEFT JOIN route
						ON route.route_id=job_route.route_id
					WHERE job_id=job.job_id
						AND region='MAILINGS'
					GROUP BY region
				   ) AS mailings
			FROM job
			LEFT JOIN job_route
			ON job.job_id=job_route.job_id
			LEFT JOIN route
			ON job_route.route_id=route.route_id
			LEFT JOIN client
			ON client.client_id=job.client_id
			WHERE year(job.delivery_date)='$year'
				  	AND month(job.delivery_date)='$month'
					#AND job.finished<>'Y'
					AND job.cancelled<>'Y' 
					AND (job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590) )
			GROUP BY job.job_no

			) AS job
			ORDER BY Client,Publication
			";
			

				  
		
			//echo nl2br($qry );
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->collField["Invoiced"]=true;
		$tab->collField["BBC"]=true;
		$tab->collField["Coural"]=true;
		$tab->collField["Bdls"]=true;
		$tab->collField["Cust."]=true;
		$tab->collField["Cust. (inc.)"]=true;
		$tab->collField["coural_qty"]=true;
		
		
		$tab->collField["coural_dollars"]=true;
		$tab->collField["Freight"]=true;
		$tab->collField["$"]=true;
		$tab->collField["%"]=true;
		
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=0;
		$tab->hiddenFields["Bundles2"]=1;
		$tab->hiddenFields["coural_qty"]=1;
		$tab->hiddenFields["coural_dollars"]=1;
		$tab->hiddenFields["Cust."]=1;
		
		$tab->startTable();
		$tab->startNewLine();
			$tab->addLineWithStyle("Job Details","sql_extra_head",5);
			$tab->addLineWithStyle("Quantities","sql_extra_head",4);
			$tab->addLineWithStyle("Rates","sql_extra_head",1);
			$tab->addLineWithStyle("Cost Structure","sql_extra_head",4);
			$tab->addLineWithStyle("Totals","sql_extra_head",3);
			$tab->addLineWithStyle("Margin","sql_extra_head",2);
		$tab->stopNewLine();		
		$tab->writeTable();
		
		//print_r(array_sum($tab->collFieldVal));
		
		$invoiced = array_sum($tab->collFieldVal["Invoiced"]);
		$bbc = number_format(array_sum($tab->collFieldVal["BBC"]),0,'.','');
		$coural_qty = number_format(array_sum($tab->collFieldVal["coural_qty"]),0,'.','');
		$bundles = array_sum($tab->collFieldVal["Bdls"]);
		$cust = array_sum($tab->collFieldVal["Cust."]);
		$cust_inc = array_sum($tab->collFieldVal["Cust. (inc.)"]);
		$coural_dollars = number_format(array_sum($tab->collFieldVal["coural_dollars"]),2,'.','');
		$freight = number_format(array_sum($tab->collFieldVal["Freight"]),2,'.','');
		$dollar = number_format(array_sum($tab->collFieldVal["$"]),2,'.','');
		$perc = number_format(100 * $dollar / $cust_inc,1);
		//$perc = number_format(array_sum($tab->collFieldVal["%"])/count($tab->collFieldVal["%"]),1);
		
		$tab->startNewLine();
			$tab->addLines("",3);
			$tab->addLine("Totals:");
			$tab->addLine("");
			$tab->addLine($invoiced);
			$tab->addLine($bbc);
			$tab->addLine($coural_qty);
			$tab->addLine($bundles);
			$tab->addLines("",5);
			//$tab->addLine($cust);
			$tab->addLine($cust_inc);
			$tab->addLine($coural_dollars);
			$tab->addLine($freight);
			$tab->addLine($dollar);
			$tab->addLine($perc);
		$tab->stopNewLine();		
		$tab->stopTable();			
	}	
}
if($report=="revenue2"){
	if($job || $client_id){
		if(!$frommonth && ! $fromyear){
			$start_date = '1990-01-01';
		}
		else{
			$start_date = date('Y-m-d',mktime(0,0,0,$frommonth,1,$fromyear));
		}
		
		if(!$tomonth && !$toyear){
			$end_date = '2030-12-31';
		}
		else{
			$end_date = date('Y-m-d',mktime(0,0,0,$tomonth,31,$toyear));
		}

		if($client_id){
			$client = get("client","name","WHERE client_id=$client_id");
			$where_client = "AND job.client_id=$client_id";
		}
		if($job){
			$where_job = "AND job.job_no=$job";
		}
			
		//$dist  = get("operator","company","WHERE operator_id=$dist_id");
		//$title = "Distributor: $dist, $month_show/$year";
		$title = "Revenue for $client";
?>
				<div class="weekly_head">
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
					<h3 class="weekly_head_h2"><?=$title?></h3>
				</div>				
			
<?	
	$qry = "SELECT job.Record,
				   MONTHNAME(job.delivery_date) AS Month,
				   CONCAT(YEAR(job.delivery_date),MONTH(job.delivery_date)) AS MonthS,
				   job.invoice_no AS 'Invoice #',
				   job.job_no AS 'Job #',
			       CONCAT(LEFT(job.client,10),'...') AS 'Client',
				   CONCAT(LEFT(job.publication,10),'...') AS 'Publication',
				   job.Weight,
				   job.invoice_qty AS 'Invoiced',
				   job.qty_bbc AS 'BBC',
				   job.Coural,				   
				   job.Coural AS 'coural_qty',	
				   IF(job.Bundles IS NULL,0,job.Bundles) AS Bdls,
				   @bundles := IF(job.Bundles2 IS NULL,0,job.Bundles2) AS Bundles2,
				   round(job.Normal,4) AS Normal,
				   /*round(job.bbc_rate,4) AS 'BBC',*/
				   job.Dist,
				   job.SubDist AS 'Sub Dist',
				   job.Cont,
				   job.folding_fee + job.premium AS `Add`,
				   @cust2 := ROUND((job.invoice_qty)*job.Normal+job.qty_bbc*job.bbc_rate+@bundles ,2)
						AS 'Cust.',
				  /* job.fuel_surcharge_fact AS 'F/Surch.',
				   job.discount_fact,*/
				   @cust := ROUND(@cust2 * (1+job.fuel_surcharge_fact-discount_fact),2) 
						AS 'Cust. (inc.)',
				   @coural := round((job.Coural-IF(job.mailings IS NOT NULL,job.mailings,0))*(job.Dist+job.SubDist+job.Cont)+@bundles,2)
						AS 'Coural',
				   round((job.Coural-IF(job.mailings IS NOT NULL,job.mailings,0))*(job.Dist+job.SubDist+job.Cont)+@bundles,2)
						AS 'coural_dollars',
				   @freight := round(job.lbc_charge+job.freight_charge+job.lbc_charge_bbc,2)
						  AS 'Freight',
				   @margin := round(@cust-@coural-@freight,2) AS '$',
				   IF(@cust >0,
					  round(100*@margin/@cust,1),
					  0)
					  AS '%'   
				   
			FROM
			(
			SELECT job.job_id AS 'Record', 
				   job.invoice_no,
				   job.job_no,
				   route.region,
				   job.delivery_date,
				   client.name AS client,
				   job.publication,
   				   job.add_premium_to_invoice,
				   job.add_folding_to_invoice,
				   job.premium,
				   round(job.weight)  AS 'Weight',
				   job.invoice_qty,
				   SUM(
						IF(	job_route.dest_type<>'bundles', 
							amount,0)
						)
						AS 'Coural',
				   SUM(
						IF(	job_route.dest_type='bundles',
							amount,0)
						)
						AS 'Bundles',
				   SUM(
						IF(	job_route.dest_type='bundles',
							amount*job.bundle_sell,0)
						)
						AS 'Bundles2',						
				   job.qty_bbc,
				   round(job.rate+".get_add_rates_qry().",4) AS 'Normal',
				   round(job.rate_bbc,4) AS 'bbc_rate',
				   round(job.dist_rate,4) AS 'Dist',
				   round(job.subdist_rate,4) AS 'SubDist',
				   round(job.contr_rate+job.folding_fee+job.premium,4) AS 'Cont',
				   round(job.fuel_surcharge,1) AS fuel_surcharge,
				   round(job.fuel_surcharge/100,2) AS fuel_surcharge_fact,
				   round(job.discount/100,2) AS discount_fact,
				   job.discount,
				   ROUND(".get_add_rates_qry().",4) AS folding_fee,
				   job.lbc_charge,
				   job.lbc_charge_bbc,
				   job.freight_charge,
				   (
				   	SELECT SUM(amount)
					FROM job_route
					LEFT JOIN route
						ON route.route_id=job_route.route_id
					WHERE job_id=job.job_id
						AND region='MAILINGS'
					GROUP BY region
				   ) AS mailings
			FROM job
			LEFT JOIN job_route
			ON job.job_id=job_route.job_id
			LEFT JOIN route
			ON job_route.route_id=route.route_id
			LEFT JOIN client
			ON client.client_id=job.client_id
			WHERE job.delivery_date BETWEEN '$start_date' AND '$end_date'
					$where_client
					$where_job
					#AND job.finished<>'Y'
					AND job.cancelled<>'Y' 
					AND (job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590) )
			GROUP BY job.job_no

			) AS job
			ORDER BY MonthS,Client,Publication
			";
			

				  
		
			//echo nl2br($qry );
		$tab  = new MySQLTable("reports.php",$qry);
		$tab->letDieOnEmptySet = true;
		$tab->collField["Invoiced"]=true;
		$tab->collField["BBC"]=true;
		$tab->collField["Coural"]=true;
		$tab->collField["Bdls"]=true;
		$tab->collField["Cust."]=true;
		$tab->collField["Cust. (inc.)"]=true;
		$tab->collField["coural_qty"]=true;
		
		
		$tab->collField["coural_dollars"]=true;
		$tab->collField["Freight"]=true;
		$tab->collField["$"]=true;
		$tab->collField["%"]=true;
		
		$tab->hasEditButton=false;
		$tab->hasDeleteButton=false;
		$tab->hasAddButton=false;
		$tab->showRec=0;
		$tab->hiddenFields["Bundles2"]=1;
		$tab->hiddenFields["MonthS"]=1;
		$tab->hiddenFields["coural_qty"]=1;
		$tab->hiddenFields["coural_dollars"]=1;
		$tab->hiddenFields["Cust."]=1;
		
		$tab->startTable();
		$tab->startNewLine();
			$tab->addLineWithStyle("Job Details","sql_extra_head",6);
			$tab->addLineWithStyle("Quantities","sql_extra_head",4);
			$tab->addLineWithStyle("Rates","sql_extra_head",1);
			$tab->addLineWithStyle("Cost Structure","sql_extra_head",4);
			$tab->addLineWithStyle("Totals","sql_extra_head",3);
			$tab->addLineWithStyle("Margin","sql_extra_head",2);
		$tab->stopNewLine();		
		$tab->writeTable();
		
		//print_r(array_sum($tab->collFieldVal));
		
		
		$invoiced = array_sum($tab->collFieldVal["Invoiced"]);
		$bbc = number_format(array_sum($tab->collFieldVal["BBC"]),0,'.','');
		$coural_qty = number_format(array_sum($tab->collFieldVal["coural_qty"]),0,'.','');
		$bundles = array_sum($tab->collFieldVal["Bdls"]);
		$cust = array_sum($tab->collFieldVal["Cust."]);
		$cust_inc = array_sum($tab->collFieldVal["Cust. (inc.)"]);
		$coural_dollars = number_format(array_sum($tab->collFieldVal["coural_dollars"]),2,'.','');
		$freight = number_format(array_sum($tab->collFieldVal["Freight"]),2,'.','');
		$dollar = number_format(array_sum($tab->collFieldVal["$"]),2,'.','');
		//$perc = number_format(array_sum($tab->collFieldVal["%"])/count($tab->collFieldVal["%"]),1);
		$perc = number_format(100 * $dollar / $cust_inc,1);
		
		$tab->startNewLine();
			$tab->addLines("",4);
			$tab->addLine("Totals:");
			$tab->addLine("");
			$tab->addLine($invoiced);
			$tab->addLine($bbc);
			$tab->addLine($coural_qty);
			$tab->addLine($bundles);
			$tab->addLines("",5);
			//$tab->addLine($cust);
			$tab->addLine($cust_inc);
			$tab->addLine($coural_dollars);
			$tab->addLine($freight);
			$tab->addLine($dollar);
			$tab->addLine($perc);
		$tab->stopNewLine();		
		$tab->stopTable();			
	}	
}






if($report=="rep_cirpay_by_dist" || $report=="rep_cirpay_by_dist_compare"){


	if(isset($comment2)){
		$comment2 = nl2br($comment2);
		$qry = "UPDATE last_print_comment SET comment2='$comment2' WHERE last_print_comment_id=2";
		query($qry);
	}
	if($month&&$year){
?>
				<div class="weekly_head">	
					<h3 style='margin:0 0 0 0'>Circular Payout by Distributor</h3>
				</div>				
			
<?			
		$month_show = date('F',mktime(0,0,0,$month,1,$year));
		
		if($compare_report){
			$month_show1 = date('F',mktime(0,0,0,$month+1,1,$year));
			
			$month1 = date('m',mktime(0,0,0,$month+1,1,$year));
			$year1 = date('Y',mktime(0,0,0,$month+1,1,$year));
			
			$add_where_month = " OR month(delivery_date)='$month1' ";
			$add_where_year = " OR month(delivery_date)='$month1' ";
			$add_sort = "Sort,delivery_date,job_no";
			$show_pub = false;
			$fomat_line = true;
			$sel_add_pub = "IF(month(delivery_date)%2=0,
						   		CONCAT('<font color=\'red\'>',job.publication,'</font>'),
								CONCAT('<font color=\'blue\'>',job.publication,'</font>'))
						   					   			AS 'Publication',";
			$sel_add_date = "IF(month(delivery_date)%2=0,
						   		CONCAT('<font color=\'red\'>',job.delivery_date,'</font>'),
								CONCAT('<font color=\'blue\'>',job.delivery_date,'</font>'))
						   					   			AS 'Date',";
		}
		else{
			$month1 = $month;
			$year1 = $year;
			$fomat_line = false;
			$add_sort = "Sort,job_no,delivery_date";
			$show_pub = true;
			$sel_add_pub = "job.publication
						   		AS 'Publication',";
			$sel_add_date = "job.delivery_date
						   		AS 'Date',";
		}
		
	
		if($dist_id) {
			$dist_list[]=$dist_id;
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
		}
		foreach($dist_list as $dist){
			$dist_name = get("operator","company","WHERE operator_id='$dist'");
			$gst_no	   = get("address","gst_num","WHERE operator_id='$dist'");
			$title = "Distributor: $dist_name, $month_show/$year";
			 
			 $group 	= "job.job_no";
			 $dist_comp	= get("operator","company","WHERE operator_id=$dist");
		
			$qry = "SELECT 	payout.*,
							@tot := ROUND(`Dist Amt`+ `S/Dist Amt` + `Cont Amt` + `Bdl Amt`,2) AS Total,
							ROUND(".(1+$GST_CIRCULAR)."*@tot,2) AS 'Total (incl. GST)'	
							
					FROM
					(
						SELECT $sel_add_date
							   job.job_no					AS 'Job',
							   job.publication				AS 'Sort',
							   $sel_add_pub
								ROUND(job.weight,0)					AS Weight,		
							   SUM(
									IF(
										job_route.dest_type<>'bundles' ,
										amount,0)
									)
															AS 'Quantity',	
										   
							   SUM(
									IF(
										job_route.dest_type='bundles',
										amount,0)
									)
															AS 'Qty Bdl',											
															
							   round(job.dist_rate,4)   	AS 'Dist-Rate',				   
							   round((job.subdist_rate),4)    AS 'S/Dist-Rate',
							   round(job.contr_rate+job.folding_fee + job.premium,4)      AS 'Cont-Rate',
							   round(job.folding_fee + job.premium,4)      AS 'Add',
							   round(
									SUM(
										IF( job_route.dest_type<>'bundles' ,
											job_route.amount,
											0
										)
									)*
									job.dist_rate,2)     		AS 'Dist Amt' ,   
							   round(	
									SUM(
										IF(job_route.dest_type<>'bundles' ,
											job_route.amount * (job_route.subdist_rate_red),
											0
										)
									)*
							   job.subdist_rate,2)     		AS 'S/Dist Amt' ,   
							   round(SUM(
										IF(job_route.dest_type<>'bundles' ,
											job_route.amount,
											0
										))*
							   (job.contr_rate+folding_fee + job.premium),2)     		AS 'Cont Amt' ,   						   
	
							   round(SUM(
									IF(	job_route.dest_type='bundles' ,
										job_route.amount*job_route.bundle_price,
										0
									)
									)
								,2)     		
															AS 'Bdl Amt'
						FROM job
						LEFT JOIN job_route
						ON job_route.job_id = job.job_id
						LEFT JOIN route
						ON job_route.route_id = route.route_id
						WHERE 	(
									(year(delivery_date)='$year' AND month(delivery_date)='$month')
									OR
									(year(delivery_date)='$year1' AND month(delivery_date)='$month1')
								)
							AND job_route.dist_id=$dist
							#AND job.finished<>'Y'
							AND job.cancelled<>'Y'			
							AND job_route.dist_id NOT IN (813,814,815,583,584,585,586,587,588,589,590)
						GROUP BY $group
						ORDER BY $add_sort
					) AS payout";
			//echo nl2br($qry);
			$sum=1;
			if($sum>0){
?>
				<div class="weekly_head">
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>
					<p>COURAL (RURAL COURIERS SOCIETY LTD)<br />
						PO BOX 1233<br />
						PALMERSTON NORTH PHONE: (06) 357 3129 FAX: (06) 356 6618</p>		
					<h3 style='margin:0 0 0 0'><?=$title?></h3>
					<h4>GST NO:<?=$gst_no?></h4>
				</div>				
			
<?								
			
				$tab  = new MySQLTable("rep_revenue.php",$qry);
				$tab->hasEditButton=false;
				$tab->hasDeleteButton=false;
				$tab->hasAddButton=false;
				$tab->formatLine=$fomat_line;
				$tab->hiddenFields["Sort"]=true;
				
				$tab->collField["Quantity"]=true;
				$tab->collField["Qty Bdl"]=true;
				$tab->collField["Dist Amt"]=true;
				$tab->collField["S/Dist Amt"]=true;
				$tab->collField["Cont Amt"]=true;
				$tab->collField["Bdl Amt"]=true;
				$tab->collField["Total"]=true;
				$tab->collField["Total (incl. GST)"]=true;
				
				$tab->noRepFields["Publication"]=$show_pub;
				$tab->colWidth["Date"]=80;
				$tab->startTable();
				$tab->writeTable();
				
				
				$tab->startNewLine();
					$tab->addLineWithStyle("","sql_comment_line_bg0",11);
				$tab->stopNewLine();			
								

				$tab->startNewLine();
					$tab->addLineWithStyle("","sql_comment_line_bg0",11);
				$tab->stopNewLine();				
				
				$qty = array_sum($tab->collFieldVal["Quantity"]);
				$qty_bdl = number_format(array_sum($tab->collFieldVal["Qty Bdl"]),0,'.','');
				$dist_amt = number_format(array_sum($tab->collFieldVal["Dist Amt"]),2,'.','');
				$sdist_amt = number_format(array_sum($tab->collFieldVal["S/Dist Amt"]),2,'.','');
				$contr_amt = number_format(array_sum($tab->collFieldVal["Cont Amt"]),2,'.','');
				$bdl_amt = number_format(array_sum($tab->collFieldVal["Bdl Amt"]),2,'.','');
				$tot_pay = number_format(array_sum($tab->collFieldVal["Total"]),2,'.','');
				$tot_pay_gst = number_format(array_sum($tab->collFieldVal["Total (incl. GST)"]),2,'.','');
				
				$tab->startNewLine();
					$tab->addLines("",1);
					$tab->addLine("Totals.:");
					$tab->addLine("");
					$tab->addLine($qty);
					$tab->addLine($qty_bdl);
					//$tab->addLineWithStyle("BUYER GEN. INV.-IRD APPR.","",3);
					//$tab->addLineWithStyle("GST NO: 24-992-802","",2);
					//$tab->addLineWithStyle("DATE: ".date('d/m/Y'),"",2);
					$tab->addLines("",3);
					$tab->addLine($dist_amt);
					$tab->addLine($sdist_amt);
					$tab->addLine($contr_amt);
					$tab->addLine($bdl_amt);
					$tab->addLine($tot_pay);
					$tab->addLine($tot_pay_gst);
				$tab->stopNewLine();					
						
				$tab->startNewLine();
					$tab->addLineWithStyle("<br />".$comment2,"sql_comment_line_bg0",11);
				$tab->stopNewLine();				
				
				$tab->stopTable();
?>
				<hr class="pagebreak_after">
<?				
			}
		}				//foreach dist_list as dist	
?>
		<div>
			<h3 class="weekly_head_h2">Distributor Totals</h3>
		</div>
<?
			$qry = "SELECT 
				operator.company AS Name,
				SUM(IF(job_route.dest_type<>'bundles',amount,0)) AS Circ,
				SUM(IF(job_route.dest_type='bundles',amount,0)) AS Bundles,
				ROUND(SUM(IF(job_route.dest_type<>'bundles',amount*(job.dist_rate+(subdist_rate_red)*job.subdist_rate+(job.contr_rate+folding_fee+job.premium)),0)),2) AS 'Amount Circ',
				ROUND(SUM(IF(job_route.dest_type='bundles',(amount*bundle_price),0)),2) AS 'Amount Bundles',
				
				ROUND(SUM(IF(job_route.dest_type<>'bundles',amount*(job.dist_rate+(subdist_rate_red)*job.subdist_rate+(job.contr_rate+folding_fee+job.premium)),0))+
					SUM(IF(job_route.dest_type='bundles',amount*bundle_price,0)),2) AS 'Total',
				ROUND(".(1+$GST_CIRCULAR)."*(SUM(IF(job_route.dest_type<>'bundles',amount*(job.dist_rate+(subdist_rate_red)*job.subdist_rate+(job.contr_rate+folding_fee+job.premium)),0))+
					SUM(IF(job_route.dest_type='bundles',amount*bundle_price,0))),2) AS 'Total (incl. GST)'
				
			FROM job
			LEFT JOIN job_route
			ON job.job_id=job_route.job_id
			LEFT JOIN route
			ON route.route_id=job_route.route_id
			LEFT JOIN operator
			ON operator.operator_id=job_route.dist_id
			WHERE year(delivery_date)='$year' 
				AND month(delivery_date)='$month'			
				#AND job.finished<>'Y'
				AND job.cancelled<>'Y'
				AND job_route.dist_id NOT IN (812,590)				
			GROUP BY job_route.dist_id";

			
			$tab  = new MySQLTable("rep_revenue.php",$qry);
			
			$tab->collField["Circ"]=true;
			$tab->collField["Bundles"]=true;
			$tab->collField["Amount Circ"]=true;
			$tab->collField["Amount Bundles"]=true;
			$tab->collField["Total"]=true;
			$tab->collField["Total (incl. GST)"]=true;
			
			$tab->hasEditButton=false;
			$tab->hasDeleteButton=false;
			$tab->hasAddButton=false;
			$tab->startTable();		
			$tab->writeTable();		
			
			$Circ = number_format(array_sum($tab->collFieldVal["Circ"]),0,'.','');
			$Bundles = number_format(array_sum($tab->collFieldVal["Bundles"]),0,'.','');
			$AntCirc = number_format(array_sum($tab->collFieldVal["Amount Circ"]),2,'.','');
			$AmtBundles = number_format(array_sum($tab->collFieldVal["Amount Bundles"]),2,'.','');
			$AmtCircGST = number_format(array_sum($tab->collFieldVal["Total"]),2,'.','');
			$AmtBundlesGST = number_format(array_sum($tab->collFieldVal["Total (incl. GST)"]),2,'.','');
			
			$tab->startNewLine();
				$tab->addLineWithStyle("Total","sql_extra_line_text_grey");
				$tab->addLineWithStyle($Circ,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($Bundles,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($AntCirc,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($AmtBundles,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($AmtCircGST,"sql_extra_line_number_grey");
				$tab->addLineWithStyle($AmtBundlesGST,"sql_extra_line_number_grey");
			$tab->stopNewLine();						
			$tab->stopTable();					
	}//	if($year && $month && $distributor)
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


function split_weight_client_price(){
	$qry = "SELECT * 
			FROM client_price";
	$res = query($qry);
	while($cp = mysql_fetch_object($res)){
		$buffer = explode('-',$cp->weight);
	
		if(!isset($buffer[1])){
			$buffer[1] = $buffer[0];
		}
		
		$from 	= intval($buffer[0]);
		$to 	= intval($buffer[1]);
		if(is_numeric($from) && is_numeric($to)){
			if(!$buffer[0]) $buffer[0]=0;
			if(!$buffer[1]) $buffer[1]=0;
			$qry = "UPDATE client_price SET from_weight = ".$from.",to_weight = ".$to." WHERE client_price_id='$cp->client_price_id'";
			query($qry); 	
		}
		else{
			$qry = "UPDATE client_price SET from_weight = 0,to_weight = 0";
			query($qry); 	
		}
	}
}

if($report=="rep_rate_discr" && $month && $year){
	$start_date = $year.'-'.$month.'-01';
	$end_date = $year.'-'.$month.'-31';
	split_weight_client_price();
	
	switch($choice){
		case "job":
			$extra_title = "Payouts as per Template";
			$num_cols = 4;
			$num_cols_job = 9;
			
			$on = "AND (
						dist_rate <> pa_dist
						OR subdist_rate <> pa_sdist
						OR contr_rate <> pa_cont
					)";
			
			$select = "
						
						IF(dist_rate<>pa_dist,
							CONCAT('<font color=\'red\'>',dist_rate,'</font>'),
							pa_dist)
								AS `Dist`,
						
						IF(subdist_rate<>pa_sdist,
							CONCAT('<font color=\'red\'>',subdist_rate,'</font>'),
							subdist_rate)
								AS `S/Dist`,
						
						IF(contr_rate<>pa_cont,
							CONCAT('<font color=\'red\'>',contr_rate,'</font>'),
							contr_rate)
								AS `Contr`,
						client_price.weight AS `Weight`,
				        pa_dist AS `Dist`,
				        pa_sdist AS `S/Dist`,
				        pa_cont AS `Contr`
						
			";
			break;
		case "rate":
			$extra_title = "Standard Rates as per Template";
			$num_cols = 7;
			$num_cols_job = 7;
			
			$on = " AND rate NOT IN (pr_u_1,pr_u_2,pr_u_3,pr_u_4,pr_u_5,pr_u_6) ";
			
			$select = "
				
				IF(rate NOT IN (pr_u_1,pr_u_2,pr_u_3,pr_u_4,pr_u_5,pr_u_6),
							CONCAT('<font color=\'red\'>',rate,'</font>'),
							rate) 
								AS Rate,
				client_price.weight AS `Weight`,
				IF(rate<>pr_u_1 AND pr_u_1>0,
					CONCAT('<font color=\'red\'>',pr_u_1,'</font>'),
					pr_u_1)
						AS `Rate 1`,
				IF(rate<>pr_u_2 AND pr_u_2>0,
					CONCAT('<font color=\'red\'>',pr_u_2,'</font>'),
					pr_u_2)
						AS `Rate 2`,
				IF(rate<>pr_u_3 AND pr_u_3>0,
					CONCAT('<font color=\'red\'>',pr_u_3,'</font>'),
					pr_u_3)
						AS `Rate 3`,
				IF(rate<>pr_u_4 AND pr_u_4>0,
					CONCAT('<font color=\'red\'>',pr_u_4,'</font>'),
					pr_u_4)
						AS `Rate 4`,
				IF(rate<>pr_u_5 AND pr_u_5>0,
					CONCAT('<font color=\'red\'>',pr_u_5,'</font>'),
					pr_u_5)
						AS `Rate 5`,
				IF(rate<>pr_u_6 AND pr_u_6>0,
					CONCAT('<font color=\'red\'>',pr_u_6,'</font>'),
					pr_u_6)
						AS `Rate 6`
			";
			break;
	}
	
?>
				<div class="weekly_head">
					<div class="weekly_logo"><img src="images/coural_logo.jpg" width="71" height="38" /></div>				
					<h3 class="weekly_head_h2"><?=$title?></h3>
				</div>				
			
<?		
		
	$qry = "SELECT job_no AS Job,
					publication as Publication,
					client.name AS Client,
					/*invoice_no AS Invoice,
					delivery_date AS Date,*/
					(SELECT SUM(amount) FROM job_route WHERE job_route.job_id=job.job_id) AS Qty, 
					IF(inc_linehaul = '' || inc_linehaul IS NULL, 'N/A', inc_linehaul) 
											AS `L/H`,
					ROUND(job.weight,0) AS Weight,	
					
					$select
					
					
			FROM job
			LEFT JOIN client
			ON client.client_id=job.client_id
			LEFT JOIN client_price
			ON client_price.client_id=job.client_id
					AND job.weight BETWEEN from_weight AND to_weight 
 					$on
			WHERE delivery_date BETWEEN '$start_date' AND '$end_date'
				AND job.weight>0
				AND client_price.to_weight>0
			ORDER BY Publication";
	
	$tab  = new MySQLTable("rep_revenue.php",$qry);
	$tab->hasEditButton=false;
	$tab->hasDeleteButton=false;
	$tab->hasAddButton=false;
	$tab->startTable();
		$tab->startNewLine();
			$tab->addLineWithStyle("Job Details","sql_extra_head",$num_cols_job);
			$tab->addLineWithStyle($extra_title,"sql_extra_head",$num_cols);
		$tab->stopNewLine();
		$tab->writeTable();
	$tab->stopTable();
}

if($report=="rep_payout_breakdown_by_dist2"){
	if($dist_id)
		print_op2($dist_id,get_ops_for_dist_from_job($dist_id,$year,$month),$month,$year,$comment);
}
?>
