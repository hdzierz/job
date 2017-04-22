<?php
# LOGOUT
extract($_GET);
extract($_POST);
unset($HTTP_SESSION_VARS['valid_user']);

session_destroy();

setcookie("coural_username", "", time() - 3600,"/");
setcookie("coural_fullname", "", time() - 3600,"/");
setcookie("coural_security",'', time() - 3600,"/");
setcookie("coural_key_set",'', time() - 3600,"/");

header("Location: login.php?origin=".urlencode($origin));
?>
