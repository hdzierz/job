<?


// Similar to MySQLTable. Writes the result to a PDF file
class MySQLPDFTable extends FPDF
{
	var $mysql=null;
	var $hasDivider=true;
	var $footer = false;
	
	var $collField = array();
	var $collFieldVal = array();
	
	var $norepField = array();
	var $norepFieldVal = array();
	
	var $maxNumRows = 1000000;
	var $numRows = 0;
	var $addLineText = array();
	
	var $hasfieldAliases = false;
	
	var $maxchar = 30;
	
	var $fontSize=6;
	
	var $emph = array();
	
	var $border = 0;
	
	function MySQLPDFTable($mysql,$orientation='p'){
		parent::FPDF($orientation,"mm","A4");
		
		$this->mysql = $mysql;
	}
	
	
	function getMaxW(){
		
	}
	
	// One of the majot problems using this class is when text has to be wrapped in a cell. There is a function
	// called MultiCell, but that makes things even more complicated. Thus, we have to wrap the text manually.
	
	function WordWrap(&$text, $maxwidth,$sep='::')
	{
	    $text = trim($text);
	    if ($text==='')
	        return 0;
	    $space = $this->GetStringWidth(' ');
	    $lines = explode("\n", $text);
	    $text = '';
	    $count = 0;
	
	    foreach ($lines as $line)
	    {
	        $words = preg_split('/ +/', $line);
	        $width = 0;
	
	        foreach ($words as $word)
	        {
	            $wordwidth = $this->GetStringWidth($word);
	            if ($width + $wordwidth <= $maxwidth)
	            {
	                $width += $wordwidth + $space;
	                $text .= $word.' ';
	            }
	            else
	            {
	                $width = $wordwidth + $space;
	                $text = rtrim($text).$sep.$word.' ';
	                $count++;
	            }
	        }
	        $text = rtrim($text).$sep;
	        $count++;
	    }
	    $text = rtrim($text);
	    return $count;
	}
	
	// Return the sum of a collection field. Same as in MySQLTable
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
	
	// Resets the collection fields
	function refreshCollFields(){
		foreach($this->collFieldVal as $field){
			$field = array();
		}
	}
	
	// Adds a new page to the PDF file
	function PageBreak(){
		$this->AddPage();
	}
	
	//Load data from the database
	function LoadData($qry)
	{
		$data = $this->mysql->get_set($qry);
	    return $data;
	}
	
	
	
	//Write the header of the table
	function WriteHeader($header,$width){
		//Column widths
		
	    $w=$width;
		
		$this->width = $width;
	    //Header
		foreach($header as $key=>$alias){
	    //for($i=0;$i<count($header);$i++){
	    	//$this->SetFillColor(180,180,180);
			$this->SetFillColor(180,180,180);
	    	$this->SetTextColor(255,255,255);
			
			if($this->hasFieldAliases){
				
					$this->Cell($w[$key],7,$alias,0,0,'C',1);
			}
			else{
				$this->Cell($w[$alias],7,$alias,0,0,'C',1);
			}

			
	        //$this->Cell($w[$header[$i]],7,$header[$i],0,0,'C',1);
	    }
	    $this->Ln();
	    $this->SetTextColor(0,0,0);
	}
	
	// Write an extra line
	function StartLine($font_size,$r=255,$g=255,$b=255,$style='B'){
		$this->SetFont('Helvetica',$style,$font_size);
		$this->SetFillColor($r,$g,$b);
	}
	
	// Write the extra line cell
	function WriteLine($txt,$just='L',$h=6,$maxw=200){
		//$this->addLineText = $this->WordWrap($txt, $maxw);
		$this->Cell($maxw,$h,$txt,'',0,$just,1);
		
	}
	
	// Finish the extra line
	function StopLine(){
		$this->Ln();
		
		/*if(count($this->addLineText)>1){
			foreach($this->addLineText as $txt){
				$this->Cell($maxw,$h,$txt,'',0,$just,1);
			}
		}
		$this->Ln();*/
		$this->SetFont('Helvetica','B',$this->fontSize);
	}
	
	// write a blank line
	function writeBlankLine($just='L',$h=6,$maxw=200){
		$this->StartLine($h);
			$this->WriteLine("",$just,$h,$maxw);
		$this->StopLine();
	}
	
	// Write the actual table cells
	function WriteTable($header,$data,$width,$row_h=7,$endborder=0, $comp_even=false)
	{
	    //Column widths
	    $w=$width;
	    
	    $maxw = array_sum($w);
	    
	    //Data
	   
	  	$rcount=0;
	  	$old_row = false;
	  	$is_even=1;
	    foreach($data as $row)
	    {
			$rcount++;
			
			$is_even=0;
			if($rcount%2==0){
				$is_even=1;
			}
			
			$this->SetFillColor(255,255,255);
				
	    	if($this->numRows>$this->maxNumRows){
				$this->PageBreak();
				$this->numRows=0;
				$this->WriteHeader($header,$width);
			}
	    	$buf = array();
	    	foreach($header as $key=>$value){
				if($this->hasFieldAliases){
					$head=$key;
				}
				else{
					$head=$value;
				}
				//echo $head."<br />";
				
				
				$is_same_str = false;
				if($comp_even){
					
					if(trim($row[$head])==($this->norepFieldVal[$is_even][$head])){
						//echo $row[$head].'/'.$this->norepFieldVal[$is_even][$head]."<br />";
						$is_same_str = true;	
					}
				}
				else{
					if(trim($row[$head])==trim($this->norepFieldVal[$head])){
						$is_same_str = true;	
					}
				}
				
				$this->norepFieldVal[$head] = $row[$head];
				$this->norepFieldVal[$is_even][$head] = $row[$head]; 
				
				$this->WordWrap($row[$head],$this->maxchar);
				$text = explode("::",$row[$head]);
				
				if(is_numeric($text[0])) $just='R';
				else $just='L';
				// Collecting data 
				if($this->collField[$head]){
					$this->collFieldVal[$head][]=$text[0];
				}
	    		
				for($i=1;$i<count($text);$i++){
					if(trim($text[$i])<>'')
						$buf[$i][$head] = $text[$i];
				}
				if (($head=="Weight" || $head =="Date Deliv.") && $endborder && $rcount%2!=0) $border=1;
				else $border=$this->border;
				if($this->norepField[$head]){
					if(!$is_same_str)
						$this->Cell($w[$head],$row_h,$text[0],0,0,$just,1);
					else
						$this->Cell($w[$head],$row_h,'',$border,0,$just,1);
				}
				else{
					$this->Cell($w[$head],$row_h,$text[0],$border,0,$just,1);
				}
				
	    	}
	    	$this->Ln(0.2);
	    	$this->Ln();
			

	    	if(count($buf)>1){
				$start=true;
				$count = 0;
				foreach($buf as $b){
					$start=false;
					foreach($header as $head){
						if(trim($w[$head])){
							$this->Cell(trim($w[$head]),$row_h,$b[$head],'');
						}
						$count++;
					}
					$this->Ln();
				}
	    	}
			
	    	
	    	//if($this->hasDivider){
	    	//	$this->writeDivider($maxw);
	    	//}
	    	$old_row = $row;
	    }
	}
	
	// Write a divider
	function writeDivider($maxw){
		$this->SetFillColor(200,200,200);
		$this->Cell($maxw,0.3,"",0,0,'C',1);
	
		$this->Ln();
		$this->SetFillColor(255,255,255);
	}
	
	// Page footer
	function Footer()
	{
		if($this->footer){
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Page number
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}
	
	
}


?>
