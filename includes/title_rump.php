<?
	$tt_start = microtime(true);
	//die("System down for maintenance until ~11.40am 6 April 2011");
	include $dir."includes/Select.php";
	require_once $dir."includes/MySQLSelect.php";
	require_once $dir."includes/MySQLTable.php";
	require_once $dir."includes/MySQLExport.php";
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html>
<head>
	<title>COURAL Rural Courier</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<link rel="shortcut icon" href="/job/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/job/favicon.ico" type="image/x-icon">
	
	<link href="https://jobs.coural.co.nz/job_test/css_rump/styles_screen.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="https://jobs.coural.co.nz/job_test/css_rump/styles_print.css" media="print" rel="stylesheet" type="text/css" />
	<link rel="alternate stylesheet" type="text/css" href="css/print-preview.css" media="screen" title="Print Preview" />
	
	<!--<link href="css/MySQLTable_screen.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="css/MySQLTable_print.css" media="print" rel="stylesheet" type="text/css" />-->
	<link href="css/autosuggest.css" media="screen" rel="stylesheet" type="text/css" />
	
	<link href="js.js" type="text/javascript" />
	<link href="menulist.js" type="text/javascript" />
	

	
</head>

<script type="text/javascript" src="javascripts/print.js"></script>	
<script language="javascript" src="javascripts/clock.js" ></script>
<script language="javascript" src="javascripts/zxml.js">
</script>
<script language="javascript" src="javascripts/autosuggest.js">
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--

var db = (document.body) ? 1 : 0;
var scroll = (window.scrollTo) ? 1 : 0;

function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else {
    begin += 2;
  }
  var end = document.cookie.indexOf(";", begin);
  if (end == -1) end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function saveScroll() {
  if (!scroll) return;
  var now = new Date();
  now.setTime(now.getTime() + 365 * 24 * 60 * 60 * 1000);
 // var x = (db) ? document.body.scrollLeft : pageXOffset;
	 var x = document.body.scrollLeft;
  	var y = document.body.scrollTop;
  setCookie("xy", x + "_" + y, now);
}

function loadScroll() {
  if (!scroll) return;
  var xy = getCookie("xy");
  if (!xy) return;
  var ar = xy.split("_");
  if (ar.length == 2) scrollTo(parseInt(ar[0]), parseInt(ar[1]));
}


// -->
</SCRIPT>




<body onload="loadScroll()" onunload="saveScroll()"  >

<!--<body onload="StartClock()" onunload="KillClock()"  leftmargin="4" topmargin="4" marginwidth="4" marginheight="4" onLoad="MM_preloadImages('../images/logo.gif')">-->

<div id="wrapper0">
