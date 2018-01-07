<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
require_once('includes/mail_aid_functions.php');
require_once('parcels/classes.php');

ini_set('max_execution_time', 3*60*60);

$files = $_POST['file'];
$filecs = $_POST['filec'];

$files = glob("MobileScan/Export*.csv");

print_r($files);

$date = $year.'-'.$month.'-15';
$qry = "UPDATE parcel_run_date SET dtt='$date' WHERE type='mobile'";
query($qry);
   
foreach($files as $f){
        $file = basename($f);
	    $batch_no = mobileFileReader::getBatchNo($file);
	    if (($handle = fopen("MobileScan/".$file, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
		    if($data[0] != "Batch_ID"){
			$ticket = new mobileTicket($data);
			$ticket->redeem($batch_no, $year, $month);
		    }
		}
		fclose($handle);
	    }
	    rename("MobileScan/".$file, "MobileScan/Processed_".$file);
	    $MESSAGE.= "File $file processed.\n";
} 


//send_mail($MESSAGE);


function send_mail($body){
    $mailer = new FreakMailer();
    //$mailer->SMTPKeepAlive = true; 
    $mailer->Subject =  "Mobile Redemption run ".date("Y-m-d h:i:m");
    $mailer->Body = $body;
    //$mailer->From = "dochelge@gmail.com";
    //$mailer->SMTPDebug = 2;
    //$mailer->AuthType = "NTLM";
    //$mailer->SMTPSecure = "tls";  
    $mailer->AddAddress("coural@coural.co.nz", 'Coural Head Office');
    $mailer->AddAddress("hdzierz@gmail.com", 'Coural Head Office');
    if(!$mailer->Send())
    {
        echo "<font color='red'>Mail to $company failed (".$email.") : ".$mailer->ErrorInfo."</font><br />";
    }
    else{
        echo "<font color='green'>SUCCESS</font><br />";
    }
}



?>


