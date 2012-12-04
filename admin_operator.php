<?
include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "admin_operator/functions.php";


?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
		include "admin_operator/submenu.php";
?>		
	</div>	
	<div id="content">
<?
		// This file also includes the div for interaction
		include "admin_operator/actions.php";
?>
		<div id="interaction">
<?
			include "admin_operator/interaction.php";
?>			
		</div>			
<?
		include "admin_operator/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

