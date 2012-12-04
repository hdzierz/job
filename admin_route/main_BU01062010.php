
<script language="javascript">
	function checknum(strString,field)
		   //  check for valid numeric strings	
		   {
		   var strValidChars = "0123456789";
		   var strChar;
		   var blnResult = true;
		
		   if (strString.length == 0) field.value="";
		
		   //  test strString consists of valid characters listed above
		   for (i = 0; i < strString.length && blnResult == true; i++)
			  {
			  strChar = strString.charAt(i);
			  if (strValidChars.indexOf(strChar) == -1)
				 {
				 blnResult =  field.value="";
				 // substr(field.value,0,strlen(field.value)-1)
				 }
			  }
		   return blnResult;
	}

</script>
<?

if($action=="errors"){
	$qry = "";

	$tab = new MySQLTable();
	$tab->startTable();
		$tab->writeTable();
	$tab->stopTable();
}


if($action=="change_region"){
?>
	<h3>Select Region</h3>
	<form name="change_region" action="admin_route.php" method="get">
		<table>
			<tr>
				<td>
<?				
					$sel = new MySQLSelect("region","region","route","admin_route.php","change_region","region");				
					$sel->selectWidth=20;
					$sel->selectOnChange="";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
?>					
				</td>
				<td>
					<input name="submit" type="submit" value="Change" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="edit_region" />
 	</form>
<?
}


if($action=="edit_region"){
	$seq = get("route","seq_region","WHERE region='$region'");
?>
	<form name="change_region" action="admin_route.php" method="get">
		<table>
			<tr>
				<th>Name</th>
				<th>Sequence</th>
			</tr>		
			<tr>
				<td>
					<input type="text" name="region_name" value="<?=$region?>" />
				</td>
				<td>
					<input type="text" name="seq_region" value="<?=$seq?>" />
				</td>				
				<td>
					<input name="submit" type="submit" value="Save" />
					<input name="cancel" type="submit" value="Cancel" />
					<input name="delete" type="button" value="Delete" onClick="return confirmDelete('admin_route.php?action=delete_region&region='+document.change_region.region_name.value);" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="save_region" />
		<input type="hidden" name="region" value="<?=$region?>" />
 	</form>
<?	
}

if($action=="change_area"){
?>
	<h3>Select Area</h3>
	<form name="change_area" action="admin_route.php" method="get">
		<table>
			<tr>
				<td>
<?				
					$sel = new MySQLSelect("area","area","route","admin_route.php","change_area","area");				
					$sel->selectWidth=20;					
					$sel->selectOnChange="";
					$sel->startSelect();
					$sel->writeSelect();
					$sel->stopSelect();
?>					
				</td>
				<td>
					<input name="submit" type="submit" value="Change" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="edit_area" />
 	</form>
<?
}

if($action=="edit_area"){
	$seq = get("route","seq_area","WHERE area='$area'");
?>
	<form name="edit_area" action="admin_route.php" method="get">
		<table>
			<tr>
				<th>Name</th>
				<th>Sequence</th>
			</tr>
			<tr>
				<td>
					<input type="text" name="area_name" value="<?=$area?>" />
				</td>
				<td>
					<input type="text" name="seq_area" value="<?=$seq?>" />
				</td>				
				<td>
					<input name="submit" type="submit" value="Save" />
					<input name="cancel" type="submit" value="Cancel" />			
					<input name="delete" type="button" value="Delete" onClick="return confirmDelete('admin_route.php?action=delete_area&area='+document.edit_area.area_name.value);" />							
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="save_area" />
		<input type="hidden" name="area" value="<?=$area?>" />
 	</form>
<?	
}



//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.route								//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////
if($action=="edit" || $action=="add"){
		$sql   = "SELECT * FROM route WHERE route_id='$record'";
		$route = mysql_fetch_object(query($sql));
		$descr = str_replace("<br />", "", $route->description);

		if(!$island)
			$island 		= $route->island;
		if(!$region)
			$region			= $route->region;
		if(!$area)
			$area			= $route->area;
		if(!$code)
			$code			= $route->code;
		if(!$no_ticket_header)
			$no_ticket_header			= $route->no_ticket_header;				
		if(!$num_farmers)
			$num_farmers	= $route->num_farmers;
		if(!$num_lifestyle)
			$num_lifestyle	= $route->num_lifestyle;
		if(!$num_sheep)
			$num_sheep		= $route->num_sheep;
		if(!$num_beef)
			$num_beef		= $route->num_beef;
		if(!$num_hort)
			$num_hort		= $route->num_hort;
		if(!$num_sheepbeef)
			$num_sheepbeef	= $route->num_sheepbeef;
		if(!$num_dairybeef)
			$num_dairybeef	= $route->num_dairybeef;
		if(!$num_dairies)
			$num_dairies	= $route->num_dairies;
		if(!$num_nzfw)
			$num_nzfw	= $route->num_nzfw;

		if(!$rmt)
			$rmt	= $route->rmt;
		if(!$rm_rr)
			$rm_rr	= $route->rm_rr;
		if(!$rm_f)
			$rm_f	= $route->rm_f;
		if(!$rm_d)
			$rm_d	= $route->rm_d;
			
			
		if(!$pmp_areacode)
			$pmp_areacode	= $route->pmp_areacode;
		if(!$pmp_runcode)
			$pmp_runcode	= $route->pmp_runcode;
			
		if(!$seq_region)
			$seq_region	= $route->seq_region;
		if(!$seq_area)
			$seq_area	= $route->seq_area;
		if(!$seq_code)
			$seq_code	= $route->seq_code;		
			
		if(!$is_hidden)
			$is_hidden	= $route->is_hidden;									
?>	
		<form name="editroute" action="admin_route.php?action=save" method="get">
			<table>
			  <tr>
					<td>Island</td>
					<td>
						<?
							$sel = new MySQLSelect("island","island","route","admin_route.php","editroute","island");
							$sel->selectSize=1;
							$sel->setOptionIsVal($island);
							$sel->onChangeAction="edit";
							$sel->addOnSimpleChange("record",$record);
							$sel->orderField="island";
							
							$sel->addOnChange("region_new");
							$sel->addOnChange("area_new");
							$sel->addOnChange("code_new");
							
							$sel->addOnChange("num_farmers");
							$sel->addOnChange("num_lifestyle");
							$sel->addOnChange("num_sheep");
							$sel->addOnChange("num_beef");
							$sel->addOnChange("num_hort");
							$sel->addOnChange("num_sheepbeef");
							$sel->addOnChange("num_dairybeef");
							
							$sel->addOnChange("num_dairies");
							$sel->addOnChange("num_nzfw");
							$sel->addOnChange("rm_rr");
							$sel->addOnChange("rm_f");
							$sel->addOnChange("rm_d");
							
							$sel->addOnChangeChecked("is_hidden");
							
							$sel->addOnChange("pmp_areacode");
							$sel->addOnChange("pmp_runcode");
							
							$sel->addOnChange("seq_code");								
							
							$sel->startSelect();						
							$sel->writeSelect();
							$sel->stopSelect();		
						?>					
					</td>
					<td colspan="2">
					<?
						if($record>0 || $route_id>0){
					?>
							<a href="admin_route.php?action=show_aff&route_id=<?=$record?>">Edit Affiliation</a>
					<?
						}
						else{
					?>
							<font style="font-size:0.8em; color:#0000FF ">For editing the affiliation save  the new route first and reopen the edit screen.</font>
					<?
						}
					?>
					</td>			
					<td>Number Farmers:</td>
					<td><input style="width:18em; "  type="text" name="num_farmers" value="<?=$num_farmers?>" onKeyUp="javascript:checknum(this.value, this);" /></td>					
				</tr>
				<tr>
					<td>Region:</td>
					<td>
						<?
							$sel = new MySQLSelect("region","region","route","admin_route.php","editroute","region");
							$sel->selectSize=1;
							$sel->setOptionIsVal($region);
							$sel->onChangeAction="edit";
							$sel->addOnSimpleChange("record",$record);
							$sel->orderField="seq_region";
							
							$sel->addVar("island",$island);
							
							$sel->addOnChange("region_new");
							$sel->addOnChange("area_new");
							$sel->addOnChange("code_new");
							
							$sel->addOnChange("num_farmers");
							$sel->addOnChange("num_lifestyle");
							$sel->addOnChange("num_sheep");
							$sel->addOnChange("num_beef");
							$sel->addOnChange("num_hort");
							$sel->addOnChange("num_sheepbeef");
							$sel->addOnChange("num_dairybeef");
							$sel->addOnChange("num_dairies");
							$sel->addOnChange("num_nzfw");
							$sel->addOnChange("rm_rr");
							$sel->addOnChange("rm_f");
							$sel->addOnChange("rm_d");						
							
							$sel->addOnChangeChecked("is_hidden");
							
							$sel->addOnChange("pmp_areacode");
							$sel->addOnChange("pmp_runcode");
							
							$sel->addOnChange("seq_code");								
							
							$sel->startSelect();						
							$sel->writeSelect();
							$sel->stopSelect();		
						?>
					</td>
					<td colspan="2">&nbsp;</td>
					<td>Number Lifestyle:</td>
					<td><input style="width:18em; "  type="text" name="num_lifestyle" value="<?=$num_lifestyle?>" onKeyUp="javascript:checknum(this.value, this);" /></td>					
				</tr>
				<tr>
					<td>Or new region</td>
					<td><input style="width:18em; "  type="text" name="region_new" value="<?=$region_new?>" /></td>
					<td colspan="2">&nbsp;</td>

					<td>Number Dairies:</td>
					<td><input style="width:18em; "  type="text" name="num_dairies" value="<?=$num_dairies?>" onKeyUp="javascript:checknum(this.value, this);" /></td>					
				</tr>					
				<tr>
					<td>Area:</td>
					<td>
						<?
							$sel = new MySQLSelect("area","area","route","admin_route.php","editroute","area");
							$sel->selectSize=1;
							$sel->setOptionIsVal($area);
							$sel->orderField="seq_area";
							$sel->onChangeAction="edit";
							
							$sel->addVar("region",$region);			
							$sel->addVar("island",$island);			
							$sel->addOnSimpleChange("record",$record);		
							$sel->addOnChange("region_new");
							$sel->addOnChange("area_new");
							$sel->addOnChange("code_new");
							
							$sel->addOnChange("num_farmers");
							$sel->addOnChange("num_lifestyle");
							$sel->addOnChange("num_sheep");
							$sel->addOnChange("num_beef");
							$sel->addOnChange("num_hort");
							$sel->addOnChange("num_sheepbeef");
							$sel->addOnChange("num_dairybeef");
							$sel->addOnChange("num_dairies");
							$sel->addOnChange("num_nzfw");
							$sel->addOnChange("rm_rr");
							$sel->addOnChange("rm_f");
							$sel->addOnChange("rm_d");				
							
							$sel->addOnChangeChecked("is_hidden");
							
							$sel->addOnChange("pmp_areacode");
							$sel->addOnChange("pmp_runcode");
							
							$sel->addOnChange("seq_code");									
							
							$sel->startSelect();
							$sel->writeSelect();
							$sel->stopSelect();		
						?>
					</td>
					<td colspan="2">&nbsp;</td>					
					<td>Number Sheep:</td>
					<td><input style="width:18em; "  type="text" name="num_sheep" value="<?=$num_sheep?>" onKeyUp="javascript:checknum(this.value, this);" /></td>							
				</tr>
				<tr>
					<td>Or new Area</td>
					<td><input style="width:18em; "  type="text" name="area_new" value="<?=$area_new?>" /></td>
					<td colspan="2">&nbsp;</td>
					<td>Number Beef:</td>
					<td><input style="width:18em; "  type="text" name="num_beef" value="<?=$num_beef?>" onKeyUp="javascript:checknum(this.value, this);" /></td>										
				</tr>
				<tr>
					<td>RD:</td>
					<td>
						<?
							$sel = new MySQLSelect("code","code","route","admin_route.php","editroute","code");
							$sel->selectSize=1;
							$sel->optionDefValue="";
							$sel->setOptionIsVal($code);
							$sel->orderField="seq_code";
							$sel->selectOnChange="";
							$sel->addSQLWhere("region",$region);			
							$sel->addSQLWhere("area",$area);
							$sel->startSelect();
							$sel->writeSelect();
							$sel->stopSelect();		
						?>
					</td>
					<td colspan="2">&nbsp;</td>
					<td>Number Sheep/Beef:</td>
					<td><input style="width:18em; "  type="text" name="num_sheepbeef" value="<?=$num_sheepbeef?>" onKeyUp="javascript:checknum(this.value, this);" /></td>															
				</tr>
				<tr>
					<td>Or new RD</td>
					<td><input style="width:18em; "  type="text" name="code_new" value="<?=$code_new?>" /></td>
					<td>Is Hidden</td>
					<td><input name="is_hidden" type="checkbox" value="Y" <? if($is_hidden=='Y' || $is_hidden=="true"){ ?> checked <? }?> /></td>
					<td>Number Dairy/Beef:</td>
					<td><input style="width:18em; "  type="text" name="num_dairybeef" value="<?=$num_dairybeef?>" onKeyUp="javascript:checknum(this.value, this);" /></td>
					
				</tr>			
				<tr>
					<td>PMP Area Code:</td>
					<td>
						<input style="width:18em; "  type="text" name="pmp_areacode" value="<?=$pmp_areacode?>" />
					</td>		
					<td>Seq. RD:</td>
					<td><input style="width:5em; "  type="text" name="seq_code" value="<?=$seq_code?>" /></td>																							
					
					<td>Number Hort:</td>
					<td><input style="width:18em; "  type="text" name="num_hort" value="<?=$num_hort?>" onKeyUp="javascript:checknum(this.value, this);" /></td>			
					
				</tr>			
				<tr>
					<td>PMP Run Code:</td>
					<td><input style="width:18em; "  type="text" name="pmp_runcode" value="<?=$pmp_runcode?>" /></td>			
					<td colspan="2">&nbsp;</td>
					
					
					<td>Number F@90%</td>
					<td><input style="width:18em; "  type="text" name="num_nzfw" value="<?=$num_nzfw?>" onKeyUp="javascript:checknum(this.value, this);" /></td>								
				</tr>
				<tr>
					<td>No Ticket Header:</td>
					<td><input name="no_ticket_header" type="checkbox" value="Y" <? if($no_ticket_header=='Y' || $no_ticket_header=="true"){ ?> checked <? }?> /></td>
					<td colspan="2"></td>
					<td>RMT RR:</td>
					<td><input style="width:18em; "  type="text" name="rm_rr" value="<?=$rm_rr?>" onKeyUp="javascript:checknum(this.value, this);" /></td>																
				</tr>
				<tr>
					<td colspan="4"></td>
					<td>RM F:</td>
					<td><input style="width:18em; "  type="text" name="rm_f" value="<?=$rm_f?>" onKeyUp="javascript:checknum(this.value, this);" /></td>											
				</tr>
				<tr>
					<td colspan="4"></td>
					
					<td>Rm D:</td>
					<td><input style="width:18em; "  type="text" name="rm_d" value="<?=$rm_d?>" onKeyUp="javascript:checknum(this.value, this);" /></td>											

				</tr>		
				<tr>
					<td colspan="4"></td>
					<td>RMT:</td>
					<td><input style="width:18em; " readonly="1"  type="text" name="rmt" value="<?=$rmt?>" onKeyUp="javascript:checknum(this.value, this);" /></td>											
				</tr>				
				<tr>
					<td>Description:</td>
					<td colspan="5" valign="top">
						<textarea rows="10" cols="80" name="description"><?=$descr?></textarea>
					</td>				
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="submit" value="Save" />
						<input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_route.php?island=<?=$route->island?>&region=<?=$route->region?>&area=<?=$route->area?>'" />
						<input type="hidden" name="action" value="save" />						
						<input type="hidden" name="record" value="<?=$record?>" />
					</td>
				</tr>
			</table>
	
			
<?
}

//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.route								//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////
if($action=="edit_aff"||$action=="edit_aff2"){
		if($action=="edit_aff2"){	
			$sql   = "SELECT * FROM route_aff 
						LEFT JOIN route
						ON route.route_id=route_aff.route_id
						WHERE route_aff.route_aff_id='$record'
						ORDER BY app_date DESC
						LIMIT 1";
			$title = "Edit an existing route affiliation.";
		}
		else{
			$sql   = "SELECT * FROM route_aff 
						LEFT JOIN route
						ON route.route_id=route_aff.route_id
						WHERE route.route_id='$route_id'
						ORDER BY app_date DESC
						LIMIT 1";		
			$title = "Add a new route affiliation.";
		}
					
		
		$route = mysql_fetch_object(query($sql));
		$route_id = $route->route_id;

		if(!$island)
			$island 		= $route->island;
		if(!$contractor_id)
			$contractor_id 	= $route->contractor_id;
		if(!$region)
			$region			= $route->region;
		if(!$subdist_id)
			$subdist_id		= $route->subdist_id;
		if(!$dropoff_id)
			$dropoff_id		= $route->dropoff_id;			
		if(!$dist_id)
			$dist_id		= $route->dist_id;
			
		if(!$env_contractor_id)
			$env_contractor_id 	= $route->env_contractor_id;
		if(!$env_subdist_id)
			$env_subdist_id		= $route->env_subdist_id;
		if(!$env_dropoff_id)
			$env_dropoff_id		= $route->env_dropoff_id;			
		if(!$env_dist_id)
			$env_dist_id		= $route->env_dist_id;
			
		if(!$area)
			$area			= $route->area;
		if(!$code)
			$code			= $route->code;			
		if($route){
			$app_date			= date("Y-m-d",strtotime($route->app_date));	
			$stop_date			= date("Y-m-d",strtotime($route->stop_date));	
		}
		else{
			$app_date = date("Y-m-d");
			$stop_date = date("Y-m-d");
		}
		
		if($action=="edit_aff"){
			$app_date = date("Y-m-d");
			$stop_date = date("2037-12-31");
		}
		
?>	
		<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
		<form name="editroute" action="admin_route.php?action=save" method="get">
			<table>
				<tr>
					<th colspan="100"><?=$title?></th>
				</tr>
			  <tr>
					<td>Island</td>
					<td><?=$island?></td>
					<td>Distr.</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_dist='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","dist_id");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->setOptionIsVal($dist_id);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>		
					<td>PC/Distr.</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_dist='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","env_dist_id");
							$sel->selectOnChange="";
							$sel->optionDefText = "Same as dist";
							$sel->selectSize=1;
							$sel->setOptionIsVal($env_dist_id);
							$sel->startSelect();
							$sel->addOption("-2","None");
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>							
					<td>Start Date:</td>											
					<td><script language="javascript">DateInput("app_date", true, "YYYY-MM-DD","<?=$app_date?>")</script></td>
				</tr>
				<tr>
					<td>Region:</td>
					<td><?=$region?></td>
					<td>Sub-Distr.</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_subdist='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","subdist_id");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->setOptionIsVal($subdist_id);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>		
					<td>PC/Sub-Distr.</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_subdist='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","env_subdist_id");
							$sel->selectOnChange="";
							$sel->optionDefText = "Same as subdist";
							$sel->selectSize=1;
							$sel->setOptionIsVal($env_subdist_id);
							$sel->startSelect();
							$sel->addOption("-2","None");
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>		
<? 
					if($action=="edit_aff2"){
?>
					<td>End Date:</td>											
					<td><script language="javascript">DateInput("stop_date", true, "YYYY-MM-DD","<?=$stop_date?>")</script></td>					
<?
					}
?>						
				</tr>
				<tr>
					<td>Area:</td>
					<td><?=$area?></td>
					<td>Contractor:</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_contr='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","contractor_id");
							$sel->selectSize=1;
							$sel->selectOnChange="";
							$sel->setOptionIsVal($contractor_id);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>				
					<td>PC/Contractor:</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_contr='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","env_contractor_id");
							$sel->selectSize=1;
							$sel->selectOnChange="";
							$sel->optionDefText = "Same as contractor";
							$sel->setOptionIsVal($env_contractor_id);
							$sel->startSelect();
							$sel->addOption("-2","None");
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>																	
				</tr>					
				<tr>
					<td>RD:</td>
					<td><?=$code?>
					<td>Drop Off</td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_dropoff='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","dropoff_id");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->setOptionIsVal($dropoff_id);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>		
					<td>PC/Drop Off</td>
					<td  valign="top" >
						<?
							//$operator_id = get("operator","operator_id","WHERE operator_id='$env_dropoff_id' AND is_alt_dropoff='Y'");
							//if(!$operator_id) $env_dropoff_id = "0";
							
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_alt_dropoff='Y' OR is_dropoff='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","env_dropoff_id");
							$sel->selectOnChange="";
							$sel->optionDefText = "Same as DO";
							$sel->selectSize=1;
							$sel->setOptionIsVal($env_dropoff_id);
							$sel->startSelect();
							$sel->addOption("-2","None");
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>														
				</tr>
				<!--<tr>
					<td colspan="2">&nbsp;</td>
					<td>Alternate Distributor<sup>*</sup></td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_dist='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","env_dist_id");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->optionDefText = "Same as distr.";
							$sel->setOptionIsVal($env_dist_id);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>				
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>Alternate Contractor.<sup>*</sup></td>
					<td  valign="top" >
						<?
							$sql_op = "SELECT operator.operator_id AS id,operator.company AS name FROM operator
												WHERE is_contr='Y'
												GROUP BY company
												ORDER BY company";							
							$sel = new MySQLSelect("operator_id","company","operator","admin_route.php","editroute","env_contractor_id");
							$sel->selectOnChange="";
							$sel->selectSize=1;
							$sel->optionDefText = "Same as distr.";
							$sel->setOptionIsVal($env_contractor_id);
							$sel->startSelect();
							$sel->writeSelectSQL($sql_op);
							$sel->stopSelect();						
						?>
					</td>								
				</tr>		
				<tr>
					<td height="50" colspan="4">Appl. PC for whole region <input type="checkbox" name="app_whole_reg" value="Y" /></td>
				</tr>-->		
				<tr>
					<td colspan="2" align="center">
<?
						if($action=="edit_aff2"){
?>					
							<input type="submit" name="submit" value="Save" />
<?
						}
						else{
?>					
							<input type="submit" name="submit" value="Add" />
<?						
						}
?>
						<input type="button" name="cancel" value="Cancel" onClick="window.location.href='admin_route.php?action=show_aff&record=<?=$route_id?>'" />
<?
						if($action=="edit_aff2"){
?>											
							<input type="hidden" name="action" value="change_aff" />						
<?						
						}
						else{
?>					
							<input type="hidden" name="action" value="add_aff" />	
							<input type="hidden" name="stop_date" value="<?=$stop_date?>" />	
<?						
						}
?>
						
						<input type="hidden" name="route_id" value="<?=$route_id?>" />
						<input type="hidden" name="dest" value="<?=$action?>" />
						<input type="hidden" name="record" value="<?=$route_id?>" />
						<input type="hidden" name="route_aff_id" value="<?=$route->route_aff_id?>" />
					</td>
				</tr>
				<tr>
					<td style="font-size:0.8em; " colspan="6"><sup>*</sup>Change start date first.</td>				
				</tr>
			</table>
<?
}
if($action=="show_aff"){
	if(!$route_id) $route_id=$record;
?>		
	<div>
		<input type="button" onClick="document.location.href='admin_route.php?action=edit&record=<?=$route_id?>'" value="Go Back" />	
		<h3>Current Schedule of Changes</h3>
	</div>
<?
	$qry = "SELECT  route_aff_id AS Record,
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.dist_id) AS Dist,
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.subdist_id) AS 'S/Dist',
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.contractor_id) AS 'Contr',
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.dropoff_id) AS 'Dropoff',
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.env_dist_id) AS 'PC/Dist',
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.env_subdist_id) AS 'PC/S/Dist',
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.env_contractor_id) AS 'PC/Contr',
					(SELECT company FROM operator WHERE operator.operator_id=route_aff.env_dropoff_id) AS 'PC/Dropoff',
					app_date AS 'Start Date',
					stop_date AS 'End Date'
			FROM route
			LEFT JOIN route_aff
			ON route.route_id=route_aff.route_id
			WHERE route.route_id='$route_id'
			ORDER BY app_date DESC";
	
	$res = query($qry);
	$tab = new MySQLTable("admin_route.php",$qry,"test");
	$tab->formatLine=true;
	$tab->hasAddButton=true;
	$tab->hasEditButton=true;
	
	$tab->showRec=0;
	$tab->onClickAddButtonAction="edit_aff";
	$tab->onClickEditButtonAction="edit_aff2";
	$tab->onClickDeleteButtonAction="delete_aff";
	if(mysql_num_rows($res)<=1){
		$tab->hasDeleteButton=false;
	}
	$tab->onClickAddButtonAdd = "+'&route_id=".$route_id."'";
	$tab->onClickEditButtonAdd = "+'&route_id=".$route_id."'";
	$tab->cssSQLTable="sqltable_scroll";	
	
	$tab->wrap1 = "Dropoff";
	
	$tab->startList();
	$tab->writeList();
	$tab->stopList();
}




if($action=="run_show_old_numbers"){
	$where=" WHERE year(backup_date)='$year' ";
	if($island)  $where.=" AND island='$island'";
	if($region)  $where.=" AND region='$region'";
	if($area) $where.=" AND area='$area'";
	if($code) $where.=" AND code='$code'";
	
	if($region){
		$sql = "SELECT route_id AS Record,
					route_old_num.backup_date AS 'Backup Date',
					operator.company AS Contractor,
					(SELECT company FROM operator WHERE operator.operator_id=route_old_num.dist_id) AS Distributor,
					(SELECT company FROM operator WHERE operator.operator_id=route_old_num.subdist_id) AS 'Sub-Distributor',
					island,
					region,
					area,
					code,
					description,
					pmp_areacode AS PMP_AREA,
					pmp_runcode AS PMP_RUN,
					num_farmers AS Farmers,
					num_lifestyle AS Lifestyle,
					num_dairies AS Dairy,
					num_sheep AS Sheep,
					num_beef AS Beef,
					num_sheepbeef AS 'Sheep/Beef',
					num_dairybeef AS 'Dairy/Beef',
					num_hort AS Hort,
					num_nzfw AS 'F@90%',
					num_spare AS Spare,
					num_spare2 AS Spare2
				FROM route_old_num
				LEFT JOIN operator
				ON route_old_num.contractor_id=operator.operator_id
				$where
				ORDER BY island,seq_region,seq_area,seq_code";
		$routeTab = new MySQLTable("admin_route.php",$sql);
		$routeTab->showRec=0;	
		$routeTab->hasExtEditButton=0;		
		$routeTab->hasEditButton=0;		
		$routeTab->hasDeleteButton=0;		
		$routeTab->hasAddButton=0;		
		$routeTab->wrap1   = "PMP_RUN";
		$routeTab->writeList();	
	}
}


if($action=="maintain_sequence"){
	$where=" WHERE route_id>0 ";
	if($island)  $where.=" AND island='$island'";
	if($region)  $where.=" AND region='$region'";
	if($area) $where.=" AND area='$area'";
	if($code) $where.=" AND code='$code'";
	
	if($region){
		$qry = "SELECT route_id,
					island,
					region,
					area,
					code,
					seq_region,
					seq_area,
					seq_code
				FROM route
				$where
				ORDER BY island,seq_region,seq_area,seq_code";
		$res = query($qry);
?>
		<form name="numbers" action="admin_route.php" method="post">
			<table>
				<tr>
					<td colspan="3">
						<input type="submit" name="submit" value="Save" />
						<input type="submit" name="cancel" value="Cancel" />
					</td>
				</tr>
				<tr>
					<th class="sqltabhead">Isl.</th>
					<th class="sqltabhead">Reg.</th>
					<th class="sqltabhead">Area</th>
					<th class="sqltabhead">RD</th>
					<th class="sqltabhead">Seq. Reg.</th>
					<th class="sqltabhead">Seq. Area</th>
					<th class="sqltabhead">Seq. RD</th>
				</tr>				
<?
				while($route = mysql_fetch_object($res)){
?>		
				<tr>
					<td class="sqltabunevenline"><?=$route->island?></td>
					<td class="sqltabevenline"><?=$route->region?></td>
					<td class="sqltabunevenline"><?=$route->area?></td>
					<td class="sqltabevenline"><?=$route->code?></td>
					<td><input size="5" type="text" name="pmp_areacode[<?=$route->route_id?>]" value="<?=$route->seq_region?>" /></td>
					<td><input size="5" type="text" name="pmp_runcode[<?=$route->route_id?>]" value="<?=$route->seq_area?>" /></td>
					<td><input size="5" type="text" name="num_farmers[<?=$route->route_id?>]" value="<?=$route->seq_code?>" /></td>
				</tr>
				<input type="hidden" name="id[<?=$route->route_id?>]" value="<?=$route->route_id?>" />
<?
				}
?>			
			</table>
			<input type="hidden" name="action" value="save_numbers" />
			<input type="hidden" name="region" value="<?=$region?>" />
			<input type="hidden" name="area" value="<?=$area?>" />
			<input type="hidden" name="code" value="<?=$code?>" />
		</form>
<?				
	}
}

if($action=="maintain_numbers"){
	$where=" WHERE route_id>0 ";
	if($island)  $where.=" AND island='$island'";
	if($region)  $where.=" AND region='$region'";
	if($area) $where.=" AND area='$area'";
	if($code) $where.=" AND code='$code'";
	
	if($region){
		$qry = "SELECT route_id,
					island,
					region,
					area,
					code,
					num_farmers,
					num_lifestyle,
					num_dairies,
					num_sheep,
					num_beef,
					num_sheepbeef,
					num_dairybeef
					num_hort,
					num_nzfw,
					num_spare,
					num_spare2
				FROM route
				$where
				ORDER BY island,seq_region,seq_area,seq_code";
		$res = query($qry);
?>
		<form name="numbers" action="admin_route.php" method="post">
			<table>
				<tr>
					<td colspan="3">
						<input type="submit" name="submit" value="Save" />
						<input type="submit" name="cancel" value="Cancel" />
					</td>
				</tr>
				<tr>
					<th class="sqltabhead">Isl.</th>
					<th class="sqltabhead">Reg.</th>
					<th class="sqltabhead">Area</th>
					<th class="sqltabhead">RD</th>
					<!--<th class="sqltabhead">PMP Area</th>
					<th class="sqltabhead">PMP Run</th>-->
					
					<th class="sqltabhead">Farm.</th>
					<th class="sqltabhead">Life</th>
					<th class="sqltabhead">Dairy</th>
					<th class="sqltabhead">Sheep</th>
					<th class="sqltabhead">Beef</th>
					<th class="sqltabhead">Shp/Be</th>
					<th class="sqltabhead">Dair/Be</th>
					<th class="sqltabhead">Hort</th>
					<th class="sqltabhead">F@90%</th>
					<th class="sqltabhead">Spare</th>
				</tr>				
<?
				while($route = mysql_fetch_object($res)){
?>		
				<tr>
					<td class="sqltabunevenline"><?=$route->island?></td>
					<td class="sqltabevenline"><?=$route->region?></td>
					<td class="sqltabunevenline"><?=$route->area?></td>
					<td class="sqltabevenline"><?=$route->code?></td>
					<!--<td><input size="5" type="text" name="pmp_areacode[<?=$route->route_id?>]" value="<?=$route->pmp_areacode?>" /></td>
					<td><input size="5" type="text" name="pmp_runcode[<?=$route->route_id?>]" value="<?=$route->pmp_runcode?>" /></td>-->
					<td><input size="5" type="text" name="num_farmers[<?=$route->route_id?>]" value="<?=$route->num_farmers?>" /></td>
					<td><input size="5" type="text" name="num_lifestyle[<?=$route->route_id?>]" value="<?=$route->num_lifestyle?>" /></td>
					<td><input size="5" type="text" name="num_dairies[<?=$route->route_id?>]" value="<?=$route->num_dairies?>" /></td>
					<td><input size="5" type="text" name="num_sheep[<?=$route->route_id?>]" value="<?=$route->num_sheep?>" /></td>
					<td><input size="5" type="text" name="num_beef[<?=$route->route_id?>]" value="<?=$route->num_beef?>" /></td>
					<td><input size="5" type="text" name="num_sheepbeef[<?=$route->route_id?>]" value="<?=$route->num_sheepbeef?>" /></td>
					<td><input size="5" type="text" name="num_spare2[<?=$route->route_id?>]" value="<?=$route->num_spare2?>" /></td>
					<td><input size="5" type="text" name="num_hort[<?=$route->route_id?>]" value="<?=$route->num_hort?>" /></td>
					<td><input size="5" type="text" name="num_nzfw[<?=$route->route_id?>]" value="<?=$route->num_nzfw?>" /></td>
					<td><input size="5" type="text" name="num_spare[<?=$route->route_id?>]" value="<?=$route->num_spare?>" /></td>
				</tr>
				<input type="hidden" name="id[<?=$route->route_id?>]" value="<?=$route->route_id?>" />
<?
				}
?>			
			</table>
			<input type="hidden" name="action" value="save_numbers" />
			<input type="hidden" name="island" value="<?=$island?>" />
			<input type="hidden" name="region" value="<?=$region?>" />
			<input type="hidden" name="area" value="<?=$area?>" />
			<input type="hidden" name="code" value="<?=$code?>" />
		</form>
<?				
	}
}



//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////
function ckeck_amp($value){
	if(strpos($value,'axx123y')){
		$value = str_replace('axx123y','&',$value);
	}
	else
		$value =$value;	
	return $value;
}

if($action=="" || !isset($action)){
	$now = date("Y-m-d");
	$where=" WHERE 
			stop_date>='$now'
			AND app_date<='$now'";
	$region = ckeck_amp($region);
	$area = ckeck_amp($area);
	$code = ckeck_amp($code);

	if($island)  $where.=" AND island='$island'";
	if($region)  $where.=" AND region='$region'";
	if($area) $where.=" AND area='$area'";
	if($code) $where.=" AND code='$code'";
	
	
	if($region){
		
		$sql = "SELECT route.route_id AS Record,
					island AS Island,
					region AS Region,
					area AS Area,
					code AS RD,
					description AS Description,
					IF(is_hidden='Y',CONCAT('<font color=\'red\'><b>',is_hidden,'</b></font>'),is_hidden)	
								AS 'Is Hidden',
					pmp_areacode AS PMP_AREA,
					pmp_runcode AS PMP_RUN,
					CONCAT('<a href=\'admin_address.php?record=',address_id,'\'>',company,'</a>') AS Contractor,
					seq_region AS 'Seq. Region',
					seq_area AS 'Seq. Area',
					seq_code AS 'Seq. RD',
					
					num_farmers+num_lifestyle AS Total,
					num_farmers AS Farmers,
					num_lifestyle AS Lifestyle,
					num_dairies AS Dairy,
					num_sheep AS Sheep,
					num_beef AS Beef,
					num_sheepbeef AS 'Sheep/Beef',
					num_dairybeef AS 'Dairy/Beef',
					num_hort AS Hort,
					num_nzfw AS 'F@90%',
					rmt AS RMT,
					rm_rr AS 'RMT RR',
					rm_f AS 'RMT F',
					rm_d AS 'RMT D'
					
				FROM route
				LEFT JOIN route_aff
				ON route.route_id = route_aff.route_id
				LEFT JOIN operator
				ON operator.operator_id=route_aff.contractor_id
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				$where
				ORDER BY app_date,island,seq_region,seq_area,seq_code";
		//echo nl2br($sql);
		$res = query($sql);
		if(mysql_num_rows($res)==0){
			$where=" WHERE route.route_id IS NOT NULL";
			$region = ckeck_amp($region);
			$area = ckeck_amp($area);
			$code = ckeck_amp($code);
		
			if($island)  $where.=" AND island='$island'";
			if($region)  $where.=" AND region='$region'";
			if($area) $where.=" AND area='$area'";
			if($code) $where.=" AND code='$code'";		
			$sql = "SELECT route.route_id AS Record,
					island AS Island,
					region AS Region,
					area AS Area,
					code AS RD,
					description AS Description,
					IF(is_hidden='Y',CONCAT('<font color=\'red\'><b>',is_hidden,'</b></font>'),is_hidden)	
								AS 'Is Hidden',
					pmp_areacode AS PMP_AREA,
					pmp_runcode AS PMP_RUN,
					CONCAT('<a href=\'admin_address.php?record=',address_id,'\'>',company,'</a>') AS Contractor,					
					num_farmers+num_lifestyle AS Total,
					num_farmers AS Farmers,
					num_lifestyle AS Lifestyle,
					num_dairies AS Dairy,
					num_sheep AS Sheep,
					num_beef AS Beef,
					num_sheepbeef AS 'Sheep/Beef',
					num_dairybeef AS 'Dairy/Beef',
					num_hort AS Hort,
					num_nzfw AS 'F@90%',
					rmt AS RMT,
					rm_rr AS 'RMT RR',
					rm_f AS 'RMT F',
					rm_d AS 'RMT D',
					seq_region AS 'Seq. Region',
					seq_area AS 'Seq. Area',
					seq_code AS 'Seq. RD'
				FROM route
				LEFT JOIN route_aff
				ON route.route_id = route_aff.route_id
				LEFT JOIN operator
				ON operator.operator_id=route_aff.contractor_id
				LEFT JOIN address
				ON operator.operator_id=address.operator_id
				$where
				ORDER BY island,seq_region,seq_area,seq_code";
				echo "No route affiliation for the current date. Please check consistency of the route affiliation dates.";
		}
		//echo nl2br($sql);
		$routeTab = new MySQLTable("admin_route.php",$sql);
		$routeTab->showRec=0;	
		$routeTab->hasExtEditButton=0;		
		$routeTab->wrap1   = "Seq. RD";
		$routeTab->writeList();	
	}

}
?>