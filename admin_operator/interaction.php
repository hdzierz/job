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

if($action=="" || !isset($action)){
	if(!$letter){
		$letter='abcdefghijklmnopq';
	}
?>
	<div id="message">
		<?=$interaction_message?>
	</div>
		<p>
			<a href="admin_operator.php?action=&letter=A">A</a>
			<a href="admin_operator.php?action=&letter=B">B</a>
			<a href="admin_operator.php?action=&letter=C">C</a>
			<a href="admin_operator.php?action=&letter=D">D</a>
			<a href="admin_operator.php?action=&letter=E">E</a>
			<a href="admin_operator.php?action=&letter=F">F</a>
			<a href="admin_operator.php?action=&letter=G">G</a>
			<a href="admin_operator.php?action=&letter=H">H</a>
			<a href="admin_operator.php?action=&letter=I">I</a>
			<a href="admin_operator.php?action=&letter=J">J</a>
			<a href="admin_operator.php?action=&letter=K">K</a>
			<a href="admin_operator.php?action=&letter=L">L</a>
			<a href="admin_operator.php?action=&letter=M">M</a>
			<a href="admin_operator.php?action=&letter=N">N</a>
			<a href="admin_operator.php?action=&letter=O">O</a>
			<a href="admin_operator.php?action=&letter=P">P</a>
			<a href="admin_operator.php?action=&letter=Q">Q</a>
			<a href="admin_operator.php?action=&letter=R">R</a>
			<a href="admin_operator.php?action=&letter=S">S</a>
			<a href="admin_operator.php?action=&letter=T">T</a>
			<a href="admin_operator.php?action=&letter=U">U</a>
			<a href="admin_operator.php?action=&letter=V">V</a>
			<a href="admin_operator.php?action=&letter=W">W</a>
			<a href="admin_operator.php?action=&letter=X">X</a>
			<a href="admin_operator.php?action=&letter=Y">Y</a>
			<a href="admin_operator.php?action=&letter=Z">Z</a>
		</p>	
<?		
}
?>