<?php

// Change the following to suit your own installation:

/*	$host = "localhost";
	$database = "";
	$user = "";
	$password = "";*/
	
	include "../includes/mysql.php";
	include "../includes/mysql_aid_functions.php";

// You don't have touch a thing from here on unless you really want to:

//	mysql_connect($host,$user,$password);
//	mysql_select_db($database);

$query_str = $_SERVER['QUERY_STRING'];
$page = $_SERVER['HTTP_REFERER'];
$page = substr($page,0,strpos($page,"?"));
if ($_POST['name']) {
	$search = $_POST['name'];
	$sql = "SELECT address_id 	AS record,
					CONCAT(name,', ',first_name)	AS print1,
					CONCAT(' ',address_id) 	AS print2
			FROM address 
			WHERE CONCAT(name,', ',first_name) like '$search%' 
			ORDER BY print1";
	$record_n="record";
}

if ($_POST['company']) {
	$search = $_POST['company'];
	$sql = "SELECT address_id 	AS record,
					company		AS print1
			FROM address
			LEFT JOIN
			operator
			ON  address.operator_id=operator.operator_id
			WHERE company like '$search%' order by company limit 1000";
	$record_n="record";
}

if ($_POST['bundle_contr']) {
	$search = $_POST['bundle_contr'];
	$job_id=$_GET["job_id"];
	$delivery_date = get("job","delivery_date","WHERE job_id='$job_id'");
		$sql = "SELECT DISTINCT route_aff.route_id 	AS record,
						company		AS print1,
						CONCAT(route.area,'-',route.code) 	AS print2
				FROM address
				LEFT JOIN
				operator
				ON  address.operator_id=operator.operator_id
				LEFT JOIN route_aff
				ON route_aff.contractor_id=operator.operator_id
				LEFT JOIN route
				ON route_aff.route_id=route.route_id
				WHERE company like '$search%' 
					AND is_contr='Y' 
					AND route_aff.app_date<='$delivery_date'
					AND route_aff.stop_date>'$delivery_date'
				order by company limit 1000";
	$record_n="bund_route";
	
	$query_str = str_replace("action=addarea","action=edit",$query_str);
}


if ($_POST['client']) {
	$search = $_POST['client'];
	$sql = "SELECT client_id 	AS record,
					name		AS print1,
					''			AS print2
			FROM client
			WHERE client.name LIKE '$search%' 
			ORDER BY name limit 1000";	
	$record_n="client";
}

if ($_POST['pub']) {
	$search = $_POST['pub'];
	$sql = "SELECT 	DISTINCT publication AS record,
					publication	AS print1,
					''			AS print2
			FROM job
			WHERE job.publication LIKE '$search%' 
			ORDER BY publication limit 1000";	
	$record_n="pub";
}

if ($_POST['job']) {
	$search = $_POST['job'];
	$sql = "SELECT 	DISTINCT job_no AS record,
					job_no	AS print1,
					''			AS print2
			FROM job
			WHERE job.job_no LIKE '$search%' 
			ORDER BY job_no limit 1000";	
	$record_n="job";
}
			
$rs = mysql_query($sql);
$rows = mysql_num_rows($rs);
?>
<ul>

<? 
$x=0;

/*
if (strstr($query_str,"$record_n=")) {
	$str_length = strlen($record_n)+1;
	//$tmp = substr($query_str,strpos($query_str,"accountid=")+10,strpos($query_str,"&"));
	$tmp = substr($query_str,strpos($query_str,"$record_n=")+$str_length);
	$tmp = substr($tmp,strpos($tmp,"&")+1);

	$link = $page."?$record_n=all&$tmp";
}
else if ($query_str) {
	$link = $page."?$record_n=all&$QUERY_STRING";
}
else {
	$link = $page."?$record_n=all";
}

	
?>
	<li><a href="<?=$link?>">All</a></li>
<?
*/

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
