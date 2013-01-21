<?


if($action=="edit_branch"){
	$qry = "SELECT * FROM client_branch WHERE client_branch_id='$record'";
	$res = query($qry);
	$branch = mysql_fetch_object($res);
	$address = $branch->address;
	
	$client = get("client","name","WHERE client_id='$client_id'");
?>
	<form action="admin_client.php">
		<table>
			<tr>
				<th colspan="2">Edit branch for client <?=$client?></th>
			</tr>
			<tr>
				<td>Address</td>
				<td>
					<textarea rows="10" cols="30" name="address"><?=$address?></textarea>
				</td>
				<td><input type="submit" name="submit" value="Save" /></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="save_branch" />
		<input type="hidden" name="client_branch_id" value="<?=$record?>" />
		<input type="hidden" name="client_id" value="<?=$client_id?>" />
	</form>
<?	
}

if($action=="show_branches"){
	$client = get("client","name","WHERE client_id='$client_id'");
?>
	<h4>Branches for client <?=$client?></h4>
<?

	$qry = "SELECT client_branch_id,address AS Address FROM client_branch WHERE client_id='$client_id'";
	
	$clientTab = new MySQLTable("admin_client.php",$qry);
	$clientTab->showRec  = 0;
	$clientTab->onClickEditButtonAction = "edit_branch";
	$clientTab->onClickEditButtonAdd = "&client_id=$client_id";
	
	$clientTab->onClickAddButtonAction = "edit_branch";
	$clientTab->onClickAddButtonAdd = "&client_id=$client_id";
	
	$clientTab->onClickDeleteButtonAction = "delete_branch";
	$clientTab->onClickDeleteButtonAdd = "&client_id=$client_id";
	
	$clientTab->startTable();		
		$clientTab->writeTable();		
	$clientTab->stopTable();		
}


//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.user									//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////
if($action=="edit" || $action=="add"){
	$sql  = "SELECT *
			FROM client 
			WHERE client.client_id='$record'";
			
	$client = mysql_fetch_object(query($sql));
	
	if(!$client_id) $client_id=$client->client_id;
	if(!$name) $name=$client->name;
	if(!$card_id) $card_id=$client->card_id;
	if(!$publication) $publication=$client->publication;
	//if(!$contact) $contact=$client->contact;
	if(!$contact_details) $contact_details=$client->contact_details;
	if(!$delivery_details) $delivery_details=$client->delivery_details;
	if(!$is_parcel_courier) $is_parcel_courier=$client->is_parcel_courier;
	if(!$has_discount) $has_discount=$client->has_discount;
	if(!$email) $email=$client->email;
	if(!$is_hauler) $is_hauler=$client->is_hauler;
	if(!$is_linehaul) $is_linehaul=$client->is_linehaul;
	if(!$invoice_details) $invoice_details=$client->invoice_details;
	if(!$discount) $discount=$client->discount;
	//if(!$phone) $phone=$client->phone;
	
	
?>
	<form name="editclient" action="admin_client.php?action=save" method="get">
		<table>
			<tr>
				<td>Card ID</td>
				<td><input size="50" type="text" name="card_id" value="<?=$card_id?>" /> </td>
			</tr>
			<tr>
				<td>Client</td>
				<td><input size="50" type="text" name="name" value="<?=$name?>" /> </td>
			</tr>
			<tr>
				<td>Contact Details</td>
				<td><textarea cols="59" rows="5" name="contact_details"><?=$contact_details?></textarea>
				</td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input size="50" type="text" name="email" value="<?=$email?>" /> </td>
			</tr>
			<tr>
				<td>Invoice Details</td>
				<td><textarea cols="59" rows="5" name="invoice_details"><?=$invoice_details?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					
				</td>
				<td><a href="admin_client.php?action=manage_price_template&client_id=<?=$record?>&dest=edit">Edit price templates</a></td>
			</tr>
			<tr>
				<td>Is Courier</td>
				<td>
					<input type="checkbox" name="is_parcel_courier" value="1" <? if($is_parcel_courier){ ?> checked <? }?> /> 
				</td>
			</tr>
			<tr>
				<td>Is Hauler</td>
				<td>
					<input type="checkbox" name="is_hauler" value="1" <? if($is_hauler){ ?> checked <? }?> /> 
				</td>
			</tr>
			<tr>
				<td>LineHaul Display</td>
				<td>
					<input type="checkbox" name="is_linehaul" value="1" <? if($is_linehaul){ ?> checked <? }?> /> 
				</td>
			</tr>
			<tr>
				<td>Parcel Discount</td>
				<td>
					<input type="text" name="discount" value="<?=$discount?>" /> 
				</td>
			</tr>
			<tr>
				<td>Has Discount</td>
				<td>
					<input type="checkbox" name="has_discount" value="1" <? if($has_discount){ ?> checked <? }?> /> 
				</td>
			</tr>
			
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" value="Save" />
					<input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_client.php'" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="dest" value="<?=$action?>" />
		<input type="hidden" name="client_id" value="<?=$record?>" />
	</form>
<?
}

if($action=="manage_price_template"){
	if($filter=="Load"){
		$sel_client_id = $temp_client_id;
	}
	else{
		$sel_client_id = $client_id;
	}

	$print_date = date("d M Y");
	$qry = "SELECT * FROM client WHERE client_id='$sel_client_id'";
	$res_client = query($qry);
	$client = mysql_fetch_object($res_client);
	
	$qry_a = "SELECT weight,
					IF(pa_dist=0,'',pa_dist) AS pa_dist,
					IF(pa_sdist=0,'',pa_sdist) AS pa_sdist,
					IF(pa_cont=0,'',pa_cont) AS pa_cont,
					IF(pa_dist=0,'',pa_dist) AS pa_dist,
					IF(pr_u_1=0,'',pr_u_1) AS pr_u_1,
					IF(pr_u_2=0,'',pr_u_2) AS pr_u_2,
					IF(pr_u_3=0,'',pr_u_3) AS pr_u_3,
					IF(pr_u_4=0,'',pr_u_4) AS pr_u_4,
					IF(pr_u_5=0,'',pr_u_5) AS pr_u_5,
					IF(pr_u_6=0,'',pr_u_6) AS pr_u_6
					
					
			FROM client_price 
			WHERE client_id='$sel_client_id' ORDER BY client_price_id";
	$res_clientp = query($qry_a);
	while($client_price = mysql_fetch_object($res_clientp)){
		$weight[] =  $client_price->weight;
		$pa_dist[] = $client_price->pa_dist;
		$pa_sdist[] = $client_price->pa_sdist;
		$pa_cont[] = $client_price->pa_cont;
		$pr_u_1[] = $client_price->pr_u_1;
		$pr_u_2[] = $client_price->pr_u_2;
		$pr_u_3[] = $client_price->pr_u_3;
		$pr_u_4[] = $client_price->pr_u_4;
		$pr_u_5[] = $client_price->pr_u_5;
		$pr_u_6[] = $client_price->pr_u_6;
	}
	
	$qry = "SELECT * FROM client_pub WHERE client_id = '$sel_client_id'";
	$res_clientpub = query($qry);
	while($client_pub = mysql_fetch_object($res_clientpub)){
		$pub_weekly[$client_pub->client_pub_num] = $client_pub->pub_weekly;
		$pub_weekly_ff[$client_pub->client_pub_num] = $client_pub->pub_weekly_ff;
		$pub_weekly_lh[$client_pub->client_pub_num] = $client_pub->pub_weekly_lh;
		$pub_fortnightly[$client_pub->client_pub_num] = $client_pub->pub_fortnightly;
		$pub_fortnightly_ff[$client_pub->client_pub_num] = $client_pub->pub_fortnightly_ff;
		$pub_fortnightly_lh[$client_pub->client_pub_num] = $client_pub->pub_fortnightly_lh;
		$pub_monthly[$client_pub->client_pub_num] = $client_pub->pub_monthly;
		$pub_monthly_ff[$client_pub->client_pub_num] = $client_pub->pub_monthly_ff;
		$pub_monthly_lh[$client_pub->client_pub_num] = $client_pub->pub_monthly_lh;
		$pub_other[$client_pub->client_pub_num] = $client_pub->pub_other;
		$pub_other_ff[$client_pub->client_pub_num] = $client_pub->pub_other_ff;
		$pub_other_lh[$client_pub->client_pub_num] = $client_pub->pub_other_lh;
	}

?>
	<style>
		div#price_template_form input{
			width:5em;
		}
		
		div#price_template_form .pub_input{
			width:10em;
		}
		
		div#price_template_form th{
			font-size:0.8em;
		}
	</style>
	<div id="price_template_form">
	<form action="admin_client.php" method="post">
		<table >
			<tr>
				<th align="left">Client:</th>
					<th><?=$client->name?></th>
				<th>Printed:</th>
				<td><?php echo $print_date; ?></td>
			</tr>
			<tr>
				<td>Network Costs:</td>
				<td>
					<input type="text" name="net_costs" id="net_costs" value="<?=$client->net_costs?>" style="width:140px"  />
				</td>
				<td>Notes:</td>
				<td rowspan="3"><textarea name="notes" cols="80" rows="6" style="font-family:verdana;font-size:12px"><?=$client->notes?></textarea></td>
			</tr>
			<tr>
				<td>Pricing basis:</td>
				<td>
					<input type="text" name="base_price" id="base_price" value="<?=$client->base_price?>" style="width:140px"  />
				</td>
			</tr>
			<tr>
				<td>Linehaul:</td>
				<td>
					<input type="text" name="linehaul" id="linehaul" value="<?=$client->linehaul?>" style="width:140px"  />
				</td>
			</tr>
		</table>
		<br />
		<table>
			<tr>
				<th align="left" colspan="5">Regular Bookings</th>
			</tr>
			<tr>
				<th>Weekly</th>
				<th>F/F</th>
				<th>L/H</th>
				<th>Fortnightly</th>
				<th>F/F</th>
				<th>L/H</th>
				<th>Monthly</th>
				<th>F/F</th>
				<th>L/H</th>
				<th>Other</th>
				<th>F/F</th>
				<th>L/H</th>
			</tr>
<?
			for($i=0;$i<22;$i++){
?>			
				<tr>
					<td>
						<input class="pub_input" type="text" name="pub_weekly[<?=$i?>]" value="<?=$pub_weekly[$i]?>" />
					</td>
					<td valign="top">
						<input type="checkbox" style="width:40px" value="1" name="pub_weekly_ff[<?=$i?>]" <? if($pub_weekly_ff[$i]){ ?> checked <? }?> />
					</td>
					<td valign="top">
						<input type="checkbox" style="width:40px" value="1" name="pub_weekly_lh[<?=$i?>]" <? if($pub_weekly_lh[$i]){ ?> checked <? }?> />
					</td>
					<td>
						<input class="pub_input" type="text" name="pub_fortnightly[<?=$i?>]" value="<?=$pub_fortnightly[$i]?>" />
					</td>
					<td valign="top">
						<input type="checkbox" style="width:40px" value="1" name="pub_fortnightly_ff[<?=$i?>]" <? if($pub_fortnightly_ff[$i]){ ?> checked <? }?> />
					</td>
					<td valign="top">
						<input type="checkbox" style="width:40px" value="1" name="pub_fortnightly_lh[<?=$i?>]" <? if($pub_fortnightly_lh[$i]){ ?> checked <? }?> />
					</td>
					<td>
						<input class="pub_input" type="text" name="pub_monthly[<?=$i?>]" value="<?=$pub_monthly[$i]?>" />
					</td>
					<td valign="top"> 
						<input type="checkbox" style="width:40px" value="1" name="pub_monthly_ff[<?=$i?>]" <? if($pub_monthly_ff[$i]){ ?> checked <? }?> />
					</td>
					<td valign="top"> 
						<input type="checkbox" style="width:40px" value="1" name="pub_monthly_lh[<?=$i?>]" <? if($pub_monthly_lh[$i]){ ?> checked <? }?> />
					</td>
					<td>
						<input class="pub_input" type="text" name="pub_other[<?=$i?>]" value="<?=$pub_other[$i]?>" />
					</td>
					<!-- <td valign="top">
						<input type="checkbox" style="width:40px" value="1" name="pub_other_ff[<?=$i?>]" <? if($pub_other_ff[$i]){ ?> checked <? }?> />
					</td>
					<td valign="top">
						<input type="checkbox" style="width:40px" value="1" name="pub_other_lh[<?=$i?>]" <? if($pub_other_lh[$i]){ ?> checked <? }?> />
					</td>-->
				</tr>
<?
			}
?>			
		</table>
		<br />
		<br />
		<br />
		<table>
			<tr>
				<th align="left" colspan="8">Network Costs and Selling Prices</th>
			</tr>
			<tr>
				<th>Weight</th>
				<th>Dist</th>
				<th>S/Dist</th>
				<th>Cont</th>
				
				<th><textarea name="u_nw_1" cols="18" rows="2" style="font-family:verdana; font-size:11px"><?=$client->u_nw_1?></textarea></th>
				<th><textarea name="u_nw_2" cols="18" rows="2" style="font-family:verdana; font-size:11px"><?=$client->u_nw_2?></textarea></th>
				<th><textarea name="u_nw_3" cols="18" rows="2" style="font-family:verdana; font-size:11px"><?=$client->u_nw_3?></textarea></th>
				<th><textarea name="u_nw_4" cols="18" rows="2" style="font-family:verdana; font-size:11px"><?=$client->u_nw_4?></textarea></th>
				<th><textarea name="u_nw_5" cols="18" rows="2" style="font-family:verdana; font-size:11px"><?=$client->u_nw_5?></textarea></th>
				<th><textarea name="u_nw_6" cols="18" rows="2" style="font-family:verdana; font-size:11px"><?=$client->u_nw_6?></textarea></th>
								
				
				
			</tr>
<?
			$weight[0] = "0-25";
			$weight[1] = "26-50";
			for($i=50;$i<=750;$i+=50){
				$weight[] = ($i+1)."-".($i+50);
			}
			
			for($i=0;$i<17;$i++){
?>			
				<tr>
					<td align="center"><input style="background-color:#CCCCCC " readonly="true" type="text" name="weight[<?=$i?>]" value="<?=$weight[$i]?>" /></td>
					<td align="center"><input type="text" name="pa_dist[<?=$i?>]" value="<?=$pa_dist[$i]?>" /></td>
					<td align="center"><input type="text" name="pa_sdist[<?=$i?>]" value="<?=$pa_sdist[$i]?>" /></td>
					<td align="center"><input type="text" name="pa_cont[<?=$i?>]" value="<?=$pa_cont[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_1[<?=$i?>]" value="<?=$pr_u_1[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_2[<?=$i?>]" value="<?=$pr_u_2[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_3[<?=$i?>]" value="<?=$pr_u_3[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_4[<?=$i?>]" value="<?=$pr_u_4[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_5[<?=$i?>]" value="<?=$pr_u_5[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_6[<?=$i?>]" value="<?=$pr_u_6[$i]?>" /></td>
				</tr>
<?
			}
?>			
			<tr>
				<th align="left" colspan="3">Special weights</th>
			</tr>
<?
			for($i=17;$i<24;$i++){
?>			
				<tr>
					<td align="center"><input type="text" name="weight[<?=$i?>]" value="<?=$weight[$i]?>" /></td>
					<td align="center"><input type="text" name="pa_dist[<?=$i?>]" value="<?=$pa_dist[$i]?>" /></td>
					<td align="center"><input type="text" name="pa_sdist[<?=$i?>]" value="<?=$pa_sdist[$i]?>" /></td>
					<td align="center"><input type="text" name="pa_cont[<?=$i?>]" value="<?=$pa_cont[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_1[<?=$i?>]" value="<?=$pr_u_1[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_2[<?=$i?>]" value="<?=$pr_u_2[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_3[<?=$i?>]" value="<?=$pr_u_3[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_4[<?=$i?>]" value="<?=$pr_u_4[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_5[<?=$i?>]" value="<?=$pr_u_5[$i]?>" /></td>
					<td align="center"><input type="text" name="pr_u_6[<?=$i?>]" value="<?=$pr_u_6[$i]?>" /></td>
				</tr>
<?
			}
?>						
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Save" /></td>
				<?
					if($dest=="edit"){
?>
						<td><input type="button" name="cancel" value="Back" onClick="window.location.href='admin_client.php?action=edit&record=<?=$client_id?>'" /></td>
<?					
					}
					else{
				?>
						<td><input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_client.php'" /></td>
				<?
					}
				?>
			</tr>
		</table>
		<input type="hidden" name="client_id" value="<?=$client_id?>" />
		<input type="hidden" name="action" value="<?=$action?>" />
		<input type="hidden" name="dest" value="<?=$dest?>" />
	</form>
	</div>
<?	
}

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="" || !isset($action)){
	if($is_courier>-1) $where_add = "WHERE is_parcel_courier='$is_courier'";
	$sql = "SELECT 	client.client_id			AS Record,
					client.card_id 				AS 'Card ID',
					client.name 				AS Client,
					client.contact_details 		AS Contact,
					client.invoice_details 		AS 'Invoice Det.',
					IF(is_parcel_courier=1,'Yes','No')			
												AS 'Is Courier',
					IF(is_hauler=1,'Yes','No')			
												AS 'Is Hauler',
					IF(has_discount=1,'Yes','No')			
												AS 'Has Discount',
					discount					AS Discount,
					CONCAT('<a href=\'admin_client.php?action=show_branches&client_id=',client.client_id,'\'>Show</a>')
								AS Branches
			FROM client
			$where_add\n
			ORDER BY client.name";
	$clientTab = new MySQLTable("admin_client.php",$sql);
	$clientTab->showRec=0;
	$clientTab->startTable();
	$clientTab->writeTable();
	$clientTab->stopTable();		
}

?>