<?
include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "proc_temp/functions.php";


?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "proc_temp/submenu.php";
?>		
	</div>
	<div id="content">
<?
		include "proc_temp/actions.php";
		include "proc_temp/print.php";
?>	
		<div id="interaction">
<?
			include "proc_temp/interaction.php";
?>			
		</div>		
<?
		include "proc_temp/main.php";
?>
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

