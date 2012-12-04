<?
//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.user									//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////
if($action=="edit"||$action=="add"){
	$sql  = "SELECT * FROM operator 
			LEFT JOIN address
			ON address.operator_id=operator.operator_id
			WHERE operator.operator_id='$record'";
	$operator = mysql_fetch_object(query($sql));	
	
	$operator_id = $operator->operator_id;
	$address_id  = $operator->address_id;
	
?>	
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="editoperator" action="admin_operator.php?action=save" method="get">
		<table>
			<tr>
				<td>Name</td>
				<td><input size="40" type="text" name="company" value="<?=$operator->company?>" /> </td>
				<td>Distributor:</td>
				<td><input size="20" type="text" name="distributor" value="<?=$operator->distributor?>" /> </td>
				<td>Sub-Distributor:</td>
				<td><input size="20" type="text" name="sub_dist" value="<?=$operator->sub_dist?>" /> </td>
<!--					<td  rowspan="8" valign="top" >
						<?
							$sql_op = "SELECT operator.company AS id,operator.company AS name FROM operator
												WHERE is_dist='Y'
												GROUP BY company
												ORDER BY company";						
							$sel = new MySQLSelect("distributor","","","");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->setOptionIsVal($operator->distributor);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>
					<td>Sub-Distributor:</td>
					<td  rowspan="8" valign="top" >
						<?
							$sql_op = "SELECT operator.company AS id,operator.company AS name FROM operator
												WHERE is_subdist='Y'
												GROUP BY company
												ORDER BY company";	
							$sel = new MySQLSelect("sub_dist","","","");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->setOptionIsVal($operator->sub_dist);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>-->
				</tr>
				
			<tr>
				<td>Distributor</td>
				<td><input type="checkbox" value="Y" name="is_dist" <? if($operator->is_dist=="Y"){?> checked <? }?> /></td>
			</tr>
			<tr>
				<td>Subdistributor</td>
				<td><input type="checkbox" value="Y" name="is_subdist" <? if($operator->is_subdist=="Y"){?> checked <? }?>  /></td>
			</tr>
			<tr>
				<td>Contractor</td>
				<td><input type="checkbox" value="Y" name="is_contr" <? if($operator->is_contr=="Y"){?> checked <? }?>  /></td>
			</tr>			
			<tr>
				<td>Alias</td>
				<td><input size="40" type="text" name="alias" value="<?=$operator->alias?>" /> </td>
			</tr>
			<tr>
				<td>Shares</td>
				<td><input size="40" type="text" name="shares" value="<?=$operator->shares?>" /> </td>
			</tr>
			<tr>
				<td>Identifier</td>
				<td><input size="40" type="text" name="identifier" value="<?=$operator->identifier?>" /> </td>
			</tr>
			<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
			<tr>
				<td>Date Started</td>
				<td>
					<script language="javascript">DateInput("date_started", true, "YYYY-MM-DD","2007-06-01")</script>
				</td>
			</tr>
			<tr>
				<td>Date Left</td>
				<td>
					<script language="javascript">DateInput("date_left", true, "YYYY-MM-DD","2007-06-01")</script>
				</td>
			</tr>															
			<tr>
				<td>Coural Contract (Y/N)</td>
				<td><input type="checkbox" value="Y" name="contract" <? if($operator->contract=="Y"){?> checked <? }?> /></td>
			</tr>															
			<tr>
				<td>Agency Contract (Y/N)</td>
				<td><input type="checkbox" value="Y" name="agency" <? if($operator->agency=="Y"){?> checked <? }?> /></td>				
			</tr>			
			<tr>
				<td colspan="6"><hr /></td>
			</tr>					
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td>Salutation</td>
							<td><input size="40" type="text" name="salutation" value="<?=$operator->salutation?>" /> </td>
						</tr>
						<tr>
							<td>First Name</td>
							<td><input size="40" type="text" name="first_name" value="<?=$operator->first_name?>" /> </td>
						</tr>
						<tr>
							<td>Name</td>
							<td><input size="40" type="text" name="name" value="<?=$operator->name?>" /> </td>
						</tr>
						<tr>
							<td>Date of Birth</td>
							<td><script language="javascript">DateInput("birthdate", true, "YYYY-MM-DD","2007-06-01")</script></td>
						</tr>
						<tr>
							<td>Salutation 2</td>
							<td><input size="40" type="text" name="salutation2" value="<?=$operator->salutation2?>" /> </td>
						</tr>
						<tr>
							<td>First Name 2</td>
							<td><input size="40" type="text" name="first_name2" value="<?=$operator->first_name2?>" /> </td>
						</tr>
						<tr>
							<td>Name 2</td>
							<td><input size="40" type="text" name="name2" value="<?=$operator->name2?>" /> </td>
						</tr>
						<tr>
							<td>Date of Birth 2</td>
							<td><script language="javascript">DateInput("birthdate2", true, "YYYY-MM-DD","2007-06-01")</script></td>
						</tr>	
						<tr>
							<td>Bank Account No.</td>
							<td><input size="40" type="text" name="bank_num" value="<?=$operator->bank_num?>" /> </td>
						</tr>
						<tr>
							<td>GST No.</td>
							<td><input size="40" type="text" name="gst_num" value="<?=$operator->gst_num?>" /> </td>
						</tr>						
					</table>
				</td>
				<td  colspan="4">
					<table>
						<tr>
							<td>Address</td>
							<td><input size="40" type="text" name="address" value="<?=$operator->address?>" /> </td>
						</tr>					
						<tr>
							<td>Address 2</td>
							<td><input size="40" type="text" name="address2" value="<?=$operator->address2?>" /> </td>
						</tr>					
						<tr>
							<td>Postal Address</td>
							<td><input size="40" type="text" name="postal_addr" value="<?=$operator->postal_addr?>" /> </td>
						</tr>					
						<tr>
							<td>City</td>
							<td><input size="40" type="text" name="city" value="<?=$operator->city?>" /> </td>
						</tr>					
						<tr>
							<td>Postcode</td>
							<td><input size="40" type="text" name="postcode" value="<?=$operator->postcode?>" /> </td>
						</tr>					
						<tr>
							<td>Country</td>
							<td><input size="40" type="text" name="country" value="<?=$operator->country?>" /> </td>
						</tr>					
						<tr>
							<td>Phone</td>
							<td><input size="40" type="text" name="phone" value="<?=$operator->phone?>" /> </td>
						</tr>					
						<tr>
							<td>Phone2</td>
							<td><input size="40" type="text" name="phone2" value="<?=$operator->phone2?>" /> </td>
						</tr>					
						<tr>
							<td>Mobile</td>
							<td><input size="40" type="text" name="mobile" value="<?=$operator->mobile?>" /> </td>
						</tr>					
						<tr>
							<td>Mobile 2</td>
							<td><input size="40" type="text" name="mobile2" value="<?=$operator->mobile2?>" /> </td>
						</tr>					
						<tr>
							<td>Facsimile</td>
							<td><input size="40" type="text" name="fax" value="<?=$operator->fax?>" /> </td>
						</tr>					
						<tr>
							<td>Email</td>
							<td><input size="40" type="text" name="email" value="<?=$operator->email?>" /> </td>
						</tr>					
						<tr>
							<td>WWW</td>
							<td><input size="40" type="text" name="www" value="<?=$operator->www?>" /> </td>
						</tr>					
						<tr>
							<td>EText</td>
							<td><input size="40" type="text" name="etext" value="<?=$operator->etext?>" /> </td>
						</tr>					
						
					</table>
				</td>
			</tr>		
			<tr>
				<td colspan="6"><hr /></td>
			</tr>
			<tr>
				<td colspan="2" align="center">				
					<input type="submit" name="submit" value="Save" />
					<input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_operator.php'" />
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="address_id" value="<?=$address_id?>" />
					<input type="hidden" name="dest" value="<?=$action?>" />
					<input type="hidden" name="record" value="<?=$operator_id?>" />					
				</td>
			</tr>				
		</table>
	</form>
<?	
}

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.operator								//
//////////////////////////////////////////////////////////

if($action=="" || !isset($action)){
?>
		<div id="interaction">
<?
			include "admin_operator/interaction.php";
?>			
		</div>		
<?		
	$sql = "SELECT 	operator_id 	AS Record,
					company		 	AS Operator,
					is_dist			AS Dist,
					is_subdist		AS Subdist,
					is_contr	 	AS Contr,
					distributor		AS Dist,
					sub_dist		AS 'Sub-Dist',
					alias		 	AS Alias,
					shares		 	AS Shares,
					identifier	 	AS Identifier,
					date_started 	AS 'Date Started',
					date_left    	AS 'Date Left',
					contract	 	AS 'Contract',
					agency		 	AS 'Agency'
			FROM operator
			WHERE company LIKE('$letter%')
			ORDER BY company";
	$operatorTab = new MySQLTable("admin_operator.php",$sql);
	$operatorTab->showRec=0;
//	$operatorTab->hasCopyButton=true;
	$operatorTab->detailField="address_id";
	$operatorTab->detailAddress="admin_address.php";
//	$operatorTab->startTable();
	$operatorTab->wrap1="Alias";
	$operatorTab->writeList();		
//	$operatorTab->stopTable();
}



?>