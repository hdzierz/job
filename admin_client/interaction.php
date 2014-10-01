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
   <script type="text/javascript" src="javascripts/prototype.js"></script>
   <script type="text/javascript" src="javascripts/effects.js"></script>
   <script type="text/javascript" src="javascripts/controls.js"></script>
   <script type="text/javascript" src="includes/calendarDateInput.js"></script>

	<form action="admin_client.php" method="get">
        <table>
            <tr>
                <td>
            		<select name="is_courier">
	    	        	<option <? if($is_courier=="-1"){ ?> selected <? }?>  value="-1">All</option>
		            	<option <? if($is_courier==1){ ?> selected <? }?> value="1">Couriers</option>
			         <option <? if($is_courier==0){ ?> selected <? }?> value="0">Circular Clients</option>
       	        	</select>
                </td>
                <td>
                    <input type="submit" name="submit" value="Show" />
                </td>
                <td>
                    Client: 
                    <input type="text" id="client" name="client" />
                    <div id="hint" class="hint"></div>
                    <script type="text/javascript">
                        new Ajax.Autocompleter("client","hint","includes/search_server.php");
                    </script>
                </td>
            </tr>
        </table>
	</form>
<?
}

?>

