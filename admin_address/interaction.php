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


if($action=="" || !isset($action) || $cancel=="Cancel"){
?>
		<script type="text/javascript" src="javascripts/prototype.js"></script>
		<script type="text/javascript" src="javascripts/effects.js"></script>
		<script type="text/javascript" src="javascripts/controls.js"></script>

		<form name="addresstype" action="admin_address.php" method="get">
			<table width="90%">
				<tr>
					<td>
						Name:
						<input type="text" id="name" name="name" />
						<div id="hint" class="hint"></div>
						<script type="text/javascript">	
							new Ajax.Autocompleter("name","hint","includes/search_server.php");
						</script>      
					</td>
					<td>
						Company:
						<input type="text" id="company" name="company" />
						<div id="hint" class="hint"></div>
						<script type="text/javascript">	
							new Ajax.Autocompleter("company","hint","includes/search_server.php");
						</script>      
					</td>	
                    <td>
                        ID:
                        <input type="text" name="operator" value="" />
                    </td>				
					<td align="center">
		<?
						$qry = "SELECT DISTINCT type FROM address WHERE type<>'' AND type IS NOT NULL";
						$res_type = query($qry);
		
						$sel = new Select("choice");
						$sel->setOptionIsVal($choice);
						$sel->defaultText="All";
						if(!$choice)
							$sel->setOptionIsVal("All");
						$sel->start();
						$sel->addOption("dist","Distributors");
						$sel->addOption("subdist","Sub.-Distributors");
						$sel->addOption("dropoff","Drop Off Points");
						$sel->addOption("altdo","Alternate Drop Off Points");						
						$sel->addOption("contr","Contractors");
						$sel->addOption("share","All Shareholders");
						$sel->addOption("distshare","Distributors (Shareholders)");
						$sel->addOption("subdistshare","Sub.-Distributors (Shareholders)");
						$sel->addOption("contrshare","Contractors (Shareholders)");
						$sel->stop();
		?>
						<input type="submit" name="submit" value="Show" />
					</td>
				</tr>
			</table>
		</form>
		
<?		
}
?>
