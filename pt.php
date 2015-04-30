<?php
    require_once('includes/mysql.php');
    require_once('includes/mysql_aid_functions.php');

    $log = fopen("log/pt.log", "a+"); 

    fwrite($log, "Writing ticket log started at ".date("Y-m-d H:i:s")."\n");
    $qry = "SELECT * 
            FROM parcel_job_ticket
            LEFT JOIN parcel_job
                ON parcel_job.job_id=parcel_job_ticket.job_id
                WHERE parcel_job.order_date>'2011-03-31'";
    $res = query($qry);

    while($o = mysql_fetch_object($res)){
        //fwrite($log, $o->job_id."/".$o->start.":".$o->end."\n");
        $r = array();
        for($i = $o->start; $i<=$o->end;$i++){
            //$r[] = "({$o->job_id}, '{$o->type}', {$i})";
            $qry2 = "INSERT INTO parcel_tickets(job_id, type, ticket_no)
                     VALUES({$o->job_id}, '{$o->type}', {$i})";
            query($qry2);
        }

        //$rows = implode(',', $r);
        //$qry2 = "INSERT INTO parcel_tickets(job_id, type, ticket_no)
        //             VALUES{$rows}";
        //query($qry2);
    }
    fwrite($log, "Writing ticket log ended at ".date("Y-m-d H:i:s")."\n");
    fclose($log);
?>
