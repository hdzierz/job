<?

//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.user									//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////
if($action=="edit"||$action=="add"){
	$sql  = "SELECT * FROM address 
			LEFT JOIN operator
			ON address.operator_id=operator.operator_id
			WHERE address.address_id='$record'";
	$operator = mysql_fetch_object(query($sql));	

    if($operator->etext){
        $qry = "SELECT * FROM auth_user WHERE username='{$operator->etext}'";
        $res = query($qry);
        $u = mysql_fetch_object($res);
        $passwd_link = "<a target='_blank' href='http://jobs.coural.co.nz:8080/admin/auth/user/{$u->id}/password/'>Set password</a>";
    }
    else{
        $passwd_link='';
    }
	
	$operator_id = $operator->operator_id;
	$address_id  = $operator->address_id;
	
	$today=date("Y-m-d");
	$date_started = $operator->date_started;
	if(!$date_started) $date_started="1990-01-01";
	
	$date_left    = $operator->date_left;
	if(!$date_left) $date_left="1990-01-01";
	
	$share_bought   = $operator->share_bought;
	if(!$share_bought) $share_bought="1990-01-01";
	$share_sold   = $operator->share_sold;
	if(!$share_sold) $share_sold="1990-01-01";
    
    if(!$send_contr_sheet) $send_contr_sheet = $operator->send_contr_sheet;
	
	$company = stripslashes($operator->company);
	
	if(!$operator->rate_red_fact) $rate_red_fact = 0.0;
	else {$rate_red_fact = 100*(1.0-$operator->rate_red_fact);}
	
	if($operator->is_contr=='Y'){
		
		$qry = "SELECT code 
				FROM route 
				LEFT JOIN route_aff
				ON route.route_id=route_aff.route_id
				WHERE contractor_id='$operator->operator_id'
					AND '$today' >= app_date 
					AND '$today' < stop_date";
		$res = query($qry);
		//$route   = get("route","code"," WHERE contractor_id='$operator->operator_id'");
		$dist_id = get("route_aff","dist_id"," WHERE contractor_id='$operator->operator_id' AND '$today' >= app_date AND '$today' < stop_date");
		$distributor = get("operator","company","WHERE operator_id='$dist_id'");
	}
	
?>	
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="editoperator" action="admin_address.php?action=save" method="get">
		<table class="address_box">
			<tr>
				<td>Name</td>
				<td><b><?=$company?></b></td>
			</tr>
<?
			if($distributor){
?>			
			<tr>
				<td colspan="4">Current (<?=$today?>): Distr.: <b><?=$distributor?></b> / RD(s): 
<?
				$start=true;
				while($route = mysql_fetch_object($res)){
					if($start){
						$sep = "";
						$start=false;
					}
					else{
						$sep=" - ";
					}
					echo $sep."<b>$route->code</b>";
				}
?>			
				</td>
			</tr>
<?
			}
?>			
			<tr>
				<td colspan="9"><hr /></td>
			</tr>				
			<tr>
				<td>Card ID</td>
				<td><input size="10" type="text" name="card_id" value="<?=$operator->card_id?>" /> </td>
			</tr>			
			<tr>
				<td>Salutation</td>
				<td><input size="10" type="text" name="salutation" value="<?=$operator->salutation?>" /> </td>
				<!--<td>Address Type</td>-->
				<td><input size="40" type="hidden" name="type" value="" /> </td>
			</tr>
			<tr>
				<td>First Name</td>
				<td><input size="40" type="text" name="first_name" value="<?=$operator->first_name?>" /> </td>
				<td>Mail Type</td>
				<td>
<?
					$sel = new Select("mail_type");
					$sel->setOptionIsVal($operator->mail_type);
					$sel->start();
					$sel->AddOption("e","EMailer");
					$sel->AddOption("f","Faxer");
					$sel->AddOption("m","Mailer");
					$sel->stop();
?>							
				</td>												
			</tr>
			<tr>
				<td>Surname</td>
				<td><input size="40" type="text" name="name" value="<?=$operator->name?>" /> </td>
				<td>Phone</td>
				<td><input size="40" type="text" name="phone" value="<?=$operator->phone?>" /> </td>									
			</tr>
			<tr>
				<td>Salutation 2</td>
				<td><input size="40" type="text" name="salutation2" value="<?=$operator->salutation2?>" /> </td>
				<td>Phone2</td>
				<td><input size="40" type="text" name="phone2" value="<?=$operator->phone2?>" /> </td>							
			</tr>
			<tr>
				<td>First Name 2</td>
				<td><input size="40" type="text" name="first_name2" value="<?=$operator->first_name2?>" /> </td>
				<td>Mobile</td>
				<td><input size="40" type="text" name="mobile" value="<?=$operator->mobile?>" /> </td>								
			</tr>
			<tr>
				<td>Surname 2</td>
				<td><input size="40" type="text" name="name2" value="<?=$operator->name2?>" /> </td>
				<td>Mobile 2</td>
				<td><input size="40" type="text" name="mobile2" value="<?=$operator->mobile2?>" /> </td>									
			</tr>
			<tr>
				<td>Bank Account No.</td>
				<td><input size="40" type="text" name="bank_num" value="<?=$operator->bank_num?>" /> </td>
				<td>Facsimile</td>
				<td><input size="40" type="text" name="fax" value="<?=$operator->fax?>" /> </td>									</tr>
			<tr>
				<td>Address</td>
				<td><input size="40" type="text" name="address" value="<?=$operator->address?>" /> </td>
				<td>Email</td>
				<td><input size="40" type="text" name="email" value="<?=$operator->email?>" /> </td>									
			</tr>					
			<tr>
				<td>Address 2</td>
				<td><input size="40" type="text" name="address2" value="<?=$operator->address2?>" /> </td>
				<td>EText</td>
				<td><input size="40" type="text" name="etext" value="<?=$operator->etext?>" /> <?php echo $passwd_link;   ?></td>				
				
			</tr>					
			<tr>
				<td>Postal Address</td>
				<td><input size="40" type="text" name="postal_addr" value="<?=$operator->postal_addr?>" /> </td>
				<td>GST No.</td>
				<td><input size="40" type="text" name="gst_num" value="<?=$operator->gst_num?>" /> </td>						
			</tr>					
			<tr>
				<td>City</td>
				<td><input size="40" type="text" name="city" value="<?=$operator->city?>" /> </td>
				<td>Mail Type2</td>
				<td>
<?
					$sel = new Select("alt_mail_type");
					$sel->setOptionIsVal($operator->alt_mail_type);
					$sel->start();
					$sel->AddOption("e","EMailer");
					$sel->AddOption("f","Faxer");
					$sel->AddOption("m","Mailer");
					$sel->stop();
?>							
				</td>	
			</tr>					
			<tr>
				<td>Postcode</td>
				<td><input size="40" type="text" name="postcode" value="<?=$operator->postcode?>" /> </td>
				<td>Email2</td>
				<td><input size="40" type="text" name="alt_email" value="<?=$operator->alt_email?>" /> </td>
			</tr>					
			<tr>
				<td>Country</td>
				<td><input size="40" type="text" name="country" value="<?=$operator->country?>" /> </td>
				<td>Facsimile2</td>
				<td><input size="40" type="text" name="alt_fax" value="<?=$operator->alt_fax?>" /> </td>
			</tr>					
			<tr>
				<td colspan="9"><hr /></td>
			</tr>
			<tr>
				<td>Same Drop Off as Address</td>
				<td><input type="checkbox" value="Y" name="same_as_add" /></td>
				<td>Send contractor job details</td>
				<td><input type="checkbox" value="Y" <?php if($operator->parcel_send_di=='Y') echo "checked"; ?> name="parcel_send_di" /></td>
					
			</tr>
            <tr>
                <td></td>
                <td></td>
                <td>Send del advices to drop off</td>
                <td>
                    <input type="checkbox" value="Y" <?php if($operator->send_contr_sheet=='Y') echo "checked"; ?> name="send_contr_sheet" />
                </td>
            </tr>
			<tr>
				<td>Drop Off Address:</td>
				<td colspan="4">
					<input type="text" name="do_address" size="40" value="<?=$operator->do_address?>" />
				</td>				
			</tr>
			<tr>
				<td>Drop Off City:</td>
				<td colspan="4">
					<input type="text" name="do_city" size="40" value="<?=$operator->do_city?>" />
				</td>					
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top">Drop Off Notes:</td>
				<td>
					<textarea class="show_on_screen" name="deliv_notes" cols="20" rows="5" ><?=$operator->deliv_notes?></textarea>
					<span class="show_on_print"><?=$operator->deliv_notes?></span>
				</td>	
				<td valign="top">PC/Drop Off Notes:</td>
				<td>
					<textarea class="show_on_screen" name="env_deliv_notes" cols="20" rows="5" ><?=$operator->env_deliv_notes?></textarea>
					<span class="show_on_print"><?=$operator->env_deliv_notes?></span>
				</td>														
			</tr>					
			<tr>
				<td colspan="9"><hr /></td>
			</tr>			
			<tr>
				<td>Is Distributor</td>
				<td><input type="checkbox" value="Y" name="is_dist" <? if($operator->is_dist=="Y"){?> checked <? }?>  /></td>
				<td>Mailing List</td>
				<td><input type="checkbox" value="Y" name="is_current" <? if($operator->is_current=="Y"){?> checked <? }?>  /></td>
			</tr>
			<tr>		
				<td>Is Sub-Distributor</td>
				<td><input type="checkbox" value="Y" name="is_subdist" <? if($operator->is_subdist=="Y"){?> checked <? }?>  /></td>				
				<td>Is Alt Drop Off</td>
				<td><input type="checkbox" value="Y" name="is_alt_dropoff" <? if($operator->is_alt_dropoff=="Y"){?> checked <? }?>  /></td>								
				
			</tr>
			<tr>		
				<td>Is Drop Off</td>
				<td><input type="checkbox" value="Y" name="is_dropoff" <? if($operator->is_dropoff=="Y"){?> checked <? }?>  /></td>
				<td>S/Dist Sequence</td>
				<td><input type="text" name="subdist_seq" value="<?=$operator->subdist_seq?>" /></td>
			</tr>			
			<tr>				
				<td>Is Contractor</td>
				<td><input type="checkbox" value="Y" name="is_contr" <? if($operator->is_contr=="Y"){?> checked <? }?>  /></td>
				<td>S/Dist Rate Reduction Factor [%]</td>
				<td><input type="text" name="rate_red_fact" value="<?=$rate_red_fact?>" /></td>
			</tr>
			<!--<tr>				
				<td>Is Hauler NI</td>
				<td><input type="checkbox" value="Y" name="is_hauler_ni" <? if($operator->is_hauler_ni=="Y"){?> checked <? }?>  /></td>
			</tr>

			<tr>				
				<td>Is Hauler SI</td>
				<td><input type="checkbox" value="Y" name="is_hauler_si" <? if($operator->is_hauler_si=="Y"){?> checked <? }?>  /></td>
			</tr>-->
			
			<tr>
				<td colspan="9"><hr /></td>
			</tr>										
			<tr>
				<td>Trading as</td>
				<td><input size="40" type="text" name="alias" value="<?=$operator->alias?>" /> </td>					
			</tr>
			<tr>
				<td>Latest Departure Time</td>
				<td><input size="40" type="text" name="latest_dep" value="<?=$operator->latest_dep?>" /> </td>
			</tr>			
			<tr>
				<td>Date Started</td>
				<td>
					<script language="javascript">DateInput("date_started", true, "YYYY-MM-DD","<?=$date_started?>")</script>
				</td>
			</tr>
			<tr>
				<td>Date Left</td>
				<td>
					<script language="javascript">DateInput("date_left", true, "YYYY-MM-DD","<?=$date_left?>")</script>
				</td>
			</tr>															
			<tr>
				<td>Has Coural Contract</td>
				<td><input type="checkbox" value="Y" name="contract" <? if($operator->contract=="Y"){?> checked <? }?> /></td>
			</tr>															
			<tr>
				<td>Has Agency Contract</td>
				<td><input type="checkbox" value="Y" name="agency" <? if($operator->agency=="Y"){?> checked <? }?> /></td>				
			</tr>			
			<tr>
				<td colspan="9"><hr /></td>
			</tr>
			<tr>
				<td>Is Shareholder</td>
				<td><input type="checkbox" name="is_shareholder" value="Y" <? if($operator->is_shareholder=="Y"){ ?> checked <? }?> /> </td>						
			</tr>
			<tr>
				<td>Shares</td>
				<td><input size="10" type="text" name="shares" value="<?=$operator->shares?>" /> </td>								
			</tr>
			<tr>
				<td>Share Bought</td>				
				<td>
					<script language="javascript">DateInput("share_bought", true, "YYYY-MM-DD","<?=$share_bought?>")</script>
				</td>			
				<td>Share Sold</td>				
				<td>
					<script language="javascript">DateInput("share_sold", true, "YYYY-MM-DD","<?=$share_sold?>")</script>
				</td>													
			</tr>
			<tr>
				<td>Share Notes:</td>
				<td>
					<textarea class="show_on_screen" name="share_notes" cols="30" rows="6"><?=$operator->share_notes?></textarea>
					<span class="show_on_print"><?=$operator->env_deliv_notes?></span>
				</td>									
			</tr>
			<tr>
				<td colspan="9"><hr /></td>
			</tr>			
			<tr>
				<td colspan="2" align="center">				
					<input class="show_on_screen" type="submit" name="submit" value="Save" />
					<input class="show_on_screen" type="submit" name="cancel" value="Cancel" />
					<? if($action=="edit"){if($operator->client_id){?>
						<input class="show_on_screen" type="submit" name="makeclient" value="Remove From Parcel Client List" />
					<? }else{ ?>
						<input class="show_on_screen" type="submit" name="makeclient" value="Add To Parcel Client List" />
					<? }}?>
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="dest" value="<?=$action?>" />
					<input type="hidden" name="record" value="<?=$record?>" />
					<input type="hidden" value="N" name="is_hauler_si" />
					<input type="hidden" value="N" name="is_hauler_ni" />
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
// USES: 	coural.address/coural.operator/coural.client//
//////////////////////////////////////////////////////////

if($action=="" || !isset($action)){
	$where = "WHERE address_id IS NOT NULL ";

	if($choice == "all"){
	}
	else if($choice=="dist"){
		$where.=" AND is_dist='Y'";
	}
	else if($choice=="subdist"){
		$where.=" AND is_subdist='Y'";
	}
	else if($choice=="dropoff"){
		$where.=" AND is_dropoff='Y'";
	}		
	else if($choice=="altdo"){
		$where.=" AND is_alt_dropoff='Y'";
	}			
	else if($choice=="contr"){
		$where.=" AND is_contr='Y'";
	}
	else if($choice=="distshare"){
		$where.=" AND is_dist='Y' AND shares>0 ";
	}
	else if($choice=="subdistshare"){
		$where.=" AND is_subdist='Y' AND shares>0 ";
	}
	else if($choice=="contrshare"){
		$where.=" AND is_contr='Y' AND shares>0 ";
	}
	else if($choice=="share"){
		$where.=" AND shares>0 ";
	}
	else if(substr($choice,0,4)=="type"){
		$ch = substr($choice,5,strlen($choice));
		$where.=" AND type='$ch' ";
	}

	if(!$record && !$choice) $record=1;
		
	if($record) $where=" WHERE address_id='$record' ";
	
	
	$sql = "SELECT  address_id  AS ID,
					card_id  	AS 'Card ID',
					op.company	AS Operator,
					
					is_current 	AS 'Current',
					type 		AS 'Address Type',
					/*route.region AS Region,
					route.area	AS Area,
					route.code	AS RD,*/
					salutation 	AS Salutation,
					first_name 	AS 'First Name',
					address.name AS Name,
					salutation2	AS Salutation2,
					first_name2	AS 'First Name 2',
					name2 		AS 'Name 2',
					bank_num	AS 'Bank Acccount No.',
					gst_num		AS 'GST No.',
					
					IF(mail_type='e','EMailer',
						IF(mail_type='f','Faxer',
							IF(mail_type='m','Mailer',0))) AS 'Mail Type',
					IF(alt_mail_type='e','EMailer',
						IF(alt_mail_type='f','Faxer',
							IF(alt_mail_type='m','Mailer',0))) AS 'Mail Type 2',
					address		AS Address,
					address2	AS 'Address 2',
					postal_addr	AS 'Postal Address',
					city		AS City,
					postcode 	AS Postcode,
					op.do_address	AS 'DO Address',	
					op.do_city		AS 'DO City',	
					op.deliv_notes	AS 'DO Notes',	
					phone		AS Phone,
					phone2		AS 'Phone 2',
					mobile		AS 'Mobile',
					mobile2		AS 'Mobile 2',
					fax			AS 'Facsimile',
					alt_fax			AS 'Facsimile 2',
					email		AS 'Email',
					alt_email		AS 'Email 2',
					subdist_seq		AS 'S/Dist Seq.',
					ROUND(100*(1-rate_red_fact),0)   
									AS 'S/Dist Rate Red. Fact. [%]',
					
					op.is_dist		AS Dist,
					op.is_subdist	AS Subdist,
					op.is_dropoff	AS DropOff,
					op.is_alt_dropoff	AS 'Alt DropOff',
					op.is_contr	 	AS Contr,
					op.is_hauler_ni	 	AS 'NI Hauler',
					op.is_hauler_si	 	AS 'SI Hauler',
					IF(op.latest_dep='00:00:00','',op.latest_dep)				
									AS 'Latest Departure Time',
					op.alias		AS 'Trades as',
					op.is_shareholder AS 'Is Shareholder',
					op.shares		AS Shares,
					IF(op.share_bought<'1999-01-01','',op.share_bought)
									AS 'Share Bought',
					IF(op.share_sold<'1999-01-01','',op.share_sold)
									AS 'Share Sold',										
					op.share_notes	AS 'Share-Notes',
					IF(op.date_started<'1999-01-01','',op.date_started)
									AS 'Date Started',
					IF(op.date_left<'1999-01-01','',op.date_left)
									AS 'Date Left',
					op.contract	 	AS 'Contract',
					op.agency		AS 'Agency',
					IF(client_id IS NOT NULL,
						CONCAT('<a href=\'admin_client.php?action=edit&record=',client_id,'\'>Client Record</a>'),
						'N/A')
					AS 'Client Record'
					
					FROM address
					LEFT JOIN
					operator op
					ON op.operator_id=address.operator_id
					$where
					ORDER BY op.company,address.name";
	$clientTab = new MySQLTable("admin_address.php",$sql);
	$clientTab->showRec  = 0;
	$clientTab->highlightField="Operator";
	$clientTab->wrap1    = "Mail Type 2";
	$clientTab->wrap2    = "S/Dist Rate Red. Fact. [%]";
//	$clientTab->captionF = "Operator";
//	$clientTab->caption  = "Operator: ";
	$clientTab->writeList();		

}
