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


if($action=="" || !isset($action) || $action=="backup" || $action=="maintain_sequence" || $action=="maintain_numbers"){
?>
	<form name="narrow" action="admin_route.php">
<?
	$sel_island = new MySQLSelect("island","island","route","admin_route.php");	
	$sel_island->setOptionIsVal($island);
	if($action=="maintain_numbers") $sel_island->onChangeAction="maintain_numbers";
	if($action=="maintain_sequence") $sel_island->onChangeAction="maintain_sequence";
	$sel_island->startSelect();
	$sel_island->writeSelect();
	$sel_island->stopSelect();
	
	$sel_region = new MySQLSelect("region","region","route","admin_route.php");	
	$sel_region->setOptionIsVal($region);
	$sel_region->orderField="seq_region";
	if($action=="maintain_numbers") $sel_region->onChangeAction="maintain_numbers";
	if($action=="maintain_sequence") $sel_region->onChangeAction="maintain_sequence";
	$sel_region->addVar("island",$island);
	$sel_region->startSelect();
	$sel_region->writeSelect();
	$sel_region->stopSelect();

	
	$sel_area = new MySQLSelect("area","area","route","admin_route.php");	
	if($action=="maintain_numbers") $sel_area->onChangeAction="maintain_numbers";
	if($action=="maintain_sequence") $sel_area->onChangeAction="maintain_sequence";
	$sel_area->setOptionIsVal($area);
	$sel_area->orderField="seq_area";
	$sel_area->addVar("island",$island);
	$sel_area->addVar("region",$region);	
	$sel_area->startSelect();
	$sel_area->writeSelect();
	$sel_area->stopSelect();	
	
	$sel_code = new MySQLSelect("code","code","route","admin_route.php");	
	$sel_code->setOptionIsVal($code);
	$sel_code->orderField="seq_code";
	if($action=="maintain_numbers") $sel_code->onChangeAction="maintain_numbers";
	if($action=="maintain_sequence") $sel_code->onChangeAction="maintain_sequence";
	$sel_code->addVar("island",$island);
	$sel_code->addVar("region",$region);	
	$sel_code->addVar("area",$area);	
	$sel_code->startSelect();
	$sel_code->writeSelect();
	$sel_code->stopSelect();		
?>
    ID:
    <input type="text" name="record" value="" />
    <input type="submit" name="submit" value="Show" />
<?php	
	if($action!="maintain_sequence" && $action!="maintain_numbers"){
/*
?>
		<input type="button" name="savenum" value="Backup Values" onClick="window.location.href='admin_route.php?action=backup'" />
<?
*/
	}
?>
	</form>
<?
}


if($action=="show_old_numbers"||$action=="run_show_old_numbers"){
?>
	<form name="narrow" action="admin_route.php" method="get">
<?
	$sel_island = new MySQLSelect("island","island","route","admin_route.php?action=show_old_numbers");	
	$sel_island->setOptionIsVal($island);
	$sel_island->addOnChange("year",$year);	
	$sel_island->startSelect();
	$sel_island->writeSelect();
	$sel_island->stopSelect();
	
	$sel_region = new MySQLSelect("region","region","route","admin_route.php?action=show_old_numbers");	
	$sel_region->setOptionIsVal($region);
	$sel_region->orderField="seq_region";
	$sel_region->addVar("island",$island);
	$sel_region->addOnChange("year",$year);	
	$sel_region->startSelect();
	$sel_region->writeSelect();
	$sel_region->stopSelect();

	
	$sel_area = new MySQLSelect("area","area","route","admin_route.php?action=show_old_numbers");	
	$sel_area->setOptionIsVal($area);
	$sel_area->orderField="seq_area";
	$sel_area->addVar("island",$island);
	$sel_area->addVar("region",$region);	
	$sel_area->addOnChange("year",$year);	
	$sel_area->startSelect();
	$sel_area->writeSelect();
	$sel_area->stopSelect();	
	
	$sel_code = new MySQLSelect("code","code","route","admin_route.php?action=show_old_numbers");	
	$sel_code->setOptionIsVal($code);
	$sel_code->orderField="seq_code";
	$sel_code->addVar("island",$island);
	$sel_code->addVar("region",$region);	
	$sel_code->addVar("area",$area);	
	$sel_code->addOnChange("year",$year);	
	$sel_code->startSelect();
	$sel_code->writeSelect();
	$sel_code->stopSelect();	

	$sel_year = new MySQLSelect ("year(backup_date)","year(backup_date)","route_old_num","admin_route.php?action=show_old_numbers","narrow","year");
	$sel_year->setOptionIsVal($year);
	$sel_year->selectOnChange="";
	$sel_year->startSelect();
	$sel_year->writeSelect();
	$sel_year->stopSelect();
?>					
	<input type="submit" name="submit" value="Run" />
	<input type="hidden" name="action" value="run_show_old_numbers" />
	</form>
<?
}
?>
