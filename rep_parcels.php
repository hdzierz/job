<?

include $dir."includes/header.php";
# include html title which also includes body and html tags
include "includes/title.php";
# includes DIV menu of the page
include "includes/menu.php";

include "rep_parcels/functions.php";



?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "rep_parcels/submenu.php";
?>		
	</div>	
	<div id="content">
<?
		include $dir."rep_parcels/actions.php";
?>	
		<div id="interaction">
<?
			include "rep_parcels/interaction.php";
?>			
		</div>		
<?
		include $dir."rep_parcels/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>
