<?

include $dir."includes/header.php";
include $dir."includes/mail_aid_functions.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";

include "parcels/functions.php";
include "parcels/classes.php";
include $dir."parcels/actions.php";



?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "parcels/submenu.php";
?>		
	</div>	
	<div id="content">

		<div id="interaction">
<?
			include "parcels/interaction.php";
?>			
		</div>		
<?
		include $dir."parcels/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."parcels/footer.php";
?>
