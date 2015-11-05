
<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
require_once('includes/mail_aid_functions.php');

echo "TEST<br />";

send_test_mail();
die();
$to      = 'hdzierz@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: hdzierz@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if(!mail($to, $subject, $message, $headers)) die("URGS!");

die();

$file = fopen("tickets_unredeemed.csv", "a");

ini_set('max_execution_time', 3*60*60);

$qry = "SELECT * FROM parcel_job_ticket 
LEFT JOIN parcel_job
ON parcel_job.job_id = parcel_job_ticket.job_id
WHERE start IS NOT NULL AND end IS NOT NULL AND start <= end AND order_date > '2012-01-01'";

$res = query($qry);

while($item = mysql_fetch_object($res)){
	for($i=$item->start;$i<=$item->end;$i++){
		$qry = "INSERT INTO parcel_tickets(job_id,type,ticket_no) VALUES({$item->job_id}, '{$item->type}', $i);";
		query($qry);
	}
}
?>

