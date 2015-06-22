<?
# connect to mysql database
include $dir."includes/mysql.php";
include $dir."includes/aid_functions.php";
include $dir."includes/mysql_aid_functions.php";

require_once('includes/phpMailer/class.phpmailer.php');
require_once("includes/Mailer.php");
include $dir."includes/mail_aid_functions.php";


$qry = "SELECT * FROM schedule_mail_send_out WHERE status=1";
$res = query($qry);

$msg = "EMAIL LOG SUMMARY DELIVERY INSTRUCTIONS\n";

$ct = 0;

while($item = mysql_fetch_object($res)){
    $config = explode(',',$item->config);
    $company = get("operator", "company", "WHERE operator_id={$config[3]}");
    $ok = send_operator_mail($config[0],$config[1],$config[2],$config[3]); 
    $qry = "UPDATE schedule_mail_send_out SET status=0 WHERE oid={$item->oid}";
    query($qry);
    if($ok){
    	$msg .= "MESSAGE TO $company sent.\n";
    }
    else{
        $msg .= "MESSAGE TO $company not sent due to faulty email address.\n";
    }
	$ct++;
}

if($ct>0){
		$mailer = new FreakMailer();
		$mailer->Subject =  "EMAIL LOG SUMMARY DELIVERY INSTRUCTIONS";
		$mailer->Body = $msg;
		$mailer->From = "cloud@coural.co.nz";
		$mailer->AddReplyTo('cloud@coural.co.nz', 'noreply');
		$mailer->AddAddress("dayna@coural.co.nz", 'Coural Head Office');
		$mailer->AddAddress("howard@coural.co.nz", 'Coural Head Office');
		$mailer->AddAddress("hdzierz@gmail.com", 'Coural Head Office');
		$mailer->Send();
}
?>

