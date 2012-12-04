<?
include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "admin_route/functions.php";
include "admin_operator/functions.php";

?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "admin_route/submenu.php";
?>		
	</div>	
	<div id="content">
<?

	include "admin_route/actions.php";

?>	
		<div id="interaction">
<?
			include "admin_route/interaction.php";
?>			
		</div>			
<?
	include "admin_route/main.php";
?>
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

