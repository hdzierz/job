<?
#
# This page was created 2/2/2007 by Andrew
# It just records the name of the user, the page they are visiting, the date, and the time.
# Its so i can tell with more accuracy if and where the user is browsing so i can run tests on certain pages
#
#

$his = "INSERT INTO control_history (page, request, username) VALUES ('$PAGE_NAME','$action','$CK_USERNAME')";
$his2 = mysql_query($his);
if (!$his2) die(mysql_error());


?>