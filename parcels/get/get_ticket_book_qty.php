<?php
include "../../includes/mysql.php";
	include "../../includes/MySQLSelect.php";
	include "../../includes/mysql_aid_functions.php";

	$start = $_GET['start'];
	$type = $_GET['type'];
	$i = $_GET['i'];
	$job_qty = $_GET['job_qty'];
	
$qry = "SELECT * FROM parcel_ticket_th WHERE ".$start." BETWEEN start AND end AND '".$type."' = type";
$res_tb = query($qry);
$tb_qty = mysql_fetch_object($res_tb);
if(!$tb_qty) $tb_qty = 20;
else $tb_qty = $tb_qty->qty;

?>
<input type="text" disabled name="exp_qty[<?=$i?>]" id="exp_qty[<?=$i?>]"  value="<?=$tb_qty*$job_qty?>" />
<?
?>