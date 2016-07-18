<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
require_once('includes/mail_aid_functions.php');


ini_set('max_execution_time', 3*60*60);
$create=true;
if(false){
    $qry = "SELECT * 
	    FROM parcel_job_ticket
  	    LEFT JOIN parcel_job
                ON parcel_job.job_id = parcel_job_ticket.job_id
        WHERE start IS NOT NULL AND end IS NOT NULL AND start <= end AND order_date > '2015-01-01'";

	$res = query($qry);

	while($item = mysql_fetch_object($res)){
		$qty = $item->end - $item->start + 1;
        for($i=$item->start;$i<=$item->end;$i++){
                $qry = "INSERT INTO parcel_tickets(job_id,type,ticket_no,qty) VALUES({$item->job_id}, '{$item->type}', $i, $qty);";
                query($qry);
        }
	}
}
if($create){
	$qry = "SELECT job_no,client.name,foreign_order_no,order_date,parcel_tickets.qty,parcel_tickets.type,parcel_tickets.ticket_no,if(SUM(is_redeemed_P) IS NULL AND SUM(is_redeemed_P) IS NULL, 0, 1) AS is_redeemed, real_date AS redemption_date,parcel_job.is_random,dist.company,contr.company,route.code
			FROM parcel_tickets
			LEFT JOIN parcel_job
				ON parcel_job.job_id=parcel_tickets.job_id
			LEFT JOIN client
				ON parcel_job.client_id=client.client_id
			LEFT JOIN parcel_job_route
				ON parcel_job_route.ticket_no = parcel_tickets.ticket_no
			LEFT JOIN parcel_run
				ON parcel_run.parcel_run_id = parcel_job_route.parcel_run_id
			LEFT JOIN route
				ON parcel_job_route.route_id = route.route_id
			LEFT JOIN operator AS dist
				ON dist.operator_id=parcel_job_route.dist_id
			LEFT JOIN operator AS contr
                                ON contr.operator_id=parcel_job_route.contractor_id
			WHERE parcel_job.order_date > '2015-03-31' AND parcel_job.order_date <= '2016-03-31'
			GROUP BY parcel_tickets.ticket_no";
	$res = query($qry,1);
	$file = fopen("/tmp/tickets_unredeemed.csv", "w");
	fwrite($file,"job_no,client.name,foreign_order_no,order_date,qty,type,ticket_no,is_redeemed,redemption_date,is_random,distributor,contractor,route\n");
	$ct=0;
	while($item = mysql_fetch_row($res)){
    		fputcsv($file,$item);
		if($ct%1000==0) echo "$ct\n";
		$ct+=1;
	}

	fclose($file);
}
?>
