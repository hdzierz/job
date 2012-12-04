<?

function edit_address($record,$action,$dest){
	if($dest=="op")	
		$id_field="operator_id";
	else
		$id_field="address_id";
		$sql  = "SELECT address.*,operator.company FROM address 
			 LEFT JOIN operator 
			 ON operator.operator_id=address.operator_id
			 WHERE address.$id_field='$record'";
	$addr = mysql_fetch_object(query($sql));
	$default_date="2007-01-01";

?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="editaddr" action="admin_address.php?action=save" method="get">
		<table>
			<tr>
				<td>
					<table>
						<tr>
							<td>Share Holder</td>
							<td><input size="40" type="text" name="operator" value="<?=$addr->company?>" disabled /> </td>
						</tr>
						<tr>
							<td>Salutation</td>
							<td><input size="40" type="text" name="salutation" value="<?=$addr->salutation?>" /> </td>
						</tr>
						<tr>
							<td>First Name</td>
							<td><input size="40" type="text" name="first_name" value="<?=$addr->first_name?>" /> </td>
						</tr>
						<tr>
							<td>Name</td>
							<td><input size="40" type="text" name="name" value="<?=$addr->name?>" /> </td>
						</tr>
						<tr>
							<td>Date of Birt</td>
							<td><script language="javascript">DateInput("birthdate", true, "YYYY-MM-DD","2007-06-01")</script></td>
						</tr>
						<tr>
							<td>Salutation 2</td>
							<td><input size="40" type="text" name="salutation2" value="<?=$addr->salutation2?>" /> </td>
						</tr>
						<tr>
							<td>First Name 2</td>
							<td><input size="40" type="text" name="first_name2" value="<?=$addr->first_name2?>" /> </td>
						</tr>
						<tr>
							<td>Name 2</td>
							<td><input size="40" type="text" name="name2" value="<?=$addr->name2?>" /> </td>
						</tr>
						<tr>
							<td>Date of Birth 2</td>
							<td><script language="javascript">DateInput("birthdate2", true, "YYYY-MM-DD","2007-06-01")</script></td>
						</tr>	
						<tr>
							<td>Bank Account No.</td>
							<td><input size="40" type="text" name="bank_num" value="<?=$addr->bank_num?>" /> </td>
						</tr>
						<tr>
							<td>GST No.</td>
							<td><input size="40" type="text" name="gst_num" value="<?=$addr->gst_num?>" /> </td>
						</tr>						
					</table>
				</td>
				<td>
					<table>
						<tr>
							<td>Address</td>
							<td><input size="40" type="text" name="address" value="<?=$addr->address?>" /> </td>
						</tr>					
						<tr>
							<td>Address 2</td>
							<td><input size="40" type="text" name="address2" value="<?=$addr->address2?>" /> </td>
						</tr>					
						<tr>
							<td>Postal Address</td>
							<td><input size="40" type="text" name="postal_addr" value="<?=$addr->postal_addr?>" /> </td>
						</tr>					
						<tr>
							<td>City</td>
							<td><input size="40" type="text" name="city" value="<?=$addr->city?>" /> </td>
						</tr>					
						<tr>
							<td>Postcode</td>
							<td><input size="40" type="text" name="postcode" value="<?=$addr->postcode?>" /> </td>
						</tr>					
						<tr>
							<td>Country</td>
							<td><input size="40" type="text" name="country" value="<?=$addr->country?>" /> </td>
						</tr>					
						<tr>
							<td>Phone</td>
							<td><input size="40" type="text" name="phone" value="<?=$addr->phone?>" /> </td>
						</tr>					
						<tr>
							<td>Phone2</td>
							<td><input size="40" type="text" name="phone2" value="<?=$addr->phone2?>" /> </td>
						</tr>					
						<tr>
							<td>Mobile</td>
							<td><input size="40" type="text" name="mobile" value="<?=$addr->mobile?>" /> </td>
						</tr>					
						<tr>
							<td>Mobile 2</td>
							<td><input size="40" type="text" name="mobile2" value="<?=$addr->mobile2?>" /> </td>
						</tr>					
						<tr>
							<td>Facsimile</td>
							<td><input size="40" type="text" name="fax" value="<?=$addr->fax?>" /> </td>
						</tr>					
						<tr>
							<td>Email</td>
							<td><input size="40" type="text" name="email" value="<?=$addr->email?>" /> </td>
						</tr>					
						<tr>
							<td>WWW</td>
							<td><input size="40" type="text" name="www" value="<?=$addr->www?>" /> </td>
						</tr>					
						<tr>
							<td>EText</td>
							<td><input size="40" type="text" name="etext" value="<?=$addr->etext?>" /> </td>
						</tr>					
						
					</table>
				</td>
				<td>
					<table>
						<tr>
							<td>2nd Address</td>
							<td><input size="40" type="text" name="sec_address" value="<?=$addr->sec_address?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Address 2</td>
							<td><input size="40" type="text" name="sec_address2" value="<?=$addr->sec_address2?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Postal Address</td>
							<td><input size="40" type="text" name="sec_postal_addr" value="<?=$addr->sec_postal_addr?>" /> </td>
						</tr>					
						<tr>
							<td>2nd City</td>
							<td><input size="40" type="text" name="sec_city" value="<?=$addr->sec_city?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Postcode</td>
							<td><input size="40" type="text" name="sec_postcode" value="<?=$addr->sec_postcode?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Country</td>
							<td><input size="40" type="text" name="sec_country" value="<?=$addr->sec_country?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Phone</td>
							<td><input size="40" type="text" name="sec_phone" value="<?=$addr->sec_phone?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Phone2</td>
							<td><input size="40" type="text" name="sec_phone2" value="<?=$addr->sec_phone2?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Mobile</td>
							<td><input size="40" type="text" name="sec_mobile" value="<?=$addr->sec_mobile?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Mobile2</td>
							<td><input size="40" type="text" name="sec_mobile2" value="<?=$addr->sec_mobile2?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Facsimile</td>
							<td><input size="40" type="text" name="sec_fax" value="<?=$addr->sec_fax?>" /> </td>
						</tr>					
						<tr>
							<td>2nd Email</td>
							<td><input size="40" type="text" name="sec_email" value="<?=$addr->sec_email?>" /> </td>
						</tr>					
						<tr>
							<td>2nd WWW</td>
							<td><input size="40" type="text" name="sec_www" value="<?=$addr->sec_www?>" /> </td>
						</tr>					
						<tr>
							<td>2nd EText</td>
							<td><input size="40" type="text" name="sec_etext" value="<?=$addr->sec_etext?>" /> </td>
						</tr>					
						
					</table>
				</td>				
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" value="Save" />
					<input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_address.php?record=<?=$record?>'" />
				</td>
			</tr>			
		</table>
		<input type="hidden" name="dest" value="<?=$action?>" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="record" value="<?=$record?>" />
	</form>
	<hr class="sqldivider" />
<?
}
?>