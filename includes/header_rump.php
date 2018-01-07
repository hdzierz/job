<?php

### Header file ###
session_start();
//import_request_variables("pg");
extract($_GET);
extract($_POST);

date_default_timezone_set ("Pacific/Auckland");

# connect to mysql database
include $dir."includes/mysql.php";
include $dir."includes/aid_functions.php";
include $dir."includes/mysql_aid_functions.php";

require_once('includes/phpMailer/class.phpmailer.php');
require_once("includes/Mailer.php");

session_cache_limiter('private');
header("Cache-control:private");

$SESSION_ID = session_id();

# get page name
$PAGE_NAME = substr($_SERVER["REQUEST_URI"],1);

if (!$PAGE_NAME or $PAGE_NAME == "/") $PAGE_NAME = "index.php";
if (strstr($PAGE_NAME,"?")) $PAGE_NAME = substr($PAGE_NAME,0,strpos($PAGE_NAME,"?"));


# Get the correct path for include files
$num = substr_count($_SERVER["REQUEST_URI"], "/");
for($x=1;$x<$num;$x++) {
        $DIR .= "../";
}
$DIR="";

$MAILER = new Mailer();

$GST_PARCEL = get("config","value","WHERE name='GST_PARCEL'");
$GST_CIRCULAR = get("config","value","WHERE name='GST_CIRCULAR'");

$FAX_EMAIL_ADDRESS = get("config","value","WHERE name='FAX_EMAIL_ADDRESS'");//"fax.2talk.co.nz";
$FAX_NUMBER_EMAIL_MODE = get("config","value","WHERE name='FAX_NUM_EMAIL_MODE'");//"PLAIN";
$SMS_EMAIL_ADDRESS = get("config","value","WHERE name='SMS_EMAIL_ADDRESS'");//"fax.2talk.co.nz";

//$SEND_OUTPUT_DIR = "E:/ProgramData/JobSys/";
//$SCAN_OUTPUT_DIR = "E:/ProgramData/JobSys/";

$SEND_OUTPUT_DIR = "";
$SCAN_OUTPUT_DIR = "";

?>


