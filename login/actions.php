<?
//////////////////////////////////////////////////////////
// ACTION LOGIN                                      	//
// DOES: 	processes login info				    	//
// RETURNS: Table										//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////
import_request_variables("pg");
if($action=="Login"){
	process_user_info($username,$passwd,$rememberme);
	$action="";
}

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create login table					    	//
//			using class Login.	                    	//
// RETURNS: Form/Table									//
// USES: 	nil											//
//////////////////////////////////////////////////////////

if($action==""){
?>
		<table style=" width:100%; height:20em; ">
			<tr>
				<td align="center" valign="middle">
<?
					
					$login = new Login();
					$login->writeLoginTable();
?>
				</td>
			</tr>
		</table>		
<?
}
?>