<div id="footer">
	<div style="float:left ">
		<form name="theClock">
			<input class="clock" disabled type=text name="theTime" size=8>
		</form>			
	</div>
	<a class=link href="#" onClick="MM_openBrWindow('includes/stopbank.php','ComputerCare','width=246,height=280')">Developed by Stopbank Software</a>	
</div>
</div><!--wrapper0-->
<?
	// Autosuggests.
	// DON'T  MOVE FROM FOOTER. OTHERWISE IE7 WILL DO VERY FUNNY THINGS. THESE CONTROLS HAVE TO BE CREATED JUST BEFORE THE BODY CLOSING TAG!!!
	if($action=="add_order_books" ||$target=="add_order_books"){
?>
		<script language="javascript">
		
		
			var oTextbox2 = new AutoSuggestControl(
								document.getElementById("client"), 
								new SuggestionProvider("client", "name", "client"),
								"./parcels/",
								false);   //tablename, fieldname. 
		
		</script>
<?	
	}
	if($action=="redeem"){
		
?>
		<script language="javascript">
			var oTextbox1 = new AutoSuggestControl(
								document.getElementById("contractor"), 
								new SuggestionProvider("operator", "company", "contractor"),
								"./parcels/",
								false);   //tablename, fieldname. 
			
			/*var oTextbox2 = new AutoSuggestControl(
								document.getElementById("name"), 
								new SuggestionProvider("operator", "name", "name"),
								"./parcels/",
								false); */  //tablename, fieldname. 
			
		</script>
<?	
	}
	
?>

</body>
</html>
<?php
include "history.php";
//mysql_close();
?>