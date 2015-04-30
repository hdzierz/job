
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

die()
$qry = "SELECT parcel_job.job_id,parcel_tickets.ticket_no, is_redeemed_D, order_date FROM parcel_tickets
LEFT JOIN parcel_job
ON parcel_job.job_id=parcel_tickets.job_id
LEFT JOIN parcel_job_route 
ON parcel_job_route.ticket_no = parcel_tickets.ticket_no
WHERE parcel_job.order_date > '2012-03-31' AND parcel_job.order_date <= '2013-03-31'
AND is_redeemed_D IS NULL AND is_redeemed_P IS NULL";

$res = query($qry);
$file = fopen("/tmp/tickets_unredeemed.csv", "a");
while($item = mysql_fetch_array($res)){
	fputcsv($file,$item);
}

fclose($file);

//send_test_mail();
?>
