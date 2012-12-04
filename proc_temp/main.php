<script language="javascript">
	function checkfloat(strString,field)
		   //  check for valid numeric strings	
		   {
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
<?
/*		$qry = "SELECT job_route_id,region
				FROM job_route_temp 
				LEFT JOIN route
				ON route.route_id=job_route_temp.route_id
				WHERE job_id=$job_id
				GROUP BY route.region";
				
		$res = query($qry);*/
		//$js_result="var jsArray = new array(".implode(',',$test). "); ";
?>

<script src="javascripts/ajax.js" type="text/javascript" language="javascript"></script>

<?
//////////////////////////////////////////////////////////
// SECTION INVOICE										//
//////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.invoice / coural.job_temp					//
//////////////////////////////////////////////////////////

if(!$job_id)
	$job_id=$record;



if($action=="attach_job"){
?>
	<form name="add_dest" action="proc_temp.php" method="post">
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
// SECTION job_temp											//
//////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////
// ACTION DELETE LINES                                 	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.job_temp / coural.job_route_temp				//
//////////////////////////////////////////////////////////

if($action=="edit_line"){
	$qry = "SELECT * FROM job_route_temp WHERE job_route_id=$record";
	$res_routes = query($qry);
	$line = mysql_fetch_object($res_routes);
	$region = get("route","region","WHERE route_id=$line->route_id");
	$area = get("route","area","WHERE route_id=$line->route_id");
	$code = get("route","code","WHERE route_id=$line->route_id");
	$alt_job_id = get("job_temp","alt_job_id","WHERE job_id='$job_id'");
	
	$qry = "SELECT * FROM job_route_temp WHERE route_id=$line->route_id AND job_id='$alt_job_id '";
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

if($action=="show_do_details_with_pagebreak"){
	if($export)
		show_do_details_with_pagebreak($job_id,true);
	else
		show_do_details_with_pagebreak($job_id,false);
}

if($action=="show_do_details"){
	if($export)
		show_do_details($job_id,true);
	else
		show_do_details($job_id,false);
}



if($action=="show_print_table_temp"){
	if($export)
		show_print_table_temp($job_id,true);
	else
		show_print_table_temp($job_id,false);
}

if($action=="show_print_table_temp_detail"){
	if($export)
		show_print_table_temp_detail($job_id,true);
	else
		show_print_table_temp_detail($job_id,false);
}

if($action=="show_job_details"){
	//show_table_with_sub_totals($job_id);		
	if($export)
		show_job_details($job_id,true,$choice);		
	else
		show_job_details($job_id,false,$choice);		
}

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.job_temp / coural.job_route_temp				//
//////////////////////////////////////////////////////////
if($action=="" || !isset($action)||$action=="edit"){
	$alt_job_id = get("job_temp","alt_job_id","WHERE alt_job_id='$job_id'");
	if($alt_job_id){
		$action="edit_job";
	}
	else{
		$qry = "SELECT * FROM job_temp 
				WHERE job_temp.job_id='$job_id'";
		$res = query($qry);
		$obj = mysql_fetch_object($res);
		
		if($obj->is_ioa=='Y') $delivery_date="IOA";
		else  $delivery_date=$obj->delivery_date;
		
		$max_line = get_max("job_route_temp","job_route_id","WHERE job_id='$job_id'","");
		$min_line = get_min("job_route_temp","job_route_id","WHERE job_id='$job_id'","");
		$notes 	  = nl2br(get("job_temp","comments","WHERE job_id='$job_id'"));
		$job_no = get("job_temp","job_no","WHERE job_id='$job_id'");
		
		$client = get("client","name","WHERE client_id='$obj->client_id'");
		
		$name  = get("client","name","WHERE client_id='$obj->client_id'");
		$phone = get("client","phone","WHERE client_id='$obj->client_id'");

		set_alt_do_contractors($job_id);
		
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
						<td><?=$client?></td>
						<td>Publication: </td>
						<td><?=$obj->publication?></td>					
					</tr>
					<tr>
						<td>Job. #:</td>
						<td><?=$job_no?>/<?=$job_id?></td>
						<td>Invoice #:</td>
						<td><?=$obj->invoice_no?></td>
					</tr>
					<tr>
						<td>Delivery Date:</td>
						<td><?=$delivery_date?></td>
						<td>Booking Date:</td>
						<td><?=$obj->invoice_date?></td>
					</tr>				
					<tr>
						<td>Last Changed:</td>
						<td><?=$obj->change_date?></td>
						<td>Line Hauler:</td>
						<td><?=$obj->hauler?></td>
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
				if(!$type) $type = get("job_temp","dest_type","WHERE job_id=$job_id");
				write_addroute_form($action,$job_id,$island,$region,$area,$code,$type,$bund_route);	
	?>
			</div>
			<div id="job_add_bundle">
	<?		
				if(!$type) $type = get("job_temp","dest_type","WHERE job_id=$job_id");
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
				<form name="alter" action="proc_temp.php" method="get">
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
						<td><input type="button" name="show" value="Show Selected Routes" onClick="window.location.href='proc_temp.php?job_id=<?=$job_id?>&show=1'" /></td>
						<td><input type="button" name="show" value="Close Table" onClick="window.location.href='proc_temp.php?job_id=<?=$job_id?>'" /></td>
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

if($action=="edit_job"||$action=="new_job"){
	$today = date("Y-m-d");
	$alt_job_id = get("job_temp","alt_job_id","WHERE alt_job_id='$job_id'");
	$qry 	= "	SELECT job_temp.*
				FROM job_temp 
				WHERE job_id=$job_id";
	$res 	= query($qry);
	$job_temp = mysql_fetch_object($res);
	
	$purchase_no 		= $job_temp->purchase_no;
	$invoice_no 		= $job_temp->invoice_no;
	$job_no 			= $job_temp->job_no;
	$foreign_job_no		= $job_temp->foreign_job_no;
	$delivery_date 		= $job_temp->delivery_date;
	$is_ioa 			= $job_temp->is_ioa;
	$client_id			= $job_temp->client_id;
	$publication 		= $job_temp->publication;
	$qty_bbc			= $job_temp->qty_bbc;
	$weight 			= $job_temp->weight;
	$rate 				= $job_temp->rate;
	$rate_bbc			= $job_temp->rate_bbc;
	$hauler				= $job_temp->hauler;
	$lbc_charge 		= $job_temp->lbc_charge;
	$lbc_charge_bbc		= $job_temp->lbc_charge_bbc;
	$freight_charge 	= $job_temp->freight_charge;		
	$cust_ref 			= $job_temp->cust_ref;
	$dist_rate			= $job_temp->dist_rate;
	$subdist_rate 		= $job_temp->subdist_rate;
	$contr_rate 		= $job_temp->contr_rate;
	$comments 			= $job_temp->comments;
	$dest_type			= $job_temp->dest_type;
	$invoice_qty		= $job_temp->invoice_qty;
	$regular			= $job_temp->is_regular;
	
	$qry = "SELECT SUM(IF(dest_type='bundles',job_route_temp.amount,0)) AS sum_bundles,
			bundle_price
		FROM job_route_temp
		WHERE job_id=$job_id 
			AND bundle_price>0
		GROUP BY job_id,bundle_price";
	$res_bundles = query($qry);
	
	if(!$delivery_date) 	$delivery_date=$today; 
	if(!$rate_bundle) 		$rate_bundle="1.00"; 
	if(!$dist_rate) 		$dist_rate="0.0000"; 
	if(!$subdist_rate) 		$subdist_rate="0.0000"; 
	if(!$contr_rate) 		$contr_rate="0.0000"; 
	if(!$rate) 				$rate="0.0000"; 
	if(!$rate_bbc) 			$rate_bbc="0.0000"; 

	$lbc_charge 	= sprintf("%.2f",$lbc_charge);
	$lbc_charge_bbc	= sprintf("%.2f",$lbc_charge_bbc);
	$freight_charge = sprintf("%.2f",$freight_charge);
	$weight		 	= sprintf("%d",$weight);
	$invoice_qty	= sprintf("%d",$invoice_qty);
	$qty_bbc		= sprintf("%d",$qty_bbc);
	
	if($regular=="true")
		$regular="Y";
	$contact_name  = get("client","contact","WHERE client_id='$client_id'");
	$contact_phone = get("client","phone","WHERE client_id='$client_id'");
	
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form action="proc_temp.php" method="get" name="proc_temp">
		<table id="invoice">
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
			</tr>
			<tr>
				<td>Purchase #:</td>
				<td><input type="text" name="purchase_no" value="<?=$purchase_no?>" /></td>
				<td>Invoice #:</td>
				<td><input type="text" name="invoice_no" value="<?=$invoice_no?>" /></td>		
				<td>&nbsp;</td>	
			</tr>
			<tr>
				<td>Job #</td>
				<td><input style="background-color:#EEEEEE; color:#333333 " disabled type="text" name="job_no" value="<?=$job_no?>" /></td>
				<td>Customer Reference #:</td>
				<td><input type="text" name="foreign_job_no" value="<?=$foreign_job_no?>" /></td>		
				<td>&nbsp;</td>					
			</tr>
			<tr>
				<td>Delivery Date:</td>
				<td>
<?				
					echo "<script>DateInput(\"delivery_date\", true, \"YYYY-MM-DD\",\"$delivery_date\")</script>";
?>					
				</td>				
				<td>Line Hauler:</td>	
				<td>
					<input type="text" name="hauler" value="<?=$hauler?>" />
				</td>						
				<td>&nbsp;</td>				
			</tr>
			<tr>
				<td>Or IOA</td>
				<td><input name="is_ioa" value="Y" type="checkbox" <? if($is_ioa=='Y'){?> checked <? }?> /></td>
			</tr>
			<tr>
				<td>Charge to:</td>
				<td>
<?
						$sel_name = new MySQLSelect("name","client_id","client","proc_temp.php","proc_temp","client_id");	
						$sel_name->selectWidth=15;
						$sel_name->onChangeAction=$action;
						$sel_name->setOptionIsVal($client_id);
						$sel_name->addOnChange("purchase_no");
						$sel_name->addOnChange("invoice_no");
						$sel_name->addOnChange("job_no");
						$sel_name->addOnChange("foreign_job_no");
						$sel_name->addOnChange("hauler");
						$sel_name->addOnChange("weight");
						$sel_name->addOnChange("delivery_date");
						$sel_name->addOnChange("dest_type");
						$sel_name->addOnChange("rate");
						$sel_name->addOnChange("rate_bbc");
						$sel_name->addOnChange("freight_charge");
						$sel_name->addOnChange("lbc_charge");
						$sel_name->addOnChange("lbc_charge_bbc");
						$sel_name->addOnChange("qty_bbc");
						$sel_name->addOnChange("dist_rate");
						$sel_name->addOnChange("subdist_rate");	
						$sel_name->addOnChange("contr_rate");	
						$sel_name->addOnChange("comments");		
						$sel_name->addOnChange("invoice_qty");	
						$sel_name->addOnChange("publication");	
								

						$sel_name->startSelect();
						$sel_name->writeSelect();					
						$sel_name->stopSelect();
?>					
				</td>			
				<td>Dest.-Type</td>
				<td>
<?
						$sel_farm_typ = new Select("dest_type");
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
						$sel_farm_typ->addOption("num_nzfw","F@90%");
						$sel_farm_typ->addOption("num_spare","Spare");
						$sel_farm_typ->stop();
?>
				</td>			</tr>		
			<tr>
				<td>Publication:</td>
				<td><input style="width: 15em; " name="publication" type="text" value="<?=$publication?>" /></td>
				<td>Weight:</td>
				<td><input style="text-align:right " type="text" name="weight" value="<?=$weight?>" onKeyUp="javascript:checkfloat(this.value, this);" /></td>
				<td>g</td>						
			</tr>		
			<tr>
				<th colspan="4"></th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<th colspan="4">Contact Details</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>Name</td>
				<td><input style="background-color:#EEEEEE; color:#333333 " disabled type="text" name="contact_name" value="<?=$contact_name?>" /></td>
				<td>Phone</td>
				<td><input style="background-color:#EEEEEE; color:#333333 " disabled type="text" name="contact_phone" value="<?=$contact_phone?>" /></td>
			</tr>
			<tr>
				<th colspan="4">Customer Charges</th>
				<th>&nbsp;</th>
			</tr>			
			<tr>
				<td>Rate (normal):</td>
				<td><input style="text-align:right " type="text" name="rate" value="<?=$rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>						
				<td>Quantity (normal):</td>
				<td><input style="text-align:right " type="text" name="invoice_qty" value="<?=$invoice_qty?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>			
			</tr>			
			<tr>
				<td>Rate (BBC):</td>
				<td><input style="text-align:right " type="text" name="rate_bbc" value="<?=$rate_bbc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>									
				<td>Quantity (BBC):</td>
				<td><input style="text-align:right " type="text" name="qty_bbc" value="<?=$qty_bbc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>						
			</tr>			
			<tr>
				<th colspan="4">Cost Structure</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>Distributors: $</td>
				<td><input style="text-align:right " type="text" name="dist_rate" value="<?=$dist_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td>Freight Charge: $</td>
				<td><input style="text-align:right " type="text" name="freight_charge" value="<?=$freight_charge?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
			</tr>		
			<tr>
				<td>Sub-Distributors: $</td>
				<td><input style="text-align:right " type="text" name="subdist_rate" value="<?=$subdist_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>				
				<td>LBC Charge (Mailings): $</td>
				<td><input style="text-align:right " type="text" name="lbc_charge" value="<?=$lbc_charge?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>				
			</tr>			
			<tr>
				<td>Contractors: $</td>
				<td><input style="text-align:right " type="text" name="contr_rate" value="<?=$contr_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td>LBC Charge (BBC): $</td>
				<td><input style="text-align:right " type="text" name="lbc_charge_bbc" value="<?=$lbc_charge_bbc?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>								
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
					<textarea cols="59" rows="4" name="comments"><?=$comments?></textarea>
				</td>

				<td>&nbsp;</td>
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
						<input class="input_button" type="button" name="Saveasnew" value="Save As New" onClick="window.location.href='proc_temp.php?action=load_temp&dest=<?=$dest?>&job_id=<?=$job_id?>&new_date='+document.proc_temp.delivery_date.value" />
<?					
					}
					else{
?>
						<input class="input_button" type="button" name="Saveasnew" value="Save As New" onClick="window.location.href='proc_temp.php?action=save_as_new&dest=<?=$dest?>&job_id=<?=$job_id?>&new_date='+document.proc_temp.delivery_date.value" />
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
		</table>
		<input name="today" type="hidden" value="<?=$today?>" />
		<input name="job_id" type="hidden" value="<?=$job_id?>" />
	</form>
<?	
}


?>