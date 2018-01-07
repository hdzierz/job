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

if($report=="by_region_dist"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="reports.php" method="get" >
		<table>
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
				<td>Select Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				</td>
				<td>
					<input type="submit" name="submit" value="Run" />
					<input type="submit" name="submit" value="Export" />
				</td>
				<!--<td>
					<a href="reports.php?report=by_dist&dist_id=<?=$dist_id?>&action=export" >
						<img border="0" src="images/excel.jpg" alt="Export to excel"  />
					</a>
				</td>-->				
			</tr>
		</table>
		<input type="hidden" name="report" value="by_region_dist" />
	</form>
<?
}


if($report=="pmp_updated"){
?>
		<form name="addresstype" action="reports.php" method="post">
			<table>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td width="100"></td>
					<td>PMP</td>
					<td>Type</td>
					<td>Region</td>
				</tr>
				<tr>
					
					<!--<td>Show BBC (Y)</td>
						<td><input <? if($show_bbc){?> checked <? }?> type="checkbox" value="1" name="show_bbc" /></td>-->
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td valign="top" >
		<?
						$sel = new Select("choice");
						$sel->setOptionIsVal($choice);
						$sel->defaultText="Please select...";
						$sel->start();
						$sel->addOption("1","with PMP");
						$sel->addOption("2","without PMP");
						$sel->addOption("3","Descriptions without PMP");
						$sel->addOption("4","Area Totals");
						$sel->stop();
		?>
						
					</td>
					
					
					<td valign="top" rowspan="4">
		<?
						$sel = new Select("type[]");
						$sel->setOptionIsVal($type);
						$sel->multiple=1;
						$sel->size=12;
						$sel->defaultText="Please select...";
						$sel->start();
							$sel->addOption("-1","----------------");
							$sel->addOption("Total","Total");
							$sel->addOption("Farmers","Farmers");
							$sel->addOption("L/style","L/style");
							$sel->addOption("Dairy","Dairy");
							$sel->addOption("Sheep","Sheep");
							$sel->addOption("Beef","Beef");
							$sel->addOption("S/B","S/B");
							$sel->addOption("D/B","D/B");
							$sel->addOption("Hort","Hort");
							$sel->addOption("F@90%","F@90%");
							$sel->addOption("RMT","RMT");
							$sel->addOption("RM RR","RM RR");
							$sel->addOption("RM F","RM F");
							$sel->addOption("RM D","RM D");
						$sel->stop();
		?>
						
					</td>
					
					<td valign="top" rowspan="4">
	<?
						$qry = "SELECT DISTINCT region as name, region as id FROM route ORDER BY island,seq_region";
			
						$sel = new MySQLSelect("region","region","route","","narrow","region[]");
						$sel->selectOnChange="";
						$sel->selectSize=10;
						$sel->optionDefText="All";
						$sel->optionDefValue="0";
						$sel->multiple="multiple";
						$sel->selectWidth=11;
						$sel->setOptionIsVal($region);
						$sel->startSelect();
						$sel->writeSelectSQL($qry);;
						$sel->stopSelect();
?>					
					</td>
					
				</tr>
				<tr>
					<td colspan="2"></td>
					<!--<td>Show MAILINGS (Y)</td>
					<td><input <? if($show_mailings){?> checked <? }?> type="checkbox" value="1" name="show_mailings" /></td>-->
					<td colspan="3"></td>
					
				</tr>
				<tr>
					<td>Show RM NI (yes): </td>
					<td><input type="checkbox" name="ni" value="1" <? if($ni){?> checked <? }?> /></td>
				</tr>
				<tr>
					<td>Show RM SI (yes): </td>
					<td><input type="checkbox" name="si" value="1" <? if($si){?> checked <? }?> /></td>
				</tr>
				
				<tr>
					<td><input type="submit" name="submit" value="Show" />
						<input type="submit" name="submit" value="Export" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="report" value="pmp_updated" />
		</form>
		
<?		
}


if($report=="pmp_updated_dist"){
?>
		<form name="addresstype" action="reports.php" method="get">
			<table>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>PMP</td>
					<td>Type</td>
					<td>Distributor</td>
				</tr>
				<tr>
					<td rowspan="1">
						<table>
							<tr>
								<td>Show BBC (Y)</td>
								<td><input <? if($show_bbc){?> checked <? }?> type="checkbox" value="1" name="show_bbc" /></td>
							</tr>
							<tr>
								<td>Show MAILINGS (Y)</td>
								<td><input <? if($show_mailings){?> checked <? }?> type="checkbox" value="1" name="show_mailings" /></td>
								<td colspan="3"></td>
								
							</tr>
							<tr>
								<td>NI (yes): </td>
								<td><input type="checkbox" name="ni" value="1" <? if($ni){?> checked <? }?> /></td>
							</tr>
							<tr>
								<td>SI (yes): </td>
								<td><input type="checkbox" name="si" value="1" <? if($si){?> checked <? }?> /></td>
							</tr>
							<tr>
								<td>S/Dist. (yes): </td>
								<td><input type="checkbox" name="showsdist" value="1" <? if($showsdist){?> checked <? }?> /></td>
							</tr>
							<tr>
								<td>S/Dist. PB (yes): </td>
								<td><input type="checkbox" name="breaksdist" value="1" <? if($breaksdist){?> checked <? }?> /></td>
							</tr>
							
						</table>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td valign="top" >
		<?
						$sel = new Select("choice");
						$sel->setOptionIsVal($choice);
						$sel->defaultText="Please select...";
						$sel->start();
						$sel->addOption("1","with PMP");
						$sel->addOption("2","without PMP");
						$sel->addOption("3","Descriptions without PMP");
						$sel->stop();
		?>
						
					</td>
					
					
					<td valign="top" rowspan="4">
		<?
						$sel = new Select("type[]");
						$sel->setOptionIsVal($type);
						$sel->multiple=1;
						$sel->size=12;
						$sel->defaultText="Please select...";
						$sel->start();
							$sel->addOption("-1","----------------");
							$sel->addOption("Total","Total");
							$sel->addOption("Farmers","Farmers");
							$sel->addOption("L/style","L/style");
							$sel->addOption("Dairy","Dairy");
							$sel->addOption("Sheep","Sheep");
							$sel->addOption("Beef","Beef");
							$sel->addOption("S/B","S/B");
							$sel->addOption("D/B","D/B");
							$sel->addOption("Hort","Hort");
							$sel->addOption("F@90%","F@90%");
							$sel->addOption("RMT","RMT");
							$sel->addOption("RM RR","RM RR");
							$sel->addOption("RM F","RM F");
							$sel->addOption("RM D","RM D");
						$sel->stop();
		?>
						
					</td>
					
					<td valign="top" rowspan="4">
	<?
						$qry = "SELECT DISTINCT company as name, operator_id as id FROM operator WHERE is_dist='Y' ORDER BY company";
			
						$sel = new MySQLSelect("distributor","distributor","distributor","","narrow","dist_ids[]");
						$sel->setOptionIsVal($dist_ids);
						$sel->selectOnChange="";
						$sel->selectSize=12;
						$sel->optionDefText="All";
						$sel->optionDefValue="0";
						$sel->multiple="multiple";
						$sel->selectWidth=11;
						$sel->startSelect();
						$sel->writeSelectSQL($qry);;
						$sel->stopSelect();
?>					
					</td>
					<td>
						<input type="submit" name="submit" value="Show  " /><br />
						<input type="submit" name="submit" value="Export" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="report" value="pmp_updated_dist" />
		</form>
		
<?		
}

/*
if($report=="pmp_updated_dist"){
?>
		<form name="addresstype" action="reports.php" method="post">
			<table>
				<tr>
					<td>Choose:</td>
					<td align="center">
		<?
						$sel = new Select("choice");
						$sel->setOptionIsVal($choice);
						$sel->defaultText="Please select...";
						$sel->start();
						$sel->addOption("1","with PMP");
						$sel->addOption("2","without PMP");
						$sel->addOption("3","Descriptions without PMP");
						$sel->stop();
		?>
						
					</td>
					<td>Total</td>
					<td>Farmers</td>
					<td>L/Style</td>
					<td>Dairy</td>
					<td>Sheep</td>
					<td>Beef</td>
					<td>S/B</td>
					<!--<td>D/B</td>-->
					<td>Hort</td>
					<td>F@90%</td>
				<tr>
					<td>Show BBC (Y)</td>
					<td><input <? if($show_bbc){?> checked <? }?> type="checkbox" value="1" name="show_bbc" /></td>
					<td><input <? if($show_total){?> checked <? }?> type="checkbox" value="1" name="show_total" /></td>
					<td><input <? if($show_farmers){?> checked <? }?> type="checkbox" value="1" name="show_farmers" /></td>
					<td><input <? if($show_lstyle){?> checked <? }?> type="checkbox" value="1" name="show_lstyle" /></td>
					<td><input <? if($show_dairy){?> checked <? }?> type="checkbox" value="1" name="show_dairy" /></td>
					<td><input <? if($show_sheep){?> checked <? }?> type="checkbox" value="1" name="show_sheep" /></td>
					<td><input <? if($show_beef){?> checked <? }?> type="checkbox" value="1" name="show_beef" /></td>
					<td><input <? if($show_sb){?> checked <? }?> type="checkbox" value="1" name="show_sb" /></td>
					<!--<td><input <? if($show_db){?> checked <? }?> type="checkbox" value="1" name="show_db" /></td>-->
					<td><input <? if($show_hort){?> checked <? }?> type="checkbox" value="1" name="show_hort" /></td>
					<td><input <? if($show_nzfw){?> checked <? }?> type="checkbox" value="1" name="show_nzfw" /></td>
					<td width=30>&nbsp;</td>
					
					<td rowspan="4">
	<?
						$qry = "SELECT DISTINCT company as name, operator_id as id FROM operator WHERE is_dist='Y' ORDER BY company";
			
						$sel = new MySQLSelect("distributor","distributor","distributor","","narrow","dist_ids[]");
						$sel->selectOnChange="";
						$sel->selectSize=10;
						$sel->optionDefText="All";
						$sel->optionDefValue="0";
						$sel->multiple="multiple";
						$sel->selectWidth=11;
						$sel->startSelect();
						$sel->writeSelectSQL($qry);;
						$sel->stopSelect();
?>					
					</td>
				</tr>
				<tr>
					<td>Show MAILINGS (Y)</td>
					<td><input <? if($show_mailings){?> checked <? }?> type="checkbox" value="1" name="show_mailings" /></td>
				</tr>
				<tr>
					<td>NI (yes): </td>
					<td><input type="checkbox" name="ni" value="1" <? if($ni){?> checked <? }?> /></td>
				</tr>
				<tr>
					<td>SI (yes): </td>
					<td><input type="checkbox" name="si" value="1" <? if($si){?> checked <? }?> /></td>
				</tr>
				
				<tr>
					<td><input type="submit" name="submit" value="Show" />
						<input type="submit" name="submit" value="Export" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="report" value="pmp_updated_dist" />
		</form>
		
<?		
}

*/
if($report=="address_details"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="addresstype" action="reports.php" method="get">
		<table>
			<tr>
				<td colspan="3"></td>
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
			<tr>
				<td>Select Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				</td>
			</tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Run"  /><input type="submit" name="submit" value="Export"  /></td>
            </tr>			
		</table>
        <input type="hidden" name="report" value="<?=$report?>"  />
	</form>	
<?	
}

if($report=="address_details2"){		
?>
		<form name="addresstype" action="reports.php" method="get">
			<table>
				<tr>
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
					<td>
						<input type="submit" name="submit" value="Export" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="report" value="address_details" />
		</form>
		
<?		
}						
?>

<?
if($report=="total_box_holder"){
	?>
	<form name="narrow" action="reports.php" method="get" >
		<table>
			<tr>
				<td>
<?
					$sel = new MySQLSelect("region","region","route","reports.php");
					$sel->setOptionIsVal($region);
					$sel->orderField="island,seq_region";
					$sel->selectOnChange = "";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="total_box_holder" />
				</td>				
			</tr>
		</table>
	</form>
<?
}
else if($report=="by_region"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="reports.php" method="get" >
		<table>
			<tr>
				<td>Select Region:</td>
				<td>
<?
					$sel = new MySQLSelect("region","region","route","reports.php");
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($region);
					$sel->orderField="island,seq_region";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>
				<td>Select Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				</td>
				<td><input type="submit" name="submit" value="Run" /></td>
				<!--<td>
					<a href="reports.php?report=by_dist&dist_id=<?=$dist_id?>&action=export" >
						<img border="0" src="images/excel.jpg" alt="Export to excel"  />
					</a>
				</td>-->				
			</tr>
		</table>
		<input type="hidden" name="report" value="by_region" />
	</form>
<?
}
else if($report=="by_dist"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" action="reports.php" method="get" >
		<table>
			<tr>
				<td>
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","revenue","dist_id");
					$sel->selectOnChange="";
					$sel->optionDefText = "All";
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();			
?>						
				</td>
				<td>Select Date:</td>
				<td>
					<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
				<td><input type="submit" name="submit" value="Run" /></td>
			</tr>
		</table>
		<input type="hidden" name="report" value="by_dist" />
	</form>
<?
}
else if($report=="pc_dropoff" && $mode=="geo"){
		if(!$date) $date=date("Y-m-d");
?>
		<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
		<form name="narrow" id="narrow" method="post" action="reports.php">
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
				</tr>
				<tr>
					<td>Select Date:</td>
					<td>
						<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>					
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<table>		
							<tr>
								<td colspan="3">Home Phone (Yes): </td>
								<td><input type="checkbox" name="home_phone" value="1" <? if($home_phone){ ?> checked <? }?> /></td>	
							</tr>
							<tr>
								<td  colspan="3">Mobile Phone (Yes):  </td>
								<td><input type="checkbox" name="mobile_phone" value="1" <? if($mobile_phone){ ?> checked <? }?> /></td>	
			
							</tr>
						</table>
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
			<input type="hidden" value="geo" name="mode" />	
			<input type="hidden" value="pc_dropoff" name="report" />	
			
		</form>		
<?
}

else if($report=="pc_dropoff" && $mode=="dist"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" id="narrow" method="post" action="reports.php">
		<table>
			<tr>
				<th style="text-align:left ">Distributor:</th>							
				<td>
<?
					$qry = "SELECT DISTINCT 
								IF(operator.alias IS NOT NULL AND operator.alias<>'',
									operator.alias,
									CONCAT(address.name,', ',address.first_name))
										AS name,
								dist_id	AS id
							FROM route_aff
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY name";
					$sel = new MySQLSelect("","","","","narrow","dist_id");
					$sel->setOptionIsVal($dist_id);
					$sel->selectOnChange="";
					$sel->optionDefValue="-1";
					$sel->optionDefText="All";
					$sel->startSelect();
						 $sel->writeSelectSQL($qry);
					$sel->stopSelect();
?>
				</td>	
			</tr>
				<td>Select Date: </td>
				<td><script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script></td>
			</tr>
			<tr>
				<td>Home Phone (Yes): </td>
				<td><input type="checkbox" name="home_phone" value="1" <? if($home_phone){ ?> checked <? }?> /></td>	
			</tr>	
			<tr>			
				<td>Mobile Phone (Yes):  </td>
				<td><input type="checkbox" name="mobile_phone" value="1" <? if($mobile_phone){ ?> checked <? }?> /></td>	
			</tr>
			<tr>			
				<td>Email (Yes):  </td>
				<td><input type="checkbox" name="email" value="1" <? if($email){ ?> checked <? }?> /></td>	
			</tr>
			<tr>
				<td></td>
				<td>
					<input name="submit" value="Show" type="submit" />
					<input name="submit" value="Export" type="submit" />		
					<input <?php if($submit!="Show"){?> disabled <?php }?>  name="pdf_open" value="PDF" type="button" onclick="javascript:window.open('reports/bible_pdf.php?report=bible_dist&date=<?=$date?>&mobile_phone=<?=$mobile_phone?>&home_phone=<?=$home_phone?>&dist_id=<?=$dist_id?>')"/>					
				</td>
			</tr>
		</table>

		<input type="hidden" value="1" name="show" />	
		<input type="hidden" value="dist" name="mode" />			
		<input type="hidden" value="pc_dropoff" name="report" />				
	</form>
<?
}
else if($report=="bible_old"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" id="narrow" method="post" action="reports.php">
		<table>
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<th style="text-align:left " width="100">Island:</th>
							<th style="text-align:left " width="160">Region:</th>
						</tr>
						<tr valign="top">
							<td valign="center" >
								<table><tr><td valign="middle">
								<select multiple size="8" style="width:4em " name="island[]">
									<option value="NI">NI</option>
									<option value="SI">SI</option>
								</select>
								</td><td valign="middle">
								<input name="submit" type="button" value=">>" onClick="get(this,'region_reg','reports/get2/get_region.php');" />
								</td></tr></table>
							</td>
									
							<td valign="center">
								<span name="region_reg" id="region_reg">
									<table><tr><td valign="middle">
									<select name="region[]" size="8" style="width:11em "></select>
									</td><td valign="middle">
									</td></tr></table>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<!--
			<tr>
				<th style="text-align:left ">'Country':</th>							
				<td>
<?
					/*$qry = "SELECT DISTINCT 
								IF(operator.alias IS NOT NULL AND operator.alias<>'',
									operator.alias,
									CONCAT(address.name,', ',address.first_name))
										AS name,
								dist_id	AS id
							FROM route_aff
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY name";*/
					$qry = "SELECT DISTINCT
								address.country AS name,
								address.country AS id
							FROM route_aff
							LEFT JOIN route
							ON route.route_id = route_aff.route_id
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY island,seq_region,seq_area";
					/*$sel = new MySQLSelect("","","","","narrow","region");
					$sel->setOptionIsVal($region);
					$sel->selectOnChange="";
					$sel->optionDefValue="-1";
					$sel->optionDefText="All";
					$sel->startSelect();
						 $sel->writeSelectSQL($qry);
					$sel->stopSelect();*/
?>
				</td>	
			</tr>-->
			<tr>
				<td>Select Date: </td>
				<td><script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script></td>
			</tr>
			<tr>
				<td>Home Phone (Yes): </td>
				<td><input type="checkbox" name="home_phone" value="1" <? if($home_phone){ ?> checked <? }?> /></td>	
			</tr>				
				<td>Mobile Phone (Yes):  </td>
				<td><input type="checkbox" name="mobile_phone" value="1" <? if($mobile_phone){ ?> checked <? }?> /></td>	
			</tr>
			<tr>
				<td></td>
				<td>
					<input name="submit" value="Show" type="submit"  />
					<input disabled name="submit" value="Export" type="submit" />
					<input <?php if($submit!="Show"){?> disabled <?php }?> name="pdf_open" value="PDF" type="button" onclick="javascript:window.open('reports/bible_pdf.php?report=bible_region&date=<?=$date?>&mobile_phone=<?=$mobile_phone?>&home_phone=<?=$home_phone?>&island=<?=array_to_request("island",$island)?>&region=<?=array_to_request("region",$region)?>')"/>		
					<a target="_blank" href="http://get.adobe.com/reader/" style="font-size:10px">Get PDF Reader here.</a>
				</td>
			</tr>
		</table>

		<input type="hidden" value="bible" name="report" />				
	</form>
<?
}
else if($report=="bible"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" id="narrow" method="post" action="reports.php">
		<table>
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<th style="text-align:left " width="100">Island:</th>
							<th style="text-align:left " width="160">Region:</th>
						</tr>
						<tr valign="top">
							<td valign="center" >
								<table><tr><td valign="middle">
								<select multiple size="8" style="width:4em " name="island[]">
									<option value="NI">NI</option>
									<option value="SI">SI</option>
								</select>
								</td><td valign="middle">
								<input name="submit" type="button" value=">>" onClick="get(this,'region_reg','reports/get2/get_region.php');" />
								</td></tr></table>
							</td>
									
							<td valign="center">
								<span name="region_reg" id="region_reg">
									<table><tr><td valign="middle">
									<select name="region[]" size="8" style="width:11em "></select>
									</td><td valign="middle">
									</td></tr></table>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<!--
			<tr>
				<th style="text-align:left ">'Country':</th>							
				<td>
<?
					/*$qry = "SELECT DISTINCT 
								IF(operator.alias IS NOT NULL AND operator.alias<>'',
									operator.alias,
									CONCAT(address.name,', ',address.first_name))
										AS name,
								dist_id	AS id
							FROM route_aff
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY name";*/
					$qry = "SELECT DISTINCT
								address.country AS name,
								address.country AS id
							FROM route_aff
							LEFT JOIN route
							ON route.route_id = route_aff.route_id
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY island,seq_region,seq_area";
					/*$sel = new MySQLSelect("","","","","narrow","region");
					$sel->setOptionIsVal($region);
					$sel->selectOnChange="";
					$sel->optionDefValue="-1";
					$sel->optionDefText="All";
					$sel->startSelect();
						 $sel->writeSelectSQL($qry);
					$sel->stopSelect();*/
?>
				</td>	
			</tr>-->
			<tr>
				<td>Select Date: </td>
				<td><script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script></td>
			</tr>
			<tr>
				<td>Home Phone (Yes): </td>
				<td><input type="checkbox" name="home_phone" value="1" <? if($home_phone){ ?> checked <? }?> /></td>	
			</tr>				
				<td>Mobile Phone (Yes):  </td>
				<td><input type="checkbox" name="mobile_phone" value="1" <? if($mobile_phone){ ?> checked <? }?> /></td>	
			</tr>
			<tr>
				<td></td>
				<td>
					<input name="submit" value="Show" type="submit"  />
					<input disabled name="submit" value="Export" type="submit" />
					<input <?php if($submit!="Show"){?> disabled <?php }?> name="pdf_open" value="PDF" type="button" onclick="javascript:window.open('reports/bible_pdf.php?report=bible_region&date=<?=$date?>&mobile_phone=<?=$mobile_phone?>&home_phone=<?=$home_phone?>&<?=array_to_request("island",$island)?>&<?=array_to_request("region",$region)?>')"/>		
					<a target="_blank" href="http://get.adobe.com/reader/" style="font-size:10px">Get PDF Reader here.</a>
				</td>
			</tr>
		</table>

		<input type="hidden" value="bible" name="report" />				
	</form>
<?
}
else if($report=="bible_2"){
	if(!$date) $date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="narrow" id="narrow" method="post" action="reports.php">
		<table>
			<tr>
				<th style="text-align:left ">Region:</th>							
				<td>
<?
					/*$qry = "SELECT DISTINCT 
								IF(operator.alias IS NOT NULL AND operator.alias<>'',
									operator.alias,
									CONCAT(address.name,', ',address.first_name))
										AS name,
								dist_id	AS id
							FROM route_aff
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY name";*/
					$qry = "SELECT DISTINCT
								address.country AS name,
								address.country AS id
							FROM route_aff
							LEFT JOIN route
							ON route.route_id = route_aff.route_id
							LEFT JOIN
							address
							ON route_aff.dist_id=address.operator_id
							LEFT JOIN
							operator
							ON operator.operator_id=address.operator_id
							HAVING name <> ''
								AND name<>','
							ORDER BY island,seq_region,seq_area";
					$sel = new MySQLSelect("","","","","narrow","region");
					$sel->setOptionIsVal($region);
					$sel->selectOnChange="";
					$sel->optionDefValue="-1";
					$sel->optionDefText="All";
					$sel->startSelect();
						 $sel->writeSelectSQL($qry);
					$sel->stopSelect();
?>
				</td>	
			</tr>
				<td>Select Date: </td>
				<td><script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script></td>
			</tr>
			<tr>
				<td>Home Phone (Yes): </td>
				<td><input type="checkbox" name="home_phone" value="1" <? if($home_phone){ ?> checked <? }?> /></td>	
			</tr>				
				<td>Mobile Phone (Yes):  </td>
				<td><input type="checkbox" name="mobile_phone" value="1" <? if($mobile_phone){ ?> checked <? }?> /></td>	
			</tr>
			<tr>
				<td></td>
				<td>
					<input name="submit" value="Show" type="submit" />
					<input name="submit" value="Export" type="submit" />
					<input name="pdf_open" value="PDF" type="button" onclick="javascript:window.open('reports/bible_pdf.php?report=bible_region&date=<?=$date?>&mobile_phone=<?=$mobile_phone?>&home_phone=<?=$home_phone?>&region=<?=$region?>')"/>		
					<a target="_blank" href="http://get.adobe.com/reader/" style="font-size:10px">Get PDF Reader here.</a>
				</td>
			</tr>
		</table>

		<input type="hidden" value="bible" name="report" />				
	</form>
<?
}

else{
?>
	<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
<?

}


?>	
