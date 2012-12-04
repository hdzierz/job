<?

class MySQLExport{
	// Attributes
	var $qry;
	var $fileHandle;
	var $showRec=1;
	
	var $colWidth = array();
	var $fieldNames = array();
	var $hiddenFields = array();
	
	// Attributes for function writeList. Will wrap output at the specified fields
	var $wrap1;
	var $wrap2;
	var $wrap3;
	var $wrap4;
	
	var $catpionF = "";
	
	// Attributes for details. If set and field available teh class will create a link to the
	// respective address site
	var $detailField	="";
	var $detailAddress	="";
	
	// Switches on/off header. Useful for assembling tables
	var $hasHeader = true;
	
	var $cssSQLEvenLine 		= "sqltabevenline";
	var $cssSQLUnEvenLine 		= "sqltabunevenline";
	var $cssSQLEvenAddLine 		= "sqltabevenaddline";
	var $cssSQLUnEvenAddLine	= "sqltabunevenaddline";	
	var $cssSQLTabHead	 		= "sqltabhead";
	var $cssSQLTabHeadAction	= "sqltabheadaction";
	var $cssSQLTable	 		= "sqltable";
	var $cssSQLDevider			= "sqldevider";
	var $cssSQLSumLine			= "sqlsumline";
	var $cssSQListElementCaption= "sqllistelementcaption";
	
	var $colCount=0;
	
	var $mode;
	
	// Constructor
	function MySQLExport($fnI,$qryI,$mode = "HTML"){
		$this->fileHandle = fopen($fnI,'w+');
		$this->qry  	  = $qryI;
		$this->mode = $mode;
		switch($mode){
			case "HTML": 
				$this->table_start = "<table>"; 
				$this->table_end = "</table>"; 
				$this->row_start = "<tr>"; 
				$this->row_end = "</tr>"; 
				$this->value_start = "<td>"; 
				$this->value_end = "</td>"; 
				$this->header_value_start = "<th>"; 
				$this->header_value_end = "</th>"; 
			break;
			case "CSV":
				$this->table_start = ""; 
				$this->table_end = ""; 
				$this->row_start = ""; 
				$this->row_end = "\n"; 
				$this->value_start = "\""; 
				$this->value_end = "\","; 
				$this->header_value_start = "\""; 
				$this->header_value_end = "\","; 
			break;
		}
	}
	function query(){
		if(!$res = mysql_query($this->qry)){
			$message = "ERROR in Query: ".$this->query."ERROR: ".mysql_error();
			die($message);
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

	function startTable(){
		fwrite($this->fileHandle,$this->table_start);
	}	
	function stopTable(){
		//$this->writeDevider($this->colCount);
		fwrite($this->fileHandle,$this->table_end );
	}
		
	function writeDevider($colspan){
		fwrite($this->fileHandle,"<tr><td colspan='($colspan)'><hr class='sqldevider' /></td></tr>");
	}
	
	function writeTableHeader($res){
		$num_fields  = mysql_num_fields($res);
		fwrite($this->fileHandle,$this->row_start);
		$address_loc = 1;
		if($this->showRec){
			$first=0;
		}
		else
			$first=1;
		for($i=$first;$i<$num_fields;$i++){
			$field_name = mysql_field_name($res,$i);
			if($this->fieldNames[$field_name]){
				$fn = $this->fieldNames[$field_name];
			}
			else{
				$fn = $field_name;
			}		
			if(mysql_field_name($res,$i)==$this->detailField){
				$address_loc=$i;
			}
			else if($this->hiddenFields[$field_name]==1){
				fwrite($this->fileHandle,"");
			}
			else{
				fwrite($this->fileHandle,$this->header_value_start.$fn.$this->header_value_end);
			}
		}//for($i=$first;$i<$num_fields;$i++)
		fwrite($this->fileHandle,$this->row_end);
		return $address_loc;
	}
	function writeTableElement($res,$line,$num_fields){
		fwrite($this->fileHandle,$this->row_start);
			if($this->showRec)
				$first=0;
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
				
				$value = $line[$i];
				
				if(fmod($i,2)==0){			
					$class = $this->cssSQLEvenLine;
				}
				else{
					$class = $this->cssSQLUnEvenLine;
				}
				
				if($this->colWidth[$field_name]){ 
					$fwidth="width='".$this->colWidth[$field_name]."'" ;
				}
				if($this->hiddenFields[$field_name]==1){
					fwrite($this->fileHandle,"");
				}
				else{
					if($is_num) {
						switch($this->mode){
							case "HTML": fwrite($this->fileHandle,"<td  $fwidth align='right'>$value</td>"); break;
							case "CSV": fwrite($this->fileHandle,"$value,"); break;
						}
						
					}
					else{
						fwrite($this->fileHandle,$this->value_start.$value.$this->value_end);
					}
				}
				//fwrite($this->fileHandle,"</td>");
			}
			fwrite($this->fileHandle,$this->row_end);
	}
	function startNewLine(){
		if(!$this->showRec) $this->colCount=1;	
		else $this->colCount=0;
		fwrite($this->fileHandle,$this->row_start);
	}
	function addLines($value,$count){
		if($this->showRec) $count++;
		for($i=0;$i<$count;$i++){
			$this->addLine($value);
		}
	}
		
	function addLine($value){
		if($value==""){
			switch($this->mode){
				case "HTML": fwrite($this->fileHandle,"<td style=''>$value</td>"); break;
				case "CSV": fwrite($this->fileHandle,"$value,"); break;
			}
			
		}
		else if(fmod($this->colCount,2)==0){
			switch($this->mode){
				case "HTML": fwrite($this->fileHandle,"<td align='right'>$value</td>");break;
				case "CSV": fwrite($this->fileHandle,"$value,"); break;
			}
			
		}
		else{
			switch($this->mode){
				case "HTML": fwrite($this->fileHandle,"<td align='right'>$value</td>"); break;
				case "CSV": fwrite($this->fileHandle,"$value,"); break;
			}
			
		}
		$this->colCount++;
	}
	
	function addBoldLine($value,$colspan){
		if($value==""){
			switch($this->mode){
				case "HTML": fwrite($this->fileHandle,"<td colspan='$colspan'></td>"); break;
				case "CSV": fwrite($this->fileHandle,"$value,"); break;
			}
			
		}
		else{
			switch($this->mode){
				case "HTML": fwrite($this->fileHandle,"<td colspan='$colspan'><b>$value</b></td>"); break;
				case "CSV": fwrite($this->fileHandle,"$value,"); break;
			}
			
		}
		$this->colCount++;
	}
		
	function addLineWithStyle($value,$style,$colspan=1){
		$this->addBoldLine($value,$colspan);
	}
	function stopNewLine(){
		fwrite($this->fileHandle,$this->row_end);
		$this->colCount=0;
	}
	function writeSQLTableElement($qry){
		$res = query($qry);
		$num_fields  = mysql_num_fields($res);
		$address_loc = 1;
		$this->writeTableHeader($res);
		while($line = mysql_fetch_array($res,MYSQL_BOTH)){	
			$this->writeTableElement($res,$line,$num_fields);
		}//while($line = mysql_fetch_array($res,MYSQL_BOTH))
	}
	function writeTable(){
		$res = $this->query();
		$this->writeTableWithRes($res);
	}
	function writeTableWithRes($res){
		$num_fields  = mysql_num_fields($res);
		$address_loc = 1;
		
		if($this->hasAddButton){
			$this->writeAddButton();
			$this->writeDevider($num_fields+1);
		}
		if($this->hasSubmitButton){
			$this->writeSubmitButton();
			$this->writeDevider($num_fields+1);
		}
		$this->writeTableHeader($res);
		//$this->writeDevider($num_fields+1);
		
		while($line = mysql_fetch_array($res,MYSQL_BOTH)){	
			$this->writeTableElement($res,$line,$num_fields);
		}//while($line = mysql_fetch_array($res,MYSQL_BOTH))
	}	
	function addHiddenInput($name,$value){}
}

?>