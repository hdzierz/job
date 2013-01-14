
<?php
require_once('includes/mysql.php');
require_once('includes/mysql_aid_functions.php');
require_once('includes/mail_aid_functions.php');
/*
$fp = fsockopen("mail.coural.co.nz", 25, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: www.example.com\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}
*/
send_test_mail();
?>