<?

$dir = "../";
### Header file ###
session_start();
import_request_variables("pg");

# connect to mysql database
//include $dir."includes/mysql.php";
include $dir."includes/mysql.class.php";
include $dir."includes/aid_functions.php";
include $dir."includes/mysql_aid_functions.php";
//require_once "Mail.php";

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


include $dir."includes/fpdf/fpdf.php";
define('FPDF_FONTPATH',$dir.'includes/fpdf/font/');
require_once $dir."includes/MySQLPDFTable.php";
?>