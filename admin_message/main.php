<script language="javascript">
	function checkfloat(strString,field)
		   //  check for valid numeric strings	
		   {
		   var strValidChars = "0123456789.";
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

	function eventTrigger (e) {
		if (! e)
			e = event;
		return e.target || e.srcElement;
	}
	
	function checkAll(obj)
	{
		// set the form to look at (your form is called form1)
		var frm = document.table
		// get the form elements
		var el = frm.elements
		// loop through the elements...
		for(i=0;i<el.length;i++) {
		  // and check if it is a checkbox
		  if(el[i].type == "checkbox" ) {
			// if it is a checkbox and you submitted yes to the function
			if(obj == "yes")
			  // tick the box
			  el[i].checked = true;
			else
			  // otherwise untick the box
			  el[i].checked = false;
			}
		  }
	}

	
</script>
<!-- Ajax Scripts -->
<script src="javascripts/ajax.js" type="text/javascript" language="javascript"></script>

<?


//////////////////////////////////////////////////////////
// SECTION message											//
//////////////////////////////////////////////////////////


if(!$action){
	$qry = "SELECT message_id,title FROM message";

	$tab = new MySQLTable("admin_message.php",$qry,"nocoll");
	$tab->formatLine=true;
	$tab->cssSQLTable="sqltable_scroll";

	$tab->showRec=0;
	$tab->colWidth["Action"]=1000;
	$tab->hasAddButton=true;
	$tab->hasEditButton=true;
	$tab->hasDeleteButton=true;
	$tab->startTable();
	$tab->writeTable();	
	$tab->stopTable();			
}

// message edit screen. Lets the user book routes to a message
if($action=="add_addresses" || $action == "change_addresses"){

	$qry = "SELECT * FROM message 
			WHERE message_id='$message_id'";
	$res = query($qry);
	$message = mysql_fetch_object($res);
	
	if(!$is_current) $is_current = 0;
	if(!$is_shareholder) $is_shareholder = 0;
	if(!$op_type) $op_type = 0;
	if(!$send_type) $send_type = 0;
	if(!$dist) $dist = 0;
	
?>
		<script language="javascript">
			function set_Button_on(){
				var result = "<input name=\'submit1\' value=\'Add Route(s)\' type=\'submit\' />";
				document.getElementById('add_route_wrap').innerHTML = result;  
			}
			function set_Button_off(){
				var result = "<input disabled name=\'submit1\' value=\'Add Route(s)\' type=\'submit\' />";
				document.getElementById('add_route_wrap').innerHTML = result;  
			}			
		</script>
		<div id="job_header">
			<div id="edit_job_details">
			</div>						
			<table cellpadding="5">
				<tr>
					<th>Title</th>
					<td><?=$message->title?></td>
				</tr>
				<tr>
					<th>Message</th>	
					<td><?=$message->message?></td>
				</tr>
			</table>
			
		</div>
		<div id="job_add_route">
			<form name="alter" action="admin_message.php" method="get">
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
						<td>
							<input type="submit" name="submit" value="Add" />
						</td>
					</tr>
					<input type="hidden" name="action" value="send_messages" />
			</table>
			<input type="hidden" value="add_addresses" name="action" />
			<input type="hidden" value="add_operator" name="subaction" />
			<input type="hidden" value="<?=$message_id?>" name="message_id" />
			</form>

		 </div>
		 <div id="job_show_route">
			Select:
			<span class="set_button" onClick="return checkAll('yes')">All</span>
			<span class="set_button" onClick="return checkAll('no')">None</span>
<?
			show_table($message_id);		
?>
		</div>
<?		
}


// Creates a new message or the user may change general message info
if($action=="edit" || $action=="add"){
	
	$message_id = $record;
	$today = date("Y-m-d");
	if($message_id>0){
		$title = get("message","title","WHERE message_id=$message_id");
		$message = get("message","message","WHERE message_id=$message_id");
	}
?>
	<script type="text/javascript" src="includes/calendarDateInput.js"></script> 
	<script language="javascript">
		function nl2br( str ) {
			// http://kevin.vanzonneveld.net
			// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   improved by: Philip Peterson
			// *     example 1: nl2br('Kevin\nvan\nZonneveld');
			// *     returns 1: 'Kevin<br/>\nvan<br/>\nZonneveld'
		 
			return str.replace(/([^>])\n/g, '$1<br />\n');
		}
		function setPrintComment(){
			var text = nl2br(document.getElementById("comment").value);
			document.getElementById("print_comment").innerHTML=text;
		}
		
		function checkNumChars(id){
			var control = document.getElementById(id);
			var text = control.value;
			//alert(control.value);
			if(text.length>160){
				alert('Message too long (>160 chars)!');
				return false;
			}
			return true;
		}
	</script>
	<form action="admin_message.php" method="get" name="message">
		<table style="font-size:0.9em; background-color:yellow;	" id="message_form" >
			<tr>
				<th colspan="5"><h1>Coural Messages</h1></th>
			</tr>
			<tr>
				<td>Title:</td>
				<td colspan="3">
					<input type='text' id="title" class="show_on_screen" name="title" value="<?=$title?>" />
				
			</tr>			
			<tr>
				<td>Message:</td>
				<td colspan="3">
					<textarea id="smessage" class="show_on_screen" cols="59" rows="4" name="message"><?=$message?></textarea>
				
			</tr>
			<tr>
<?
			if($message_id){
?>			
				<td style="text-align:center " colspan="5">
					<input class="input_button" type="submit" value="Next" name="submit" onClick="return checkNumChars('smessage')" />
					<input class="input_button" type="submit" value="Cancel" name="cancel"  onClick="document.location.href='index.php'"  />					
					<input name="action" type="hidden" value="add_addresses" />		
				</td>
<?
			}
			else{
			?>			
				<td style="text-align:center " colspan="5">
					<input class="input_button" type="submit" value="Next" name="submit" onClick="return checkNumChars('smessage')"  />
					<input class="input_button" type="button" value="Cancel" name="cancel" onClick="document.location.href='index.php'"  />
					<input class="input_button" name="action" type="hidden" value="add_addresses" />
				</td>
<?
			}
?>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
		</table>
		<input name="subaction" type="hidden" value="update_message" />		
		<input name="today" type="hidden" value="<?=$today?>" />
		<input name="message_id" type="hidden" value="<?=$message_id?>" />
	</form>
<?	
}
 
?>