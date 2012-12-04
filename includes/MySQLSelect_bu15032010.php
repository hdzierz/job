<script language="javascript">
	function urldecode(form){
		formObj = document.getElementById(form);
		for(i=0;i<formObj.length;i++){
			formObj[i].value = escape(formObj[i].value);
		}
	}
</script>

<?
class MySQLSelect{
	var $actionPage;
	var $SQLTable;
	var $formName;
	var $onChangeAction="";
	var $SQLField;
	var $SQLIDField;
	var $optionIsVal;
	var $selectOnChange;
	var $selectName;
	var $cssSelect="sql_select";
	var $SQLWhere;
	var $orderField;
	var $sortOrder="ASC";
	var $multiple="";
	var $selectSize  	= 1;
	var $selectWidth 	= 10;
	var $optionDefText	="Please select";
	var $optionDefValue	="0";
	var $optionDelimiter="-------------";
	var $isDisabled  = false;
	
	function MySQLSelect($SQLFieldI,$SQLIDFieldI,$SQLTableI,$actionPageI,$formNameI="narrow",$selectNameI=""){
		$this->formName  		= $formNameI;
		if($selectNameI=="") $this->selectName = $SQLFieldI;
		else $this->selectName = $selectNameI;
		$this->SQLField			= $SQLFieldI;
		$this->orderField		= $SQLFieldI;
		$this->SQLIDField   	= $SQLIDFieldI;
		$this->actionPage		= $actionPageI;
		$this->SQLTable			= $SQLTableI;		
		$this->SQLWhere			= " WHERE $this->SQLField IS NOT NULL ";
		if(strpos($actionPageI,"?"))
			$this->selectOnChange	= "document.location.href='$this->actionPage%s&$this->selectName='+document.$this->formName.$this->selectName.value";
		else
			$this->selectOnChange	= "document.location.href='$this->actionPage?action=%s&$this->selectName='+document.$this->formName.$this->selectName.value";
	}
	
	function setOptionIsVal($optionIsValI){
		$this->optionIsVal = $optionIsValI;
	}
	function addVar($name,$value){
		if(strpos($value,'axx123y')){
			$value = str_replace('axx123y','&',$value);
		}
		else
			$value =$value;		
		if($name){
			$this->SQLWhere    		.= $this->where."AND $name='$value' ";
			$this->selectOnChange  	.= "+'&$name='+document.$this->formName.$name.value";
		}
	}

	function addOnChange($name){
		if($name){
			$this->selectOnChange  	.= "+'&$name='+document.$this->formName.$name.value";
		}
	}
	
	function addOnChangeChecked($name){
		if($name){
			$this->selectOnChange  	.= "+'&$name='+document.$this->formName.$name.checked";
		}
	}

	
	function addOnSimpleChange($name,$value){
		if($name){
			$this->selectOnChange  	.= "+'&$name=$value'";
		}
	}	
	
	function addSQLWhere($name,$value){
		if(strpos($value,'axx123y')){
			$value = str_replace('axx123y','&',$value);
		}
		else
			$value =$value;	
		if($name){
			$this->SQLWhere    		.= $this->where."AND $name='$value' ";
		}
	}
	
	function addSQLWhereNot($name,$value){
		if(strpos($value,'axx123y')){
			$value = str_replace('axx123y','&',$value);
		}
		else
			$value =$value;	
		if($name){
			$this->SQLWhere    		.= $this->where."AND $name<>'$value' ";
		}
	}	
	
	function debug(){
		echo "ONCHANGE: ".$this->selectOnChange."<br /> WHERE: ".$this->SQLWhere."<br />";
	}
	
	function startSelect(){		
		$this->selectOnChange = sprintf($this->selectOnChange,$this->onChangeAction);
?>
		
		<select <?=$this->multiple?>  <? if($this->isDisabled){?> disabled <? }?> class="<?=$this->cssSelect?>" style="width:<?=$this->selectWidth?>em "  size="<?=$this->selectSize?>" name="<?=$this->selectName?>" onChange="<?=$this->selectOnChange?>" >
<?			
	}
	function stopSelect(){
?>
		</select>
		
<?		
	}
	function writeSelectSQL($qry){
		$res = query($qry);
		if($this->optionDefText){
?>
			<option <? if($this->optionDefValue==$this->optionIsVal){?> selected <? }?> value="<?=$this->optionDefValue?>"><?=$this->optionDefText?></option>
			<option value="-75"><?=$this->optionDelimiter?></option>
<?
			}
?>
<?
			while($obj = mysql_fetch_object($res)){
				if(strpos($obj->id,'&')){
					$id = str_replace('&','axx123y',$obj->id);
				}
				else
					$id =$obj->id;
?>
				<option <? if($id==$this->optionIsVal){?> selected <? }?> value="<?=$id?>"><?=$obj->name?></option>
<?
			}

	}
	function addOption($value,$text,$valueI=false){
		if($valueI===false){
			$valueI = $this->optionIsVal;
		}
?>
				<option <? if($value==$valueI){?> selected <? }?> value="<?=$value?>"><?=$text?></option>
<?		
	}
	function writeSelect(){
		echo $qry = "SELECT DISTINCT $this->SQLField AS name, 
								$this->SQLIDField AS id
				FROM $this->SQLTable
				$this->SQLWhere
				ORDER BY $this->orderField $this->sortOrder";
		$this->writeSelectSQL($qry);
	}
}
?>