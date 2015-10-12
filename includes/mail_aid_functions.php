<?


require_once('includes/phpMailer/class.phpmailer.php');
// Configuration settings for My Site
$ADMIN_EMAIL = "howard@coural.co.nz";

$FAX_EMAIL_ADDRESS = get("config","value","WHERE name='FAX_EMAIL_ADDRESS'");//"fax.2talk.co.nz";
$FAX_NUM_EMAIL_MODE = get("config","value","WHERE name='FAX_NUM_EMAIL_MODE'");//"PLAIN";

// Email Settings
$site['from_name'] = 'Coural'; // from email name
$site['from_email'] = 'cloud@coural.co.nz'; // from email address
 
// Just in case we need to relay to a different server,
// provide an option to use external mail server.
//$site['smtp_mode'] = 'enabled'; // enabled or disabled
//$site['smtp_host'] = "mail.dzierzon.co.nz:587";

//$site['smtp_port'] = intval(587);
//$site['smtp_username'] = 'hdzierz@dzierzon.co.nz';
//$site['smtp_password'] = "zt90undr";

$site['smtp_host'] = "mail.coural.co.nz:587";
$site['smtp_username'] = 'cloud@coural.co.nz';
$site['smtp_password'] = "Rur4lD3l1v3ry";

//$site['smtp_host'] = "localhost";

//$ADMIN_EMAIL = "hdzierz@gmail.com";


function log_mail($adr, $subject, $err_info, $err_count){
    $qry = "INSERT INTO email_log(address, subject, error_count, error_info) VALUES('%s','%s', %d, '%s')";
    foreach($adr as $a){
        if(is_array($a) && count($a) == 2){
            $q = sprintf($qry, $a[0], $subject, $err_count, $err_info);
            query($q);
        }
    }
    //$f = fopen('tmp/email.log', 'a+');
    //fwrite($f, $adr.': '.date('Y-m-d')."\n\n\n");
    //fclose($f);
}



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
		
        // Comes from config.php $site array

        if($site['smtp_mode'] == 'enabled')
        {
            $this->isSMTP();
            $this->Host = $site['smtp_host'];
            $this->Port = intval($site['smtp_port']);
			
            if($site['smtp_username'] != '')
            {
                $this->SMTPAuth = true;
                //$this->SMTPSecure = "tls";
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
    
    function Send(){
        $to = implode(',',$this->to);
        #$this->ClearAddresses();
        #$this->AddAddress("hdzierz@gmail.com", "Head office");
        log_mail($this->to, $this->Subject, $this->ErrorInfo, $this->error_count);
        return parent::Send();
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
			if($fax)
				$email = $fax."@".$FAX_EMAIL_ADDRESS;
			else
				$email = "coural@coural.co.nz";
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
	$mailer->From = "cloud@coural.co.nz";
	//$mailer->SMTPDebug = 2;
	//$mailer->AuthType = "NTLM";
    //$mail->SMTPSecure = "tls";  
    $mailer->AddReplyTo('cloud@coural.co.nz', 'oreply');
	$mailer->AddAddress("hdzierz@gmail.com", 'Coural Head Office');
	if(!$mailer->Send())
	{
		echo "<font color='red'>Mail to $company failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
	}
	else{
		echo "<font color='green'>SUCCESS</font><br />";
	}
}

$MAIL_DEBUG = false;
function send_operator_mail($target,$dir,$file,$id,$email=false){
	global $ADMIN_EMAIL;
    	global $MAIL_DEBUG;
	$alt_send_required = false;
	
	$ok = true;
	if($target=="JOB DROP OFF DETAILS"){
		$company = get("client","name","WHERE client_id='$id'");
		$email = get("client","email","WHERE client_id='$id'", 1);
	}
	else{
		$company = get("operator","company","WHERE operator_id='$id'");
		$alt_email=$email;
		if(!$email) 
		{
			$email = get_email($id);
			$alt_email = get_email($id,true);
            if(!$email){
                $email = $alt_email;
                $alt_email = false;
            }
		}
		
	}

	$mailer = new FreakMailer();
    $mailer->SMTPDebug = 2;
	if($email){
		
		//$mailer->SMTPKeepAlive = true; 
		$mailer->Subject = 	get_subject($id,$target);
		$mailer->Body = get_body($id,$target);
		$mailer->From = "coural@coural.co.nz";
        $mailer->AddReplyTo('coural@coural.co.nz', 'Coural Head Office');
        $mailer->AddAddress($email, 'Coural Head Office');
		
		//$mailer->AddAddress('hdzierz@gmail.com', 'Coural Head Office');
		//if($alt_email)
		//{
		//	$mailer->AddAddress($alt_email, 'Coural Head Office');
		//}
		
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
			$ok = false;
			echo "<font color='red'>Mail to $company failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
			$mailer->Body = "WRONG EMAIL ADDRESS";
            $mailer->ClearAddresses();
			$mailer->AddAddress($ADMIN_EMAIL, 'Coural Head Office');
            $mailer->Send();
			echo 'Mail forwarded  to <strong>COURAL ADMIN</strong> sent ('.$ADMIN_EMAIL.')!<br />';
			echo "<font color='red'>Mail to COURAL ADMIN failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
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
                $mailer->ClearAddresses();
				$mailer->AddAddress($ADMIN_EMAIL, 'Coural Head Office');
                $mailer->Send();
				echo 'Mail forwarded  to <strong>COURAL ADMIN</strong> alternative fax not sent ('.$alt_email.')!<br />';
				echo "<font color='red'>Mail to COURAL ADMIN failed (".$alt_email.") : ".$mailer->ErrorInfo."</font><br />";
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
            $mailer->ClearAddresses();
            $mailer->AddAddress($ADMIN_EMAIL, 'Coural Head Office');
            $mailer->Send();
			echo "<font color='red'>Mail to COURAL ADMIN failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
		}
		$ok = false;
	}
	return $ok;
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
	$ok = true;
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
