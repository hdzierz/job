<?
include $dir."includes/header.php";
# include html title which also includes body and html tags
include "proc_invoice/functions.php";
include "proc_invoice/actions.php";
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";


?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "proc_invoice/submenu.php";
?>		
	</div>
	<div id="content">
		<div id="interaction">
<?
			include "proc_invoice/interaction.php";
?>			
		</div>				
<?
		include "proc_invoice/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

