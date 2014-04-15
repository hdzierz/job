<?
class Login{
	function Login(){}
	function writeLoginTable(){
		global $origin;
?>
		<form name="login" method="get" action="login.php">
			<table class="login">
				<tr>
					<td style="font-weight:bold " colspan="2">Please login below:</td>
				</tr>
				<tr>
					<th class="loginh">Username :</th>
					<td><input type="text" name="username" /></td>
				</tr>
				<tr>
					<th class="loginh">Password</th>
					<td><input type="password" name="passwd" /></td>
				</tr>
				<tr>
					<th class="loginh">Remember me:</th>
					<td><input type="checkbox" name="rememberme" value="yes" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2"><input type="submit" name="action" value="Login" /></td>
				</tr>
			</table>
			<input type="hidden" name="origin" value="<?=$origin?>" />
		</form>		
<?
	}
}

?>
