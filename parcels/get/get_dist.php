<?
	include "../../includes/mysql.php";
	include "../../includes/MySQLSelect.php";
	include "../../includes/mysql_aid_functions.php";

	


	if(isset($_POST['cont'])){
		$date_year = $_POST['date_year'];
		$date_month = $_POST['date_month'];
		$has_run = $_POST['has_run'];
		$cur_run = $_POST['run'];
		$user_id = $_POST['user_id'];
		
		$date = $date_year."-".$date_month."-15";
		
		$buffer = explode("[",$_POST['cont']);
		
		$buffer_route = explode("--",$buffer[0]);
		$route = trim($buffer_route[1]);
		
		$route = str_replace("andamp","&",$route);
		
		$buffer = explode("]",$buffer[1]);
		$contractor_id=$buffer[0];
		$where_add = "AND operator.operator_id='$contractor_id'";

		
		/*$buffer = explode("--",$_POST['cont']);
		$route = $buffer[1];
		$contractor = str_replace("andamp","&",$buffer[0]);
		
		if($_POST['is_name']){
			$buffer = explode(",",$contractor);
			$first_name = $buffer[1];
			$name = $buffer[0];
			$where_add = "AND name='$name' AND first_name='$first_name'";
		}
		else{
			$where_add = "AND company='$contractor'";
		}*/
		
		
		$route_id = get("route","route_id","WHERE code='$route'");
		$now = date("Y-m-d");
		$qry = "SELECT * 
				FROM route_aff
				LEFT JOIN route
				ON route_aff.route_id=route.route_id
				LEFT JOIN operator
				ON env_contractor_id=operator.operator_id
				LEFT JOIN address
				ON address.operator_id=operator.operator_id
				WHERE route.route_id='$route_id'
					AND '$now' BETWEEN app_date AND stop_date
					$where_add";
		$res = query($qry,0);
		$obj = mysql_fetch_object($res);
		$dist_id = $obj->env_dist_id;
		
		$month = date("Y-m",strtotime($date));
		$dist_name  = get("operator","company","WHERE operator_id='$dist_id'");
		//$parcel_run_id = get("parcel_run","parcel_run_id","WHERE actual='1' AND user_id='$user_id' AND run='$cur_run' AND dist_id='$dist_id' AND date LIKE '$this_month%'");
		$parcel_run_id = get("parcel_run","parcel_run_id","WHERE actual='1' AND run='$cur_run' AND dist_id='$dist_id' AND date LIKE '$month%'");
		//echo "WHERE run='$cur_run' AND dist_id='$dist_id' AND date LIKE '$month%'";
		//echo $parcel_run_id;
		if(!$parcel_run_id){
		//if(true){
			
			
			$qry = "SELECT MAX(run) AS run FROM parcel_run WHERE dist_id='$dist_id' AND date LIKE '$month%'";
			$res = query($qry);
			$run_obj = mysql_fetch_object($res);
			
			if(!$run_obj->run) $run=0;
			else $run = $run_obj->run;
			
			if($has_run)
				$run = $cur_run;
			else
				$run++;
			
			if(!$dist_id) $dist_name = "<font color='red'>No distributor!</font>";
			else{
				$qry = "INSERT INTO parcel_run SET user_id='$user_id', run = '$run', date = '$date', route_id = '$route_id', dist_id = '$dist_id', contractor_id = '$contractor_id', real_date=now()";
				query($qry);
			}
		}
		else{
			$run = $cur_run;
		}
		
?>
		Distributor: <?=$dist_name?>
		<strong> / Page:  <input style="width:3em; " type="text" name="run" value="<?=$run?>" /></strong>
		<input style="width:3em; " type="hidden" name="dist_id" value="<?=$dist_id?>" />
<?		
	}
?>