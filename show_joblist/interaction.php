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

if($action=="send_messages"){
	die('Under construction');
	?>
		<form action="index.php" method="post">
			<table>
				<tr>
					<tr>
						<td>Date:</td>
						<td>
							<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>		
						</td>
						<td>
							Current:
						</td>
						<td>
							<?
							$sel = new Select("is_current");
							$sel->setOptionIsVal($is_current);
							$sel->defaultText="All";
							$sel->start();
							$sel->addOption("1","Is current");
							$sel->addOption("0","Is not current");
							$sel->stop();
							?>
							
						</td>
						<td>
							Shareholder:
						</td>
						<td>
							<?
							$sel = new Select("is_shareholder");
							$sel->setOptionIsVal($is_shareholder);
							$sel->defaultText="All";
							$sel->start();
							$sel->addOption("1","Is shareholder");
							$sel->addOption("0","Is not shareholder");
							$sel->stop();
							?>
							
						</td>
						<td>
							Type:
						</td>
						<td>
							<?
							$sel = new Select("op_type");
							$sel->setOptionIsVal($op_type);
							$sel->defaultText="All";
							$sel->start();
							$sel->addOption("con","Contractor");
							$sel->addOption("sdist","S/Dist");
							$sel->addOption("dist","Dist");
							$sel->stop();
							?>
						<td>
							Distributors:
						</td>
						<td>
							<?
							$sel_year = new MySQLSelect ("company","operator_id","operator","index.php","send_messages","dist");
							$sel_year->setOptionIsVal($dist);
							$sel_year->selectOnChange="";
							$sel_year->startSelect();
							$sel_year->writeSelect();
							$sel_year->stopSelect();
							?>
							
						</td>
						
					</tr>
					<tr>
						<td>
							<input type="submit" name="submit" value="List" />
						</td>
					</tr>
					<input type="hidden" name="action" value="send_messages" />
			</table>
		</form>
	<?
}

if($action=="regular_jobs"){
	$today = date("Y-m-15");
	if(!$start_date || !$final_date){
		$start_date = date("Y-m-15",strtotime($today." -1 month"));
		$final_date = date("Y-m-15",strtotime($today." +2 month"));
	}
	
	?>
		<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
		<form action="index.php" method="post">
			<table>
				<tr>
					<tr>
						<td>From:</td>
						<td>
							<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>		
						</td>
						<td>
							To:
						</td>
						<td>
							<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>		
						</td>
						<td>
							<input type="submit" name="submit" value="View" />
						</td>
					</tr>
					<input type="hidden" name="action" value="regular_jobs" />
			</table>
		</form>
	<?
}

if($action=="clean_jobs"){
	if(!$date) $date = '2000-01-01';
	$warning = "This action has major impact on the job system. You will permanently remove jobs from the job list. Continue?";
	?>
		<script type="text/javascript" src="javascripts/prototype.js"></script>
		<script type="text/javascript" src="javascripts/effects.js"></script>
		<script type="text/javascript" src="javascripts/controls.js"></script>		
		<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
		<script language="javascript">
			function confirm_archive(){
				var is_sure = confirm('<?php echo $warning;?>');
				if(is_sure){
					return confirm('Are you sure the date?');
				} 
				return false;
			}
		</script>
		
		<table>
			<tr>
			<form action="index.php" method="post">
				<tr>
					<td>Clean up to:</td>
					<td>
						<script language="javascript">DateInput("date", true, "YYYY-MM-DD","<?=$date?>")</script>		
					</td>
					<td>
						<input type="submit" name="submit" value="Archive" onclick="return confirm_archive()" />
					</td>
				</tr>
				<input type="hidden" name="action" value="clean_jobs" />
			</form>
		</table>
	
	<?php
}

if($action==""){
	if(!$start_date) $start_date = date("Y-m-d");
	if(!$final_date) $final_date = date("Y-m-d");
?>
		<script type="text/javascript" src="javascripts/prototype.js"></script>
		<script type="text/javascript" src="javascripts/effects.js"></script>
		<script type="text/javascript" src="javascripts/controls.js"></script>		
		<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
		
		<table>
			<tr>
				<td>Client: </td>
				<td>
					<input type="text" id="client" name="client" />
					<div id="hint" class="hint"></div>
					<script type="text/javascript">	
						new Ajax.Autocompleter("client","hint","includes/search_server.php");
					</script>   
				</td>
				<td>Publication: </td>
				<td>
					<input type="text" id="pub" name="pub" />
					<div id="hint" class="hint"></div>
					<script type="text/javascript">	
						new Ajax.Autocompleter("pub","hint","includes/search_server.php");
					</script>  
				</td>
				<td>Job #: </td>
				<td>
					<input type="text" id="job" name="job" />
					<div id="hint" class="hint"></div>
					<script type="text/javascript">	
						new Ajax.Autocompleter("job","hint","includes/search_server.php");
					</script>  
				</td>
				<td>
					<input type="button" name="all" type="button" value="All" onClick="document.location.href='index.php?remove=1'" />  	
				</td>
			</tr>
			<form action="index.php" method="post">
				<tr>
					<td>From:</td>
					<td>
						<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>		
					</td>
					<td>To:</td>
					<td>
						<script language="javascript">DateInput("final_date", true, "YYYY-MM-DD","<?=$final_date?>")</script>						
					</td>
					<td>
						<input type="submit" name="submit" value="Filter" />
					</td>
				</tr>
				<input type="hidden" name="pub" value="<?=$pub?>" />
				<input type="hidden" name="client" value="<?=$client?>" />
			</form>
		</table>
<?	
}
if($action=="show_old_jobs" || $action=="show_old_jobs_by_pub"){
?>
	<form name="show_old_jobs" action="index.php" method="get">
		<table>
			<tr>
				<td>Year:</td>
				<td>
<?				
					$sel_year = new MySQLSelect ("year(delivery_date)","year(delivery_date)","job","index.php","show_old_jobs","year");
					$sel_year->setOptionIsVal($year);
					$sel_year->selectOnChange="";
					$sel_year->startSelect();
					$sel_year->writeSelect();
					$sel_year->stopSelect();
?>				
				</td>
				<td>Month:</td>
				<td>
<?				
					$sel_month = new MySQLSelect ("month(delivery_date)","month(delivery_date)","job","index.php","show_old_jobs","month");
					$sel_month->setOptionIsVal($month);
					$sel_month->selectOnChange="";
					$sel_month->startSelect();
					$sel_month->writeSelect();
					$sel_month->stopSelect();
?>								
				</td>
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="action" value="show_old_jobs" />
				</td>
			</tr>
	</form>
	<form name="show_old_jobs" action="index.php" method="get">
			<tr>
				
				<td>Client:</td>
				<td>
<?				
					$sel_year = new MySQLSelect ("name","client_id","client","index.php","show_old_jobs","client_id");
					$sel_year->setOptionIsVal($client_id);
					$sel_year->selectOnChange="";
					$sel_year->startSelect();
					$sel_year->writeSelect();
					$sel_year->stopSelect();
?>						
				</td>
				<td>and/or Publication:</td>
				<td>
<?				
					$sel_year = new MySQLSelect ("publication","publication","job","index.php","show_old_jobs","publication");
					$sel_year->setOptionIsVal($publication);
					$sel_year->selectOnChange="";
					$sel_year->startSelect();
					$sel_year->writeSelect();
					$sel_year->stopSelect();
?>						
				</td>
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="action" value="show_old_jobs_by_pub" />
				</td>
			</tr>			
		</table>
	</form>
	
<?
}
else if($action=="update_aff"){
	if(!$start_date) 	$start_date=date("Y-m-d");
	if(!$end_date) 		$end_date=date("Y-m-d");
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<form name="show_old_jobs" action="index.php" method="get">
		<table>
			<tr>
				<td>Start Date:</td>
				<td>
					<script language="javascript">DateInput("start_date", true, "YYYY-MM-DD","<?=$start_date?>")</script>		
				</td>
				<td>End Date:</td>
				<td>
					<script language="javascript">DateInput("end_date", true, "YYYY-MM-DD","<?=$end_date?>")</script>						
				</td>
				<td>
					<input type="submit" value="Run!" />
					<input type="hidden" name="action" value="update_aff" />
				</td>
			</tr>
		</table>
	</form>
<?
}

if($action=="gst"){
?>
	<form name="configure_gst" action="index.php" method="get">
		<table>
			<tr>
				<td>GST in %:</td>
				<td>
					<input name="gst" type="text" value="<?php echo 100*$GST_CIRCULAR; ?>" />		
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

 if($action=="fax_email"){
 	?>
 	<form name="fax_email" action="index.php" method="get">
		<table>
			<tr>
				<td>Email address:</td>
				<td>
					<input name="email" type="text" value="<?php echo $FAX_EMAIL_ADDRESS; ?>" />		
				</td>
				<td>
					<?php 
					$sel = new Select("mode");
					$sel->setOptionIsVal($FAX_NUMBER_EMAIL_MODE);
					$sel->defaultText="Please select...";
					$sel->start();
					$sel->addOption("PLAIN","Area code plus number");
					$sel->addOption("WITHOUTZERO","Area code without zero plus number");
					$sel->addOption("INT","0064 plus area code plus number");
					$sel->addOption("INTWITHOUTZERO","64 plus area code plus number");
					$sel->addOption("COURAL","coural");
					$sel->stop();
					?>
				</td>
				<td>
					<input type="submit" value="Save"  />
					<input type="hidden" name="action" value="fax_email" />
				</td>
			</tr>
		</table>
	</form>
	<?php
 }

?>