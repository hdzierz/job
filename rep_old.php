<?

include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";

include "rep_old/functions.php";



?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "rep_old/submenu.php";
?>		
	</div>	
	<div id="content">
<?
		include $dir."rep_old/actions.php";
?>	
		<div id="interaction">
<?
			include "rep_old/interaction.php";
?>			
		</div>		
<?
		include $dir."rep_old/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>
