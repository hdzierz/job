<?
	include "../../includes/mysql.php";
	include "../../includes/MySQLSelect.php";
	include "../../includes/mysql_aid_functions.php";
	
	if(isset($_POST['area'])){
		$regions = $_POST['region'];
		$islands  = $_POST['island'];	
		$areas = $_POST['area'];
		$dest_type = $_POST['dest_type'];
        $is_pmp = false;
        if(isset($_POST['pmp'])) $is_pmp=true;
		
		if($dest_type=="num_total") $dest_type="(num_farmers+num_lifestyle)";
	
		$qry = "SELECT code as name, route_id as id FROM route WHERE is_hidden<>'Y'";
        if($areas[0]!='0'){
			$start=true;
			foreach($areas as $ar){
				if(strpos($ar,'axx123y')){
					$ar = str_replace('axx123y','&',$ar);
				}			
				if($start){
					$qry .= "AND (area = '$ar' ";
					$start=false;
				}
				else{
					$qry .= "OR area = '$ar' ";
				}
			}
			$qry .= ")";
		}
		if($regions[0]!='0'){
			$start=true;
			foreach($regions as $reg){
				if(strpos($reg,'axx123y')){
					$ar = str_replace('axx123y','&',$reg);
				}						
				if($start){
					$qry .= "AND (region = '$reg' ";
					$start=false;
				}
				else{
					$qry .= "OR region = '$reg' ";
				}
			}
			$qry .= ")";		
		}
		else{
			$start=true;
			foreach($islands as $isle){
				if($start){
					$qry .= "AND (island = '$isle' ";
					$start=false;
				}
				else{
					$qry .= "OR island = '$isle' ";
				}
			}
			$qry .= ")";						
		}
        if($is_pmp)
            $qry .= "ORDER BY pmp_areacode, pmp_runcode ";
        else
		    $qry .= "ORDER BY island,seq_region,seq_area,seq_code ";

		$sel = new MySQLSelect("code","code", "route","","narrow","code[]");
        $sel->selectOnChange="";
		$sel->optionDefText="All";
		$sel->multiple="multiple";
		$sel->selectSize=10;
		$sel->selectWidth=15;
		$sel->startSelect();
		$sel->writeSelectSQL($qry);
		$sel->stopSelect();
	}		
?>
