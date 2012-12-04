<?

//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.user									//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////

if($action=="edit"||$action=="add"){
	$sql  = "SELECT * FROM user WHERE user_id='$record'";
	
	$user = mysql_fetch_object(query($sql));
?>
	<form name="edituser" action="admin_user.php?action=save" method="post">
		<table>
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" value="<?=$user->username?>" /> </td>
			</tr>
			<tr>
				<td>Password</td>
				<td>
					<input type="password" name="passwd" value="" /> 
				</td>
			</tr>
			<tr>
				<td>Retype Password</td>
				<td>
					<input type="password" name="passwd2" value="" /> 
				</td>
			</tr>			
			
			<tr>
				<td>Current Jobs:</td>
				<td><input type="checkbox" name="page_main" value="Y" <? if($user->page_main=="Y"){ ?> checked <? }?> /></td>
			</tr>	
			
			<tr>
				<td>Job Booking:</td>
				<td><input type="checkbox" name="page_procjob" value="Y" <? if($user->page_procjob=="Y"){ ?> checked <? }?> /></td>
			</tr>	
			
			<tr>
				<td>Maintanance Reports:</td>
				<td><input type="checkbox" name="page_reports" value="Y" <? if($user->page_reports=="Y"){ ?> checked <? }?> /></td>
			</tr>	
			<tr>
				<td>Job Reports:</td>
				<td><input type="checkbox" name="page_rep_revenue" value="Y" <? if($user->page_rep_revenue=="Y"){ ?> checked <? }?> /></td>
			</tr>	
			
			<tr>
				<td>Invoices:</td>
				<td><input type="checkbox" name="page_invoice" value="Y" <? if($user->page_invoice=="Y"){ ?> checked <? }?> /></td>
			</tr>	
			
			<tr>
				<td>Parcels:</td>
				<td><input type="checkbox" name="page_parcels" value="Y" <? if($user->page_parcels=="Y"){ ?> checked <? }?> /></td>
			</tr>		
			<tr>
				<td>Parcel Reports:</td>
				<td><input type="checkbox" name="page_rep_parcels" value="Y" <? if($user->page_rep_parcels=="Y"){ ?> checked <? }?> /></td>
			</tr>			
			
			
			<tr>
				<td>Users:</td>
				<td><input type="checkbox" name="page_useradmin" value="Y" <? if($user->page_useradmin=="Y"){ ?> checked <? }?> /></td>
			</tr>
			<tr>
				<td>Routes:</td>
				<td><input type="checkbox" name="page_routeadmin" value="Y" <? if($user->page_routeadmin=="Y"){ ?> checked <? }?> /></td>
			</tr>
			<tr>
				<td>Clients:</td>
				<td><input type="checkbox" name="page_clientadmin" value="Y" <? if($user->page_clientadmin=="Y"){ ?> checked <? }?> /></td>
			</tr>			
			<tr>
				<td>Addresses:</td>
				<td><input type="checkbox" name="page_addradmin" value="Y" <? if($user->page_addradmin=="Y"){ ?> checked <? }?> /></td>
			</tr>	
			<tr>
				<td>GST:</td>
				<td><input type="checkbox" name="gst" value="Y" <? if($user->gst=="Y"){ ?> checked <? }?> /></td>
			</tr>			
			
			
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" value="Save" />
					<input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_user.php'" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="dest" value="<?=$action?>" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="record" value="<?=$record?>" />
		<input type="hidden" name="message" value="Test" />
	</form>
	<hr class="sqldivider" />
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
	$sql = "SELECT   user_id AS Record,
					 username AS User, 
					 page_useradmin AS Users,
					 page_routeadmin AS Routes,
					 page_clientadmin AS Clients,
					 page_addradmin AS Addresses,
					 page_main AS 'Current Jobs',
					 page_procjob AS 'Job Booking',
					 page_reports AS 'Maint. Reports',
					 page_rep_revenue AS 'Job Reports',
					 page_invoice AS 'Invoices',
					 page_parcels AS Parcels,
					 page_rep_parcels AS 'Parcel Reports',
					 gst AS 'change GST'
			FROM user";
	$userTab = new MySQLTable("admin_user.php",$sql);
	$userTab->showRec=0;
	$userTab->startTable();
	$userTab->writeTable();
	$userTab->stopTable();		
}


?>