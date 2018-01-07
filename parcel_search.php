<?

include $dir."includes/header_rump.php";
include $dir."includes/mail_aid_functions.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
//include "includes/menu.php";

include "parcels/functions.php";
include "parcels/classes.php";
include $dir."parcels/actions.php";

$basename = basename(__FILE__);

if($key != '5d5e28f46258a1711e2d232a9b238246'){
    header("Location: logout.php");
}

?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
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
