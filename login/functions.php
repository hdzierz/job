<?

// Defense against MySQL injections ('a' or 'b' = 'b'). Returns true if password contains quotes otherwise
// false.
// Inserted by Helge 30/04/07

function check_passwd_for_inject($passwd){
	return false;
	if(ereg("\"",$passwd) or ereg("\'",$passwd)){
		return true;
	}
	else 
		return false;
}

function process_user_info($username,$passwd,$rememberme){
	if (isset($username) AND isset($passwd) AND $username != "" AND $passwd != "") {
		// Checking whether user/passwd combinatino exists
		$passwd=md5($passwd);
		$sql  = "SELECT * FROM user WHERE username = '$username' and passwd = '$passwd'";
		
		if(!$rs   = mysql_query($sql)) die(mysql_error());
		$data = mysql_fetch_object($rs);
		
		// Storing the request		
		$sql2 = "INSERT INTO control_login (username, passwd) VALUES ('$username', '$passwd')";
		$res2 = mysql_query($sql2);
		if(mysql_error()) die(mysql_error());
	
		if ($data and !check_passwd_for_inject($password) ) {
		
			if ($rememberme == "yes") {
				# set cookie with expiry in 7 days
				$expiry = time()+60*60*24*7;
			}
			else $expiry = "0";
			$path = "/";
			// Setting the userinfo for the session
			setcookie("coural_username",$data->username,$expiry,$path);
			setcookie("coural_userid",$data->user_id,$expiry,$path);
			setcookie("coural_fullname", $data->fullname, $expiry,$path);
			
			setcookie("coural_security",$data->security_lev,$expiry,$path);
			setcookie("coural_page_main",$data->coural_page_main,$expiry,$path);
			setcookie("coural_page_procjob",$data->page_procjob,$expiry,$path);
			setcookie("coural_page_invoice",$data->page_invoice,$expiry,$path);
			setcookie("coural_page_useradmin",$data->page_useradmin,$expiry,$path);
			setcookie("coural_page_routeadmin",$data->page_routeadmin,$expiry,$path);
			setcookie("coural_page_clientadmin",$data->page_clientadmin,$expiry,$path);
			setcookie("coural_page_addradmin",$data->page_addradmin,$expiry,$path);
			setcookie("coural_page_opadmin",$data->page_opadmin,$expiry,$path);
			setcookie("coural_page_reports",$data->page_reports,$expiry,$path);
			setcookie("coural_page_rep_revenue",$data->page_rep_revenue,$expiry,$path);
			
			setcookie("coural_page_parcels",$data->page_parcels,$expiry,$path);
			setcookie("coural_page_rep_parcels",$data->page_rep_parcels,$expiry,$path);
			setcookie("coural_change_gst",$data->gst,$expiry,$path);
			
			$THEIR_IP=($_SERVER['REMOTE_ADDR']);
			// Reslocating to start page if login was successful
			header("Location: index.php"); 
		}
		else {
			echo "<p class='error'>Details don't match</p>";
		
			# unsuccessfull login - redirect back to login page
			header("Location: login.php");
		}  
	}	
}


?>