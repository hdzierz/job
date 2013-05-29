<div style="float:right; margin:0.5ex 0.2em 0.2ex 1em ">
	<!--<p><a href="#" onclick="print_preview(); return false;">Print this page</a></p>-->

	<!--<a href="reports.php?report=by_region&region=<?=$region?>&action=export" ><img border="0" src="images/excel.jpg" alt="Export to excel"  /> </a>-->
	<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
</div>
	<script language="javascript">
		function message(id){
			var result = "<select name=\"dummy\" size=\"10\" style=\"width:15em \"><option style=\'font-size:0.8em;background-color:#990000; color:black;\'>loading...</option></select>"
			document.getElementById(id).innerHTML = result;  
		}
		function set_Button_on(){
			var result = "<input name=\'submit1\' value=\'Show\' type=\'submit\' />";
			document.getElementById('add_route_wrap1').innerHTML = result;  
			var result = "<input name=\'submit2\' value=\'Export\' type=\'submit\' />";
			document.getElementById('add_route_wrap2').innerHTML = result;  			
		}
		
		function set_SButton_on(){
			document.getElementById('submit_show').disabled = false;  	 
			document.getElementById('submit_export').disabled = false;  			
		}
		
		function set_Button_off(){
			var result = "<input disabled name=\'submit1\' value=\'Show\' type=\'submit\' />";
			document.getElementById('add_route_wrap1').innerHTML = result;  
			var result = "<input disabled  name=\'submit2\' value=\'Export\' type=\'submit\' />";
			document.getElementById('add_route_wrap2').innerHTML = result;  	
		}			
		
	</script>
	<script src="javascripts/ajax.js" type="text/javascript" language="javascript"></script>
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

if($action=="search_tickets" || $action=="show_ticket_notes"){
?>
	<form name="manage_rates" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Start Ticket:</td>
				<td><input type="text" name="start_ticket" value="<?=$start_ticket?> " /></td>
				<td>Final Ticket:</td>
				<td><input type="text" name="final_ticket" value="<?=$final_ticket?> " /></td>
				<td colspan="4" align="center">
					<input type="submit" name="submit" value="Search" />
				</td>
			</tr>
		</table>
	</form>
<?
}

if($action=="manage_rates"){
	if(!$start_date) $start_date = date("Y-m-d");
?>
	<form name="manage_rates" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<?
						$sel_year = new MySQLSelect ("start_date","start_date","parcel_rates","parcels.php","start_date","start_date");
						$sel_year->selectOnChange="";
						$sel_year->sortOrder="DESC";
						$sel_year->setOptionIsVal($start_date);
						$sel_year->startSelect();
						$sel_year->writeSelect();
						$sel_year->stopSelect();					
					?>
				</td>
				<td colspan="4" align="center">
					<input type="submit" name="submit" value="Show" />
				</td>
			</tr>
		</table>
	</form>
<?
}

if($action=="show_tickets"){
?>
	<form name="sell_tickets" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Delivery #:</td>
				<td>
					<td><input type="text" name="job_no" id="job_no" value="<?=$job_no?>" /></td>
				</td>
				<td colspan="4" align="center">
					<input type="submit" value="Show!" />
				</td>
			</tr>
		</table>
	</form>
<?
}
if($action=="receive_tickets"){
	

	if($date) $date_show = $date;
	else if(!$date_show) $date_show = date("Y-m-d");


?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="receive_tickets" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Date:</td>
				<td>
					<script language="javascript">DateInput("date_show", true, "YYYY-MM-DD","<?=$date_show?>")</script>
				</td>
				<!--
				<td>Final Date:</td>
				<td>
					<script language="javascript">DateInput("date_final", true, "YYYY-MM-DD","<?=$date_final?>")</script>
				</td>-->
				<!--<td>
					Receipt Number: <input type="text" name="parcel_th_receipt_id" value="<?=$parcel_th_receipt_id?>" />
				</td>-->
				<td colspan="4" align="center">
					<input type="submit" value="Show previous date!" />
				</td>
			</tr>
		</table>
	</form>
<?
}

if(!$action){
	$last_day_of_month = date("t");
	if(!$date_final) $date_final=date("Y-m-$last_day_of_month");
	if(!$date_start) $date_start=date("Y-m-01");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="redeem" action="parcels.php?action=update_pjr" method="post">
		<input style="float:right " type="submit" name="submit" value="Update Redemption Rates" />
	</form>
	<form name="weekly_job" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<script language="javascript">DateInput("date_start", true, "YYYY-MM-DD","<?=$date_start?>")</script>
				</td>
				<td>Final Date:</td>
				<td>
					<script language="javascript">DateInput("date_final", true, "YYYY-MM-DD","<?=$date_final?>")</script>
				</td>
				<td colspan="4" align="center">
					<input type="submit" value="Show!" />
				</td>
				<td>
					
				</td>
			</tr>
		</table>
	</form>
	
<?
}

if($action=="show_redeemed"){
	if(!$real_date) $real_date=date("Y-m-d");
	if($run) $run_inter=$run;
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="redeem" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Select Distributor:</td>
				<td>
<?
					$sel = new MySQLSelect("company","operator_id","operator","reports.php","redeem","dist_id");
					$sel->addSQLWhere("is_dist",'Y');
					$sel->optionDefText	 = "Please Select...";
					$sel->optionDefValue = "";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>
				<td>Date:</td>										
				<td>
					<script language="javascript">DateInput("real_date", true, "YYYY-MM-DD","<?=$real_date?>")</script>
				</td>
				<td>Page:</td>
				<td><input type="text" name="run_inter" id="run_inter" value="<?=$run_inter?>" /></td>
				<td><input type="submit" name="submit" value="Show" /></td>
			</tr>
		</table>
	</form>
<?
}

if($action=="process_xerox_scan2"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="redeem" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Select Distributor:</td>
				<td>
<?
					$sel = new MySQLSelect("company","operator_id","operator","reports.php","redeem","dist_id");
					$sel->addSQLWhere("is_dist",'Y');
					$sel->optionDefText	 = "All";
					$sel->optionDefValue = "";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>
				<td>Date:</td>										
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>
				</td>
				<!--<td>Type:</td>
				<td>
<?
					$sel = new Select("type");		
					$sel->setOptionIsVal($type);	
					$sel->start();
						$sel->addOption("CD","CD");
						$sel->addOption("CP","CP");
						$sel->addOption("SR","SR");
						$sel->addOption("EX","EX");
					$sel->stop();
?>					
				</td>-->
				<td>Show processed (yes)</td>
				<td>
					<input <? if($is_processed){ ?> checked <? }?> type="checkbox" value="1" name="is_processed" />
				</td>
				<td><input type="submit" name="filter" value="Show" /></td>
			</tr>
		</table>
		<input type="hidden" name="action" id="action" value="<?=$action?>" />
	</form>
<?
}

if($action=="print_ticket_header_sheet"){
	?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="redeem" action="parcels.php?action=<?=$action?>" method="post">
		<table>
			<tr>
				<td>Select Distributor:</td>
				<td>
<?
					$sel = new MySQLSelect("company","operator_id","operator","reports.php","redeem","dist_id");
					$sel->addSQLWhere("is_dist",'Y');
					$sel->optionDefText	 = "All";
					$sel->optionDefValue = "";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>
				<td>Select Contractor:</td>
				<td>
<?
					$sel = new MySQLSelect("company","operator_id","operator","reports.php","redeem","contr_id");
					$sel->addSQLWhere("is_contr",'Y');
					$sel->optionDefText	 = "All";
					$sel->optionDefValue = "";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($contr_id);
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>
				<td><input type="submit" name="filter" value="Show" /></td>
			</tr>
		</table>
		<input type="hidden" name="action" id="action" value="<?=$action?>" />
	</form>
<?
}

if($action=="gst"){
?>
	<form name="configure_gst" action="parcels.php" method="get">
		<table>
			<tr>
				<td>GST in %:</td>
				<td>
					<input name="gst" type="text" value="<?php echo 100*$GST_PARCEL; ?>" />		
				</td>
				<td>
					<input type="submit" value="Save" onclick="return confirm('Changing the GST has a major impact on the system\'s behaviour. Continue?')" />
					<input type="hidden" name="action" value="gst" />
				</td>
			</tr>
		</table>
	</form>
<?
}	

?>	
