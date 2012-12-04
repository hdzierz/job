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

if($action=="select_jobs"){
	if(!$date) $date = date("Y/m/t");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form method="post" action="proc_invoice.php" name="interface">
		<table>
			<tr>
				<td>
					Month:
					<?
						$sel_month = new Select("month");
						$sel_month->setOptionIsVal($month);
						$sel_month->writeMonthSelect();
					?>
				</td>
				<td>
					Year:
					<?
						$sel_year = new Select("year");
						$sel_year->setOptionIsVal($year);
						$sel_year->writeYearSelectFT();
					?>
				</td>
				<td>
					Show unprocessed only(Y): <input type="checkbox" name="unproc_only" value="Y" <?php if($unproc_only=='Y'){ ?> checked <?php } ?> />
				</td>
				<td>Invoice Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				</td>
				<!--<td>
					FS: <input type="text" name="fuel_surcharge" value="<?=$fuel_surcharge?>" />
				</td>-->
				<td>
					<input class="coural_button" type="submit" name="reload" value="Show"  />
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="select_jobs" />
	</form>
<?	
}

?>
