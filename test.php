
<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
//require_once('includes/mail_aid_functions.php');
/*
$fp = fsockopen("mail.coural.co.nz", 25, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: www.example.com\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}
*/
require_once('includes/phpMailer/class.phpmailer.php');

// Email Settings
$site['from_name'] = 'Coural'; // from email name
$site['from_email'] = $ADMIN_EMAIL; // from email address
 
// Just in case we need to relay to a different server,
// provide an option to use external mail server.
$site['smtp_mode'] = 'enabled'; // enabled or disabled
//$site['smtp_host'] = "ssl://smtp.ramsu.co.nz:465";
//$site['smtp_host'] = "ssl://smtp.gmail.com:465";
$site['smtp_host'] = 'mail.coural.co.nz';

$site['smtp_port'] = intval(25);
//$site['smtp_username'] = "support_send@coural.co.nz";
//$site['smtp_username'] = "ruralcouriers@gmail.com";
$site['smtp_username'] = 'Admin@coural.co.nz';

//$site['smtp_password'] = "zt90undr";
//$site['smtp_password'] = "oeckel6b&z";
$site['smtp_password'] = "8KP_Adm1n";

class FreakMailer extends PHPMailer
{
    var $priority = 1;
    var $to_name;
    var $to_email;
    var $From = null;
    var $FromName = null;
    var $Sender = null;
	

    function FreakMailer()
    {
        global $site;
		$this->isSMTP();
        // Comes from config.php $site array

        if($site['smtp_mode'] == 'enabled')
        {
            $this->Host = $site['smtp_host'];
            $this->Port = intval($site['smtp_port']);
			
            if($site['smtp_username'] != '')
            {
                //$this->SMTPAuth = true;
                $this->Username = $site['smtp_username'];
                $this->Password = $site['smtp_password'];
            }
            $this->Mailer = "smtp";
        }
        if(!$this->From)
        {
            $this->From = $site['from_email'];
        }
        if(!$this->FromName)
        {
            $this-> FromName = $site['from_name'];
        }
        if(!$this->Sender)
        {
            $this->Sender = $site['from_email'];
        }
        $this->Priority = $this->priority;
    }
}
function send_test_mail(){
	$mailer = new FreakMailer();
	$mailer->SMTPKeepAlive = true; 
	$mailer->Subject = 	"TEST New Server";
	$mailer->Body = "Hello. We have implemented a new email server.";
	$mailer->From = "coural@coural.co.nz";
	$mailer->SMTPDebug = 2;
	$mailer->AuthType = "NTLM";
	$mailer->AddAddress("helge.dzierzon@plantandfood.co.nz", 'Coural Head Office');
	if(!$mailer->Send())
	{
		echo "<font color='red'>Mail to $company failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
	}
	else{
		echo "<font color='green'>SUCCESS</font><br />";
	}
}
send_test_mail();
?>