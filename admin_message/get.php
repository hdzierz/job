<?
	include "../includes/mysql.php";
	include "../includes/MySQLSelect.php";
	include "../includes/mysql_aid_functions.php";
	if(isset($_GET['island'])){
		$islands = $_GET['island'];
		$isle1 = $islands[0];
		$isle2 = $islands[1];
		
		if($isle2){
			$qry = "SELECT DISTINCT region as name, region as id FROM route ORDER BY island,seq_region";
		}
		else{
			$qry = "SELECT DISTINCT region as name, region as id FROM route WHERE island='$isle1' ORDER BY island,seq_region";
		}

		$sel = new MySQLSelect("region","region","route","","narrow","region[]");
		$sel->selectOnChange="";
		$sel->selectSize=10;
		$sel->optionDefText="";
//		$sel->optionDefText="All";
//		$sel->optionDefValue="0";
		$sel->multiple="";
		$sel->selectWidth=11;
		$sel->startSelect();
		$sel->writeSelectSQL($qry);;
		$sel->stopSelect();
?>		
		<input name="submit" type="button" value=">>" onClick="javascript:get(this.parentNode,'area_reg','proc_job/get.php');" />
<?		
	}
	if(isset($_GET['region'])){
		$regions = $_GET['region'];
		$qry = "SELECT DISTINCT area as name, area as id FROM route WHERE route_id IS NOT NULL ";
		if($regions[0]!='0'){
			$start=true;
			foreach($regions as $reg){
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
		$qry .= " ORDER BY seq_region,seq_area ";

		$sel = new MySQLSelect("area","area","route","","narrow","area[]");
		$sel->selectOnChange="";
		$sel->optionDefText="All";
		$sel->optionDefValue="0";
		//$sel->orderField="seq_area";
		$sel->multiple="multiple";
		$sel->selectSize=10;
		$sel->selectWidth=15;
		$sel->startSelect();
		$sel->writeSelectSQL($qry);
		$sel->stopSelect();
?>		
		<input name="submit" type="button" value=">>" onClick="javascript:get(this.parentNode,'code_reg','proc_job/get.php');" />
<?				
	}	
	if(isset($_GET['area'])){
		$areas = $_GET['area'];
		$qry = "SELECT DISTINCT CONCAT(area,'/',code) as name, route_id as id FROM route WHERE route_id IS NOT NULL ";
		if($areas[0]!='0'){
			$start=true;
			foreach($areas as $ar){
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
		$qry .= "ORDER BY seq_region,seq_area,seq_code ";

		$sel = new MySQLSelect("code","code","route","","narrow","code[]");
		$sel->selectOnChange="";
		$sel->optionDefText="";
		$sel->multiple="multiple";
		$sel->selectSize=10;
		$sel->selectWidth=15;
		$sel->startSelect();
		$sel->writeSelectSQL($qry);
		$sel->stopSelect();
	}		
?>

