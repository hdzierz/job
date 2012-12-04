<?


# include necessary files
require "../includes/mysql.php";
require "../includes/aid_functions.php";
require "../includes/mysql_aid_functions.php";
require "pdf_functions.php";
require "../includes/fpdf/pdf.class.php";

$report = $_REQUEST["report"];
switch($report){
	case "bible_dist":
		write_bible_dist_pdf();
	break;
	case "bible_region":
		write_bible_region_pdf();
	break;
};

function write_bible_region_pdf(){
	
	$date = $_REQUEST["date"];
	$date_out = date("d M Y",strtotime($date));
	$dist_id = $_REQUEST["dist_id"];
	$region = $_REQUEST["region"];
	$island = $_REQUEST["island"];
	$mode = $_REQUEST["mode"];
	$home_phone = $_REQUEST["home_phone"];
	$mobile_phone = $_REQUEST["mobile_phone"];
	
	//get data
	$bible_data = bible_region_qry($date,$island,$region,$home_phone,$mobile_phone);
	if(count($bible_data)==0) return;
	//var_dump($bible_data); die();
	//start pdf
	$pdf=new BibleByRegion_PDF('L','mm','A4');
	$pdf->SetMargins(5,5,5);
	$pdf->SetAutoPageBreak(1,10);
	//phone column on or off
	if ($home_phone==1 || $mobile_phone==1){
		$pdf->phoneColumn=true;
		//column widths
		$pdf->widths = array(40,40,30,61,20,95);
		$pdf->columnHeadings = array("RD","Name","Phone","Parcel Dropoff","Latest Dep.","Description");
	}
	else{
		$pdf->phoneColumn=false;
		//column widths
			$pdf->widths = array(40,40,70,40,95);
			$pdf->columnHeadings = array("RD","Name","Parcel Dropoff","Latest Dep.","Description");
	}
	
	//var_dump($pdf->columnHeadings);
	$pdf->AliasNbPages();	
	
	$count=0;
	//echo "'$dist_id'";exit;
	foreach ($bible_data as $reg => $reg_info){
		
		$pdf->currentDate = $date_out;
		$pdf->currentDist_out = "Region: ".$reg."\n";
		$pdf->currentRegion = "Region: ".$reg."\n";
		$pdf->AddPage();	
		
		$pdf->SetFont('Arial','',9);
		$pdf->border=true;
		foreach ($reg_info as $key => $reg_data){
			//echo $reg_data."<br />";
			//var_dump($reg_data); echo "<br />";echo "<br />";echo "<br />";
			//create line
			$pdf->Row($reg_data);
		}	
		$count++;
	}
	
	
	$pdf->Output();

}

function write_bible_dist_pdf(){
	
	$date = $_REQUEST["date"];
	$date_out = date("d M Y",strtotime($date));
	$dist_id = $_REQUEST["dist_id"];
	$mode = $_REQUEST["mode"];
	$home_phone = $_REQUEST["home_phone"];
	$mobile_phone = $_REQUEST["mobile_phone"];
	
	//get data
	$bible_data = bible_dist_qry($date,$dist_id,$home_phone,$mobile_phone);
	
	//var_dump($bible_data); die();
	//start pdf
	$pdf=new BibleByDist_PDF('L','mm','A4');
	$pdf->SetMargins(5,5,5);
	$pdf->SetAutoPageBreak(1,10);
	//phone column on or off
	if ($home_phone || $mobile_phone){
		$pdf->phoneColumn=true;
		//column widths
		$pdf->widths = array(40,40,30,51,20,104);
		$pdf->columnHeadings = array("RD","Name","Phone","Parcel Dropoff","Latest Dep.","Description");
	}
	else{
		$pdf->phoneColumn=false;
		//column widths
			$pdf->widths = array(40,40,60,40,105);
			$pdf->columnHeadings = array("RD","Name","Parcel Dropoff","Latest Dep.","Description");
	}
	
	//$pdf->AliasNbPages();	

	$count=0;
	//echo "'$dist_id'";exit;
	$pdf->AliasNbPages();	
	foreach ($bible_data as $dist_id => $dist_info){
		$cur_dist = $dist_info['dist_info'];
		//var_dump($cur_dist); die();
		$pdf->currentDate = $date_out;
		$pdf->currentDist = $cur_dist->name;
		$pdf->currentPhone = $cur_dist->phone;
		$pdf->currentMobile= $cur_dist->mobile;
		$pdf->currentRegion =$cur_dist->country;
		$pdf->currentEmail =$cur_dist->email;
		$pdf->currentDist_out = "Date: ".$pdf->currentDate."\n".
								"Distributor: ".$pdf->currentDist."\n".
								"Phone: ".$pdf->currentPhone.", ".
								"Mobile: ".$pdf->currentMobile.", \n".
								"Email: ".$pdf->currentEmail ;
		
			
		
		$pdf->SetFont('Arial','',9);
		$pdf->border=true;
		foreach($dist_info['sub_dist'] as $sdist_id => $subdist_info){
			
			$cur_sdist = $subdist_info['subdist_info'];
			
			$pdf->currentSDist = $cur_sdist->name;
			$pdf->currentSPhone = $cur_sdist->phone;
			$pdf->currentSMobile= $cur_sdist->mobile;
			$pdf->currentSRegion =$cur_sdist->country;
			$pdf->currentSEmail =$cur_sdist->email;
			$pdf->currentSDist_out = "S/Dist.: ".$pdf->currentSDist.": ".
									"Phone: ".$pdf->currentSPhone.", ".
									"Mobile: ".$pdf->currentSMobile.", ".
									"Email: ".$pdf->currentSEmail ;
			$pdf->AddPage();
			//var_dump($subdist_info["records"]);
			foreach ($subdist_info["records"] as $key => $sub_dist_info){
				//create line
				$pdf->Row($sub_dist_info);
			}	
		}
		$count++;
	}
	
	
	$pdf->Output();

}

?>