<?php
# connect to mysql
$db_connect = mysql_connect('192.168.100.23:3306', 'admin', 'zt90undr');
$testing=mysql_select_db('coural', $db_connect); 
if (!$testing) die("error".mysql_error());
?>
