<?php

### Header file ###
session_start();
import_request_variables("pg");

date_default_timezone_set ("Pacific/Auckland");

# connect to mysql database
include $dir."includes/mysql.php";
include $dir."includes/aid_functions.php";
include $dir."includes/mysql_aid_functions.php";

require_once('includes/phpMailer/class.phpmailer.php');
require_once("includes/Mailer.php");

# check if user is logged in
if (!isset($_COOKIE['coural_username'])) {
	header("Location: login.php");
}


session_cache_limiter('private');
header("Cache-control:private");

$SESSION_ID = session_id();

# get the session vars 
$CK_USERNAME       	 = $_COOKIE['coural_username'];
$CK_USERID       	 = $_COOKIE['coural_userid'];
$CK_FULL_NAME      	 = $_COOKIE['coural_fullname'];
$CK_SEC_LEVEL      	 = $_COOKIE['coural_security'];
$CK_PAGE_INDEX     	 = $_COOKIE['coural_page_main'];
$CK_PAGE_USERADMIN 	 = $_COOKIE['coural_page_useradmin'];
$CK_PAGE_ROUTEADMIN	 = $_COOKIE['coural_page_routeadmin'];
$CK_PAGE_CLIENTADMIN = $_COOKIE['coural_page_clientadmin'];
$CK_PAGE_ADDRADMIN 	 = $_COOKIE['coural_page_addradmin'];
$CK_PAGE_OPADMIN 	 = $_COOKIE['coural_page_opadmin'];
$CK_PAGE_INVOICEPROC = $_COOKIE['coural_page_procinvoice'];
$CK_PAGE_JOBPROC 	 = $_COOKIE['coural_page_procjob'];
$CK_PAGE_INVOICE 	 = $_COOKIE['coural_page_invoice'];
$CK_PAGE_REPORTS 	 = $_COOKIE['coural_page_reports'];
$CK_PAGE_REVENUE 	 = $_COOKIE['coural_page_rep_revenue'];

$CK_PAGE_REPPARCELS  = $_COOKIE['coural_page_rep_parcels'];
$CK_PAGE_PARCELS	 = $_COOKIE['coural_page_parcels'];
$CK_CHANGE_GST	 	 = $_COOKIE['coural_change_gst'];

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


