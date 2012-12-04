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
	function checkint(strString,field)
		   //  check for valid numeric strings	
		   {
		   var strValidChars = "0123456789";
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
	
</script>

<?

//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.invoice / coural.job /coural.client	//
//////////////////////////////////////////////////////////

if($action=="save" || $action=="add"){
	$error=false;
	
	if(!$invoice_no){
		echo "No Invoice No!";
		$error=true;
	}
	else if(!$job_no){
		echo "No Job No!";
		$error=true;
	}
	else if(!$purchase_no){
		echo "No Purchase No!";
		$error=true;
	}
	else if(!$delivery_date){
		echo "No Delivery Date No!";
		$error=true;
	}
	else if(!$weight){
		echo "No Weight!";
		$error=true;
	}
	else if(!$quantity){
		echo "No Bundles!";
		$error=true;
	}
	else if(!$rate){
		echo "No Charge out Rate!";
		$error=true;
	}
	else if(!$dist_rate){
		echo "No Distributor Rate!";
		$error=true;
	}
	else if(!$subdist_rate){
		echo "No Sub-Distributor Rate!";
		$error=true;
	}
	else if(!$contr_rate){
		echo "No Contractor Rate!";
		$error=true;
	}
	else if(!$malcove_charge){
		echo "No Malcove Rate!";
		$error=true;
	}
	else if(!$malcove_invoice_no){
		echo "No Malcove Invoice No.!";
		$error=true;
	}
	else if(!$coural_allowance){
		echo "No Coural Freight Allowance!";
		$error=true;
	}
	
	if(!$error){
		if($action=="add"){
			$client_id=get("client_pub","client_id","WHERE client_pub_id='$publication'");
			$sql = "INSERT INTO invoice(
						client_id,
						client_pub_id,
						invoice_no,
						job_no,
						purchase_no,
						foreign_job_no,
						delivery_date,
						invoice_date,
						weight,
						quantity,
						rate,
						dist_rate,
						subdist_rate,
						contr_rate,
						malcove_charge,
						malcove_invoice_no,
						coural_allowance,
						comments)
					VALUES(
						'$client_id',
						'$publication',
						'$invoice_no',
						'$purchase_no',
						'$job_no',
						'$foreign_job_no',
						'$delivery_date',
						'$today',
						'$weight',
						'$quantity',
						'$rate',
						'$dist_rate',
						'$subdist_rate',
						'$contr_rate',
						'$malcove_charge',
						'$malcove_invoice_no',
						'$coural_allowance',
						'$comments')";
		}
		else{
			$sql = "UPDATE invoice SET
						client_id			='$client_id',
						client_pub_id		='$client_pub_id',
						invoice_no			='$invoice_no',
						purchase_no			='$purchase_no',
						job_no				='$job_no',
						foreign_job_no		='$foreign_job_no',
						delivery_date		='$delivery_date',
						invoice_date		='$invoice_date',
						weight				='$weight',
						quantity			='$quantity',
						rate				='$rate',
						dist_rate			='$dist_rate',
						subdist_rate		='$subdist_rate',
						contr_rate			='$contr_rate',
						malcove_charge		='$malcove_charge',
						malcove_invoice_no	='$malcove_invoice_no',
						coural_allowance	='$coural_allowance',
						comments			='$comments',	
					WHERE invoice_id='$record'";
		}
		query($sql);
		$job_id = mysql_insert_id();
		echo "This internediate step will disappear!";
		?>Invoice saved. <a href="proc_job.php?action=&dest=<?=$dest?>&invoice_id=<?=$job_id?>">Continue</a><?

	}
	else
		$action="";
}

//////////////////////////////////////////////////////////
// ACTION CONFIRM                                      	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.invoice								//
//////////////////////////////////////////////////////////

if($action=="confirm"){
}

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.invoice / coural.job					//
//////////////////////////////////////////////////////////

if($action=="" || !isset($action)){
	$today = date("Y-m-d");
	if($invoice_id){
		$qry 	= "SELECT * FROM invoice WHERE invoice_id=$invoice_id";
		$res 	= query($qry);
		$invoice = mysql_fetch_object($res);
		
		$purchase_no 		= $invoice->purchase_no;
		$invoice_no 		= $invoice->invoice_no;
		$job_no 			= $invoice->job_no;
		$foreign_job_no		= $invoice->foreign_job_no;
		$delivery_date 		= $invoice->delivery_date;
		$client_id			= $invoice->client_id;
		$name = get("client","name","WHERE client_id='$client_id'");
		$client_pub_id 		= $invoice->client_pub_id;
		$publication = get("client_pub","publication","WHERE client_pub_id='$client_pub_id'");
		$send_publication 	= $invoice->send_publication;
		$quantity 			= $invoice->quantity;
		$weight 			= $invoice->weight;
		$rate 				= $invoice->rate;
		$malcove_charge 	= $invoice->malcove_charge;
		$malcove_invoice_no = $invoice->malcove_invoice_no;
		$subdist_rate 		= $invoice->subdist_rate;
		$contr_rate 		= $invoice->contr_rate;
		$coural_allowance 	= $invoice->coural_allowance;
		$comments 			= $invoice->comments;
	}
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form action="proc_invoice.php" method="get" name="proc_invoice">
		<table id="invoice">
			<tr>
				<th colspan="5"><h1>Coural Invoicing Details</h1></th>
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
				<td><input type="text" name="job_no" value="<?=$job_no?>" /></td>
				<td>Their Job #:</td>
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

				<td colspan="3">&nbsp;</td>	
			</tr>
			<tr>
				<td>Charge to:</td>
				<td>
<?
						$sel_name = new MySQLSelect("name","name","client","proc_invoice.php","proc_invoice");	
						$sel_name->width=11;
						$sel_name->setOptionIsVal($name);
						$sel_name->addOnChange("purchase_no");
						$sel_name->addOnChange("invoice_no");
						$sel_name->addOnChange("job_no");
						$sel_name->addOnChange("foreign_job_no");
						$sel_name->addOnChange("quantity");
						$sel_name->addOnChange("weight");
						$sel_name->addOnChange("rate");
						$sel_name->addOnChange("malcove_charge");
						$sel_name->addOnChange("dist_rate");
						$sel_name->addOnChange("malcove_invoice_no");	
						$sel_name->addOnChange("subdist_rate");	
						$sel_name->addOnChange("contr_rate");	
						$sel_name->addOnChange("coural_allowance");		
						$sel_name->addOnChange("comments");						
						
						$sel_name->setOptionIsVal($name);
						$sel_name->startSelect();
						$sel_name->writeSelect();					
						$sel_name->stopSelect();
?>					
				</td>			
				<td>&nbsp;</td>
				<td><input type="text" name="send_name" value="<?=$name?>" /></td>	
				<td>&nbsp;</td>		
			</tr>		
			<tr>
				<td>Circular:</td>
				<td>				
<?
						$client_id = get("client","client_id","WHERE name='$name'");
						$sel_circ = new MySQLSelect("publication","client_pub_id","client_pub","proc_invoice.php","proc_invoice");	
						$sel_circ->width=11;
						$sel_circ->setOptionIsVal($client_id);
						$sel_circ->addOnChange("purchase_no");
						$sel_circ->addOnChange("invoice_no");
						$sel_circ->addOnChange("job_no");
						$sel_circ->addOnChange("foreign_job_no");
						$sel_circ->addOnChange("quantity");
						$sel_circ->addOnChange("weight");
						$sel_circ->addOnChange("rate");
						$sel_circ->addOnChange("malcove_charge");
						$sel_circ->addOnChange("dist_rate");
						$sel_circ->addOnChange("malcove_invoice_no");	
						$sel_circ->addOnChange("subdist_rate");	
						$sel_circ->addOnChange("contr_rate");	
						$sel_circ->addOnChange("coural_allowance");		
						$sel_circ->addOnChange("comments");						
						$sel_circ->setOptionIsVal($publication);
						$sel_circ->addSQLWhere("client_id",$client_id);
						$sel_circ->selectOnChange.="+'&client_id=$client_id'";
						$sel_circ->selectOnChange.="+'&name=$name'";
						$sel_circ->startSelect();
						$sel_circ->writeSelect();					
						$sel_circ->stopSelect();
						$send_publication = get("client_pub","publication","WHERE client_pub_id='$publication'");					
?>					
				</td>
				<td>&nbsp;</td>
				<td><input type="text" name="send_publication" value="<?=$send_publication?>"  /></td>		
				<td>&nbsp;</td>	
			</tr>		
			<tr>
				<th colspan="4"></th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>Delivered Bundles @ 1$ ea:</td>
				<td><input type="text" name="quantity" value="<?=$quantity?>"onKeyUp="javascript:checkint(this.value, this);"  /></td>					
				<td>Weight:</td>
				<td><input type="text" name="weight" value="<?=$weight?>" /></td>
				<td>g</td>
			</tr>
			<tr>
				<th colspan="4">Charges</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>Charge out rate:</td>
				<td><input type="text" name="rate" value="<?=$rate?>" onKeyUp="javascript:checkfloat(this.value, this);" /></td>
				<td>Client Charge: $</td>
				<td><input type="text" name="malcove_charge" value="<?=$malcove_charge?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Distributors:</td>
				<td><input type="text" name="dist_rate" value="<?=$dist_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td>Client Invoice #:</td>
				<td><input type="text" name="malcove_invoice_no" value="<?=$malcove_invoice_no?>" /></td>				
				<td>&nbsp;</td>
			</tr>		
			<tr>
				<td>Sub-Distributors:</td>
				<td><input type="text" name="subdist_rate" value="<?=$subdist_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td colspan="3">&nbsp;</td>
			</tr>			
			<tr>
				<td>Contractors:</td>
				<td><input type="text" name="contr_rate" value="<?=$contr_rate?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td>Coural Freight Allowance:</td>
				<td><input type="text" name="coural_allowance" value="<?=$coural_allowance?>" onKeyUp="javascript:checkfloat(this.value, this);"  /></td>
				<td>&cent /copy</td>
			</tr>						
			<tr>
				<td>Comments:</td>
				<td colspan="3">
					<textarea cols="59" rows="5" name="comments"><?=$comments?></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="text-align:center " colspan="5">
					<input type="submit" value="Save" name="submit" />
					<input type="button" value="Cancel" name="cancel" onClick="window.location.href='proc_invoice.php'" />
				</td>
			</tr>
		</table>
		<input name="action" type="hidden" value="add" />
		<input name="dest" type="hidden" value="invoice" />
		<input name="today" type="hidden" value="<?=$today?>" />
	</form>
<?	
}

	
?>