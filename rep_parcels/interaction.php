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

if($report=="ticket_sold"){
	if(!$start_date) $start_date=date("2008-10-01");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>					
				</td>
				<td>Select Final Date:</td>
				<td>
					<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>					
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="report" value="ticket_sold" />
	</form>
<?
}


if($report=="ticket_trace"){
    if(!$start_date) $start_date=date("2008-10-01");
    if(!$final_date) $final_date=date("Y-m-d");
?>
    <script type="text/javascript" src="includes/calendarDateInput.js"></script>
    <form name="narrow" action="rep_parcels.php" method="get" >
        <table>
            <tr>
                <td>Select Start Date:</td>
                <td>
                    <script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>
                </td>
                <td>Select Final Date:</td>
                <td>
                    <script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>
                </td>
                <td>
                    <input type="submit" name="submit" value="Run" />
                </td>
            </tr>
        </table>
        <input type="hidden" name="report" value="ticket_trace" />
    </form>
<?
}


if($report=="ticket_unredeemed" || $report=="ticket_unredeemed_val"){
	if(!$date) $date=date("Y-m-t");

?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				</td>
				<td>Select number of months:</td>
				<td>
					<?
						$sel = new Select("num_months");
						$sel->setOptionIsVal($num_months);
						$sel->start();
							$sel->addOption("1","1");
							$sel->addOption("2","2");
							$sel->addOption("3","3");
							$sel->addOption("4","4");
							$sel->addOption("5","5");
							$sel->addOption("6","6");
							$sel->addOption("7","7");
							$sel->addOption("8","8");
							$sel->addOption("9","9");
							$sel->addOption("10","10");
							$sel->addOption("11","11");
							$sel->addOption("12","12");
						$sel->stop();
					?>					
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="report" value="<?php echo $report; ?>" />
	</form>
<?
}


if($report=="ticket_unsold"){
	if(!$start_date) $start_date=date("2008-10-01");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>					
				</td>
				<td>Select Final Date:</td>
				<td>
					<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>					
				</td>
				<td>
					<?
							$sel = new Select("type");
							$sel->setOptionIsVal($type);
							$sel->start();
								$sel->addOption("CP","Parcels");
								$sel->addOption("CD","Documents");
								$sel->addOption("SR","Signature");
								$sel->addOption("RP","Pickup");
							$sel->stop();
						?>
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="report" value="ticket_unsold" />
	</form>
<?
}


if($report=="ticket_redeemed_by_contractor"){
	if(!$start_date) $start_date=date("Y-m-d");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>					
				</td>
				<td>Select Final Date:</td>
				<td>
					<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>					
				</td>
			</tr>
			<tr>
				<td>OR Redemption Month:</td>
				<td>
						<?
							$sel = new Select("date_year");
							$sel->setOptionIsVal($date_year);
							$sel->writeYearSelectFT();
							
							$sel = new Select("date_month");
							$sel->setOptionIsVal($date_month);
							$sel->writeMonthSelect();
						?>
				</td>
			</tr>
			<tr>
				<td>Distributor:</td>
				<td>
<?				
					$sql_op = "SELECT 0 AS id, 'All' AS name
                                UNION
                                (
                                SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company
                                )";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_dist","dist_id");
					//$sel->optionDefText = "All";
					$sel->optionDefVal = "0";
					$sel->selectOnChange="";
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();					
?>									
				</td>
			</tr>
            <tr>    
                <td>Mobile:<input type="checkbox" name="mobile" value="true" <? if($mobile){ ?> checked <? } ?>></td>
            </tr>
			<tr>
				<td>
					<input type="submit" name="submit" value="Run" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="report" value="ticket_redeemed_by_contractor" />
	</form>
<?
}

if($report=="ticket_redeemed"){
	if(!$start_date) $start_date=date("Y-m-d");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>					
				</td>
				<td>Select Final Date:</td>
				<td>
					<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>					
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
					<!--<input type="submit" name="submit" value="Export" />-->
				</td>
				<!--<td>
					<a href="reports.php?report=by_dist&dist_id=<?=$dist_id?>&action=export" >
						<img border="0" src="images/excel.jpg" alt="Export to excel"  />
					</a>
				</td>-->				
			</tr>
		</table>
		<input type="hidden" name="report" value="ticket_redeemed" />
	</form>
<?
}
if($report=="ticket_redeemed2"){
	if(!$start_date) $start_date=date("Y-m-d");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Year:</td>
				<td>
<?
					$sel = new Select("year");
						$sel->setOptionIsVal($year);
					$sel->writeYearSelectFT();
?>													
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
					<!--<input type="submit" name="submit" value="Export" />-->
				</td>
			</tr>
		</table>
		<input type="hidden" name="report" value="ticket_redeemed2" />
	</form>
<?
}
if($report=="invoice"){
	if($send_report_id){
		$qry = "SELECT * FROM send_report WHERE send_report_id='$send_report_id'";
		$res = query($qry);
		$rep = mysql_fetch_object($res);
		$start_date = $rep->start_date;
		$final_date = $rep->final_date;
		$dist_id = $rep->dist_id;
	}
	if(!$start_date) $start_date=date("Y-m-d");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<script language="javascript">
		function change_target(){
			document.getElementById("report").value="invoice_send";
		}
	</script>
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>					
				</td>
			</tr>
			<tr>
				<td>Select Final Date:</td>
				<td>
					<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>					
				</td>
			</tr>
			<tr>
				<td>OR</td>
			</tr>
			<tr>
				<td>Redemption Month:</td>
				<td>
						<?
							$sel = new Select("date_month");
							$sel->setOptionIsVal($date_month);
							$sel->writeMonthSelect();
						?>
						<?
							$sel = new Select("date_year");
							$sel->setOptionIsVal($date_year);
							$sel->writeYearSelectFT();
							
	
						?>
				</td>
			</tr>
			<tr>
				<td>Distributor:</td>
				<td>
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_dist","dist_id");
					//$sel->optionDefText = "All";
					$sel->optionDefVal = "0";
					$sel->selectOnChange="";
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();					
?>									
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" value="Run" />
					<input type="submit" name="submit" value="Create Invoice #" />
					<?
						if($submit){
					?>
							<input type="submit" name="submit" value="Send Out" onClick="change_target()" />
					<?
						}
					?>
				</td>
			</tr>
		</table>
		<input id="report" type="hidden" name="report" value="invoice" />
	</form>
<?
}
if($report=="tickets_received"){
	if(!$start_date) $start_date=date("Y-m-d");
	if(!$final_date) $final_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Select Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>					
				</td>
				<td>Select Final Date:</td>
				<td>
					<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>					
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
					<!--<input type="submit" name="submit" value="Export" />-->
				</td>
				<!--<td>
					<a href="reports.php?report=by_dist&dist_id=<?=$dist_id?>&action=export" >
						<img border="0" src="images/excel.jpg" alt="Export to excel"  />
					</a>
				</td>-->				
			</tr>
		</table>
		<input type="hidden" name="report" value="tickets_received" />
	</form>
<?
}

if($report=="ticket_sales"){
	?>
		<form name="narrow" action="rep_parcels.php" method="get" >
		<table>
			<tr>
				<td>Redemption Month:</td>
				<td>
						<?
							$sel = new Select("date_month");
							$sel->setOptionIsVal($date_month);
							$sel->writeMonthSelect();
						?>
						<?
							$sel = new Select("date_year");
							$sel->setOptionIsVal($date_year);
							$sel->writeYearSelectFT();
							
	
						?>
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
				</td>
			</tr>
		</table>
		<input id="report" type="hidden" name="report" value="ticket_sales" />
	</form>
<?php 
}

?>	
