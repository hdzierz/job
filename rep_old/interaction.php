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
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
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
?>	

<?php
if($report=="envelopes"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="addresstype" action="rep_old.php" method="get">
		<table>
			<tr>
				<td colspan="3"></td>
				<th>Include</th>
				<th>Exclude</th>
				<th>Both</th>
			</tr>
			<tr>
				<td>Select Distributor:</td>
				<td>
<?
					$sel = new MySQLSelect("company","operator_id","operator","reports.php");
					$sel->addSQLWhere("is_dist",'Y');
					$sel->optionDefText	 = "All";
					$sel->optionDefValue = "All";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($company);
					//$sel->orderField="island,seq_region";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>	
				<td style=" font-style:italic ">Is Current: </td>
				<td>
					<input type="radio" name="is_current" value="Y" <? if($is_current=='Y'){ ?> checked <? }?> />
				</td>			
				<td>
					<input type="radio" name="is_current" value="N" <? if($is_current=='N'){ ?> checked <? }?> />
				</td>
				<td>
					<input type="radio" name="is_current" value="B" <? if($is_current=='B'){ ?> checked <? }?> />
				</td>		
				<td>
					<input type="submit" name="submit" value="Run!" />
					<input type="hidden" name="report" value="envelopes" />
				</td>			
			</tr>
			<tr>
				<td>Select Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				</td>
				<td style=" font-style:italic ">Distributor: </td>
				<td>
					<input type="radio" name="is_dist" value="Y" <? if($is_dist=='Y'){ ?> checked <? }?> />
				</td>
				<td>
					<input type="radio" name="is_dist" value="N" <? if($is_dist=='N'){ ?> checked <? }?> />
				</td>			
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style=" font-style:italic ">S/Distributor: </td>
				<td>
					<input type="radio" name="is_subdist" value="Y" <? if($is_subdist=='Y'){ ?> checked <? }?> />
				</td>	
				<td>
					<input type="radio" name="is_subdist" value="N" <? if($is_subdist=='N'){ ?> checked <? }?> />
				</td>	
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style=" font-style:italic ">Contractor: </td>
				<td>
					<input type="radio" name="is_contractor" value="Y" <? if($is_contractor=='Y'){ ?> checked <? }?> />
				</td>	
				<td>
					<input type="radio" name="is_contractor" value="N" <? if($is_contractor=='N'){ ?> checked <? }?> />
				</td>	
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style=" font-style:italic ">Is Shareholder: </td>
				<td>
					<input type="radio" name="is_shareholder" value="Y" <? if($is_shareholder=='Y'){ ?> checked <? }?> />
				</td>
				<td>
					<input type="radio" name="is_shareholder" value="N" <? if($is_shareholder=='N'){ ?> checked <? }?> />
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style=" font-style:italic ">Has Agency Contract: </td>
				<td>
					<input type="radio" name="agency" value="Y" <? if($agency=='Y'){ ?> checked <? }?> />
				</td>			
				<td>
					<input type="radio" name="agency" value="N" <? if($agency=='N'){ ?> checked <? }?> />
				</td>		
			</tr>		
			<tr>
				<td colspan="2">&nbsp;</td>
			
				<td style=" font-style:italic ">Has Coural Contract: </td>
				<td>
					<input type="radio" name="contract" value="Y" <? if($contract=='Y'){ ?> checked <? }?> />
				</td>							
			
				<td>
					<input type="radio" name="contract" value="N" <? if($contract=='N'){ ?> checked <? }?> />
				</td>		
			</tr>
			
		</table>
	</form>	
<?	
}
if($report=="rep_send_out"){
	$today=date("Y-m-d");
	
	if(!$date_start)
		$date_start=$today;
	if(!$date_final)
		$date_final=$today;
?>
	

	<form name="weekly_job" action="rep_old.php" method="post">
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
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" value="List Send Outs" />
					<input type="hidden" name="report" value="rep_send_out" />
				</td>
			</tr>			
		</table>
	</form>

<?
}
if($report=="rep_cirpay_by_payee"){
	$today=date("Y-m-d");
?>
	<form name="rep_cirpay_by_payee" action="rep_old.php" method="get">
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
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_payee","dist_id");
					$sel->optionDefText = "All";
					$sel->selectOnChange="";
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();					
?>							
				</td>							
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="rep_cirpay_by_payee" />
				</td>
				<td><a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a></td>
			</tr>			
		</table>
	</form>				
<?	
}

if($report=="rep_cirpay_by_payee_subsum"){
	$today=date("Y-m-d");
?>
	<form name="rep_cirpay_by_payee_subsum" action="rep_old.php" method="get">
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
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_payee","dist_id");
					$sel->optionDefText = "All";
					$sel->selectOnChange="";
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();					
?>							
				</td>							
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="rep_cirpay_by_payee_subsum" />
				</td>
				<td><a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a></td>
			</tr>			
		</table>
	</form>				
<?	
}

if($report=="rep_payout_breakdown"){
	$today=date("Y-m-d");
?>
	<form name="rep_payout_breakdown" action="rep_old.php" method="post">
		<table>
			<tr>
				<td>
					Level:
				</td>
				<td>
					<?
						$sel = new Select ("level");
						$sel->setOptionIsVal($level);
						$sel->start();
							$sel->addOption("dist","Distributor");
							$sel->addOption("subdist","S/Distributor");
							$sel->addOption("contr","Contractor");
						$sel->stop();					
					?>
				</td>
				
			</tr>
			<tr>
				<td>
					Month:
				</td>
				<td>
					<?
						$sel_month = new Select("month");
						$sel_month->setOptionIsVal($month);
						$sel_month->writeMonthSelect();
					?>
				</td>
			</tr>
			<tr>
				<td>
					Year:
				</td>
				<td>
					<?
						$sel_year = new Select("year");
						$sel_year->setOptionIsVal($year);
						$sel_year->writeYearSelectFT();
					?>
				</td>			
			</tr>
			<tr>
				<td>
					Target:
				</td>
				<td>
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name 
										FROM operator
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_dist","operator_id");
					$sel->selectSize=1;
					$sel->setOptionIsVal($operator_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();					
?>							
				</td>				
			</tr>
			<tr>
				<td colspan="6" align="center">
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="rep_payout_breakdown" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<?	
}

if($report=="rep_payout_breakdown_by_dist"){
	$today=date("Y-m-d");
?>
	<form name="rep_payout_breakdown" action="rep_old.php" method="post">
		<table>
			<tr>
				<td>
					Distributor:
				</td>
				<td>
					<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name 
										FROM operator
										WHERE is_dist='Y'
										ORDER BY company";							
					$sel = new MySQLSelect("dist_id","company","operator","rep_revenue.php","rep_cirpay_by_dist","dist_id");
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();					
?>							
					
				</td>
				
			</tr>	
			<tr>
				<td>Target:</td>
				<td>
					<?php
						if(!$mode) $mode='contr';
						$sel_target = new Select("mode");
						$sel_target->setOptionIsVal($mode);
						$sel_target->start();
							$sel_target->addOption('contr','Contractor');
							$sel_target->addOption('subdist','S/Dist');
							$sel_target->addOption('both','Both');
						$sel_target->stop(); 
					?>
				</td>
			</tr>	
			<tr>
				<td>
					Month:
				</td>
				<td>
					<?
						$sel_month = new Select("month");
						$sel_month->setOptionIsVal($month);
						$sel_month->writeMonthSelect();
					?>
				</td>
			</tr>
			<tr>
				<td>
					Year:
				</td>
				<td>
					<?
						$sel_year = new Select("year");
						$sel_year->setOptionIsVal($year);
						$sel_year->writeYearSelectFT();
					?>
				</td>			
			</tr>
			<tr>
				<td colspan="6" align="center">
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="rep_payout_breakdown_by_dist" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<?	
}
if($report=="delivery_details"){
?>
	<form name="narrow" id="narrow" method="post" action="rep_old.php?report=delivery_details">
		<table height="250"  class="form">
			<td>
				<th id="addjob_header" colspan="7">Add Route</th>
			</td>
			<tr>
				<th style="text-align:left " width="100">Island:</th>
				<th style="text-align:left " width="160">Region:</th>
				<th style="text-align:left " width="250">Area:</th>
				<th style="text-align:left " width="200">Type:</th>						
			</tr>
			<tr valign="top">
				<td valign="top" >
					<select multiple size="5" style="width:4em " name="island[]" onchange="set_enabled()">
						<option value="NI">NI</option>
						<option value="SI">SI</option>
					</select>
					<input name="submit" type="button" value=">>" onClick="set_Button_off();get(this,'region_reg','reports/get/get_region.php');" />
				</td>
						
				<td valign="top">
					<span name="region_reg" id="region_reg">
						<select name="region[]" size="10" style="width:11em "></select>
						<input disabled name="sub_reg" type="button" value=">>" />
					</span>
				</td>
				<td valign="top">
					<span name="area_reg" id="area_reg">
						<select name="area[]" size="10" style="width:15em "></select>
						<!--<input disabled name="sub_area" type="button" value=">>"  />-->
					</span>
				</td>
				<td>
<?
					$sel = new Select("type");
					$sel->setOptionIsVal($type);
					$sel->defaultText="Please select...";
					$sel->start();
					$sel->addOption("num_total","Total");
					$sel->addOption("num_farmers","Farmer");
					$sel->addOption("num_lifestyle","Lifestyle");
					$sel->addOption("num_dairies","Dairy");
					$sel->addOption("num_sheep","Sheep");
					$sel->addOption("num_beef","Beef");
					$sel->addOption("num_sheepbeef","Sheep/Beef");
					//$sel->addOption("num_dairybeef","Dairy/Beef");
					$sel->addOption("num_hort","Hort");
					$sel->addOption("num_nzfw","F@90%");
					$sel->stop();
?>
				</td>				
			</tr>				
		</table>
		<span id="add_route_wrap1"><input disabled name="submit1" value="Show" type="submit" /></span>
		<span id="add_route_wrap2"><input disabled name="submit2" value="Export" type="submit" /></span>
	</form>
<?	
}
if($report=="dropoff_details"){
?>
		<form name="narrow" id="narrow" method="post" action="rep_old.php">
			<table height="250"  class="form">
				<tr>
					<th style="text-align:left " width="100">Island:</th>
					<th style="text-align:left " width="160">Region:</th>
				</tr>
				<tr valign="top">
					<td valign="center" >
						<table><tr><td valign="middle">
						<select multiple size="10" style="width:4em " name="island[]">
							<option value="NI">NI</option>
							<option value="SI">SI</option>
						</select>
						</td><td valign="middle">
						<input name="submit" type="button" value=">>" onClick="set_SButton_on();get(this,'region_reg','reports/get2/get_region.php');" />
						</td></tr></table>
					</td>
							
					<td valign="center">
						<span name="region_reg" id="region_reg">
							<table><tr><td valign="middle">
							<select name="region[]" size="10" style="width:11em "></select>
							</td><td valign="middle">
							</td></tr></table>
						</span>
					</td>
					<td>
<?
						$sel = new Select("type");
						$sel->setOptionIsVal($type);
						$sel->defaultText="Please select...";
						$sel->start();
						$sel->addOption("num_total","Total");
						$sel->addOption("num_farmers","Farmer");
						$sel->addOption("num_lifestyle","Lifestyle");
						$sel->addOption("num_dairies","Dairy");
						$sel->addOption("num_sheep","Sheep");
						$sel->addOption("num_beef","Beef");
						$sel->addOption("num_sheepbeef","Sheep/Beef");
						//$sel->addOption("num_dairybeef","Dairy/Beef");
						$sel->addOption("num_hort","Hort");
						$sel->addOption("num_nzfw","F@90%");
						$sel->stop();
?>
				</td>				
				</tr>
				<tr>
					<td colspan="4">
						<input disabled name="submit" id="submit_show"  value="Show" type="submit" />
						<input disabled name="submit" id="submit_export"value="Export" type="submit" />
<?
			if($show){
?>
						<input name="submit" value="Print" type="button" onClick="self.print()" />
<?				
			}
?>									
					</td>
				</tr>
			</table>
			
			<!--<span id="add_route_wrap3"><input name="submit" value="Print" type="button" onClick="self.print()" /></span>-->

			<input type="hidden" value="1" name="show" />
			<input type="hidden" value="dropoff_details" name="report" />	
			
		</form>		
<?
}

?>

