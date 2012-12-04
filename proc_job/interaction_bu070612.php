<script>
function printpreview()
{
var OLECMDID = 7;
/* OLECMDID values:
* 6 - print
* 7 - print preview
* 1 - open window
* 4 - Save As
*/
var PROMPT = 1; // 2 DONTPROMPTUSER
var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
WebBrowser1.ExecWB(OLECMDID, PROMPT);
WebBrowser1.outerHTML = "";
}
</script>

<?
if(!$job_id)
	$job_id=$record;

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

if(!$job_id) $job_id=$record;
$alt_job_id = get("job","alt_job_id","WHERE alt_job_id='$job_id'");
if(!$alt_job_id && ($action=="" || $action=="save_as_new" || $action=="edit")){
	$is_quote = get("job","is_quote","WHERE job_id='$job_id'");	
?>
<!--	<input class="coural_button" type="button" name="Confirm" value="Confirm" onClick="window.location.href='index.php?action=confirm&job_id=<?=$job_id?>'" />-->
<?
	if($is_quote=='Y'){
?>
		<input class="coural_button" type="button" name="Close" value="Close Screen" onClick="window.location.href='index.php?action=show_quotes'" />&nbsp;&nbsp;
<?
	}
	else{
?>
		<input class="coural_button" type="button" name="Close" value="Close Screen" onClick="window.location.href='index.php'" />&nbsp;&nbsp;
<?	
	}
?>	
	<input class="coural_button" type="button" name="Saveasnew" value="Save As New" onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=save_as_new&job_id=<?=$job_id?>'" />
	<input class="coural_button" type="button" name="Saveasnew" value="Save As Template" onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=save_as_template&job_id=<?=$job_id?>'" />	
	<input class="coural_button" type="button" name="Saveasnew" value="Save As New Updated" onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=save_as_new_updated&job_id=<?=$job_id?>'" />
	<input class="coural_button" type="button" name="ShowDP" value="Show DO Det." onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=show_do_details&job_id=<?=$job_id?>'" />
	<input class="coural_button" type="button" name="ShowDP" value="Show DO Det. PB" onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=show_do_details_with_pagebreak&job_id=<?=$job_id?>'" />
	<!--<input class="coural_button" type="button" name="ShowJD" value="Show Sum. Job Det." onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=show_job_details1&job_id=<?=$job_id?>'" />-->
	<input class="coural_button" type="button" name="CheckN" value="Show Job Det." onClick="window.location.href='proc_job.php?pub=<?=$pub?>&client=<?=$client?>&action=show_job_details&job_id=<?=$job_id?>&choice=0'" />
<?
	$alt_job_id = get("job","alt_job_id","WHERE job_id='$job_id'");	
	if($alt_job_id){
?>
		<input class="coural_button" type="button" name="attach" value="Detach Job" onClick="window.location.href='proc_job.php?action=detach_job&alt_job_id=<?=$alt_job_id?>&job_id=<?=$job_id?>'" />	
<?
	}
	else{
?>
		<input class="coural_button" type="button" name="attach" value="Attach Job" onClick="window.location.href='proc_job.php?action=attach_job&job_id=<?=$job_id?>'" />	
<?	
	}	
?>	
	
<?	
}	
else if($action=="show_do_details"){
	$str = "window.location.href='proc_job.php?pub=$pub&client=$client&action=edit&job_id=$job_id'";
	$estr = "window.location.href='proc_job/export.php?pub=$pub&client=$client&action=$action&job_id=$job_id'";
	$sostr = "window.location.href='proc_job.php?pub=$pub&client=$client&action=show_do_details_send&job_id=$job_id'";
?>
	<input class="coural_button" type="button" name="Print" value="Print" onClick="self.print();" />
	<input class="coural_button" type="button" name="export" value="Export" onClick="<?=$estr?>" />
	<input class="coural_button" type="button" name="Back" value="Back" onClick="<?=$str?>" />
	<input class="coural_button" type="button" name="export" value="Send Out" onClick="<?=$sostr?>" />
<?	
}
else if($action=="show_job_details"){
	$str = "window.location.href='proc_job.php?pub=$pub&client=$client&action=edit&job_id=$job_id'";
	$rstr = "window.location.href='proc_job.php?pub=$pub&client=$client&action=show_job_details&job_id=$job_id&choice='+document.interface.choice.value";
	$estr = "window.location.href='proc_job/export.php?pub=$pub&client=$client&action=$action&job_id=$job_id&choice='+document.interface.choice.value";
	$sstr = "window.location.href='proc_job.php?pub=$pub&client=$client&action=show_job_details_send&job_id=$job_id&choice='+document.interface.choice.value";

?>
	<form method="post" action="proc_job.php" name="interface">
	<table>
		<tr>
			<td>
				<input class="coural_button" type="button" name="Print" value="Print" onClick="self.print();" />
			</td>
			<td>
				<input class="coural_button" type="button" name="Back" value="Back" onClick="<?=$str?>" />
			</td>
			<td>
		<?	
				$sel = new Select("choice");
				$sel->setOptionIsVal($choice);
				$sel->defaultText = "All";
				$sel->start();
					$sel->addOption("bbh","BBH");
					$sel->addOption("mail","MAILINGS");
				$sel->stop();
		?>	
			</td>
			<td>
				<input class="coural_button" type="button" name="reload" value="Reload" onClick="<?=$rstr?>" />
			</td>
			<td>
				<input class="coural_button" type="button" name="export" value="Export" onClick="<?=$estr?>" />
			</td>		
			<td>
				<input class="coural_button" type="button" name="export" value="Send Out" onClick="<?=$sstr?>" />
			</td>		
			
		</tr>
	</table>
	</form>
<?	
}


else if($action=="check_numbers"){
	$str = "window.location.href='proc_job.php?pub=$pub&client=$client&action=edit&job_id=$job_id'";
	$estr = "window.location.href='proc_job/export.php?pub=$pub&client=$client&action=$action&job_id=$job_id'";
?>
	<input class="coural_button" type="button" name="Print" value="Print" onClick="self.print();" />
	<input class="coural_button" type="button" name="export" value="Export" onClick="<?=$estr?>" />
	<input class="coural_button" type="button" name="Back" value="Back" onClick="<?=$str?>" />
<?	
}

if($action=="show_do_details_with_pagebreak" || $action=="show_do_details_with_island_pb"){
	$str = "window.location.href='proc_job.php?pub=$pub&client=$client&action=edit&job_id=$job_id'";
	$estr = "window.location.href='proc_job/export.php?pub=$pub&client=$client&action=$action&job_id=$job_id'";
	$istr = "window.location.href='proc_job.php?pub=$pub&client=$client&action=show_do_details_with_island_pb&job_id=$job_id'";
?>
	<input class="coural_button" type="button" name="island_pb" value="Island PB" onClick="<?=$istr?>" />
	<input class="coural_button" type="button" name="Print" value="Print" onClick="self.print();" />
	<input class="coural_button" type="button" name="export" value="Export" onClick="<?=$estr?>" />
	<input class="coural_button" type="button" name="Back" value="Back" onClick="<?=$str?>" />
<?	
}
?>
