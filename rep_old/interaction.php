<div style="float:right; margin:0.5ex 0.2em 0.2ex 1em ">
	<!--<p><a href="#" onclick="print_preview(); return false;">Print this page</a></p>-->

	<!--<a href="reports.php?report=by_region&region=<?=$region?>&action=export" ><img border="0" src="images/excel.jpg" alt="Export to excel"  /> </a>-->
	<a href="#" onClick="self.print()"><img border="0" src="images/print.gif" alt="Print"  /> </a>
</div>
	<script language="javascript">
		function message(id){
			var result = "<select name=\"dummy\" size=\"10\" style=\"width:15em \"><option style=\'font-size:0.8em;background-color:#990000; color:black;\'>loading...</option></select>"
			document.getElementById(id).innerHTML = result;  
		}
		function set_Button_on(){
			var result = "<input name=\'submit1\' value=\'Show\' type=\'submit\' />";
			document.getElementById('add_route_wrap1').innerHTML = result;  
			var result = "<input name=\'submit2\' value=\'Export\' type=\'submit\' />";
			document.getElementById('add_route_wrap2').innerHTML = result;  			
		}
		
		function set_SButton_on(){
			document.getElementById('submit_show').disabled = false;  	 
			document.getElementById('submit_export').disabled = false;  			
		}
		
		function set_Button_off(){
			var result = "<input disabled name=\'submit1\' value=\'Show\' type=\'submit\' />";
			document.getElementById('add_route_wrap1').innerHTML = result;  
			var result = "<input disabled  name=\'submit2\' value=\'Export\' type=\'submit\' />";
			document.getElementById('add_route_wrap2').innerHTML = result;  	
		}			
		
	</script>
	<script src="javascripts/ajax.js" type="text/javascript" language="javascript"></script>
<?

if($MESSAGE){
?>		
		<div id="message" >
			<?=$MESSAGE?>
		</div>
<?
}
if($ERROR){
?>		
		<div id="error" >
			<?=$ERROR?>
		</div>
<?
}
?>	
