<?php

// Thsis woudo be the auto suggest call for the Ajax.Auotomplete.
// NOT USED AT TEH MOMENT

	include "../../includes/mysql.php";
	include "../../includes/mysql_aid_functions.php";

// You don't have touch a thing from here on unless you really want to:

//	mysql_connect($host,$user,$password);
//	mysql_select_db($database);

$query_str = $_SERVER['QUERY_STRING'];
$page = $_SERVER['HTTP_REFERER'];
$page = substr($page,0,strpos($page,"?"));

$now = date("Y-m-d");

if ($_POST['contractor_new']) {
	$search = $_POST['contractor_new'];
	
	$sql = "Select 	CONCAT(name,', ',first_name,'--',route.code,' \[',operator.operator_id,'\]')  AS print1,
						route.route_id AS record
					from operator
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					LEFT JOIN route_aff
					ON route_aff.env_contractor_id=operator.operator_id
					LEFT JOIN route
					ON route.route_id = route_aff.route_id
					where CONCAT(name,', ',first_name,'--',route.code) like '".$search."%' 
					$where_add 
					
					AND '$now' BETWEEN app_date AND stop_date
					order by name,first_name,route.code";  
	//AND route.is_hidden<>'Y' ## this parameter removed from the query at Howard Ede's request 3/8/09 JW
	$record_n="route_id";
}

$rs = mysql_query($sql);
$rows = mysql_num_rows($rs);
?>
<ul>
<li><?=$_GET['action']?></li>
<? 
$x=0;

while($data = mysql_fetch_object($rs)) { 
	if (strstr($query_str,"$record_n=")) {
		$str_length = strlen($record_n)+1;
		//$tmp = substr($query_str,strpos($query_str,"accountid=")+10,strpos($query_str,"&"));

		$tmp0 = substr($query_str,0,strpos($query_str,"$record_n="));
		$tmp = substr($query_str,strpos($query_str,"$record_n=")+$str_length);
		$tmp = substr($tmp,strpos($tmp,"&")+1);
	
		$link = $page."?$record_n=$data->record&$tmp0&$tmp";
	}
	else if ($query_str) {
		$link = $page."?$record_n=$data->record&$query_str";
	}
	else {
		$link = $page."?$record_n=$data->record";
	}
		
?>
	<li><a href="<?=$link?>"><? echo stripslashes($data->print1);?><? if($data->print2) echo ", ".stripslashes($data->print2);?></a></li>
<?

	$x++;
	if ($x==12) break;
}
if ($x < $rows) {
?>
<li><center><?=($rows-$x)?> More results</center></li>
<?
}
if ($x == 0) {
?>
<li>No Results</li>
<?
}
?>

</ul>
