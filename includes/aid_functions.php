<?
// Reoplaces html new line with  normal new line
function br2nl($string)
{
	return  str_replace('<br />', "\n", $string);
  //  return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

// Redirects a page. Uses Javascrip when header has already been sent.
function redirect($filename) 
{
	if (!headers_sent())
		header('Location: '.$filename);
	else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="'.$filename.'";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$filename.'" />';
		echo '</noscript>';
	}
}

function array_to_mysql($array,$is_str=true){
	$first=true;
	$result = "(";
	foreach($array as $item){
		if($is_str) $item = '\''.$item.'\'';
		if($first){
			$result.=$item;
		}
		else{
			$result.=','.$item;
		}
		$first = false;
	}
	$result.=')';
	return $result;
}

function array_to_request($name,$array){
	$first=true;
	$result = "";
	foreach($array as $item){
		$result.='&'.$name."[]=".$item;
	}
	return $result;
}


// Return all routes of a contractor
function get_routes_from_contr($contractor_id){
	$qry = "SELECT * 
			FROM route_aff
			LEFT JOIN operator
			ON env_contractor_id=operator_id
			WHERE operator_id='$contractor_id'";
	
	$res = query($qry);
	
	
	$routes = array();
	while($route = mysql_fetch_object($res)){
		$routes[] = $route->route_id;
	}
	
	return $routes;
}

// Return all routes of a contractor
function get_contr_from_dist($dist_id){
	$qry = "SELECT * 
			FROM route_aff
			LEFT JOIN operator
			ON contractor_id=operator_id
			WHERE dist_id='$dist_id'";
	
	$res = query($qry);
	
	
	$contractors = array();
	while($c = mysql_fetch_object($res)){
		$contractors[] = $c->operator_id;
	}
	
	return $contractors;
}

function get_subdist_from_dist($dist_id){
	$qry = "SELECT * 
			FROM route_aff
			LEFT JOIN operator
			ON subdist_id=operator_id
			WHERE dist_id='$dist_id'";
	
	$res = query($qry);
	
	
	$contractors = array();
	while($c = mysql_fetch_object($res)){
		$contractors[] = $c->operator_id;
	}
	
	return $contractors;
}

// Returns the difference between two dates as a number of days. Includes the reference day
function date_diff2($end_date, $begin_date)
{
	$diff = (abs(strtotime($end_date)-strtotime($begin_date)) / 86400)+1;
	return $diff;
}

// Not used I believe
function error($msg){
	echo "<p class='error'>".$msg."</p>";
}
// Not used I believe
function msg($msg){
	echo "<p class='message'>".$msg."</p>";
}

// Returns NULL if a value is empty. Useful for database queries
function make_empty_null($value){
	if($value=="") $value="NULL";
	else $value="'$value'";
	return $value;
}

// UPdates teh route affiliations for a whole job
function update_route_aff($job_id){
	if($job_id)
		$qry = "select * FROM job_route LEFT JOIN job ON job.job_id=job_route.job_id WHERE job.job_id='$job_id'";
	else
		$qry = "select * FROM job_route LEFT JOIN job ON job.job_id=job_route.job_id";
	
	$res = query($qry);
	$old_job_id = 0;
	while($route = mysql_fetch_object($res)){
		if($old_job_id<>$route->job_id)echo "JOB: ".$route->job_id."<br />";
		$old_job_id = $route->job_id;
		$date=$route->delivery_date;
		
		$qry_1 = "SELECT * 
				FROM route_aff 
				WHERE route_id='$route->route_id'
					AND '$date' >= app_date
					AND '$date' < stop_date
				ORDER BY app_date DESC LIMIT 1";
		$res_b = query($qry_1);
		$ids = mysql_fetch_object($res_b);
		
		$qry = "UPDATE job_route 
				SET dist_id='$ids->dist_id',
					subdist_id='$ids->subdist_id',
					contractor_id='$ids->contractor_id',
					dropoff_id='$ids->dropoff_id',
					doff = IF(alt_dropoff_id>0,alt_dropoff_id,'$ids->dropoff_id')
				WHERE job_route_id='$route->job_route_id'";
				
		if(!$ids->dist_id){
			$qry = "SELECT * FROM route WHERE route_id='$route->route_id'";
			$res_route = query($qry);
			$route_err = mysql_fetch_object($res_route);
			$message = "Date inconsistency in route: $route_err->region / $route_err->area /  $route_err->code. Route might not have a distributor.<br />";
		}
		else 
			query($qry);
	}
	return $message;
}

// Lists files in a directory
function dir_list ($directory) 
{

    // create an array to hold directory list
    $results = array();

    // create a handler for the directory
    $handler = opendir($directory);

    // keep going until all files in directory have been read
    while ($file = readdir($handler)) {

        // if $file isn't this directory or its parent, 
        // add it to the results array
        if ($file != '.' && $file != '..')
            $results[] = $file;
    }

    // tidy up: close the handler
    closedir($handler);

    // done!
    return $results;

}

function print_pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
	
}


?>