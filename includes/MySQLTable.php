<script language="javascript">
		function confirmDelete() {
			return confirm("Are you sure you want to delete?");
		}
</script>		


<?

class MySQLTable{
	// Attributes
	var $qry;
	var $page;
	var $formPage;
	var $editPage;
	var $showRec=1;
	var $name = "default";
	var $actionButtonTableType="href";
	var $actionButtonListType="button";
	var $actionButtonType="href";
	var $hiddenFields = array();
	var $colWidth = array();
	
	var $collField = array();
	var $collFieldVal = array();
	
	var $fieldNames = array();
	
	var $sumFields = array();
	var $sumGroupField = array();
	var $sumGroupFieldValue = array();
	var $sumGroupPrevFieldValue = array();
	var $sumValues = array();
	
	var $noRepFields = array();
	var $noRepValues = array();
	
	var $emptyMessage = "No records";
	
	// Attributes for function writeList. Will wrap output at the specified fields
	var $wrap1;
	var $wrap2;
	var $wrap3;
	var $wrap4;
	
	var $captionF = "";
	
	// Attributes for details. If set and field available teh class will create a link to the
	// respective address site
	var $detailField	="";
	var $detailAddress	="";
	
	var $highlightField="ID";
	var $cssSQLHighlightHead="sqllist_high_head";
	var $cssSQLHighlightValue="sqllist_high_value";
	
	var $highlightSpecifierField 		= "Edited";
	
	var $cssSQLHighlightedUnEvenLine	= "sqltabunevenline_high";
	var $cssSQLHighlightedEvenLine 		= "sqltabevenline_high";
	var $cssSQLHighlightedUnEvenCol		= "sqltabunevencol_high";
	var $cssSQLHighlightedEvenCol 		= "sqltabevencol_high";
	var $editableField = "";
	
	var $hasSum = false;
	var $sumField = "SUM";
	var $sumFieldValue = "Grand Total:";
	var $cssSQLSumLine = "sqltab_sumline";
	var $cssSQLSumLineHover = "sqltab_sumline_hover";
	
	
	var $highlightSpecifierValue		= 'Y';
	// Switch on/off buttons
	var $hasCopyButton		=false;
	var $hasCancelButton	=false;
	var $hasReopenButton	=false;
	var $hasFinishButton	=false;
	var $hasAddButton		=true;
	var $hasDeleteButton	=true;
	var $hasEditButton		=true;
	var $hasExtEditButton	=false;
	
	var $hasSubmitButton	=false;
	var $hasSubmitButton2	=false;
	var $submitOnClick		= "";
	var $hasSelectField		=false;
	var $hasCheckBoxes		=false;
	var $checkDefaultOn		= true;
	var $hasSelectFieldBeforeSubmit = false;
	
	var $selectField;
	
	var $checkboxTitle	= "Select";
	
	// Name for Submit button
	var	$submitButtonName	= "Submit";
	var	$submitButtonValue	= "Submit";
	var	$submitButtonName2	= "Submit 2";
	var	$submitButtonValue2	= "Submit 2";	
	
	// Sets additional info for onClicks
	var $onClickDeleteButton;
	var $onClickCopyButton;
	var $onClickEditButton;
	var $onClickAddButton;
	
	var $onClickEditButtonAction		="edit";
	var $onClickAddButtonAction			="add";
	var $onClickDeleteButtonAction		="delete";

	var $onClickDeleteButtonAdd	="";
	var $onClickCopyButtonAdd	="";
	var $onClickEditButtonAdd	="";
	var $onClickAddButtonAdd	="";	
	
	var $letDieOnEmptySet = false;
	
	
	// Switches on/off header. Useful for assembling tables
	var $hasHeader = true;
	
	var $formatLine=false;
	
	var $cssSQLEvenLine 		= "sqltabevenline";
	var $cssSQLUnEvenLine 		= "sqltabunevenline";
	
	var $cssSQLEvenLinehover 	= "sqltabevenlinehover";
	var $cssSQLUnEvenLinehover	= "sqltabunevenlinehover";
	
	var $cssSQLEvenCol 			= "sqltabevencol";
	var $cssSQLUnEvenCol 		= "sqltabunevencol";
	
	var $cssSQLEvenAddLine 		= "sqltabevenaddline";
	var $cssSQLUnEvenAddLine	= "sqltabunevenaddline";	
	var $cssSQLTabHead	 		= "sqltabhead";
	var $cssSQLTabSep 			= "sqltabsep";
	var $cssSQLTabHeadAction	= "sqltabheadaction";
	var $cssSQLList		 		= "sqllist";
	var $cssSQLTable	 		= "sqltable";
	var $cssSQLButton			= "sqlbutton";
	var $cssSQLDeleteButton		= "sqldeletebutton";
	var $cssSQLDevider			= "sqldevider";
	var $cssSQListElementCaption= "sqllistelementcaption";
	var $cssSelectField			= "sqlselectfield";
	
	var $colCount=0;
	var $lineCount=0;
	
	var $groupBy="";
	var $orderBy="";
	var $hasLineSeperator=false;
	var $sqlTabVAlign="middle";
	
	var $maxLinesOnPage=10000;
	var $pageContinueText = "Continued";
	var $page_num=0;
	var $hasForm = true;
	var $numRows=0;
	
	var $roundField = array();
	
	// Constructor
	function MySQLTable($locI,$qryI="",$nameI="default",$editPageI=""){
		$this->page = $locI;
		$this->formPage = $locI;
		$this->qry  = $qryI;
		$this->name = $nameI;
		if(!$editPageI)
			$this->editPage=$locI;
		else
			$this->editPage=$editPageI;
			
		$this->onClickDeleteButton 		= "window.location.href='$this->page?action=%s&record=%d'";
		$this->onClickCopyButton	 	= "window.location.href='$this->page?action=copy&record=%d'";
		$this->onClickCancelButton	 	= "window.location.href='$this->page?action=cancel&record=%d'";
		$this->onClickReopenButton	 	= "window.location.href='$this->page?action=reopen&record=%d'";
		$this->onClickFinishButton	 	= "window.location.href='$this->page?action=finish&record=%d'";
		$this->onClickEditButton	 	= "window.location.href='$this->editPage?action=%s&record=%d'";
		$this->onClickExtEditButton	 	= "window.location.href='$this->page?action=extedit&record=%d'";
		$this->onClickAddButton	 		= "window.location.href='$this->page?action=%s&record=-1'";		
	}
	
	function getSum($field,$decp=2,$del=false){
		if(is_array($this->collFieldVal[$field])){
			$result = number_format(array_sum($this->collFieldVal[$field]),$decp,'.','');
		}
		else{
			$result = number_format(0.0,$decp,'.','');
		}
		if($del)
			$this->collFieldVal[$field] = array();
		return $result;
	}
	
	function setHiddenField($field){
		$this->hiddenFields[$field]=1;
	}
	function query(){
		$qry = $this->qry.$this->groupBy.$this->orderBy;
		if(!$res = mysql_query($this->qry)){
			$message = "ERROR in Query: ".$this->query."ERROR: ".mysql_error();
			die($message);
		}
		if(mysql_num_rows($res)==0 && $this->letDieOnEmptySet){
			die("Result set empty");
		}
		return $res;		
	}
	
	function getFieldNumber($name,$res,$num_fields){
		for($i=0;$i<$num_fields;$i++){
			if($name==mysql_field_name($res,$i)){
				return $i;
			}
		}
		return -1;
	}
	function writeListElement($num_fields,$line,$res){
		$caption_num = $this->getFieldNumber($this->captionF,$res,$num_fields);
		if($this->showRec)
			$first=0;
		else
			$first=1;	
?>
		<tr>
			<td colspan="4" class="sqllistelementcaption"><?=$this->caption?> <?=$line[$caption_num]?></td>
		</tr>
		<tr>
			<td valign="top">
				<table>
<?
					for($i=$first;$i<$num_fields;$i++){
						$field_name = mysql_field_name($res,$i);
						$count++;
						if($field_name==$this->highlightField){
						?>
							<tr>
								<th width="150" class="<?=$this->cssSQLHighlightHead?>" ><?=$field_name?></th>
								<td width="240" class="<?=$this->cssSQLHighlightValue?>"><?=stripslashes($line[$i])?></td>
							</tr>
						<?						
						}
						else{
						?>
							<tr>
								<th width="150" class="<?=$this->cssSQLTabHead?>" ><?=$field_name?></th>
								<td width="240" class="<?=$this->cssSQLEvenLine?>"><?=stripslashes($line[$i])?></td>
							</tr>
						<?
						}
						if($field_name==$this->wrap1||$field_name==$this->wrap2){
							?>
			  </table>
		</td>
		<td  valign="top">
			<table>
							<?
						}
					}//for($i=$first;$i<$num_fields;$i++)
?>
		  </table>
		  </td>
		</tr>
<?		
	}
	function startList(){
		$this->actionButtonType=$this->actionButtonListType;
?>
		<table width="90%" class="<?=$cssSQLList?>">
<?		
	}
	function startTable(){
		$this->lineCount=0;
		$this->actionButtonType=$this->actionButtonTableType;
		if($this->hasForm){
?>
			<form name="table" id="table"  action="<?=$this->formPage?>" method="post">
<?
		}
?>		
			<table border="0"  cellpadding="0" class="<?=$this->cssSQLTable?>">
<?		
	}	
	function addHiddenInput($name,$value){
?>
		<input name="<?=$name?>" id="<?=$name?>"  type="hidden" value="<?=$value?>" />
<?	
	}
	
	function addInput($name,$value,$label,$width=10,$readonly=false){
?>
		<?=$label?> <input style="width:<?=$width?>em " readonly="<?=$readonly?>" name="<?=$name?>" id="<?=$name?>"  type="text" value="<?=$value?>" />
<?	
	}
	
	function addCheckbox($name,$value,$label,$readonly=false){
?>
		<?=$label?> <input readonly="<?=$readonly?>" name="<?=$name?>" id="<?=$name?>"  type="checkbox" <? if($value){ echo "checked"; }?> />
<?	
	}
	
	function stopTable(){
		$this->lineCount=0;
		$this->numRows=0;
		//$this->writeDevider($this->colCount);
?>
			</table>
<?
		if($this->hasForm){
?>			
			</form>
<?	
		}
	}
	function stopList(){
//		$this->writeDevider($num_fields+1);
?>
		</table>
<?	
	}
		
	function writeButton($class,$name,$value,$onClick,$record){
		if($this->actionButtonType=="button"){
?>	
			<input class="<?=$class?>" type="button" name="<?=$name?>" value="<?=$value?>" onClick="<?=$onClick?>+'&dest=<?=$this->name?>'" />
<?
		}
		else{
			$href_list = explode(';',$onClick);
			$href = substr($href_list[1],21,strlen($href_list[1])+1);
			$href = str_replace("'","",$href);
			
			$class_href = str_replace("button","href",$class);
			if($name=="delete")
				$class_href.="_delete";
				
			$onClick = str_replace("window.location.href=","",$onClick);
			$onClick = str_replace("'","",$onClick);
			if($name=="delete"){
?>
		
				<a href="<?=$onClick?>" class="<?=$class_href?>" onClick="return confirmDelete()" ><?=$name?></a>
<?		
			}
			else{
?>
				<a href="<?=$onClick?>" class="<?=$class_href?>" ><?=$name?></a>
<?			
			}
		}
	}
	function writeAddButton(){
		$onClickAddButton = sprintf($this->onClickAddButton,$this->onClickAddButtonAction);
		$onClickAddButton=$onClickAddButton.$this->onClickAddButtonAdd;
?>
			<tr>
				<td>
<?
					$this->writeButton($this->cssSQLButton,"add","Add",$onClickAddButton,"");
?>					
				</td>			
			</tr>
<?		
	}
	function writeSubmitButton($name,$value){
?>
			<input style="width:10em; " class="sqlbutton" type="submit" name="<?=$name?>" value="<?=$value?>" onClick="<?=$this->submitOnClick?>" />
<?		
	}	
	function writeDevider($colspan){
?>			
			<tr>
				<td colspan="<?=($colspan)?>"><hr class="sqldevider" /></td>
			</tr>
<?	
	}
	function writeListButtons($line,$res,$num_fields){
		$cancel_field = $this->getFieldNumber("Cancelled",$res,$num_fields);
		$onClickEditButton		= sprintf($this->onClickEditButton,$this->onClickEditButtonAction,$line[0]).$this->onClickEditButtonAdd;
		$onClickExtEditButton	= sprintf($this->onClickExtEditButton,$line[0]).$this->onClickExtEditButtonAdd;
		$onClickDeleteButton	= sprintf($this->onClickDeleteButton,$this->onClickDeleteButtonAction,$line[0]);
		$onClickDeleteButton.$this->onClickDeleteButtonAdd;
		$onClickCopyButton		= sprintf($this->onClickCopyButton,$line[0]).$this->onClickCopyButtonAdd;
		$onClickCancelButton	= sprintf($this->onClickCancelButton,$line[0]).$this->onClickCancelButtonAdd;
		$onClickReopenButton	= sprintf($this->onClickReopenButton,$line[0]).$this->onClickReopenButtonAdd;
		$onClickFinishButton	= sprintf($this->onClickFinishButton,$line[0]).$this->onClickFinishButtonAdd;
?>
			<tr>
				<td >
<?
				
						if($this->hasEditButton){	
							$this->writeButton($this->cssSQLButton,"edit","Edit",$onClickEditButton,$line[0]);
						}
						if($this->hasExtEditButton){	
							$this->writeButton($this->cssSQLButton,"editext","Ext.-Edit",$onClickExtEditButton,$line[0]);
						}						
						if($this->hasCopyButton){		
							$this->writeButton($this->cssSQLButton,"copy","Copy",$onClickCopyButton,$line[0]);						
						}
						if($this->hasCancelButton && $line[$cancel_field]=='N'){		
							$this->writeButton($this->cssSQLButton,"cancel","Cancel",$onClickCancelButton,$line[0]);						
						}
						if($this->hasReopenButton  && $line[$cancel_field]=='Y'){		
							$this->writeButton($this->cssSQLButton,"reopen","Reopen",$onClickCancelButton,$line[0]);						
						}						
						if($this->hasFinishButton){		
							$this->writeButton($this->cssSQLButton,"finish","Complete",$onClickFinishButton,$line[0]);						
						}												
						if($this->hasDeleteButton){
							$this->writeButton($this->cssSQLButton,"delete","Delete",$onClickDeleteButton,$line[0]);						
						}
?>						
				</td>
			</tr>
<?		
	}
	function writeList(){
		$res = $this->query();
		$num_fields = mysql_num_fields($res);
		
		$this->startList();

		if($this->hasAddButton){
			$this->writeAddButton();
		}
		if($this->hasSubmitButton){
			$this->writeSubmitButton($this->submitButtonName,$this->submitButtonValue);
		}
		if($this->hasSubmitButton2){
			$this->writeSubmitButton($this->submitButtonName2,$this->submitButtonValue2);
		}		

//		$this->writeDevider($num_fields+1);
		while($line=mysql_fetch_array($res)){
				$this->writeListElement($num_fields,$line,$res);
				$this->writeListButtons($line,$res,$num_fields);
		}//while($line=mysql_fetch_array($res))
		$this->stopList();
	}
	
	function writeTableHeader($res,$devider=true){

		$num_fields  = mysql_num_fields($res);
?>	
		<tr>
<?
		$address_loc = 1;
		if($this->showRec){
			$first=0;
		}
		else
			$first=1;
		for($i=$first;$i<$num_fields;$i++){
			$field_type = mysql_field_type($res,$i);
			$field_name = mysql_field_name($res,$i);
			
			if($field_type=='int'||
					$field_type=='double'||
					$field_type=='decimal'||
					$field_type=='real'||
					$field_type=='tinyint'){
				$is_num=true;
			}
			else
				$is_num=false;		
				
			if(mysql_field_name($res,$i)==$this->detailField){
				$address_loc=$i;
			}
			else if($this->highlightSpecifierField!=mysql_field_name($res,$i) && $this->hiddenFields[mysql_field_name($res,$i)]!=1){
				$field_name = mysql_field_name($res,$i);
				if($this->fieldNames[$field_name]){
					$fn = $this->fieldNames[$field_name];
				}
				else{
					$fn = $field_name;
				}
				if($is_num){
?>
					<th style="text-align:right " class="<?=$this->cssSQLTabHead?>" ><?=$fn?></th>
<?
				}
				else{
?>
					<th class="<?=$this->cssSQLTabHead?>" > <?=$fn?></th>
<?
				}				
			}
		}//for($i=$first;$i<$num_fields;$i++)
		if($this->hasEditButton||$this->hasDeleteButton||$this->hasCopyButton){
			if($this->formatLine){
?>
				<th width="10"  class="<?=$this->cssSQLTabHead?>" >Action</th>
<?
			}
			else{
?>
				<th width="10"  class="<?=$this->cssSQLTabHeadAction?>" >Action</th>
<?			
			}
		}
		if($this->hasCheckBoxes){
?>
			<th width="10"  class="<?=$this->cssSQLTabHead?>" ><?=$this->checkboxTitle?></th>
<?			
		}		
?>
		</tr>
<?	
		if($devider) $this->writeDevider($num_fields);
		if(mysql_num_rows($res)==0){
?>
			<tr>
				<td><?=$this->emptyMessage?></td>
			</tr>
<?			
		}
		return $address_loc;
	}
	
	function setSums($res,$line,$num_fields,$first){
		
		for($i=$first;$i<$num_fields;$i++){
			echo $field_type = mysql_field_type($res,$i);
			$field_name = mysql_field_name($res,$i);
			
			if($field_type=='int'||
					$field_type=='double'||
					$field_type=='decimal'||
					$field_type=='real'||
					$field_type=='tinyint'){
				$is_num=true;
			}
			else
				$is_num=false;		
			
			echo $this->sumFields[$field_name];
			if($this->sumFields[$field_name] && $is_num==true){
				echo $value = $line[$i];
				echo $this->sumValues[$field_name] += $value;
			}
			
			if($this->sumGroupField[$field_name]){
				$this->sumGroupPrevFieldValue = $this->sumGroupFieldValue;
				$this->sumGroupFieldValue = $value;
			}
		}
	}
	
	function writeSubSum($num_fields,$first){
		$this->startNewLine();
		
		for($i=$first;$i<$num_fields;$i++){
			$field_type = mysql_field_type($res,$i);
			$field_name = mysql_field_name($res,$i);
			
			if($field_type=='int'||
					$field_type=='double'||
					$field_type=='decimal'||
					$field_type=='real'||
					$field_type=='tinyint'){
				$is_num=true;
			}
			else
				$is_num=false;
			/*
			if($this->sumFields[$field_name]){;
				$this->addLine($this->sumValues[$field_name]);
			}
			*/
		}		
		
		$this->stopNewLine();
	}
	
	function writeTableElement($res,$line,$num_fields){


		if($this->formatLine){
			$sum_field_n = $this->getFieldNumber($this->sumField,$res,$num_fields);
			if(!$line[$sum_field_n] && $this->hasSum){
	?>
				<tr class="<?=$this->cssSQLSumLine?>" onmouseover="this.className='<?=$this->cssSQLSumLineHover?>'" onmouseout="this.className='<?=$this->cssSQLSumLine?>'">
	<?
			}
			else{
				if(fmod($this->lineCount,2)==0){
	?>
					<tr class="<?=$this->cssSQLEvenLine?>" onmouseover="this.className='<?=$this->cssSQLEvenLinehover?>'" onmouseout="this.className='<?=$this->cssSQLEvenLine?>'">
	<?
				}
				else{
	?>
					<tr class="<?=$this->cssSQLUnEvenLine?>" onmouseover="this.className='<?=$this->cssSQLUnEvenLinehover?>'" onmouseout="this.className='<?=$this->cssSQLUnEvenLine?>'">
	<?			
				}
			}
		}
		else
		{
?>
			<tr>
<?		
		}
		
			$high_field_n = $this->getFieldNumber($this->highlightSpecifierField,$res,$num_fields);
			$is_highlighted=false;
			if($line[$high_field_n]==$this->highlightSpecifierValue) $is_highlighted=true;
			if($this->showRec)
				$first=0;
			else
				$first=1;
			$colCounter=$first;

			//$this->setSums($res,$line,$num_fields,$first);
			
			//if($this->sumGroupPrevFieldValue[$field_name] != $this->sumGroupFieldValue[$field_name]){
				//$this->writeSubSum($num_fields,$first);
			//}
			
			for($i=$first;$i<$num_fields;$i++){
				$field_type = mysql_field_type($res,$i);
				$field_name = mysql_field_name($res,$i);
				
				if($field_type=='int'||
						$field_type=='double'||
						$field_type=='decimal'||
						$field_type=='real'||
						$field_type=='tinyint'){
					$is_num=true;
				}
				else
					$is_num=false;
					
				if($field_name!=$this->detailField){
					$value = $line[$i];
					
					if($this->roundField[$field_name]){
						$value = number_format($value,$this->roundField[$field_name]);
					} 
					
					if($this->collField[$field_name]){
						$this->collFieldVal[$field_name][]=$value;
					}
					
					if($field_name=="Cancelled"){
						if($value=='Y')
							$value="<img src='images/cancel.gif' alt='Cancelled' />";
						else
							$value='';
					}
					else if($field_name=="Confirmed"){
						if($value=='Y')
							$value="<img src='images/confirm.gif' alt='Confirmed' />";
						else
							$value='';					
					}
					else if($field_name==$this->sumGTField && $this->hasSum && !$line[$sum_field_n]){
						$value = $this->sumFieldValue;
					}
					
					if(fmod($colCounter,2)==0){			
						if(!$is_highlighted)
							$class = $this->cssSQLEvenCol;
						else
							$class = $this->cssSQLHighlightedEvenCol;
					}
					else{
						if(!$is_highlighted)
							$class = $this->cssSQLUnEvenCol;
						else
							$class = $this->cssSQLHighlightedUnEvenCol;
					}
					
					
					if($this->noRepValues[$field_name]==$value && $this->noRepFields[$field_name]){
						$colCounter++;
?>
						<td>&nbsp;</td>
<?
					}
					else{
						if($this->highlightField!=$field_name&&$this->editableField!=$field_name && $this->highlightSpecifierField!=$field_name&& $this->hiddenFields[$field_name]!=1){
							$colCounter++;
						?>
							<td valign="<?=$this->sqlTabVAlign?>" 
								<? 
								if($this->colWidth[$field_name]){ 
								?> 
									width="<?=$this->colWidth[$field_name]?>" 
								<? 
								}
								if($is_num) {
								?> 
									align="right" 
								<? 
								}
								if(!$this->formatLine){ 
								?>
									class="<?=$class?>" 
								<? 
								}
								?> 
							> <?=stripslashes($value)?></td>
						<?
						}
						else if($this->highlightField!=$field_name&&$this->highlightSpecifierField!=$field_name&& $this->hiddenFields[$field_name]!=1){
							$colCounter++;
						?>
							<td  valign="<?=$this->sqlTabVAlign?>"
							<? 
							if($this->colWidth[$field_name]){ 
							?> 
								width="<?=$this->colWidth[$field_name]?>" 
							<? 
							} 
							if($is_num) { 
							?> 
								align="right" 
							<? 
							} 
							?> 
							class="<?=$this->cssSelectField?>" >
								<input <? if($is_num) { ?> style="text-align:right; width:5em; "<? }?>type="text" name="efield[<?=$line[0]?>]" value="<?=stripslashes($value)?>" />
							</td>
						<?					
						}
						else if($this->highlightField==$field_name&& $this->hiddenFields[$field_name]!=1){
							$colCounter++;
						?>
							<td  valign="<?=$this->sqlTabVAlign?>" <? if($this->colWidth[$field_name]){ ?>width="<?=$this->colWidth[$field_name]?>" <? }?>  <? if($is_num) {?> align="right" <? }?> class="<?=$cssSQLHighlightValue?>" ><?=stripslashes($value)?></td>
						<?					
						}
					}//if($this->noRepFields[$field_name]==$value)
					if($this->noRepFields[$field_name]){
						$this->noRepValues[$field_name] = $value;
					}
				}
			}
			
			$this->lineCount++;
			$this->writeTableButtons($line,$res,$num_fields);
			if($this->detailField!=""){
?>
				<td  valign="<?=$this->sqlTabVAlign?>"><a href="<?=$this->detailAddress?>?action=edit&dest=op&record=<?=$line[0]?>">Details</a></td>
<?
			}

			
?>
			<!--<td><?=$this->lineCount?>/<?=$this->numRows?></td>-->
		</tr>
<?
		if($this->hasLineSeperator){
?>
			<th class="<?=$this->cssSQLTabSep?>" colspan="<?=$colCounter+1?>">&nbsp;</th>
<?			
	
		}
		$colCounter=0;
		if($this->lineCount==$this->maxLinesOnPage && $this->lineCount<$this->numRows){
			$this->stopTable();
?>
			
			<div class="pagebreak_right"><? if($this->page_num){ ?><strong>Page <?=$this->page_num?></strong> <? }?></div>
			<h3><?=$this->pageContinueText?></h3>
			
			
<?			
			$this->startTable();
			$this->writeTableHeader($res);
		}		
	}
	function writeTableButtons($line,$res,$num_fields){
		$cancel_field = $this->getFieldNumber("Cancelled",$res,$num_fields);
		$onClickEditButton		= sprintf($this->onClickEditButton,$this->onClickEditButtonAction,$line[0]).$this->onClickEditButtonAdd;
		$onClickExtEditButton	= sprintf($this->onClickExtEditButton,$line[0]).$this->onClickExtEditButtonAdd;
		//$onClickDeleteButton	= sprintf($this->onClickDeleteButton,$this->onClickDeleteButtonAction,$line[0],$line[0]);

		//$onClickDeleteButton		= sprintf($this->onClickDeleteButton,$line[0]).$this->onClickDeleteButtonAdd;
		$onClickDeleteButton		= sprintf($this->onClickDeleteButton,$this->onClickDeleteButtonAction,$line[0]).$this->onClickDeleteButtonAdd;
		$onClickCopyButton		= sprintf($this->onClickCopyButton,$line[0]).$this->onClickCopyButtonAdd;
		$onClickCancelButton	= sprintf($this->onClickCancelButton,$line[0]).$this->onClickCancelButtonAdd;
		$onClickReopenButton	= sprintf($this->onClickReopenButton,$line[0]).$this->onClickReopenButtonAdd;
		$onClickFinishButton	= sprintf($this->onClickFinishButton,$line[0]).$this->onClickFinishButtonAdd;
		
		if($this->hasEditButton||$this->hasExtEditButton||$this->hasCopyButton||$this->hasCancelButton||$this->hasReopenButton||$this->hasFinishButton||$this->hasDeleteButton){
?>
			<td align="center">
<?	
							
				if($this->hasEditButton){	
					$this->writeButton($this->cssSQLButton,"edit","Edit",$onClickEditButton,$line[0]);
				}
				if($this->hasExtEditButton){	
					$this->writeButton($this->cssSQLButton,"edit","Ext.-Edit",$onClickExtEditButton,$line[0]);
				}						
				if($this->hasCopyButton){		
					$this->writeButton($this->cssSQLButton,"copy","Copy",$onClickCopyButton,$line[0]);						
				}
				if($this->hasCancelButton && $line[$cancel_field]!='Y'){		
					$this->writeButton($this->cssSQLButton,"cancel","Cancel",$onClickCancelButton,$line[0]);						
				}
				if($this->hasReopenButton  && $line[$cancel_field]=='Y'){		
					$this->writeButton($this->cssSQLButton,"reopen","Reopen",$onClickReopenButton,$line[0]);						
				}					
				?>
					<!--<br />-->
				<?	
				if($this->hasFinishButton){		
					$this->writeButton($this->cssSQLButton,"finish","Complete",$onClickFinishButton,$line[0]);						
				}												
				if($this->hasDeleteButton){
					$this->writeButton($this->cssSQLButton,"delete","Delete",$onClickDeleteButton,$line[0]);						
				}
?>				
			</td>
<?	
		}
		if($this->hasCheckBoxes){
?>
			<td align="center"><input <? if($this->checkDefaultOn==true){?> checked <? }?> class="<?=$this->cssSelectField?>" type="checkbox" id="check[<?=$line[0]?>]" name="check[<?=$line[0]?>]" value='1' /></td>
<?			
		}
	}
	
	function startNewHeaderLine(){
?>
		<tr>
<?		
	}
	function startNewLine(){
		if(!$this->showRec) $this->colCount=1;	
		else $this->colCount=0;
		
		if($this->formatLine){
			$sum_field_n = $this->getFieldNumber($this->sumField,$res,$num_fields);
			if(fmod($this->lineCount,2)==0){
?>
				<tr class="<?=$this->cssSQLEvenLine?>" onmouseover="this.className='<?=$this->cssSQLEvenLinehover?>'" onmouseout="this.className='<?=$this->cssSQLEvenLine?>'">
<?
			}
			else{
?>
				<tr class="<?=$this->cssSQLUnEvenLine?>" onmouseover="this.className='<?=$this->cssSQLUnEvenLinehover?>'" onmouseout="this.className='<?=$this->cssSQLUnEvenLine?>'">
<?			
			}
		}
		else
		{
?>
			<tr>
<?		
		}		
		$this->lineCount++;
	}
	function addLines($value,$count){
		if($this->showRec) $count++;
		for($i=0;$i<$count;$i++){
			$this->addLine($value);
		}
	}
		
	function addLine($value,$colspan=1){
		$this->colCount+=$colspan-1;	
		if($value==""){
?>	
			<td colspan="<?=$colspan?>" style=""><?=$value?></td>
<?				
		}
		else if(fmod($this->colCount,2)==0){
?>	
			<td colspan="<?=$colspan?>" class="<?=$this->cssSQLEvenAddLine?>"><?=$value?></td>
<?		
		}
		else{
?>	
			<td colspan="<?=$colspan?>" class="<?=$this->cssSQLUnEvenAddLine?>"><?=$value?></td>
<?			
		}
		$this->colCount++;
	}
	
	function addLineWithStyle($value,$style,$colspan=1){
		$this->colCount+=$colspan-1;	
		if($value==""){
?>	
			<td colspan="<?=$colspan?>" style=""><?=$value?></td>
<?				
		}
		else{
?>	
			<td colspan="<?=$colspan?>" class="<?=$style?>"><?=$value?></td>
<?			
		}
		$this->colCount++;
	}
	
	function addHeaderLine($value,$is_num=false){
		
		if($is_num){
?>
			<th style="text-align:right " class="<?=$this->cssSQLTabHead?>" ><?=$value?></th>
<?
		}
		else{
?>
			<th class="<?=$this->cssSQLTabHead?>" > <?=$value?></th>
<?
		}				
	}
		
	function stopNewLine(){
		echo "</tr>";
		$this->colCount=0;
	}
	function writeSQLTableElement($qry,$writeh=true){
		$qry = $qry.$this->groupBy.$this->orderBy;
		
		$res = query($qry);
		$num_fields  = mysql_num_fields($res);
		$this->numRows+=mysql_num_rows($res);
		$address_loc = 1;
		if($writeh)
			$this->writeTableHeader($res);
		while($line = mysql_fetch_array($res,MYSQL_BOTH)){	
			$this->writeTableElement($res,$line,$num_fields);
		}//while($line = mysql_fetch_array($res,MYSQL_BOTH))
	}
	function writeTable(){
		$res = $this->query();
		$this->writeTableWithRes($res);
	}
	function writeTableWithRes($res,$header=true,$devider=true){
		$num_fields  = mysql_num_fields($res);
		$this->numRows+=mysql_num_rows($res);
		
		$address_loc = 1;
?>
		<tr>
			<td>
<?		
		if($this->hasAddButton){
			$this->writeAddButton();
		}
?>
			</td>
			<td>
<?		if($this->hasSelectFieldBeforeSubmit){
			$this->selectField->startSelect();
			$this->selectField->writeSelect();
			$this->selectField->stopSelect();
		}		
		
		if($this->hasSubmitButton){
			$this->writeSubmitButton($this->submitButtonName,$this->submitButtonValue);
		}
?>
			</td>
			<td>
<?		
		if($this->hasSubmitButton2){
			$this->writeSubmitButton($this->submitButtonName2,$this->submitButtonValue2);
		}		
?>
			</td>
			<td>
<?		
		if($this->hasSelectField){
			$this->selectField->startSelect();
			$this->selectField->writeSelect();
			$this->selectField->stopSelect();
		}		
?>
			</td>			
		</tr>
<?		
		if($devider) $this->writeDevider($num_fields+1);
		if($header)
			$this->writeTableHeader($res);
		//$this->writeDevider($num_fields+1);
		
		while($line = mysql_fetch_array($res,MYSQL_NUM)){	
			$this->writeTableElement($res,$line,$num_fields);
		}//while($line = mysql_fetch_array($res,MYSQL_BOTH))
	}	
}

?>