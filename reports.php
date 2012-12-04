<?
include $dir."includes/header.php";
include "reports/functions.php";
include "reports/actions.php";

# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
		include "reports/submenu.php";
?>
	</div>
	<!-- Work around for min-height bug in some IEs -->
	<!--<div id="content_min_height_hack">--> 
	<div id="content">		
		<div id="interaction">
<?
			include "reports/interaction.php";
?>			
		</div>	
<?
		include "reports/main.php";
?>
	</div>
	<!--</div>-->
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>

