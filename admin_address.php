<?
include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "admin_address/functions.php";

?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
		include "admin_address/submenu.php";
?>		
	</div>	
	<div id="content">
<?
		include "admin_address/actions.php";
?>	
		<div id="interaction">
<?
			include "admin_address/interaction.php";
?>			
		</div>			
<?
		include "admin_address/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

