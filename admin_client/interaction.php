<?
if($MESSAGE){
?>		
		<div id="message" >
			<?=$MESSAGE?>
		</div>
<?
}
if($ERROR){
?>		
		<div id="error" >
			<?=$ERROR?>
		</div>
<?
}

if($action=="manage_price_template"){
	if($dest!="edit"){
?>
	<form name="load_data" action="admin_client.php" method="get">
<?
		$sel = new MySQLSelect("name","client_id","client","admin_client.php","client_id","client_id");
		$sel->addSQLWhere("is_parcel_courier",'0');
		$sel->selectOnChange = "";
		$sel->setOptionIsVal($client_id);
		//$sel->orderField="island,seq_region";
		$sel->startSelect();
		$sel->writeSelect();
		$sel->stopSelect();
?>		
		<input type="submit" name="filter" value="Show" />
		<input type="hidden" name="action" value="<?=$action?>" />
	</form>
<?
	}
	else{
?>
	<form name="load_temp" action="admin_client.php" method="get">
		Load from other client: &nbsp;
<?
		$sel = new MySQLSelect("name","client_id","client","admin_client.php","client_id","temp_client_id");
		$sel->addSQLWhere("is_parcel_courier",'0');
		$sel->selectOnChange = "";
		$sel->setOptionIsVal($temp_client_id);
		//$sel->orderField="island,seq_region";
		$sel->startSelect();
		$sel->writeSelect();
		$sel->stopSelect();
?>		
		<input type="submit" name="filter" value="Load" />
		<input type="hidden" name="action" value="<?=$action?>" />
		<input type="hidden" name="dest" value="<?=$dest?>" />
		<input type="hidden" name="client_id" value="<?=$client_id?>" />
	</form>
<?
	}
}
if($action=="show_branches"){
?>
	<input type="button" name="back"  onClick="document.location.href='admin_client.php'" value="Back" />
<?	
}

if(!$action){
?>
	<form action="admin_client.php" method="get">
		<select name="is_courier">
			<option <? if($is_courier=="-1"){ ?> selected <? }?>  value="-1">All</option>
			<option <? if($is_courier==1){ ?> selected <? }?> value="1">Couriers</option>
			<option <? if($is_courier==0){ ?> selected <? }?> value="0">Circular Clients</option>
		</select>
		<input type="submit" name="submit" value="Show" />
	</form>
<?
}

?>

