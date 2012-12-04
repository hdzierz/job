<?php
include_once('fpdf.php');

class BibleByRegion_PDF extends FPDF
{
	var $ALIGN = '';
	
	var $widths;
	var $aligns;
	var $border = false;
	var $currentDate;
	var $currentDist;
	var $currentPhone;
	var $currentMobile;
	var $currentRegion;
	var $currentDist_out;
	var $currentHeight;
	var $phoneColumn;
	var $columnHeadings;
		
	function PDF($orientation='L',$unit='mm',$format='A4')
	{
		//Call parent constructor
		$this->FPDF($orientation,$unit,$format);
							
	}
	
	function Header()
	{	
		// Cell
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(141,141,141);
		$this->SetDrawColor(22,22,22);
		$this->SetFont('Arial','B',18);
		
		$this->MultiCell(285,10,$this->currentRegion,'LTBR','R',1);
		
				//column headings
		$this->SetFont('Arial','B',10);
		for($i=0;$i<count($this->columnHeadings);$i++)
	    {
	    	if ($i==0) $border="LTB";
	    	elseif ($i==count($this->columnHeadings)-1) $border="RTB";
	    	else $border="TB";
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        $this->Cell($w,5,$this->columnHeadings[$i],$border,0,$a,1);
	    }
	    $this->Ln();
		$this->SetTextColor(0,0,0);
		
	}
	
	function Footer()
	{
		//Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	
	}
	
	function Row($data)
	{
		
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++){
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    }
	    $h=4*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	  
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        if (!$old_x) $old_x=$x;
	        if (!$old_y) $old_y=$y; 
	        //Draw the border
	        //if($this->border) $this->Rect($x,$y,$w,$h);
	        if ($this->border) $this->Line($old_x,$old_y,$x,$y);
	        $old_x=$x;
	        $old_y=$y;   
	        //Print the text
	        $this->MultiCell($w,4,$data[$i],0,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->currentHeight = $h;
	    $this->Ln($h);
	}
	
	function WriteHTML($html)
	{
	    //HTML parser
	    $html=str_replace("\n",' ',$html);
	    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	    foreach($a as $i=>$e)
	    {
	        if($i%2==0)
	        {
	            //Text
	            if($this->HREF)
	                $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN == 'center')
                    $this->Cell(0,5,$e,0,1,'C');
                else
                    $this->Write(5,$e);
	        }
	        else
	        {
	            //Tag
	            if($e{0}=='/')
	                $this->CloseTag(strtoupper(substr($e,1)));
	            else
	            {
	                //Extract attributes
	                $a2=explode(' ',$e);
	                $tag=strtoupper(array_shift($a2));
	                $prop=array();
	                foreach($a2 as $v)
	                {
	                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
	                        $prop[strtoupper($a3[1])]=$a3[2];
	                }
	                $this->OpenTag($tag,$prop);
	            }
	        }
	    }
	}

	function OpenTag($tag,$attr)
	{
       
	    //Opening tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( $prop['WIDTH'] != '' )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }		
    }
	
	function CloseTag($tag)
	{
        //Closing tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

	function CheckTag($html)
	{
		//get string token
		$string = html_entity_decode($html);		
		 $a=preg_split('/<(.*)>/U',$string,-1,PREG_SPLIT_DELIM_CAPTURE);
		 $strArray = array();
		 $col = 0;
		 $index = 1;
		// print_pre($a); //exit;
		  foreach($a as $i=>$e)
		 {		
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else				
					if(!empty($e) && str_word_count($e)){
						if($add_ol) $e = $index++ . '. '.$e;
						if($add_ul) $e = "&bull; ".$e;
						array_push($strArray, $e);		
					}					
			}
			else
			{				
				//Tag
				if($e{0}=='/')
				{
					$this->CloseTag(strtoupper(substr($e,1)));
					if($e == '/tr'){						
						array_push($strArray, '/tr');
						$col = 0;
					}
					if($e == '/td'){
						$col++;
					 	array_push($strArray, '/td');						
					}
					if($e == '/table') array_push($strArray, '/table');
					if($e == '/ul'){ $add_ul = 0; $index = 1;}
					if($e == '/ol'){ $add_ol = 0; $index = 1;}
					
				}
				else
				{
					//Extract attributes					
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
						if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];											
					$this->OpenTag($tag,$attr);
					if($tag == 'TR')array_push($strArray, 'tr');	
					if($tag == 'TD'){
						array_push($strArray, 'td');						
					}	
					if($tag == 'TABLE')array_push($strArray, 'table');								
					if($tag == 'UL') $add_ul = 1;
					if($tag == 'OL') $add_ol = 1;
					
				}
			}
		 }
		 
		//handling with the string array
		$this->row(array('Communications Plan.','',''));
		//print_pre($strArray);
		$this->BuildTable($strArray);
		
	}
	
	function BuildTable($arr)
	{
		$obj = new ArrayObject($arr);
		$items = $obj->getIterator();
		$start_X = 80;
		$X_width = 180;
		
		//print_pre($arr);
		//get each element from the array, and check the tag, here only check 'table', '/table', 'tr', '/tr', 'td', '/td' six tags.
		while($item = current($items)){
			
			$sub = array();
			$string = '';
			
			if($item == 'table'  || $item == '/tr'){					
				$this->ln();		
				next($items);	
			}elseif($item == 'tr'|| $item == '/table'){							
				next($items);	
			}elseif($item == 'td'){					
				while(next($items) != '/tr'){ //check if row end
					$item2 = current($items);								
					if($item2 == 'table' ){							
						$stirng = '';	
					}elseif($item2 == 'tr' || $item2 == 'td'){
						$stirng = '';	
					}elseif($item2 == '/td' ){
						if(strlen($string) > 0) array_push($sub, $string);
						$stirng = '';
					}else{									
						if(prev($items) != 'td') $string .= "[BR]" .$item2  ;	// if contents contain more than one paragraphs, add break tag for each paragraph																						
						else $string = $item2;				
						next($items);												
					}
				}
				//print_pre($sub);
				$count = count($sub);
				$start = $X_width/$count;		//column width		
				if($count > 0){
					$i= 0;
					$index = 1;
					$breakTag	= array();				
					foreach($sub as $value){								
						if($count <= 1){							
							$breakTag = explode('[BR]', $value);	
							$breakTagCount = count($breakTag);		
							if($breakTagCount == 1){
								$this->setX($start_X);
								$this->Cell($X_width,10, $this->decode_html($value),1);	
							}else{
								foreach($breakTag as $subitem){
									$this->setX($start_X);
									$this->Cell($X_width,10, $this->decode_html($subitem),1);	
									if( $index++ < $breakTagCount) $this->ln();
								}
							}
						}else{						
							$width = $start_X + $X_width*$i++/$count;														
							$this->setX($width);
							$this->Cell($start,10, $this->decode_html($value),1);								
						}						
					}
				}
				//next($items);				
			}elseif($item == '/td'){		
				next($items);	
			}else{		//display the item directly			
				$this->setX($start_X);
				$this->Cell($X_width,10, $this->decode_html($item),1);
				next($items);	
			}	
		}
		
	}
	
	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
			if($this->$s>0)
				$style.=$s;
		$this->SetFont('',$style);
	}
	
	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}

	
	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}
	
	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	
	
	
	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}

}

class BibleByDist_PDF extends FPDF
{
	var $ALIGN = '';
	
	var $widths;
	var $aligns;
	var $border = false;
	var $currentDate;
	var $currentDist;
	var $currentPhone;
	var $currentMobile;
	var $currentRegion;
	var $currentDist_out;
	var $currentHeight;
	var $phoneColumn;
	var $columnHeadings;
	
	var $currentSDate;
	var $currentSDist;
	var $currentSPhone;
	var $currentSMobile;
	var $currentSDist_out;
		
	function PDF($orientation='L',$unit='mm',$format='A4')
	{
		//Call parent constructor
		$this->FPDF($orientation,$unit,$format);
							
	}
	
	function Header()
	{	
		// Cell
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(141,141,141);
		$this->SetDrawColor(22,22,22);
		$this->SetFont('Arial','B',10);
		
		$this->MultiCell(142,5,$this->currentDist_out,'LTBR','L',1);
		
		$this->SetXY(147,$this->GetY()-15);
		$this->SetFont('Arial','B',20);
		$this->Cell(143,15,$this->currentRegion,'RTB',1,'R',1);
		$this->Ln(1);
		
		$this->SetFont('Arial','B',10);
		$this->MultiCell(142+143,5,$this->currentSDist_out,'LTBR','L',1);
		$this->Ln(1);
		$this->SetFillColor(200,200,200);
		//column headings
		$this->SetFont('Arial','B',10);
		for($i=0;$i<count($this->columnHeadings);$i++)
	    {
	    	if ($i==0) $border="LTB";
	    	elseif ($i==count($this->columnHeadings)-1) $border="RTB";
	    	else $border="TB";
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        $this->Cell($w,5,$this->columnHeadings[$i],$border,0,$a,1);
	    }
	    $this->Ln();
	    
	    
		$this->SetTextColor(0,0,0);
		
	}
	
	function Footer()
	{
		//Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	
	}
	
	function Row($data)
	{
		
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++){
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    }
	    $h=4*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        if (!$old_x) $old_x=$x;
	        if (!$old_y) $old_y=$y; 
	        //Draw the border
	        //if($this->border) $this->Rect($x,$y,$w,$h);
	        if ($this->border) $this->Line($old_x,$old_y,$x,$y);
	        $old_x=$x;
	        $old_y=$y;   
	        //Print the text
	        $this->MultiCell($w,4,$data[$i],0,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->currentHeight = $h;
	    $this->Ln($h);
	}
	
	function WriteHTML($html)
	{
	    //HTML parser
	    $html=str_replace("\n",' ',$html);
	    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	    foreach($a as $i=>$e)
	    {
	        if($i%2==0)
	        {
	            //Text
	            if($this->HREF)
	                $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN == 'center')
                    $this->Cell(0,5,$e,0,1,'C');
                else
                    $this->Write(5,$e);
	        }
	        else
	        {
	            //Tag
	            if($e{0}=='/')
	                $this->CloseTag(strtoupper(substr($e,1)));
	            else
	            {
	                //Extract attributes
	                $a2=explode(' ',$e);
	                $tag=strtoupper(array_shift($a2));
	                $prop=array();
	                foreach($a2 as $v)
	                {
	                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
	                        $prop[strtoupper($a3[1])]=$a3[2];
	                }
	                $this->OpenTag($tag,$prop);
	            }
	        }
	    }
	}

	function OpenTag($tag,$attr)
	{
       
	    //Opening tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( $prop['WIDTH'] != '' )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }		
    }
	
	function CloseTag($tag)
	{
        //Closing tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

	function CheckTag($html)
	{
		//get string token
		$string = html_entity_decode($html);		
		 $a=preg_split('/<(.*)>/U',$string,-1,PREG_SPLIT_DELIM_CAPTURE);
		 $strArray = array();
		 $col = 0;
		 $index = 1;
		// print_pre($a); //exit;
		  foreach($a as $i=>$e)
		 {		
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else				
					if(!empty($e) && str_word_count($e)){
						if($add_ol) $e = $index++ . '. '.$e;
						if($add_ul) $e = "&bull; ".$e;
						array_push($strArray, $e);		
					}					
			}
			else
			{				
				//Tag
				if($e{0}=='/')
				{
					$this->CloseTag(strtoupper(substr($e,1)));
					if($e == '/tr'){						
						array_push($strArray, '/tr');
						$col = 0;
					}
					if($e == '/td'){
						$col++;
					 	array_push($strArray, '/td');						
					}
					if($e == '/table') array_push($strArray, '/table');
					if($e == '/ul'){ $add_ul = 0; $index = 1;}
					if($e == '/ol'){ $add_ol = 0; $index = 1;}
					
				}
				else
				{
					//Extract attributes					
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
						if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];											
					$this->OpenTag($tag,$attr);
					if($tag == 'TR')array_push($strArray, 'tr');	
					if($tag == 'TD'){
						array_push($strArray, 'td');						
					}	
					if($tag == 'TABLE')array_push($strArray, 'table');								
					if($tag == 'UL') $add_ul = 1;
					if($tag == 'OL') $add_ol = 1;
					
				}
			}
		 }
		 
		//handling with the string array
		$this->row(array('Communications Plan.','',''));
		//print_pre($strArray);
		$this->BuildTable($strArray);
		
	}
	
	function BuildTable($arr)
	{
		$obj = new ArrayObject($arr);
		$items = $obj->getIterator();
		$start_X = 80;
		$X_width = 180;
		
		//print_pre($arr);
		//get each element from the array, and check the tag, here only check 'table', '/table', 'tr', '/tr', 'td', '/td' six tags.
		while($item = current($items)){
			
			$sub = array();
			$string = '';
			
			if($item == 'table'  || $item == '/tr'){					
				$this->ln();		
				next($items);	
			}elseif($item == 'tr'|| $item == '/table'){							
				next($items);	
			}elseif($item == 'td'){					
				while(next($items) != '/tr'){ //check if row end
					$item2 = current($items);								
					if($item2 == 'table' ){							
						$stirng = '';	
					}elseif($item2 == 'tr' || $item2 == 'td'){
						$stirng = '';	
					}elseif($item2 == '/td' ){
						if(strlen($string) > 0) array_push($sub, $string);
						$stirng = '';
					}else{									
						if(prev($items) != 'td') $string .= "[BR]" .$item2  ;	// if contents contain more than one paragraphs, add break tag for each paragraph																						
						else $string = $item2;				
						next($items);												
					}
				}
				//print_pre($sub);
				$count = count($sub);
				$start = $X_width/$count;		//column width		
				if($count > 0){
					$i= 0;
					$index = 1;
					$breakTag	= array();				
					foreach($sub as $value){								
						if($count <= 1){							
							$breakTag = explode('[BR]', $value);	
							$breakTagCount = count($breakTag);		
							if($breakTagCount == 1){
								$this->setX($start_X);
								$this->Cell($X_width,10, $this->decode_html($value),1);	
							}else{
								foreach($breakTag as $subitem){
									$this->setX($start_X);
									$this->Cell($X_width,10, $this->decode_html($subitem),1);	
									if( $index++ < $breakTagCount) $this->ln();
								}
							}
						}else{						
							$width = $start_X + $X_width*$i++/$count;														
							$this->setX($width);
							$this->Cell($start,10, $this->decode_html($value),1);								
						}						
					}
				}
				//next($items);				
			}elseif($item == '/td'){		
				next($items);	
			}else{		//display the item directly			
				$this->setX($start_X);
				$this->Cell($X_width,10, $this->decode_html($item),1);
				next($items);	
			}	
		}
		
	}
	
	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
			if($this->$s>0)
				$style.=$s;
		$this->SetFont('',$style);
	}
	
	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}

	
	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}
	
	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	
	
	
	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}

}

?>