<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
require_once('includes/mail_aid_functions.php');


$qry = "SELECT parcel_job.*,parcel_tickets.*, order_date 
FROM parcel_tickets
LEFT JOIN parcel_job
ON parcel_job.job_id=parcel_tickets.job_id
LEFT JOIN parcel_job_route
ON parcel_job_route.ticket_no = parcel_tickets.ticket_no
WHERE parcel_job.order_date > '2014-03-31' AND parcel_job.order_date <= '2015-03-31'
AND is_redeemed_D IS NULL AND is_redeemed_P IS NULL";

$res = query($qry);
$file = fopen("/tmp/tickets_unredeemed.csv", "a");
while($item = mysql_fetch_array($res)){
    fputcsv($file,$item);
}

fclose($file);
?>
