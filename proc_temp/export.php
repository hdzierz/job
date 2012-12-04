<?
include "../includes/mysql.php";
include "../includes/MysqlTable.php";
include "../includes/MysqlSelect.php";
include "../includes/mysql_aid_functions.php";
include "functions.php";


$action = $_GET["action"];
$job_id = $_GET["job_id"];
$choice = $_GET["choice"];

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="export.xls"');

if($action=="show_job_details"){
	show_job_details($job_id,false,$choice);
}
else if($action=="show_do_details"){
	show_do_details($job_id,false);
}
else if($action=="show_job_details1"){
	show_print_table1($job_id,false);
}
else if($action=="show_do_details_with_pagebreak"){
	show_do_details_with_pagebreak($job_id,false);
}
?>
