<?php
# connect to mysql

$db_connect = mysql_connect('localhost', 'root', 'inkl67z');

$DATABASE = 'coural';
if(strpos($_SERVER['REQUEST_URI'],'job_test') !== false){
	$DATABASE = 'coural_test';
}

$testing=mysql_select_db($DATABASE, $db_connect);
if (!$testing) die("error".mysql_error());
?>
