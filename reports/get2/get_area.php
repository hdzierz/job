<?
	include "../../includes/mysql.php";
	include "../../includes/MySQLSelect.php";
	include "../../includes/mysql_aid_functions.php";
	

	if(isset($_POST['region'])){
		$regions = $_POST['region'];
		$islands  = $_POST['island'];
		
		$qry = "SELECT DISTINCT area as name, area as id FROM route WHERE route_id IS NOT NULL ";
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
		if($islands[0]!='0'){
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
		//$qry .= " ORDER BY seq_region,seq_area ";
		$qry .= " ORDER BY area ";
?>
		<table><tr><td valign="middle">
<?
		$sel = new MySQLSelect("area","area","route","","narrow","area[]");
		$sel->selectOnChange="";
		$sel->optionDefText="All";
		$sel->optionDefValue="0";
		//$sel->orderField="seq_area";
		$sel->multiple="multiple";
		$sel->selectSize=10;
		$sel->selectWidth=11;
		$sel->startSelect();
		$sel->writeSelectSQL($qry);
		$sel->stopSelect();
?>		
		</td><td valign="middle">
		<input name="sub_area" type="button" value=">>"   onClick="set_Button_on();get(this,'code_reg','reports/get2/get_code.php')" />
		</td></tr></table>
<?				
	}		
?>