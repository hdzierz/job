<?
function query($sql,$print=0,$order='',$group=''){
	$sql = $sql.$group.$order;
	if($print){ echo nl2br($sql);echo "<br />";}
	if(!$res = mysql_query($sql)){
		$message = "ERROR in Query: ".$sql."ERROR: ".mysql_error();
		die($message);
	}	
	return $res;
}

function get($table,$field,$where,$print=0){
	$qry = "SELECT $field FROM $table $where";
	if($print)
		echo $qry;
	$res = query($qry);
	$obj = mysql_fetch_assoc($res);
	return $obj[$field];
}

function get_sum($table,$field,$where,$group){
	$qry = 'SELECT SUM(`$field`) AS "$field" FROM $table $where $group';
	$res = query($qry);
	$obj = mysql_fetch_assoc($res);
	return $obj[$field];
}

function get_sum_as($table,$field,$as,$where,$group){
	$qry = "SELECT SUM($field) AS $as FROM $table $where $group";
	$res = query($qry);
	$obj = mysql_fetch_assoc($res);
	return $obj[$as];
}

function get_max($table,$field,$where,$group){
	$qry = "SELECT MAX(`$field`) AS `$field` FROM $table $where $group";
	$res = query($qry);
	$obj = mysql_fetch_assoc($res);
	return $obj[$field];
}

function get_min($table,$field,$where,$group){
	$qry = "SELECT MIN($field) AS $field FROM $table $where $group";
	$res = query($qry);
	$obj = mysql_fetch_assoc($res);
	return $obj[$field];
}

function get_avg($table,$field,$where,$group){
	$qry = "SELECT AVG($field) AS $field FROM $table $where $group";
	$res = query($qry);
	$obj = mysql_fetch_assoc($res);
	return $obj[$field];
}

function next_id($table,$id_field,$id){
	$id++;
	while(!$next_id = get($table,$id_field,"WHERE $id_field='$id'")){
		$id++;
	}
	return $next_id;
}


?>
