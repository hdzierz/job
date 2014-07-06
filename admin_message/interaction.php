<script>
function printpreview()
{
var OLECMDID = 7;
/* OLECMDID values:
* 6 - print
* 7 - print preview
* 1 - open window
* 4 - Save As
*/
var PROMPT = 1; // 2 DONTPROMPTUSER
var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
WebBrowser1.ExecWB(OLECMDID, PROMPT);
WebBrowser1.outerHTML = "";
}
</script>

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

if($action=='show'){

    if(!$date_start){
        $date_start = date('Y-m-d');
    }

    if(!$date_end){
        $date_end = date('Y-m-d');
    }

?>
    <script type="text/javascript" src="includes/calendarDateInput.js"></script>
    <form name="weekly_job" action="admin_message.php" method="post">
    <table>
        <tr>
            <td>From:</td>
            <td>
                <script language="javascript">DateInput("date_start", true, "YYYY-MM-DD","<?=$date_start?>")</script>
            </td>
            <td>To:</td>
            <td>
                <script language="javascript">DateInput("date_end", true, "YYYY-MM-DD","<?=$date_end?>")</script>
            </td>
            <td>
                 <input type="submit" value="List" />
                 <input type="hidden" name="action" value="show" />
            </td>
        </tr>
    </table>
    </form>
<?
}
?>
