<?

if($MESSAGE){
?>		
		<div id="message" >
			<!--<input name="message" value="<?=$MESSAGE?>" />-->
			$MESSAGE;
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
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
<?

if($report=="job_manifest_select"){
	$today=date("Y-m-d");
	
	if(!$date_start)
		$date_start_show=$today;
	else 
		$date_start_show=$date_start;
	if(!$date_final)
		$date_final_show=$today;
	else 
		$date_final_show=$date_final;
		
?>
	<form name="weekly_job" action="rep_revenue.php" method="post">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<script language="javascript">DateInput("date_start", true, "YYYY-MM-DD","<?=$date_start_show?>")</script>
				</td>
				<td>Show regular jobs (Y)</td>
				<td>
					<input <? if($show_regular){?> checked <? }?> type="checkbox" value="1" id="show_regular" name="show_regular" />
				</td>
			</tr>
			<tr>
				<td>Final Date:</td>
				<td>
					<script language="javascript">DateInput("date_final", true, "YYYY-MM-DD","<?=$date_final_show?>")</script>
				</td>
				<td>Show casual jobs (Y)</td>
				<td>
					<input <? if($show_casual){?> checked <? }?> type="checkbox" value="1" id="show_casual" name="show_casual" />
				</td>	
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>NI Linehauler</td>
				<td >
					<?
						$sel_year = new MySQLSelect ("name","client_id","client","index.php","","ni_line_hauler");
						$sel_year->setOptionIsVal($ni_line_hauler);
						$sel_year->selectOnChange="";
						$sel_year->addSQLWhere('is_hauler','1');
						$sel_year->startSelect();
						$sel_year->writeSelect();
						$sel_year->stopSelect();
						?>				
				</td>	
			</tr>
			
			<tr>
				<td></td>
				<td></td>
				<td>SI Linehauler</td>
				<td >
					
					<?
						$sel_year = new MySQLSelect ("name","client_id","client","index.php","","si_line_hauler");
						$sel_year->setOptionIsVal($si_line_hauler);
						$sel_year->selectOnChange="";
						$sel_year->addSQLWhere('is_hauler','1');
						$sel_year->startSelect();
						$sel_year->writeSelect();
						$sel_year->stopSelect();
						?>	
					</td>	
			</tr>
			
			<tr>
				<td colspan="4" align="center">
					<input type="submit" value="List Jobs" />
					<input type="hidden" name="report" value="job_manifest_select" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<script language="javascript">
	function check_cas_reg(control){
		var reg = document.getElementById("show_regular");
		var cas = document.getElementById("show_casual");
		if(control.checked){
			reg.checked=true;
			cas.checked=true;
		}
		else{
			reg.checked=false;
			cas.checked=false;
		}
	}
	
</script>
<?
}


//echo "'$report'";

if($report=="month_job"){
	$today=date("Y-m-d");
?>
	<form name="monthly_job" action="rep_revenue.php" method="get">
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
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="month_job" />
				</td>
				<td><a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a></td>
			</tr>
		</table>
	</form>
<?
}
if($report=="weekly_send_out"){
?>
	<table>
		<tr>
			<td><a href="rep_revenue.php?report=weekly">Back</a></td>
		</tr>
	</table>
<?
}

if($report=="job_delivery_select"){
	$today=date("Y-m-d");
	
	if(!$date_start)
		$date_start_show=$today;
	else 
		$date_start_show=$date_start;
	if(!$date_final)
		$date_final_show=$today;
	else 
		$date_final_show=$date_final;
		
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");	
	$comment2 = str_replace("<br />","",$comment2);
?>
	<form name="weekly_job" action="rep_revenue.php" method="post">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<script language="javascript">DateInput("date_start", true, "YYYY-MM-DD","<?=$date_start_show?>")</script>
				</td>
				<td>Show regular jobs (Y)</td>
				<td>
					<input <? if($show_regular){?> checked <? }?> type="checkbox" value="1" id="show_regular" name="show_regular" />
				</td>
			</tr>
			<tr>
				<td>Final Date:</td>
				<td>
					<script language="javascript">DateInput("date_final", true, "YYYY-MM-DD","<?=$date_final_show?>")</script>
				</td>
				<td>Show casual jobs (Y)</td>
				<td>
					<input <? if($show_casual){?> checked <? }?> type="checkbox" value="1" id="show_casual" name="show_casual" />
				</td>	
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Send to distributors only (Y)</td>
				<td >
					<input onclick='check_cas_reg(this)' <? if($dist_only){?> checked <? }?> type="checkbox" value="1" name="dist_only" />
				</td>	
			</tr>
			<tr>
				<td>Comment:</td>
				<td colspan="3">
					<textarea cols="80" rows="3" name="comment2"><?=$comment2?></textarea>
				</td>
			</tr>
			
			<tr>
				<td colspan="4" align="center">
					<input type="submit" value="List Jobs" />
					<input type="hidden" name="report" value="job_delivery_select" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<script language="javascript">
	function check_cas_reg(control){
		var reg = document.getElementById("show_regular");
		var cas = document.getElementById("show_casual");
		if(control.checked){
			reg.checked=true;
			cas.checked=true;
		}
		else{
			reg.checked=false;
			cas.checked=false;
		}
	}
	
</script>
<?
}

if($report=="job_delivery"){
	if($send_report_id){
		$qry = "SELECT * FROM send_report WHERE send_report_id='$send_report_id'";
		$res = query($qry);
		$rep = mysql_fetch_object($res);
		$jobs = unserialize($rep->jobs);
		$check=array();
		foreach($jobs as $j){
			$check[$j] = 1;
		}
	}
	$today=date("Y-m-d");
	
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");	
	$comment2 = str_replace("<br />","",$comment2);
	$MESSAGE = "Distributor only mode";
?>		
	<form name="weekly_job" action="rep_revenue.php" method="post">
		<table>
		
<?
		if($dist_only){
			?>
				<tr><td></td><td style="color: blue">Distributor Only Mode<br /></td></tr>
			<?php
		}
		foreach($check as $j=>$has){
			
			$job_no = get("job","job_no","WHERE job_id='$j'");
?>
				<tr>
					<td>Job <?=$job_no?></td>
					<td><input type="checkbox" checked name="check[<?=$j?>]" value="1" /></td>
				</tr>
<?		
			}
?>		
			<tr>
				<td>Show RDs for regular jobs</td>
				<td><input type="checkbox" name="show_rd_details" <? if($show_rd_details){?> checked <? }?> value="1" /></td>		
			</tr>
			<tr>
				<td>Comment:</td>
				<td colspan="3">
					<textarea cols="80" rows="3" name="comment2"><?=$comment2?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" value="Send Out" />
					<input type="hidden" name="report" value="weekly_send_out" />
					<input type="hidden" name="date_start" value="<?=$date_start?>" />
					<input type="hidden" name="date_final" value="<?=$date_final?>" />
					<input type="hidden" name="origin" value="job_delivery" />
					<input type="hidden" name="dist_only" value="<?=$dist_only?>" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<?	
}

if($report=="linehaul"){
	if($send_report_id){
		$qry = "SELECT * FROM send_report WHERE send_report_id='$send_report_id'";
		$res = query($qry);
		$rep = mysql_fetch_object($res);
		$date_start = $rep->start_date;
		$date_final = $rep->final_date;
		$company = $rep->dist_id;
		if($company==-1) $company="All";
	}
	$today=date("Y-m-d");
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");	
	$comment2 = str_replace("<br />","",$comment2);
	
	if(!$date_start)
		$date_start_show=$today;
	else 
		$date_start_show=$date_start;
	if(!$date_final)
		$date_final_show=$today;
	else 
		$date_final_show=$date_final;
	?>		
	<script language="javascript">
		function send_out(name,date_start,date_final,show_regular,show_casual,show_rd_details,client_id,pdf_only){
			document.location.href='rep_revenue.php?report=linehaul_send_out&hauler='+name+'&date_start='+date_start+'&date_final='+date_final+'&show_regular='+show_regular+'&show_casual='+show_casual+'&show_rd_details='+show_rd_details+'&client_id='+client_id+'&pdf_only='+pdf_only;
		}
	</script>
	<form name="weekly_job" action="rep_revenue.php" method="post">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<script language="javascript">DateInput("date_start", true, "YYYY-MM-DD","<?=$date_start_show?>")</script>
				</td>
				<td>Show regular jobs (Y)</td>
				<td>
					<input <? if($show_regular){?> checked <? }?> type="checkbox" value="1" name="show_regular" />
				</td>
				<td>Select Linehauler:</td>
				<td>
<?
					$sel = new MySQLSelect("name","client_id","client","reports.php");
					$sel->addSQLWhere("is_linehaul",'1');
					$sel->optionDefText	 = "All";
					$sel->optionDefValue = "All";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($name);
					//$sel->orderField="island,seq_region";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>						
			</tr>
			<tr>
				<td>Final Date:</td>
				<td>
					<script language="javascript">DateInput("date_final", true, "YYYY-MM-DD","<?=$date_final_show?>")</script>
				</td>
				<td>Show casual jobs (Y)</td>
				<td>
					<input <? if($show_casual){?> checked <? }?> type="checkbox" value="1" name="show_casual" />
				</td>				
				<td>Client:</td>
				<td>
				<?
					$sel = new MySQLSelect("name","client_id","client","reports.php","weekly_job","client_id");
					//$sel->addSQLWhere("is_linehaul",'1');
					$sel->optionDefText	 = "All";
					$sel->optionDefValue = "All";
					$sel->selectOnChange = "";
					$sel->setOptionIsVal($client_id);
					//$sel->orderField="island,seq_region";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
	
?>
				</td>				
				
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Show RDs for regular jobs</td>
				<td><input type="checkbox" name="show_rd_details" <? if($show_rd_details){?> checked <? }?> value="1" /></td>
				<td></td>
				<td>
				
				</td>
			</tr>
			<tr>
				<td>Comment:</td>
				<td colspan="3">
					<textarea cols="80" rows="3" name="comment2"><?=$comment2?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="linehaul" />
					<?
					if($date_start>="2000-01-01" && $date_final>="2000-01-01"){
					?>
						<input type="button" value="Send Out!" onClick="send_out('<?=$name?>','<?=$date_start?>','<?=$date_final?>','<?=$show_regular?>','<?=$show_casual?>','<?=$show_rd_details?>','<?=$client_id?>','0');" />
						<input type="button" value="Create PDF only!" onClick="send_out('<?=$name?>','<?=$date_start?>','<?=$date_final?>','<?=$show_regular?>','<?=$show_casual?>','<?=$show_rd_details?>','<?=$client_id?>','1');" />
					<?
					}
					?>
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<?	
	
}

if($report=="weekly"){
	if($send_report_id){
		$qry = "SELECT * FROM send_report WHERE send_report_id='$send_report_id'";
		$res = query($qry);
		$rep = mysql_fetch_object($res);
		$date_start = $rep->start_date;
		$date_final = $rep->final_date;
		$company = $rep->dist_id;
		if($company==-1) $company="All";
	}
	$today=date("Y-m-d");
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=1");	
	$comment2 = str_replace("<br />","",$comment2);
	
	if(!$date_start)
		$date_start_show=$today;
	else 
		$date_start_show=$date_start;
	if(!$date_final)
		$date_final_show=$today;
	else 
		$date_final_show=$date_final;
	if(!$submit && !$sel_contr_only) $sel_contr_only=1; 
	// iug
?>		
	<script language="javascript">
		function send_out(company,date_start,date_final,show_regular,show_casual,show_rd_details,sel_contr_only,pdf_only){
			document.location.href='rep_revenue.php?report=weekly_send_out&company='+company+'&date_start='+date_start+'&date_final='+date_final+'&show_regular='+show_regular+'&show_casual='+show_casual+'&show_rd_details='+show_rd_details+'&sel_contr_only='+sel_contr_only+'&pdf_only='+pdf_only;
		}
	</script>
	
	<form name="weekly_job" action="rep_revenue.php" method="post">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<script language="javascript">DateInput("date_start", true, "YYYY-MM-DD","<?=$date_start_show?>")</script>
				</td>
				<td>Show regular jobs (Y)</td>
				<td>
					<input <? if($show_regular){?> checked <? }?> type="checkbox" value="1" name="show_regular" />
				</td>
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
			</tr>
			<tr>
				<td>Final Date:</td>
				<td>
					<script language="javascript">DateInput("date_final", true, "YYYY-MM-DD","<?=$date_final_show?>")</script>
				</td>
				<td>Show casual jobs (Y)</td>
				<td>
					<input <? if($show_casual){?> checked <? }?> type="checkbox" value="1" name="show_casual" />
				</td>				
				<td>Show RDs for regular jobs</td>
				<td><input type="checkbox" name="show_rd_details" <? if($show_rd_details){?> checked <? }?> value="1" /></td>						
				
			</tr>
			<tr>
				<td colspan='2'>Include contractor</td>
				<td>
					<input <? if($sel_contr_only){?> checked <? }?> type="checkbox" value="1" name="sel_contr_only" />
				</td>
			</tr>
			<tr>
				<td>Comment:</td>
				<td colspan="3">
					<textarea cols="80" rows="3" name="comment2"><?=$comment2?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" name="submit" value="Run!" />
					<input type="hidden" name="report" value="weekly" />
					<?
					if($date_start>="2000-01-01" && $date_final>="2000-01-01"){
					?>
						<input type="button" value="Send Out!" onClick="send_out('<?=$company?>','<?=$date_start?>','<?=$date_final?>','<?=$show_regular?>','<?=$show_casual?>','<?=$show_rd_details?>','<?=$sel_contr_only?>','0');" />
						<input type="button" value="Create PDF only!" onClick="send_out('<?=$company?>','<?=$date_start?>','<?=$date_final?>','<?=$show_regular?>','<?=$show_casual?>','<?=$show_rd_details?>','<?=$sel_contr_only?>','1');" />
					<?
					}
					?>
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<?	
}

if($report=="label"){
	if(!isset($space_hor)) 		$space_hor	  = 50;
	if(!isset($space_vert)) 	$space_vert   = 50;
	if(!isset($kg_per_lab)) 	$kg_per_lab   = 1000;
	//if(!isset($a4_only)) 		$a4_only   	  = true;
	if(!isset($qty_per_bund)) 	$qty_per_bund  = 1;
	if(!isset($qty_per_do)) 	$qty_per_do  = 1;
	
	
?>
	<form name="label" action="rep_revenue.php" method="get">
		<table>
			<tr>
				<td>
					Job:
				</td>
				<td>
<?				
					$sel_year = new MySQLSelect ("CONCAT(job_no,IF(job_no_add IS NOT NULL,job_no_add,''),' - (',YEAR(delivery_date),')')","job_id","job","reports.php","label","job_id");
					$sel_year->selectOnChange="";
					$sel_year->sortOrder = "DESC";
					$sel_year->addSQLWhereNot("finished","Y");
					$sel_year->addSQLWhereNot("cancelled","Y");
					$sel_year->addSQLWhereNot("job_no_add","L");
					$sel_year->setOptionIsVal($job_id);
					$sel_year->startSelect();
					$sel_year->writeSelect();
					$sel_year->stopSelect();
?>				
				</td>
			</tr>
			<tr>
				<td>Give KGs per Box: </td>
				<td>
					<input type="text" name="kg_per_lab" value="<?=$kg_per_lab?>" />
					When printing 8 up labels, set kgs per box@ 15
				</td>
			</tr>
			<tr>
				<td>Quantity per bundle (A4 only): </td>
				<td>
					<input type="text" name="qty_per_bund" value="<?=$qty_per_bund?>" />
				</td>
			</tr>
			<tr>
				<td>Quantity per DO (A4 only): </td>
				<td>
					<input type="text" name="qty_per_do" value="<?=$qty_per_do?>" />
				</td>
			</tr>
			<tr>
				<td>Space horiz.: </td>
				<td><input type="text" name="space_hor" value="<?=$space_hor?>" /></td>
			</tr>
			<tr>
			</tr>
			<tr>
				<td>Space vert.:</td>
				<td><input type="text" name="space_vert" value="<?=$space_vert?>" /></td>
			</tr>
			<tr>
				<td>A4: </td>
				<td><input type="checkbox" name="a4_only" <? if($a4_only) echo "checked"; ?> /></td>
			</tr>
			<tr>
				<td>Island: </td>
				<td>
				<?
					$sel_month = new Select("island");
					$sel_month->setOptionIsVal($island);
					$sel_month->setOption("both","both");
					$sel_month->setOption("NI","NI");
					$sel_month->setOption("SI","SI");
					$sel_month->startSelect();
						$sel_month->writeSelect();
					$sel_month->stopSelect();
					
				?>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="label" />
				</td>			
				<td rowspan="3"><a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a></td>
			</tr>
		</table>
	</form>
<?	
}

if($report=="label_eight"){
	if($format==8){
		$space_hor	  = 6;
		$space_vert   = 3;
		
		$num_hor	  = 3;
		$num_vert     = 8;
		
		$margin_top = 11;
		$margin_bottom = 12;
		$margin_left = 5;
		$cell_width = 64;
		$cell_height = 32;
	}
	else{
		$space_hor	  = 5;
		$space_vert   = 1.2;
		
		$num_hor	  = 3;
		$num_vert     = 11;
		
		$margin_top = 8;
		$margin_bottom = 6;
		$margin_left = 6;
		$cell_width = 64;
		$cell_height = 24.3;

	}
	if(!isset($format)) 		$format='11';
	
	
?>
		<div id="job_add_route">
			<form name="alter" action="rep_revenue.php" method="get">
			<table class = 'form'>
				<tr>
					<tr>
						<td>
							Current:
						</td>
						<td>
							<?
							if(!$is_current) $is_current = "All";
							$sel = new Select("is_current");
							$sel->setOptionIsVal($is_current);
							$sel->defaultText="All";
							$sel->defaultValue="All";
							//$sel->multiple = true;
							$sel->size = 10;
							$sel->start();
							$sel->addOption("Y","Is current");
							$sel->addOption("N","Is not current");
							$sel->stop();
							?>
							
						</td>
						<td>
							Shareholder:
						</td>
						<td>
							<?
							if(!$is_shareholder) $is_shareholder = "All";
							$sel = new Select("is_shareholder");
							$sel->setOptionIsVal($is_shareholder);
							$sel->defaultText="All";
							$sel->defaultValue="All";
							//$sel->multiple = true;
							$sel->size = 10;
							$sel->start();
							$sel->addOption("Y","Is shareholder");
							$sel->addOption("N","Is not shareholder");
							$sel->stop();
							?>
							
						</td>
						<td>
							Type:
						</td>
						<td>
							<?
							if(!$op_type) $op_type = "All";
							//print_r($op_type);
							$sel = new Select("op_type[]");
							$sel->setOptionIsVal($op_type);
							$sel->defaultText="All";
							$sel->defaultValue="All";
							$sel->multiple = true;
							$sel->size = 10;
							$sel->start();
							$sel->addOption("is_contr","Contractor");
							$sel->addOption("is_subdist","S/Dist");
							$sel->addOption("is_dist","Dist");
							$sel->stop();
							?>
						</td>
						<td>
							Distributors:
						</td>
						<td>
							<?
							if(!$dist) $dist = array(0);
							$sel_year = new MySQLSelect ("company","operator_id","operator","index.php","send_messages","dist[]");
							$sel_year->setOptionIsVal($dist);
							$sel_year->optionDefText = "All";
							$sel_year->multiple = "multiple";
							$sel_year->selectSize = 10;
							$sel_year->addSQLWhere("is_dist",'Y');
							$sel_year->selectOnChange="";
							$sel_year->startSelect();
							$sel_year->writeSelect();
							$sel_year->stopSelect();
							?>
							
						</td>
						<td>Number of rows:</td>
						<td>
							<?
								$sel_format = new Select('format');
								$sel_format->setOptionIsVal($format);
								$sel_format->start();
									$sel_format->addOption('11','11');
									$sel_format->addOption('8','8');
								$sel_format->stop(); 
							?>
						</td>
						<td>
							<input type="submit" name="submit" value="Run" />
						</td>
					</tr>
					<input type="hidden" name="report" value="label_eight" />
			</table>
			</form>

		 </div>

<?	
}



if($report=="envelopes2"){
?>
	<div style="float:right "><a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a></div>
	<form name="envelopes" action="rep_revenue.php" method="get">
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
				<td style=" font-style:italic ">Distributor: </td>
				<td>
					<input type="checkbox" name="is_dist" value="Y" <? if($is_dist=='Y'){ ?> checked <? }?> />
				</td>			
				<td style=" font-style:italic ">Is Shareholder: </td>
				<td>
					<input type="checkbox" name="is_shareholder" value="Y" <? if($is_shareholder=='Y'){ ?> checked <? }?> />
				</td>
				<td>
					<input type="submit" name="submit" value="Run!" />
					<input type="submit" name="submit" value="Export!" />
					<input type="hidden" name="report" value="envelopes" />
				</td>			
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style=" font-style:italic ">S/Distributor: </td>
				<td>
					<input type="checkbox" name="is_subdist" value="Y" <? if($is_subdist=='Y'){ ?> checked <? }?> />
				</td>	
				<td style=" font-style:italic ">Is Current: </td>
				<td>
					<input type="checkbox" name="is_current" value="Y" <? if($is_current=='Y'){ ?> checked <? }?> />
				</td>			
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style=" font-style:italic ">Contractor: </td>
				<td>
					<input type="checkbox" name="is_contractor" value="Y" <? if($is_contractor=='Y'){ ?> checked <? }?> />
				</td>	
				<td style=" font-style:italic ">Has Coural Contract: </td>
				<td>
					<input type="checkbox" name="contract" value="Y" <? if($contract=='Y'){ ?> checked <? }?> />
				</td>			
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
				<td style=" font-style:italic ">Has Agency Contract: </td>
				<td>
					<input type="checkbox" name="agency" value="Y" <? if($agency=='Y'){ ?> checked <? }?> />
				</td>			
			</tr>						
		</table>
	</form>	
<?	
}

if($report=="revenue2"){
?>
	<form name="show_old_jobs" action="rep_revenue.php" method="get">
		<table>
			<tr>
				<td>
					From Month:
				</td>
				<td>
					<?
						$sel_month = new Select("frommonth");
						$sel_month->setOptionIsVal($frommonth);
						$sel_month->writeMonthSelect();
					?>
				</td>
				<td>
					From Year:
				</td>
				<td>
					<?
						$sel_year = new Select("fromyear");
						$sel_year->setOptionIsVal($fromyear);
						$sel_year->writeYearSelectFT();
					?>
				</td>			
				<td>
					To Month:
				</td>
				<td>
					<?
						$sel_month = new Select("tomonth");
						$sel_month->setOptionIsVal($tomonth);
						$sel_month->writeMonthSelect();
					?>
				</td>
				<td>
					To Year:
				</td>
				<td>
					<?
						$sel_year = new Select("toyear");
						$sel_year->setOptionIsVal($toyear);
						$sel_year->writeYearSelectFT();
					?>
				</td>			
			</tr>
			<tr>
				
				<td>Client:</td>
				<td>
<?				
					$sel_year = new MySQLSelect ("name","client_id","client","rep_revenue.php","show_old_jobs","client_id");
					$sel_year->setOptionIsVal($client_id);
					$sel_year->selectOnChange="";
					$sel_year->startSelect();
					$sel_year->writeSelect();
					$sel_year->stopSelect();
?>						
				</td>
				<td>and/or Job:</td>
				<td>
					<input name='job' value='<?php echo $job;?>' />						
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="revenue2" />
				</td>
			</tr>			
		</table>
	</form>
	
<?
}

if($report=="revenue"){
	$today=date("Y-m-d");
?>
	<form name="revenue" action="rep_revenue.php" method="get">
		<table>
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
				<td>
<?			/*	
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","revenue","dist_id");
					$sel->selectOnChange="";
					$sel->selectSize=1;
					$sel->setOptionIsVal($dist_id);
					$sel->startSelect();
					$sel->writeSelectSQL($sql_op);
					$sel->stopSelect();			
*/?>								
				</td>				
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="revenue" />
				</td>
				<td><a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a></td>
			</tr>			
		</table>
	</form>
<?
}

if($report=="rep_cirpay_by_dist_send_out"){
?>
	<table>
		<tr>
			<td><a href="rep_revenue.php?report=rep_cirpay_by_dist">Back</a></td>
		</tr>
	</table>
<?
}

if($report=="rep_cirpay_by_dist"){
	if($send_report_id){
		$qry = "SELECT * FROM send_report WHERE send_report_id='$send_report_id'";
		$res = query($qry);
		$rep = mysql_fetch_object($res);
		$month = $rep->month;
		$year = $rep->year;
		$dist_id = $rep->dist_id;
	}
	$today=date("Y-m-d");
	if(!$comment2)
		$comment2 = get("last_print_comment","comment2","WHERE last_print_comment_id=2");	
	$comment2 = str_replace("<br />","",$comment2);
?>
	<script language="javascript">
		function send_out_payout(month,year,dist_id){
			document.location.href='rep_revenue.php?report=rep_cirpay_by_dist_send_out&month='+month+'&year='+year+'&dist_id='+dist_id;
		}
	</script>
	<form name="rep_cirpay_by_dist" action="rep_revenue.php" method="post">
		<table>
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
					Distributor:
				</td>
				<td>
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_dist","dist_id");
					$sel->optionDefText = "All";
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
				<td>Compare (yes):</td>
				<td>
					<input type="checkbox" name="compare_report" value="1" <? if($compare_report){ ?> checked <? }?> />
				</td>
			</tr>
			<tr>
				<td>Comment:</td>
				<td colspan="3">
					<textarea cols="80" rows="3" name="comment2"><?=$comment2?></textarea>
				</td>
			</tr>				
			<tr>
				<td colspan="6" align="center">
					<input type="submit" value="Run!" />
					<?
					if($month && $year){
					?>
						<input type="button" value="Sent Out" onClick="send_out_payout('<?=$month?>','<?=$year?>','<?=$dist_id?>')" />
					<?
					}
					?>
					<input type="hidden" name="report" value="rep_cirpay_by_dist" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
			</tr>			
		</table>
	</form>
<?
}


if($report=="rep_rate_discr"){
	$today=date("Y-m-d");
?>
	<form name="rep_payout_breakdown" action="rep_revenue.php" method="post">
		<table>
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
				<td>Payouts: </td>
				<td>
					<input name="choice" type="radio" value="job" <?php if($choice=="job" || !$choice){?>checked<?php }?> />
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
				<td>Standard Rates: </td>
				<td><input name="choice" type="radio" value="rate" <?php if($choice=="rate"){?>checked<?php }?> /></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="rep_rate_discr" />
					<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
				</td>
				
			</tr>			
		</table>
	</form>
<?		
}

if($report=='rep_payout_breakdown_by_dist2'){
?>
<form name="rep_cirpay_by_dist" action="rep_revenue.php" method="post">
		<table>
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
					Distributor:
				</td>
				<td>
<?				
					$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
										WHERE is_dist='Y'
										GROUP BY company
										ORDER BY company";							
					$sel = new MySQLSelect("operator_id","company","operator","rep_revenue.php","rep_cirpay_by_dist","dist_id");
					$sel->optionDefText = "All";
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
				<td>Comment:</td>
				<td colspan="3">
					<textarea cols="80" rows="3" name="comment"><?=$comment?></textarea>
				</td>
			</tr>				
			<tr>
				<td colspan="6" align="center">
					<input type="submit" value="Run!" />
					<input type="hidden" name="report" value="rep_payout_breakdown_by_dist2" />
					<!--<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>-->
				</td>
			</tr>			
		</table>
	</form>
<?php
}

?>

