<?
	include "../../includes/mysql.php";
	include "../../includes/MySQLSelect.php";
	include "../../includes/mysql_aid_functions.php";
	if(isset($_POST['island'])){
		$islands = $_POST['island'];
		$dest_type = $_POST['dest_type'];
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
		$sel->optionDefText="All";
		$sel->optionDefValue="0";
		$sel->multiple="multiple";
		$sel->selectWidth=11;
		$sel->startSelect();
		$sel->writeSelectSQL($qry);;
		$sel->stopSelect();
?>		
		<input name="submit" type="button" value=">>" onClick="javascript:message('area_reg');set_Button_off();get(this.parentNode,'area_reg','proc_job/get/get_area.php?dest_type=<?=$dest_type?>');" />
<?		
	}
?>

