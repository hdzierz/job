
<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
require_once('includes/mail_aid_functions.php');


$qry = "SELECT * FROM parcel_job_ticket WHERE start IS NOT NULL AND end IS NOT NULL AND start <= end";

$res = query($qry);

while($item = mysql_fetch_object($res)){
	for($i=$item->start;$i<=$item->end;$i++){
		$qry = "INSERT INTO parcel_tickets(job_id,type,ticket_no) VALUES({$item->job_id}, '{$item->type}', $i);";
		query($qry);
	}
}


//send_test_mail();
?>