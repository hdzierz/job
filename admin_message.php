<?
include $dir."includes/header.php";
include $dir."includes/mail_aid_functions.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "admin_message/functions.php";
include "admin_message/functions_send.php";


?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "admin_message/submenu.php";
?>		
	</div>
	<div id="content">
<?
		include "admin_message/actions.php";
		include "admin_message/print.php";
?>	
		<div id="interaction">
<?
			include "admin_message/interaction.php";
?>			
		</div>		
<?
		include "admin_message/main.php";
?>
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

