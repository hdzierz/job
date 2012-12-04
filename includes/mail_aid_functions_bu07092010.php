<?
require_once('includes/phpMailer/class.phpmailer.php');
// Configuration settings for My Site
$ADMIN_EMAIL = "howard@coural.co.nz";

// Email Settings
$site['from_name'] = 'Coural'; // from email name
$site['from_email'] = $ADMIN_EMAIL; // from email address
 
// Just in case we need to relay to a different server,
// provide an option to use external mail server.
$site['smtp_mode'] = 'enabled'; // enabled or disabled
$site['smtp_host'] = "ssl://smtp.ramsu.co.nz:465";

$site['smtp_port'] = intval(465);
$site['smtp_username'] = "support_send@coural.co.nz";
$site['smtp_password'] = "zt90undr";

//$ADMIN_EMAIL = "hdzierz@gmail.com";

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
                $this->SMTPAuth = true;
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

function get_email($id){
	global $ADMIN_EMAIL;
	$mail_type = get("address","mail_type","WHERE operator_id='$id'");
	
	switch($mail_type){
		case 'e':
			$email = get("address","email","WHERE operator_id='$id'");
			//$email = $ADMIN_EMAIL;
		break;
		case 'f':
			$fax = get("address","fax","WHERE operator_id='$id'");
			$fax = str_replace(".",'',$fax);
			$fax = str_replace(" ",'',$fax);
			$fax = substr($fax,1);
			//$email = '001164'.$fax."@fax.mbox.co.nz";
			//$email = "00116463566618@fax.mbox.co.nz";
			$email = '64'.$fax."@kiwifax.net";
		break;
		case 'm':
			$email = false;
		break;
		default:
			$email = $ADMIN_EMAIL;
		break;
	}
	
	return $email;
}

function get_alt_email($id){
	global $ADMIN_EMAIL;
	$alt_mail_type = get("address","alt_mail_type","WHERE operator_id='$id'");
	
	switch($alt_mail_type){
		case 'e':
			$email = get("address","alt_email","WHERE operator_id='$id'");
			//$email = $ADMIN_EMAIL;
		break;
		case 'f':
			$fax = get("address","alt_fax","WHERE operator_id='$id'");
			$fax = str_replace(".",'',$fax);
			$fax = str_replace(" ",'',$fax);
			$fax = substr($fax,1);
			//$email = '001164'.$fax."@fax.mbox.co.nz";
			//$email = "00116463566618@fax.mbox.co.nz";
			$email = '64'.$fax."@kiwifax.net";
		break;
		case 'm':
			$email = false;
		break;
		default:
			$email = false;
		break;
	}
	
	return $email;
}

function get_body($target,$id){
	$mail_type = get("address","mail_type","WHERE operator_id='$id'");
	
	switch($mail_type){
		case 'e':
			$body = $target;
		break;
		case 'f':
			//$body = "Password=inkl67z";
			$body = "";
		break;
		case 'm':
			$body = "MAIL";
		break;
		default:
			$body = "NO MAIL TYPE SPECIFIED";
		break;
	}
	
	return $body;
}

function get_body_alt($target,$id){
	$mail_type = get("address","alt_mail_type","WHERE operator_id='$id'");
	
	switch($mail_type){
		case 'e':
			$body = $target;
		break;
		case 'f':
			//$body = "Password=inkl67z";
			$body = "";
		break;
		case 'm':
			$body = "MAIL";
		break;
		default:
			$body = "NO MAIL TYPE SPECIFIED";
		break;
	}
	
	return $body;
}

function send_operator_mail($target,$dir,$file,$id,$email=false){
	global $ADMIN_EMAIL;
	$alt_send_required = false;
	
	if($target=="JOB DROP OFF DETAILS"){
		$company = get("client","name","WHERE client_id='$id'");
		if(!$email) $email = get("client","email","WHERE client_id='$id'");
	}
	else{
		$company = get("operator","company","WHERE operator_id='$id'");
		$alt_email=$email;
		if(!$email) 
		{
			$email = get_email($id);
			$alt_email = get_alt_email($id);
		}
		
	}
	
	$mailer = new FreakMailer();
	if($email){
		
		
		$mailer->SMTPKeepAlive = true; 
		$mailer->Subject = "COURAL MESSAGE: ".$target;
		$mailer->Body = get_body($target,$id);
		$mailer->From = "coural@coural.co.nz";
		//$mailer->SMTPDebug = 2;
		
		$mailer->AddAddress($email, 'Coural Head Office');
		if($alt_email)
		{
			// Check if the alt is a fax number email (needs to be sent as a seperate email)
			//if(strpbrk($alt_email,'@') == "@fax.mbox.co.nz")
			if(strpbrk($alt_email,'@') == "@kiwifax.net")
			{
				$alt_send_required = true;
				echo 'Mail to <strong>'.$company.'</strong> alternative fax required!<br />';
			}
			else 
			{
				$mailer->AddAddress($alt_email, 'Coural Head Office');
			}
		}
		

		$mailer->AddAttachment($dir.'/'.$file, $file);
	
		if(!$mailer->Send())
		{
			echo "<font color='red'>Mail to $company failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
			$mailer->Body = "WRONG EMAIL ADDRESS";
			$mailer->AddAddress($ADMIN_EMAIL, 'Coural Head Office');
			echo 'Mail to <strong>COURAL ADMIN</strong> sent ('.$email.') due to missing or faulty email address!<br />';
			if(!$mailer->Send()){
				echo "<font color='red'>Mail to COURAL ADMIN failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
			}
		}
		else
		{
			echo 'Mail to <strong>'.$company.'</strong> sent ('.$email.')!<br />';
		}
		
		$mailer->ClearAddresses();
		
		if($alt_send_required)
		{
			// send (alt) email address is a fax number email, send as a seperate email.
			$mailer->AddAddress($alt_email, 'Coural Head Office');
			//$mailer->AddCC("hdzierz@gmail.com", 'Helge Dzierzon');
			$mailer->Body = get_body_alt($target, $id);
			
			if(!$mailer->Send())
			{
				echo "<font color='red'>Mail to $company failed (".$alt_email.") : ".$mailer->ErrorInfo."</font><br />";
				$mailer->Body = "WRONG EMAIL ADDRESS";
				$mailer->AddAddress($ADMIN_EMAIL, 'Coural Head Office');
				echo 'Mail to <strong>COURAL ADMIN</strong> alternative fax no sent ('.$alt_email.') due to missing or faulty email address!<br />';
				if(!$mailer->Send()){
					echo "<font color='red'>Mail to COURAL ADMIN failed (".$alt_email.") : ".$mailer->ErrorInfo."</font><br />";
				}
			}
			else
			{
				echo 'Mail to <strong>'.$company.'</strong> alternative fax no sent ('.$alt_email.')!<br />';
			}
			$mailer->ClearAddresses();
		}
		
		$mailer->ClearAttachments();
	}
	else{
		echo 'Mail to <strong>COURAL ADMIN</strong> sent ('.$email.') due to missing or faulty email address!<br />';
		if(!$mailer->Send()){
			echo "<font color='red'>Mail to COURAL ADMIN failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
		}
	}
}

?>