<?
include $dir."includes/header.php";
include $dir."includes/mail_aid_functions.php";
include "rep_revenue/actions.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";
include "rep_revenue/functions.php";


?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
		include "rep_revenue/submenu.php";
?>
	</div>
	<div id="content">		
		<div id="interaction">
<?
			include "rep_revenue/interaction.php";
?>			
		</div>	
<?
		include "rep_revenue/main.php";
?>
	</div>
</div>

<?php
# includes DIV footer
include "includes/footer.php";
?>

