<?


require_once('includes/phpMailer/class.phpmailer.php');
// Configuration settings for My Site
$ADMIN_EMAIL = "howard@coural.co.nz";

$FAX_EMAIL_ADDRESS = get("config","value","WHERE name='FAX_EMAIL_ADDRESS'");//"fax.2talk.co.nz";
$FAX_NUM_EMAIL_MODE = get("config","value","WHERE name='FAX_NUM_EMAIL_MODE'");//"PLAIN";

// Email Settings
$site['from_name'] = 'Coural'; // from email name
$site['from_email'] = $ADMIN_EMAIL; // from email address
 
// Just in case we need to relay to a different server,
// provide an option to use external mail server.
//$site['smtp_mode'] = 'enabled'; // enabled or disabled
//$site['smtp_host'] = "ssl://smtp.ramsu.co.nz:465";
//$site['smtp_host'] = "ssl://smtp.gmail.com:465";
//$site['smtp_host'] = 'mail.coural.co.nz';

//$site['smtp_port'] = intval(25);
//$site['smtp_username'] = "support_send@coural.co.nz";
//$site['smtp_username'] = "ruralcouriers@gmail.com";
//$site['smtp_username'] = 'Admin@coural.co.nz';

//$site['smtp_password'] = "zt90undr";
//$site['smtp_password'] = "8KP_Adm1n";

$site['smtp_mode'] = 'enabled'; // enabled or disabled

$ADMIN_EMAIL = "hdzierz@gmail.com";

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
		
		//$this->isSMTP();
		//$this->AuthType = "NTLM";
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

function turn_fax($dir,$file){
	$bfile = basename($file);
	
	$infile = "C:\\Program Files\\Apache Software Foundation\\Apache2.2\\htdocs\\mailtracker\\trunk\\".$dir.'/'.$file;
	$outfile = "C:\\Program Files\\Apache Software Foundation\\Apache2.2\\htdocs\\mailtracker\\trunk\\".$dir.'/'.$bfile.'tr.pdf';
	$WshShell = new COM("WScript.Shell");
	//$oExec = $WshShell->Run("cmd /C pdftk tt2.pdf cat 1-endS output tt_out.pdf > tt.txt", 0, false);
	$oExec = $WshShell->Run("cmd /C \"pdftk\" \"$infile\" cat 1-endE output \"$outfile\"> \"C:\\temp\\tt.txt\"", 0, false);

	if($oExec==0){
		return $bfile.'tr.pdf';
	}
	return false;
}


$IS_FAX = false;
function get_email($id,$is_alt=false){
	global $ADMIN_EMAIL;
	global $FAX_NUM_EMAIL_MODE;
	global $FAX_EMAIL_ADDRESS;
	global $IS_FAX;

	if($is_alt)
		$mail_type = get("address","alt_mail_type","WHERE operator_id='$id'");
	else
		$mail_type = get("address","mail_type","WHERE operator_id='$id'");
		
	switch($mail_type){
		case 'e':
			if($is_alt)
				$email = get("address","alt_email","WHERE operator_id='$id'");
			else
				$email = get("address","email","WHERE operator_id='$id'");
			//$email = $ADMIN_EMAIL;
		break;
		case 'f':
			$IS_FAX=true;
			if($is_alt)
				$fax = get("address","alt_fax","WHERE operator_id='$id'");
			else
				$fax = get("address","fax","WHERE operator_id='$id'");

			$fax = str_replace(".",'',$fax);
			$fax = str_replace(" ",'',$fax);
			
			switch($FAX_NUM_EMAIL_MODE){
				case "PLAIN":
					$fax = $fax;
					break;
				case "WITHOUTZERO":
					$fax = substr($fax,1);
					break;
				case "INT":
					$fax = substr($fax,1);
					$fax = '0064'.$fax;
					break;
				case "INTWITHOUTZERO":
					$fax = substr($fax,1);
					$fax = '64'.$fax;
					break;
				default:
					 return "coural@coural.co.nz";
					break;
			}
			
			//$fax = substr($fax,1);
			//$email = '001164'.$fax."@fax.mbox.co.nz";
			//$email = "00116463566618@fax.mbox.co.nz";
			$email = $fax."@".$FAX_EMAIL_ADDRESS;
		break;
		case 'm':
			$email = false;
		break;
		default:
			$email = false;
		break;
	}
	//echo $FAX_NUM_EMAIL_MODE.' / '.$email; die();

	return $email;
}

function get_body($id,$target,$is_alt=false){
	if($is_alt)
		$mail_type = get("address","alt_mail_type","WHERE operator_id='$id'");
	else
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


function get_subject($id, $target){
	$subject = "COURAL MESSAGE: ".$target;
	return $subject;
}
function send_test_mail(){
	$mailer = new FreakMailer();
	//$mailer->SMTPKeepAlive = true; 
	$mailer->Subject = 	"TEST";
	$mailer->Body = "TEST";
	$mailer->From = "coural@coural.co.nz";
	//$mailer->SMTPDebug = 2;
	//$mailer->AuthType = "NTLM";
	$mailer->AddAddress("helge.dzierzon@plantandfood.co.nz", 'Coural Head Office');
	if(!$mailer->Send())
	{
		echo "<font color='red'>Mail to $company failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
	}
	else{
		echo "<font color='green'>SUCCESS</font><br />";
	}
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
			$alt_email = get_email($id,true);
		}
		
	}
	
	$mailer = new FreakMailer();
	if($email){
		
		
		//$mailer->SMTPKeepAlive = true; 
		$mailer->Subject = 	get_subject($id,$target);
		$mailer->Body = get_body($id,$target);
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
		
		$mail_type = get("address","mail_type","WHERE operator_id='$id'");
		
		if($mail_type=='f' && $FAX_EMAIL_ADDRESS=="fax.2talk.co.nz"){
			$nfile = turn_fax($dir,$file);
			if(!$nfile)
				$file = $file;
			else
				$file = $nfile;
		}
		
		echo "OUTPUT FILE: ".$file."<br />";
		
		$IS_FAX=false;
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

function get_email_simple($id,$type="SMS"){
	global $SMS_EMAIL_ADDRESS;
	global $FAX_EMAIL_ADDRESS;
	//echo $type;
	switch($type){
		case "EMAIL":
			$email=get("address","email","WHERE operator_id=$id");
			if(!$email) return false;
			break;
		case "SMS":
			$sms=get("address","mobile","WHERE operator_id=$id");
			if(!$sms) return false;
			$sms = str_replace(' ','',$sms);
			$sms = str_replace('.','',$sms);
			$buffer = explode('#',$sms);
			$sms = $buffer[0];
			//echo $sms;
			$email = $sms."@".$SMS_EMAIL_ADDRESS;
			break;
		case "EMAIL or SMS":
			$email = get_email_simple($id,"EMAIL");
			if(!$email){
				$email = get_email_simple($id,"SMS");
			}
			
			break;
		case "FAX":
			$fax=get("address","fax","WHERE operator_id=$id");
			$fax = str_replace(' ','',$fax);
			$fax = str_replace('.','',$fax);
			if(!$fax) return false;
			//$buffer = explode('#',$fax);
			//$fax = $fax[0];
			$email = $fax."@".$FAX_EMAIL_ADDRESS;
			break;
		default:
			$email = false;
			break;
	}
	return $email;
}

function send_operator_simple_mail($type,$id,$subject,$body,$email=false){
	global $ADMIN_EMAIL;

	if(!$email)
		$email = get_email_simple($id,$type);
	//echo $email."<br />"; return;	
	$company = get("operator","company","WHERE operator_id=$id");
	
	$mailer = new FreakMailer();
	if($email){
		
		//echo $email."<br />";
		$mailer->SMTPKeepAlive = true; 
		$mailer->Subject = 	$subject;
		$mailer->Body = $body;
		$mailer->From = "coural@coural.co.nz";
		//$mailer->SMTPDebug = 2;
		
		$mailer->AddAddress($email, 'Coural Head Office');
	
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
		

	}
	else{
		echo 'Mail to <strong>COURAL ADMIN</strong> sent ('.$email.') due to missing or faulty email address!<br />';
	}
}

?>
