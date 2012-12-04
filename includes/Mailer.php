<?

require_once('includes/phpMailer/class.phpmailer.php');
// Configuration settings for My Site
 
class Mailer extends PHPMailer
{
    var $priority = 3;
    var $to_name;
    var $to_email;
    var $From = 'helge.dzierzon@computercare.co.nz';
    var $FromName = 'Coural';
    var $Sender = null;
	
	var $SmtpUsername = "support_send@coural.co.nz";
	var $SmtpPort = 465;
	var $SmtpPassword = "zt90undr";
	var $SmtpMode = 'enabled'; // enabled or disabled
	var $SmtpHost = "ssl://smtp.ramsu.co.nz:465";

    function Mailer()
    {
		$this->isSMTP();
        // Comes from config.php $site array

        if($this->SmtpMode == 'enabled')
        {
           $this->Host = $this->SmtpHost;
            $this->Port = $this->SmtpPort;
			
            if($this->SmtpUsername != '')
            {
                $this->SMTPAuth = true;
                $this->Username = $this->SmtpUsername;
                $this->Password = $this->SmtpPassword;
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

?>