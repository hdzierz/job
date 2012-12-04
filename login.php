<?
//include $dir."includes/header.php";
# include html title which also includes body and html tags
include $dir."includes/aid_functions.php";
include "includes/mysql.php";
include "includes/title.php";
# includes DIV menu of the page
//include "includes/menu.php";

include "login/functions.php";

# check if user is logged in
if (isset($_COOKIE['coural_username'])) {
	header("Location: index.php");
}

////////////////////////////////////////////
//Getting some headers for the specific page
////////////////////////////////////////////
include "includes/Login.php";
?>
<!-- PAGE CONTENT -->
<div id="wrapper">
	<div id="submenu">
<?
		include "login/submenu.php";
?>		
	</div>	
	<div id="content">
	<?
		include "login/actions.php";	
	?>			
	</div>
</div>

<?php
# includes DIV footer
include $dir."includes/footer.php";
?>