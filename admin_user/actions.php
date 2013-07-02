<?


//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="save"){
	if(!checkpasswd($passwd,$passwd2)){
		$ERROR = "ERROR: Passwords do not match!";
		$action=$dest;
	}
	else{
		if($passwd!='') $passwd=md5($passwd);
		if($dest=="add"){
			$sql = "INSERT INTO user(username,
									 passwd,
									 email,
									 page_useradmin,
									 page_routeadmin,
									 page_clientadmin,
									 page_addradmin,
									 page_opadmin,
									 page_main,
									 page_procjob,
									 page_reports,
									 page_rep_old,
									 page_rep_revenue,
									 page_invoice,
									 page_parcels,
						
									 page_rep_parcels,
									 gst) 
					VALUES('$username',
							'$passwd',
							'$email',
							'$page_useradmin',
							'$page_routeadmin',
							'$page_clientadmin',
							'$page_addradmin',
							'$page_opadmin',
							'$page_main',
							'$page_procjob',
							'$page_reports',
							'$page_rep_old',
							'$page_rep_revenue',
							'$page_invoice',
							'$page_parcels',
							'$page_rep_parcels',
							'$gst')";
		}
		else{
			if($passwd!='')
				$passwd_set="passwd			 ='$passwd',";
			$sql = "UPDATE user 
					SET username		 ='$username',
						$passwd_set
						page_useradmin	 ='$page_useradmin',
						email			 ='$email',
						page_routeadmin	 ='$page_routeadmin',
						page_clientadmin ='$page_clientadmin',
						page_addradmin	 ='$page_addradmin',
						page_opadmin	 ='$page_opadmin',
						page_main	 ='$page_main',
						page_procjob	 ='$page_procjob',
						page_reports	 ='$page_reports',
						page_rep_old	 ='$page_rep_old',
						page_rep_revenue	 ='$page_rep_revenue',
						page_invoice	 ='$page_invoice',
						page_parcels	 ='$page_parcels',
						page_rep_parcels	 ='$page_rep_parcels',
						gst				 ='$gst'
					WHERE user_id='$record'";
		}
		//echo nl2br($sql);
		query($sql);
		$MESSAGE = "User successfully created/changed.";
		$action="";
	}
}

//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: Erases record									//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="delete"){
	$sql = "DELETE FROM user WHERE user_id='$record'";
	query($sql);
	$action="";	
	$ERROR = "User successfully deleted.";
}


	
?>