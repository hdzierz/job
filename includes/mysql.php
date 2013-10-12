<?php
# connect to mysql

if($_SERVER['DOCUMENT_ROOT'] == "/Applications/XAMPP/xamppfiles/htdocs"){
	$db_connect = mysql_connect('localhost', 'root', 'inkl67z');
}
else if($_SERVER['DOCUMENT_ROOT'] == "/var/www/html"){
	$db_connect = mysql_connect('localhost', 'root', 'inkl67z');
}
else{
	$db_connect = mysql_connect('192.168.100.23:3306', 'admin', 'zt90undr');
}

$testing=mysql_select_db('coural', $db_connect); 
if (!$testing) die("error".mysql_error());
?>
