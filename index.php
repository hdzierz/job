<?php
include "includes/header.php";
# include html title which also includes body and html tags

include "includes/title.php";

# includes DIV menu of the page
include "includes/menu.php";

include "show_joblist/functions.php";



?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
	include "show_joblist/submenu.php";
?>		
	</div>	
	<div id="content">
<?
		include "show_joblist/actions.php";
?>	
		<div id="interaction">
<?
			include "show_joblist/interaction.php";
?>			
		</div>		
<?
		include "show_joblist/main.php";
?>		
	</div>
</div>

<?php
# includes DIV footer
include "includes/footer.php";
?>
