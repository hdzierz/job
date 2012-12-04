<?
include $dir."includes/header.php";
include $dir."includes/mail_aid_functions.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "proc_job/functions.php";
include "proc_job/functions_send.php";


?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "proc_job/submenu.php";
?>		
	</div>
	<div id="content">
<?
		include "proc_job/actions.php";
		include "proc_job/print.php";
?>	
		<div id="interaction">
<?
			include "proc_job/interaction.php";
?>			
		</div>		
<?
		include "proc_job/main.php";
?>
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

