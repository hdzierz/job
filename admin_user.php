<?
include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "admin_user/functions.php";

?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
		include "admin_user/submenu.php";
?>		
	</div>	
	<div id="content">
	
<?
		include "admin_user/actions.php";
?>
		<div id="interaction">
<?
			include "admin_user/interaction.php";
?>			
		</div>			
<?
		include "admin_user/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

