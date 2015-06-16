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

while($item = mysql_fetch_object($res)){
    $config = explode(',',$item->config);
    send_operator_mail($config[0],$config[1],$config[2],$config[3]); 
    $qry = "UPDATE schedule_mail_send_out SET status=0 WHERE oid={$item->oid}";
    query($qry);
}

?>

