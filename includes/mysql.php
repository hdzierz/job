<?php
# connect to mysql

if($_SERVER['DOCUMENT_ROOT'] == "/Applications/XAMPP/xamppfiles/htdocs"){
	$db_connect = mysql_connect('localhost', 'root', 'inkl67z');
}
else if($_SERVER['DOCUMENT_ROOT'] == "/var/www/html"){
	$db_connect = mysql_connect('localhost', 'root', 'inkl67z');
}

$db_connect = mysql_connect('localhost', 'root', 'inkl67z');

$DATABASE = 'coural';
if(strpos($_SERVER['REQUEST_URI'],'job_test') !== false){
	$testing=mysql_select_db('coural_test', $db_connect); 
	$DATABASE = 'coural_test';
}
else{
	$testing=mysql_select_db('coural_test', $db_connect);
}
if (!$testing) die("error".mysql_error());
?>
