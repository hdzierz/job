<script language="javascript">
	function checkfloat(strString,field){
		   //  check for valid numeric strings	
		   var strValidChars = "0123456789.";
		   var strChar;
		   var blnResult = true;
		
		   if (strString.length == 0) field.value="";
		
		   //  test strString consists of valid characters listed above
		   for (i = 0; i < strString.length && blnResult == true; i++)
			  {
			  strChar = strString.charAt(i);
			  if (strValidChars.indexOf(strChar) == -1)
				 {
				 blnResult =  field.value="";
				 // substr(field.value,0,strlen(field.value)-1)
				 }
			  }
		   return blnResult;
	}

	function eventTrigger (e) {
		if (! e)
			e = event;
		return e.target || e.srcElement;
	}
	
	function setCheckboxOn (e,minnum,maxnum) {
		var obj = eventTrigger (e);
		for(i=minnum;i<=maxnum;i++){
			var check = document.getElementById &&
							document.getElementById ('check['+i+']');
			if (check)
				check.checked  = true;
		}
		return true;
	}
	function setCheckboxOff (e,minnum,maxnum) {
		var obj = eventTrigger (e);
		for(i=minnum;i<=maxnum;i++){
			var check = document.getElementById &&
							document.getElementById ('check['+i+']');
			if (check)
				check.checked  = false;
		}
		return true;
	}	
	

	
</script>
<!-- Ajax Scripts -->
<script src="javascripts/ajax.js" type="text/javascript" language="javascript"></script>

<?
//send_operator_mail("JOB DROP OFF DETAILS","temp_do_det/2011_03_10_09_28_16/","dropoff_details_Lyndsays Distributors_2011_03_10_09_28_16.csv",162);
//die();
//////////////////////////////////////////////////////////
// SECTION INVOICE										//
//////////////////////////////////////////////////////////

// Sometimes teh job id comes in as record (from MySQLTable). 
if(!$job_id)
	$job_id=$record;

function get_pi_pdf($pdf, $job,$client, $island){
	$cont = explode("\n",$client->contact_details);
	
	$line_height = 10;
	$address_font_size = 19;
	
	$pdf->SetFont('Helvetica','B',18);
	$title = "PRINTER INFORMATION";
	$wl = 135;
	$pdf->Cell($wl,$line_height,$title,'LTR',0,'L');
	$str = "INSTRUCTIONS FOR COURAL";
	$wr = 135;
	$pdf->Cell($wr,$line_height,$str,'TR',1,'L');
	$pdf->Cell($wl,$line_height,'','LBR',0,'L');
	$pdf->Cell($wr,$line_height,'DROP OFF POINTS','BR',1,'L');
	
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wl,$line_height,'LODGEMENT SITE ADDRESS:','LR',0,'L');
	
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('Job Name: ');
	$pdf->Cell($width,$line_height,'Job Name: ','',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr-$width,$line_height,' '.$job->publication,'R',1,'L');
	
	$pdf->Cell($wl,$line_height,'','LR',0,'L');
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	$pdf->SetFont('Helvetica','B',$address_font_size);
	$pdf->Cell($wl,$line_height,$client->name,'LR',0,'L');
	$pdf->SetFont('Helvetica','',15);
	
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('Coural Job No.: ');
	$pdf->Cell($width,$line_height,'Coural Job No.: ','',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr-$width,$line_height,' '.$job->job_no,'R',1,'L');
	
	if(isset($cont[0])) $str = $cont[0]; else $str = '';
	$pdf->SetFont('Helvetica','B',$address_font_size);
	$pdf->Cell($wl,$line_height,$str,'LR',0,'L');
	
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('PMP Job No.: ');
	$pdf->Cell($width,$line_height,'PMP Job No.: ','',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr-$width,$line_height,' '.$job->pmp_job_no,'R',1,'L');
	
	if(isset($cont[1])) $str = $cont[1]; else $str = '';
	$pdf->SetFont('Helvetica','B',$address_font_size);
	$pdf->Cell($wl,$line_height,$str,'LR',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	
	if(isset($cont[2])) $str = $cont[2]; else $str = '';
	$pdf->SetFont('Helvetica','B',$address_font_size);
	$pdf->Cell($wl,$line_height,$str,'LR',0,'L');
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	
	if(isset($cont[3])) $str = $cont[3]; else $str = '';
	$pdf->SetFont('Helvetica','B',$address_font_size);
	$pdf->Cell($wl,$line_height,$str,'LR',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('Lodgement Date: ');
	$pdf->Cell($width,$line_height,'Lodgement Date: ','L',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wl-$width,$line_height,' '.date('d F Y',strtotime($job->lodge_date)),'R',0,'L');
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	
	if($island == "NI") $tot_drop_qty = number_format($job->ni_drop_total,0);
	else if($island == "SI") $tot_drop_qty = number_format($job->si_drop_total,0);
	else $tot_drop_qty = number_format($job->ni_drop_total+$job->si_drop_total,0);
	
	
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('Total Drop Quantity: ');
	$pdf->Cell($width,$line_height,'Total Drop Quantity: ','L',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wl-$width,$line_height,' '.$tot_drop_qty,'R',0,'L');
	
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('Total: ');
	$pdf->Cell($width,$line_height,'Total: ','',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr-$width,$line_height,' '.$job->invoice_qty,'R',1,'L');
	
	$pdf->Cell($wl,$line_height,'# Circulars in this box = ____________','LR',0,'L');
	$pdf->SetFont('Helvetica','B',15);
	$width = $pdf->GetStringWidth('Distribution Start Date: ');
	$pdf->Cell($width,$line_height,'Distribution Start Date: ','',0,'L');
	$pdf->SetFont('Helvetica','',15);
	$pdf->Cell($wr-$width,$line_height,' '.date('d F Y',strtotime($job->delivery_date)),'R',1,'L');
	
	$pdf->Cell($wl,$line_height,'','LR',0,'L');
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	
	$pdf->Cell($wl,$line_height,'This box # ________ of ________','LR',0,'L');
	$pdf->Cell($wr,$line_height,'','R',1,'L');
	
	$pdf->Cell($wl,$line_height,'','LRB',0,'L');
	$pdf->Cell($wr,$line_height,'','RB',1,'L');
	
	return $pdf;
}	
	
if($action == "print_pmp_pi"){
	$pdf = new FPDF('L');
	
	$res = query("SELECT * FROM job WHERE job_id = $job_id");
	
	$job = mysql_fetch_object($res);
	
	$res = query("SELECT * FROM client WHERE client_id=".$job->hauler_ni_id);
	$client_ni = mysql_fetch_object($res);

	$res = query("SELECT * FROM client WHERE client_id=".$job->hauler_si_id);
	$client_si = mysql_fetch_object($res);
	
	if(($client_si && $client_ni) && $client_ni->client_id != $client_si->client_id){
		$pdf->AddPage();
		$pdf = get_pi_pdf($pdf, $job, $client_ni, "NI");
		$pdf->AddPage();
		$pdf = get_pi_pdf($pdf, $job, $client_si, "SI");
	}
	else{
		$pdf->AddPage();
		$pdf = get_pi_pdf($pdf, $job, $client_ni, "BOTH");
	}
		
	if($pdf){
		$fn = "tmp/pdf_{$job->job_no}.pdf";
		$pdf->Output($fn);
		echo "<a href='$fn'>Download</a> <a href='proc_job.php?action=edit&pub=$pub&client=$client&job_id=$job_id'>Back</a>";
	}	
	
}
// This form aims at attaching a job to a reference job. You can select a dest_type.
if($action=="attach_job"){
?>
	<form name="add_dest" action="proc_job.php" method="post">
		<table>
			<tr>
				<td>Give a new type:</td>
				<td>
<?
					$sel_farm_typ = new Select("alt_dest_type");
					if($dest_type)
						$sel_farm_typ->setOptionIsVal($dest_type);
					else
						$sel_farm_typ->setOptionIsVal("num_total");
					if($action=="edit_job"){
						$sel_farm_typ->isDisabled=true;
					}
					$sel_farm_typ->selectWidth=11;
					$sel_farm_typ->start();
					$sel_farm_typ->addOption("num_total","Total");
					$sel_farm_typ->addOption("num_farmers","Farmers");
					$sel_farm_typ->addOption("num_lifestyle","Lifestyle");
					$sel_farm_typ->addOption("num_dairies","Dairy");
					$sel_farm_typ->addOption("num_sheep","Sheep");
					$sel_farm_typ->addOption("num_beef","Beef");
					$sel_farm_typ->addOption("num_sheepbeef","Sheep/Beef");
					$sel_farm_typ->addOption("num_dairybeef","Dairy/Beef");
					$sel_farm_typ->addOption("num_hort","Hort");
					$sel_farm_typ->stop();				
?>			
				</td>
				<td><input type="submit" name="submit" value="Add" /></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="do_attach_job" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />
	</form>
<?	
}


//////////////////////////////////////////////////////////
// SECTION JOB											//
//////////////////////////////////////////////////////////


// This form aims at letting the user edit booked route information lke its numbers or you may add a note.
if($action=="edit_line"){
	$qry = "SELECT * FROM job_route WHERE job_route_id=$record";
	$res_routes = query($qry);
	$line = mysql_fetch_object($res_routes);
	$region = get("route","region","WHERE route_id=$line->route_id");
	$area = get("route","area","WHERE route_id=$line->route_id");
	$code = get("route","code","WHERE route_id=$line->route_id");
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");
	
	$do_address = get("operator","do_address","WHERE operator_id=$line->doff");
	$do_city = get("operator","do_city","WHERE operator_id=$line->doff");
	$deliv_notes = get("operator","deliv_notes","WHERE operator_id=$line->doff");
	$operator_id = get("operator","operator_id","WHERE operator_id=$line->doff");
	
	$qry = "SELECT * FROM job_route WHERE route_id=$line->route_id AND job_id='$alt_job_id '";
	$res_routes2 = query($qry);
	$line2 = mysql_fetch_object($res_routes2);
	
?>
	<form name="editline" action="" method="get">
		<table>
			<tr>
				<td colspan="3" align="center"><b>Give new quantity, version and a comment.</b></td>
			</tr>
			<tr>
				<td>Quantity:</td>
<?
				if($alt_job_id){
?>				
					<td>Quantity2:</td>
<?
				}
?>
				<td>Version:</td>
<?	
				if($line->dest_type=='bundles'){
?>					
					<td>Bundle Price:</td>
<?
				}
?>					
				<td>Comment:</td>
				<td>Drop Off:</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="amount" value="<?=$line->amount?>" />
				</td>
<?
				if($alt_job_id){
?>
					<td valign="top">
						<input type="text" name="amount2" value="<?=$line2->amount?>" />
					</td>	
<?
				}
?>			
				<td valign="top">
					<input type="text" name="version" value="<?=$line->version?>" />
				</td>				
<?	
				if($line->dest_type=='bundles'){
?>				
					<td valign="top">
						<input type="text" name="bundle_price" value="<?=$line->bundle_price?>" />
					</td>				
<?
				}
?>				
				<td>
					<textarea name="notes" cols="30" rows="10"></textarea>
				</td>
				<td valign="top">
					<a href="admin_address.php?record=<?=$operator_id?>"><?=$do_address?>, <?=$do_city?></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<input type="submit" name="submit" value="Save" />
					<input type="submit" name="cancel" value="Cancel" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="save_line" />
		<input type="hidden" name="job_id" value="<?=$job_id?>" />
		<input type="hidden" name="job_route_id" value="<?=$record?>" />
	</form>
<?	

}
// Show Drop off details for the job with aditinal pagebreaks
if($action=="show_do_details_with_pagebreak"){
	if($export)
		show_do_details_with_pagebreak($job_id,true);
	else
		show_do_details_with_pagebreak($job_id,false);
}

// Show Drop off details for the job.
if($action=="show_do_details"){
	if($export)
		show_do_details($job_id,true);
	else if($send_report_id){
		show_do_details_from_send_out($send_report_id);
	}
	else
		show_do_details($job_id,false);
}

// Will allow the user to send a PDF version of the DO details to the line haulers
if($action=="show_do_details_send"){
	
	show_do_details_send($job_id,$choice);
}

// Will allow the user to send a PDF version of the job details to the line distributors
if($action=="show_job_details_send"){
	show_job_details_send($job_id,$choice);
}

// Show job details
if($action=="show_job_details"){
	if($export)
		show_job_details($job_id,true,$choice);		
	else
		show_job_details($job_id,false,$choice);		
}

// Job edit screen. Lets the user book routes to a job
if($action=="" || !isset($action)||$action=="edit"){
	if(!$client) $client="null";
	else $client = "'$client'";
	
	if($first_entry){
		if($pub) $pub = get("job","publication","WHERE job_id='$job_id'");
		$pub = addslashes($pub);
		$qry = "UPDATE current_job_screen SET publication='$pub',client_id=$client, job_id='$job'";
		query($qry);
	}

	
	$alt_job_id = get("job","alt_job_id","WHERE alt_job_id='$job_id'");
	if($alt_job_id){
		$action="edit_job";
	}
	else{
		$qry = "SELECT * FROM job 
				WHERE job.job_id='$job_id'";
		$res = query($qry);
		$obj = mysql_fetch_object($res);
		
		if($obj->is_ioa=='Y') $delivery_date="IOA";
		else{
			$delivery_date = date("d M Y",strtotime($obj->delivery_date));
		}
		
		$max_line = get_max("job_route","job_route_id","WHERE job_id='$job_id'","");
		$min_line = get_min("job_route","job_route_id","WHERE job_id='$job_id'","");
		$notes 	  = nl2br(get("job","comments","WHERE job_id='$job_id'"));
		$job_no = get("job","job_no","WHERE job_id='$job_id'");
		
		$client_name = get("client","name","WHERE client_id='$obj->client_id'");
		
		$name  = get("client","name","WHERE client_id='$obj->client_id'");
		$phone = get("client","phone","WHERE client_id='$obj->client_id'");
		
		set_alt_do_contractors($job_id);
		
		$show_invoice_date = date("d M Y",strtotime($obj->invoice_date));
		$show_change_date  = date("d M Y",strtotime($obj->change_date));
		
		$hauler_ni = get("client","name","WHERE client_id='$obj->hauler_ni_id'");
		$hauler_si = get("client","name","WHERE client_id='$obj->hauler_si_id'");
		
		if(!$hauler_ni) $hauler_ni = $obj->hauler;
		if(!$hauler_si) $hauler_si = $obj->hauler;
		
	?>
			<script language="javascript">
				function set_Button_on(){
					var result = "<input name=\'submit1\' value=\'Add Route(s)\' type=\'submit\' />";
					document.getElementById('add_route_wrap').innerHTML = result;  
				}
				function set_Button_off(){
					var result = "<input disabled name=\'submit1\' value=\'Add Route(s)\' type=\'submit\' />";
					document.getElementById('add_route_wrap').innerHTML = result;  
				}			
			</script>
			<div id="job_header">
				<div id="edit_job_details">
	<?		
					write_edit_job_details($job_id,$island,$region,$area,$code,$dest_type);	
	?>
				</div>						
				<table cellpadding="5">
					<tr>
					<?
						if($obj->is_regular=='Y') echo "REGULAR";
						else echo "CASUAL";
					?>
					</tr>
					<tr>
						<td>Client: </td>
						<td><?=$client_name?></td>
						<td>Publication: </td>
						<td><?=$obj->publication?></td>					
					</tr>
					<tr>
						<td>Job. #:</td>
						<td><?=$job_no?></td>
						<td>Invoice #:</td>
						<td><?=$obj->invoice_no?></td>
					</tr>
					<tr>
						<td>Delivery Date:</td>
						<td><?=$delivery_date?></td>
						<td>Booking Date:</td>
						<td><?=$show_invoice_date?></td>
					</tr>				
					<tr>
						<td>Last Changed:</td>
						<td><?=$show_change_date?></td>
						<td>NI Linehaul:</td>
						<td><?=$hauler_ni?></td>
						<td>SI Linehaul:</td>
						<td><?=$hauler_si?></td>
					</tr>
					<tr>
						<td>Contact Name:</td>
						<td><?=$name?></td>
						<td>Phone:</td>
						<td><?=$phone?></td>
					</tr>				
				</table>
				
			</div>
			<div id="job_add_route">
	<?		
				if(!$type) $type = get("job","dest_type","WHERE job_id=$job_id");
				write_addroute_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route);	
	?>
			</div>
			<div id="job_add_bundle">
	<?		
				if(!$type) $type = get("job","dest_type","WHERE job_id=$job_id");
				write_addbundle_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route);	
	?>
			</div>
		
			<div id="job_alter_route">
	
	<!--		  <table>
					<tr>
						<td>Select ALL:</td>
						<td align="left">
							<span class="set_button" onClick="return setCheckboxOn(event,<?=$min_line?>,<?=$max_line?>)">All</span>-->
							<!--<span class="set_button" onClick="return testR()">All</span>-->
	<!--					</td>
					</tr>
					<tr>
						<td>Select NONE:</td>
						<td align="left">
							<span class="set_button" onClick="return setCheckboxOff(event,<?=$min_line?>,<?=$max_line?>)">None</span>
						</td>					
				</tr>
			 </table>-->
				<form name="alter" action="proc_job.php" method="get">
					<table class="form">
						<tr>
							<td>Select:
								<span class="set_button" onClick="return setCheckboxOn(event,<?=$min_line?>,<?=$max_line?>)">All</span>
								<span class="set_button" onClick="return setCheckboxOff(event,<?=$min_line?>,<?=$max_line?>)">None</span>
							</td>					
							<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td>Reduce Numbers:</td>
							<td><input style="text-align:right " name="reduce_amt" type="text" value="0" /></td>
							<td><input type="submit" name="sub_reduce" value="Reduce" /></td>
				
							<td>Round Numbers:</td>
							<td><input type="submit" name="sub_round" value="Round" /></td>
						</tr>
					</table>
				<input type="hidden" value="<?=$action?>" name="dest" />
				<input type="hidden" value="<?=$job_id?>" name="job_id" />
				<input type="hidden" value="alter_job" name="action" />
				</form>
	
			 </div>
			 <div id="job_show_route">
	<!--		 	<table>
					<tr>
						<td><input type="button" name="show" value="Show Selected Routes" onClick="window.location.href='proc_job.php?job_id=<?=$job_id?>&show=1'" /></td>
						<td><input type="button" name="show" value="Close Table" onClick="window.location.href='proc_job.php?job_id=<?=$job_id?>'" /></td>
					</tr>
				</table>-->
	<?
	//			if($show){
					show_table($job_id);		
	//			}
	?>
				<h3>Notes:</h3>
				<p><?=$notes?></p>
			</div>
	<?		
	}//if alt_job_id
}

function get_add_rates_qry(){
	return "(IF(job.add_folding_to_invoice='Y',job.folding_fee,0) + IF(job.add_premium_to_invoice='Y',job.premium,0))";
}

// Creates a new job or the user may change general job info
if($action=="edit_job"||$action=="new_job"){
	if($job_id){

		// Calculates margin 
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
				   round(job.bbc_rate,4) AS 'BBC',
				   job.Dist,
				   job.inc_linehaul,
				   job.SubDist AS 'Sub Dist',
				   job.Cont,
				   @cust2 := ROUND((job.invoice_qty)*job.Normal+job.qty_bbc*job.bbc_rate+@bundles ,2)
						AS 'Cust.',
				  /* job.fuel_surcharge_fact AS 'F/Surch.',
				   job.discount_fact,
				   job.folding_fee,*/
				   @cust := ROUND(@cust2 * (1+job.fuel_surcharge_fact-discount_fact),2) 
						AS 'cust_inc',
				   @coural := round((job.Coural-job.qty_bbc-IF(job.mailings IS NOT NULL,job.mailings,0))*(job.Dist+job.SubDist+job.Cont)+@bundles,2)
						AS 'Coural',
				   round((job.Coural-job.qty_bbc-IF(job.mailings IS NOT NULL,job.mailings,0))*(job.Dist+job.SubDist+job.Cont)+@bundles,2)
						AS 'coural_dollars',
				   @freight := round(job.lbc_charge+job.freight_charge+job.lbc_charge_bbc,2)
						  AS 'Freight',
				   @margin := round(@cust-@coural-@freight,2) AS '$',
				   IF(@cust >0,
					  round(100*@margin/@cust,1),
					  0)
					  AS 'margin_percent'   
				   
			FROM
			(
			SELECT job.job_id AS 'Record', 
				   job.invoice_no,
				   job.job_no,
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
							amount*job_route.bundle_price,0)
						)
						AS 'Bundles2',						
				   job.qty_bbc,
				   round(job.rate+".get_add_rates_qry().",4) AS 'Normal',
				   round(job.rate_bbc,4) AS 'bbc_rate',
				   round(job.dist_rate,4) AS 'Dist',
				   round(job.subdist_rate,4) AS 'SubDist',
				   round(job.contr_rate+job.folding_fee,4) AS 'Cont',
				   round(job.fuel_surcharge,1) AS fuel_surcharge,
				   round(job.fuel_surcharge/100,2) AS fuel_surcharge_fact,
				   round(job.discount/100,2) AS discount_fact,
				   job.inc_linehaul,
				   job.discount,
				   ".get_add_rates_qry().",
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
			WHERE job_route.dist_id<>'590'
					AND job.job_id='$job_id'
			GROUP BY job.job_no

			) AS job
			ORDER BY Client,Publication
			";
			
		
		$res_margin = query($qry,0);
		$margin = mysql_fetch_object($res_margin);
	}


	$today = date("Y-m-d");
	$alt_job_id = get("job","alt_job_id","WHERE alt_job_id='$job_id'");
	if($dest=="job" || $alt_job_id){
		$qry 	= "	SELECT job.*
					FROM job 
					WHERE job_id=$job_id";
		$res 	= query($qry);
		$job = mysql_fetch_object($res);
		
		$purchase_no 		= $job->purchase_no;
		$invoice_no 		= $job->invoice_no;
		$job_no 			= $job->job_no;
		$pmp_job_no 		= $job->pmp_job_no;
		$lodge_date			= $job->lodge_date;
		$foreign_job_no		= $job->foreign_job_no;
		$delivery_date 		= $job->delivery_date;
		$is_ioa 			= $job->is_ioa;
		$client_id			= $job->client_id;
		$publication 		= $job->publication;
		$qty_bbc			= $job->qty_bbc;
		$weight 			= $job->weight;
		$rate 				= $job->rate;
		$inc_linehaul		= $job->inc_linehaul;
		$rate_bbc			= $job->rate_bbc;
		$hauler				= $job->hauler;
		$lbc_charge 		= $job->lbc_charge;
		$lbc_charge_bbc		= $job->lbc_charge_bbc;
		$freight_charge 	= $job->freight_charge;		
		$cust_ref 			= $job->cust_ref;
		$dist_rate			= $job->dist_rate;
		$subdist_rate 		= $job->subdist_rate;
		$contr_rate 		= $job->contr_rate;
		$folding_fee 		= $job->folding_fee;
		$discount 			= $job->discount;
		$fuel_surcharge 	= $job->fuel_surcharge;
		$comments 			= $job->comments;
		$show_comments 		= $job->show_comments;
		$dest_type			= $job->dest_type;
		$invoice_qty		= $job->invoice_qty;
		$regular			= $job->is_regular;
		$quote				= $job->is_quote;
		$hauler_ni_id		= $job->hauler_ni_id;
		$hauler_si_id		= $job->hauler_si_id;
		$ni_drop_total		= $job->ni_drop_total;
		$si_drop_total		= $job->si_drop_total;
		$desc_bbc			= $job->desc_bbc;
		$add_folding_to_invoice = $job->add_folding_to_invoice;
		$premium			= $job->premium;
		$add_premium_to_invoice = $job->add_premium_to_invoice;
		
		$qry = "SELECT SUM(IF(dest_type='bundles',job_route.amount,0)) AS sum_bundles,
				bundle_price
			FROM job_route
			WHERE job_id=$job_id 
				AND bundle_price>0
			GROUP BY job_id,bundle_price";
		$res_bundles = query($qry);
	}
	else if($dest=="template"){
		$qry 	= "	SELECT job_temp.*
					FROM job_temp 
					WHERE job_id=$job_id";
		$res 	= query($qry);
		$job = mysql_fetch_object($res);
		
		$purchase_no 		= $job->purchase_no;
		$invoice_no 		= $job->invoice_no;
		$job_no 			= $job->job_no;
		$pmp_job_no 		= $job->pmp_job_no;
		$lodge_date			= $job->lodge_date;
		$foreign_job_no		= $job->foreign_job_no;
		$delivery_date 		= $job->delivery_date;
		$is_ioa 			= $job->is_ioa;
		$client_id			= $job->client_id;
		$publication 		= $job->publication;
		$qty_bbc			= $job->qty_bbc;
		$weight 			= $job->weight;
		$rate 				= $job->rate;
		$inc_linehaul		= $job->inc_linehaul;
		$rate_bbc			= $job->rate_bbc;
		$hauler				= $job->hauler;
		$lbc_charge 		= $job->lbc_charge;
		$lbc_charge_bbc		= $job->lbc_charge_bbc;
		$freight_charge 	= $job->freight_charge;		
		$cust_ref 			= $job->cust_ref;
		$dist_rate			= $job->dist_rate;
		$subdist_rate 		= $job->subdist_rate;
		$contr_rate 		= $job->contr_rate;
		$folding_fee 		= $job->folding_fee;
		$discount 			= $job->discount;
		$fuel_surcharge 	= $job->fuel_surcharge;
		
		$hauler_ni_id		= $job->hauler_ni_id;
		$hauler_si_id		= $job->hauler_si_id;
		$ni_drop_total		= $job->ni_drop_total;
		$si_drop_total		= $job->si_drop_total;
		$desc_bbc			= $job->desc_bbc;
		$add_folding_to_invoice = $job->add_folding_to_invoice;
		$premium			= $job->premium;
		$add_premium_to_invoice = $job->add_premium_to_invoice;
		
		$comments 			= $job->comments;
		$show_comments 		= $job->show_comments;
		$dest_type			= $job->dest_type;
		$invoice_qty		= $job->invoice_qty;
		$regular			= $job->is_regular;
		$quote				= $job->is_quote;
		
		$qry = "SELECT SUM(IF(dest_type='bundles',job_route.amount,0)) AS sum_bundles,
				bundle_price
			FROM job_route
			WHERE job_id=$job_id 
				AND bundle_price>0
			GROUP BY job_id,bundle_price";
		$res_bundles = query($qry);
	}
	
	if(!$delivery_date) 	$delivery_date = $today; 
	if(!$lodge_date)		$lodge_date    = date('Y-m-d',strtotime($delivery_date." -5 days"));
	if(!$rate_bundle) 		$rate_bundle   = "1.00"; 
	if(!$dist_rate) 		$dist_rate     = "0.0000"; 
	if(!$subdist_rate) 		$subdist_rate  = "0.0000"; 
	if(!$contr_rate) 		$contr_rate    = "0.0000"; 
	if(!isset($folding_fee)) $folding_fee  = "0.00"; 
	if(!$discount) 			$discount      = "0.0";
	if(!$fuel_surcharge) 	$fuel_surcharge= "0.0"; 
	
	if(!$rate) 				$rate="0.0000"; 
	
	if(!$ni_drop_total) $ni_drop_total = "0.000";
	if(!$si_drop_total) $si_drop_total = "0.000";
	
	if(!$desc_bbc) 			$desc_bbc=""; 
	if(!$add_folding_to_invoice) 			$add_folding_to_invoice="N"; 
	if(!$premium) 			$premium="0.0000"; 
	if(!$premium_sell) 			$premium_sell="0.0000"; 
	if(!$add_premium_to_invoice) 			$add_premium_to_invoice="N"; 

	$lbc_charge 	= sprintf("%.2f",$lbc_charge);
	$lbc_charge_bbc	= sprintf("%.2f",$lbc_charge_bbc);
	$freight_charge = sprintf("%.2f",$freight_charge);
	$weight		 	= sprintf("%d",$weight);
	$invoice_qty	= sprintf("%d",$invoice_qty);
	//$qty_bbc		= sprintf("%d",$qty_bbc);
	
	if($regular=="true")
		$regular="Y";
	if($quote=="true")
		$quote="Y";		
	if($is_ioa=="true")
		$is_ioa="Y";		
	
	if($job->is_job_details_sent==1){
		$is_job_details_sent = date("Y-m-d / H:m",strtotime(get("send_report","date","WHERE jobs='$job->job_id' AND type = 'do_details' ORDER BY date DESC LIMIT 1")));
	}
	else{
		$is_job_details_sent = 'Not sent';
	}
	
	$contact_details  = get("client","contact_details","WHERE client_id='$client_id'");
	//$contact_name  = get("client","contact","WHERE client_id='$client_id'");
	//$contact_phone = get("client","phone","WHERE client_id='$client_id'");
	$card_id = get("client","card_id","WHERE client_id='$client_id'");
	
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<script language="javascript">
		function nl2br( str ) {
			// http://kevin.vanzonneveld.net
			// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   improved by: Philip Peterson
			// *     example 1: nl2br('Kevin\nvan\nZonneveld');
			// *     returns 1: 'Kevin<br/>\nvan<br/>\nZonneveld'
		 
			return str.replace(/([^>])\n/g, '$1<br />\n');
		}
		function setPrintComment(){
			var text = nl2br(document.getElementById("comment").value);
			document.getElementById("print_comment").innerHTML=text;
		}
		
		function sum_drop_tot(){
			var vni = parseFloat(document.getElementById("ni_drop_total").value);
			var vsi = parseFloat(document.getElementById("si_drop_total").value);
			
			document.getElementById("sum_drop_total").value = vni+vsi;
		}
	</script>
	<form action="proc_job.php" method="get" name="proc_job">
		<table style="font-size:0.9em; " id="invoice">
			<tr>
<?
				if($alt_job_id){
?>			
					<th colspan="5"><h1>Coural Job Details Attached Job</h1></th>
<?
				}
				else{
?>				
					<th colspan="5"><h1>Coural Job Details</h1></th>
<?
				}
?>				
			</tr>
				
			<tr>
				<td>Regular (Yes):</td>
				<td><input type="checkbox" name="regular" value="Y" <? if($regular=='Y'){?> checked <? }?> /></td>
				<td></td>
				<td>Quote (Yes):</td>
				<td><input type="checkbox" name="quote" value="Y" <? if($quote=='Y'){?> checked <? }?> /></td>
				<td></td>
				<!-- <td><b>PMP</b></td>-->
			</tr>
			<tr>
				<td>Purchase #:</td>
				<td><input type="text" name="purchase_no" onkeyup="get(this,'po_no_error','proc_job/get/check_po_no.php?job_id=<?=$job_id?>&no='+this.value);" value="<?=$purchase_no?>" /></td>
				<td>
					<span style="background-color:#EEEEEE;" id="po_no_error"></span>
				</td>
				<td>Invoice #:</td>
				<td><input type="text" name="invoice_no" value="<?=$invoice_no?>" /></td>		
				<td>&nbsp;</td>
										
			</tr>
			<tr>
				<td>Job #</td>
				<td><input style="background-color:#EEEEEE; color:#333333 " disabled type="text" name="job_no" value="<?=$job_no?>" /></td>
				<td></td>
				<td>Customer Link</td>
				<td><input type="text" name="card_id" value="<?=$card_id?>" /></td>		
				<td>&nbsp;</td>					
			</tr>
			<tr>
				<td>PMP Job #</td>
				<td><input type="text" name="pmp_job_no" value="<?=$pmp_job_no?>" /></td>
				<td></td>
				<td>Lodgement Date</td>
				<td>
<?				
					echo "<script>DateInput(\"lodge_date\", true, \"YYYY-MM-DD\",\"$lodge_date\")</script>";
?>					
				</td>	
				<td></td>			
				<td>&nbsp;</td>					
			</tr>
			<tr>
				<td>Delivery Date:</td>
				<td>
<?				
					echo "<script>DateInput(\"delivery_date\", true, \"YYYY-MM-DD\",\"$delivery_date\")</script>";
?>					
				</td>				
				<td></td>
				<td>NI Linehaul:</td>	
				<td>
<?
						$sel_name = new MySQLSelect("name","client_id","client","proc_job.php","proc_job","hauler_ni_id");	
						$sel_name->selectWidth=15;
						$sel_name->addSQLWhere("is_hauler",1);
						$sel_name->setOptionIsVal($hauler_ni_id);
						$sel_name->selectOnChange = "";

						$sel_name->startSelect();
						$sel_name->writeSelect();					
						$sel_name->stopSelect();
?>									
				</td>						
				<td>&nbsp;</td>
				<td>NI Drop Total</td>
				<td><input id="ni_drop_total" name="ni_drop_total" type="text" value="<?=$ni_drop_total?>" onkeyup="javascript:sum_drop_tot()" /></td>
			</tr>
			<tr>
				<td>Or IOA</td>
				<td><input name="is_ioa" value="Y" type="checkbox" <? if($is_ioa=='Y'){?> checked <? }?> /></td>
				<td></td>
				<td>SI Linehaul:</td>	
				<td>
<?
						$sel_name = new MySQLSelect("name","client_id","client","proc_job.php","proc_job","hauler_si_id");	
						$sel_name->selectWidth=15;
						$sel_name->addSQLWhere("is_hauler",1);
						$sel_name->setOptionIsVal($hauler_si_id);
						$sel_name->selectOnChange = "";

						$sel_name->startSelect();
						$sel_name->writeSelect();					
						$sel_name->stopSelect();
?>									
				</td>								
				<td></td>
				<td>SI Drop Total</td>
				<td><input id="si_drop_total" name="si_drop_total" type="text" value="<?=$si_drop_total?>" onkeyup="javascript:sum_drop_tot()" /></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td><!--Old Line Hauler:--></td>	
				<td><!--<input disabled name="is_ioa" value="<?=$hauler?>" type="text" />--></td>
				<td></td>
				<td>Total Check</td>
				<td><input id="sum_drop_total" name="sum_drop_total" type="text" value="<?=$ni_drop_total+$si_drop_total?>"  /></td>
			</tr>
			<tr>
				<td>Charge to:</td>
				<td>
<?
						$sel_name = new MySQLSelect("name","client_id","client","proc_job.php","proc_job","client_id");	
						$sel_name->selectWidth=15;
						$sel_name->onChangeAction=$action;
						$sel_name->setOptionIsVal($client_id);
						$sel_name->addOnChange("purchase_no");
						$sel_name->addOnChange("invoice_no");
						$sel_name->addOnChange("job_no");
						$sel_name->addOnChange("card_id");
						$sel_name->addOnChange("hauler");
						$sel_name->addOnChange("weight");
						$sel_name->addOnChange("delivery_date");
						$sel_name->addOnChange("dest_type");
						$sel_name->addOnChange("rate");
						$sel_name->addOnChange("inc_linehaul");
						$sel_name->addOnChange("rate_bbc");
						$sel_name->addOnChange("freight_charge");
						$sel_name->addOnChange("lbc_charge");
						$sel_name->addOnChange("lbc_charge_bbc");
						$sel_name->addOnChange("qty_bbc");
						$sel_name->addOnChange("dist_rate");
						$sel_name->addOnChange("subdist_rate");	
						$sel_name->addOnChange("contr_rate");	
						$sel_name->addOnChange("folding_fee");	
						$sel_name->addOnChange("fuel_surcharge");	
						//$sel_name->addOnChange("hauler_ni_id");	
						//$sel_name->addOnChange("hauler_si_id");	
						
						$sel_name->addOnChange("comments");		
						$sel_name->addOnChange("invoice_qty");	
						$sel_name->addOnChange("publication");	
						$sel_name->addOnChangeChecked("regular");
						$sel_name->addOnChangeChecked("is_ioa");
						
						$sel_name->addOnChangeChecked("quote");
						$sel_name->addOnChangeChecked("show_comments");
						$sel_name->addOnSimpleChange("job_id",$job_id);

						$sel_name->startSelect();
						$sel_name->writeSelect();					
						$sel_name->stopSelect();
?>					
				</td>	
				<td></td>				
				<td>Dest.-Type</td>
				<td>
<?
						$sel_farm_typ = new Select("dest_type");
						if($dest_type)
							$sel_farm_typ->setOptionIsVal($dest_type);
						else
							$sel_farm_typ->setOptionIsVal("num_total");
							
						//if($action=="edit_job"){
							//$sel_farm_typ->isDisabled=true;
						//}
						$sel_farm_typ->selectWidth=11;
						$sel_farm_typ->start();
						$sel_farm_typ->addOption("num_total","Total");
						$sel_farm_typ->addOption("num_farmers","Farmers");
						$sel_farm_typ->addOption("num_lifestyle","Lifestyle");
						$sel_farm_typ->addOption("num_dairies","Dairy");
						$sel_farm_typ->addOption("num_sheep","Sheep");
						$sel_farm_typ->addOption("num_beef","Beef");
						$sel_farm_typ->addOption("num_sheepbeef","Sheep/Beef");
						$sel_farm_typ->addOption("num_dairybeef","Dairy/Beef");
						$sel_farm_typ->addOption("num_hort","Hort");
						//$sel_farm_typ->addOption("num_nzfw","F@90%");
						$sel_farm_typ->addOption("rmt","Total #");
						//$sel_farm_typ->addOption("num_spare","Spare");
						$sel_farm_typ->stop();
?>
				</td>			
			</tr>		
			<tr>
				<td>Publication:</td>
				<td><input style="width: 15em; " name="publication" type="text" value="<?=$publication?>" /></td>
				<td></td>
				<td>Weight:</td>
				<td><input style="text-align:right " type="text" name="weight" value="<?=$weight?>" onKeyUp="javascript:checkfloat(this.value, this);" /></td>
				<td>g</td>						
			</tr>		
			<tr>
				<th colspan="4"></th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>Contact Details</td>
				<td colspan="3">
					<!--<textarea disabled cols="59" rows="4" name="contact_details"><?=$contact_details?></textarea>-->
					<textarea disabled class="show_on_screen" cols="59" rows="4" name="contact_details"><?=$contact_details?></textarea>
					<p class="show_on_print"><?=nl2br($contact_details)?></p>
				</td>
				<!--<td><input style="background-color:#EEEEEE; color:#333333 " disabled type="text" name="contact_name" value="<?=$contact_name?>" /></td>
				<td>Phone</td>
				<td><input style="background-color:#EEEEEE; color:#333333 " disabled type="text" name="contact_phone" value="<?=$contact_phone?>" /></td>-->
			</tr>
			<tr>
				<td></td>
				<td><a href="admin_client.php?action=manage_price_template&client_id=<?=$client_id?>" target="_blank">price templates</a></td>
			</tr>
			<tr>
				<th colspan="4">Customer Charges</th>
				<th>&nbsp;</th>
			</tr>			
			<tr>
				<td>Rate (normal):</td>
				<td><input style="text-align:right " type="text" id="rate" name="rate" value="<?=$rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>						
				<td></td>
				<td>Quantity (normal):</td>
				<td><input style="text-align:right " type="text" id="invoice_qty" name="invoice_qty" value="<?=$invoice_qty?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td></td>
				<td>Invoice Value $</td>
				<td><input style="text-align:right " type="text" id="invoice_tot" name="invoice_tot" value="<?=$margin->cust_inc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
			</tr>
			<tr>
				<td>Includes LineHaul:</td>
				<td><input type="checkbox" name="inc_linehaul" <?if($inc_linehaul=='Y') {?> checked <? } ?> value="Y"   /></td>						
			</tr>			
			<tr>
				<td>Rate (extra charge):</td>
				<td><input style="text-align:right " type="text" name="rate_bbc" value="<?=$rate_bbc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>									
				<td></td>
				<td>Quantity (extra charge)<sup>1</sup>:</td>
				<td><input style="text-align:right " type="text" name="qty_bbc" value="<?=$qty_bbc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>			
				<td></td>
				<td>Extra charge desc.</td>
				<td><input type="text" name="desc_bbc" value="<?=$desc_bbc?>"   /></td>				
			</tr>
			<tr>
				<td>Premium:</td>
				<td><input style="text-align:right " type="text" name="premium_sell" value="<?=$premium_sell?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>									
			</tr>
			<tr>
				<td colspan="4"></td>
				<td><sup>1</sup>Extra charges per 000</td>
			</tr>			
			<tr>
				<th colspan="4">Cost Structure</th>
			</tr>
			<tr>
				<td>Distributors: $</td>
				<td><input style="text-align:right " type="text" name="dist_rate" value="<?=$dist_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td></td>
				<td>NI Linehaul: $</td>
				<td><input style="text-align:right " type="text" name="freight_charge" value="<?=$freight_charge?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
			</tr>		
			<tr>
				<td>Sub-Distributors: $</td>
				<td><input style="text-align:right " type="text" name="subdist_rate" value="<?=$subdist_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>				
				<td></td>
				<td>SI Linehaul: $</td>
				<td><input style="text-align:right " type="text" name="lbc_charge" value="<?=$lbc_charge?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>				
			</tr>			
			<tr>
				<td>Contractors: $</td>
				<td><input style="text-align:right " type="text" name="contr_rate" value="<?=$contr_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td></td>
				<td>Reach Media/Other: $</td>
				<td><input style="text-align:right " type="text" name="lbc_charge_bbc" value="<?=$lbc_charge_bbc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>								
			</tr>	
			<tr>
				<td>Fuel Surcharge: </td>
				<td><input style="text-align:right " type="text" name="fuel_surcharge" value="<?=sprintf("%.1f",$fuel_surcharge)?>" onKeyUp="javascript:checkfloat(this.value, this);"  />%</td>
				<td>Add to Invoice</td>
				<td>Discount: </td>
				<td><input style="text-align:right " type="text" name="discount" value="<?=sprintf("%.1f",$discount)?>" onKeyUp="javascript:checkfloat(this.value, this);"  />%</td>	
			</tr>	
			<tr>
				<td>Folding Fee $: </td>
				<td><input style="text-align:right " type="text" name="folding_fee" value="<?=sprintf("%.4f",$folding_fee)?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td><input type="checkbox" id="add_folding_to_invoice" name="add_folding_to_invoice" value='Y' <? if($add_folding_to_invoice=='Y'){ ?> checked <? } ?> /></td>
				<td>DO Details Sent: </td>
				<td><input disabled  style="text-align:right " type="text" name="is_job_details_sent" value="<?=$is_job_details_sent?>"  /></td>
			</tr>	
			<tr>
				<td>Premium $</td>
				<td><input style="text-align:right " type="text" name="premium" value="<?=sprintf("%.4f",$premium); ?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
			</tr>
<?
			if($res_bundles){
				while($bund = mysql_fetch_object($res_bundles)){
?>
					<tr >
						<td>Bundles:</td>
						<td><input style="text-align:right " type="text" disabled value="<?=$bund->sum_bundles?>" /></td>
						<td>Price:</td>
						<td><input style="text-align:right " type="text" disabled value="<?=sprintf("%.2f",$bund->bundle_price)?>" /></td>					
					</tr>
<?			
				}
			}
?>			
			<tr>
				<td>Comments:</td>
				<td colspan="3">
					<!--<textarea cols="59" rows="4" name="comments"><?=$comments?></textarea>-->
					<textarea onChange="setPrintComment()" id="comment" class="show_on_screen" cols="59" rows="4" name="comments"><?=$comments?></textarea>
					<p id="print_comment" class="show_on_print"><?=nl2br($comments)?></p>
					Show <input name="show_comments" type="checkbox" <? if($show_comments=='Y'){?> checked <? }?> value="Y" /></td>
			</tr>
			<tr>
<?
			if($action=="edit_job"){
?>			
				<td style="text-align:center " colspan="5">
					<input class="input_button" type="submit" value="Change" name="submit" />
<?
					if($dest=="template"){
?>					
						<input class="input_button" type="button" name="Saveasnew" value="Save As New" onClick="window.location.href='proc_job.php?action=load_temp&dest=<?=$dest?>&job_id=<?=$job_id?>&new_date='+document.proc_job.delivery_date.value" />
<?					
					}
					else{
?>
						<input class="input_button" type="button" name="Saveasnew" value="Save As New" onClick="window.location.href='proc_job.php?action=save_as_new&dest=<?=$dest?>&job_id=<?=$job_id?>&new_date='+document.proc_job.delivery_date.value" />
<?					
					}
					if($alt_job_id){					
?>					
						<input class="input_button" type="button" value="Back" name="back" onClick="document.location.href='index.php'" />										
<?						
					}
					else{
?>
						<input class="input_button" type="submit" value="Cancel" name="cancel" />					
<?
					}
?>					
					<input name="action" type="hidden" value="save_job" />		
					<input name="dest" type="hidden" value="edit_job" />
				</td>
<?
			}
			else{
?>			
				<td style="text-align:center " colspan="5">
					<input class="input_button" type="submit" value="Save" name="submit" />
					<input class="input_button" type="button" value="Cancel" name="cancel" onClick="document.location.href='index.php'"  />
					<input class="input_button" name="action" type="hidden" value="add_job" />
					<input class="input_button" name="dest" type="hidden" value="new_job" />
				</td>
<?
			}
?>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
<?
			if($job_id){
?>						
				<tr bgcolor="#FFFFFF">
					<td colspan="6">Margin: &nbsp;&nbsp;<?=$margin->margin_percent?>%</td>
				</tr>
<?
			}
?>					
		</table>
		<input name="today" type="hidden" value="<?=$today?>" />
		<input name="job_id" type="hidden" value="<?=$job_id?>" />
	</form>
<?	
}


?>