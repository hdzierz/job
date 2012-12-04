<?

//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.operator								//
//////////////////////////////////////////////////////////

if($action=="save"){
	$date_started = make_empty_null($date_started);
	$date_left	  = make_empty_null($date_left);
	if($is_dist!="Y")$is_dist="N";
	if($is_subdist!="Y")$is_subdist="N";
	if($is_contr!="Y")$is_contr="N";
	if($distributor=="") $distributor=$name;
	if($shares=="") $shares=0;

	if($dest=="add"){
		$sql = "INSERT INTO operator(company,
									 is_dist,
									 is_subdist,
									 is_contr,
									 distributor,
									 sub_dist,
									 alias,
									 shares,
									 identifier,
									 date_started,
									 date_left,
									 contract,
									 agency)
				VALUES('$company',
					 '$is_dist',
					 '$is_subdist',
					 '$is_contr',
					 '$distributor',
					 '$sub_dist',
					 '$alias',
					 '$shares',
					 '$identifier',
					  $date_started,
					  $date_left,
					 '$contract',
					 '$agency')";
		$sql_address = "INSERT INTO address(operator_id)
						VALUES(%s)";
	}
	else{
		$sql = "UPDATE operator 
				SET	 company		='$company',
					 is_dist		='$is_dist',
					 is_subdist		='$is_subdist',	
					 is_contr		='$is_contr',
					 distributor	='$distributor',
					 sub_dist		= '$sub_dist',
					 alias			='$alias',
					 shares			='$shares',
					 identifier		='$identifier',
					 date_started	= $date_started,
					 date_left		= $date_left,
					 contract		= '$contract',
					 agency			= '$agency'
				WHERE operator_id='$record'";
	}
	query($sql);
	if($sql_address){
		$sql_address = sprintf($sql_address,"'".mysql_insert_id()."'");
		query($sql_address);
	}
	msg("Operator successfully changed/added");
	$action="save_address";	
}
//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="save_address"){
	if($dest=="add"){
		$sql = "INSERT INTO address(operator_id,
			salutation,first_name,name,birthdate,salutation2,first_name2,name2,birthdate2,bank_num,gst_num,
			
			address,address2,postal_addr,city,postcode,country,phone,phone2,mobile,mobile2,fax,email,www,etext,

			sec_address,sec_address2,sec_postal_addr,sec_city,sec_postcode,sec_country,sec_phone,sec_phone2,sec_mobile,
					sec_mobile2,sec_fax,sec_email,sec_www,sec_etext)
					
			VALUES(NULL,
			'$salutation','$first_name','$name','$birthdate','$salutation2','$first_name2','$name2','$birthdate2','$bank_num','$gst_num',
			
			'$address','$address2','$postal_addr','$city','$postcode','$country','$phone','$phone2','$mobile','$mobile2','
				$fax','$email','$www','$etext',

			'$sec_address','$sec_address2','$sec_postal_addr','$sec_city','$sec_postcode','$sec_country','$sec_phone',
				'$sec_phone2','$sec_mobile','$sec_mobile2','$sec_fax','$sec_email','$sec_www','$sec_etext')";
	}
	else{
		if($birthdate=="") $birthdate="NULL";
		else $birthdate="'".$birthdate."'";
		if($birthdate2=="") $birthdate2="NULL";
		else $birthdate2="'".$birthdate2."'";
		if($postcode=="") $postcode="NULL";
		else $postcode="'".$postcode."'";
		if($sec_postcode=="") $sec_postcode="NULL";
		else $sec_postcode="'".$sec_postcode."'";
		
		$sql = "UPDATE address 
				SET	salutation	='$salutation',
					first_name	='$first_name',
					name		='$name',
					birthdate	= $birthdate,
					salutation2	='$salutation2',
					first_name2	='$first_name2',
					name2		='$name2',
					birthdate2	= $birthdate2,
					bank_num	= '$bank_num',
					gst_num		= '$gst_num',
			
					address		='$address',
					address2	='$address2',
					postal_addr	='$postal_addr',
					city		='$city',
					postcode	= $postcode,
					country		='$country',
					phone		='$phone',
					phone2		='$phone2',
					mobile		='$mobile',
					mobile2		='$mobile2',
					fax			='$fax',
					email		='$email',
					www			='$www',
					etext		='$etext',

					sec_address		='$sec_address',
					sec_address2	='$sec_address2',
					sec_postal_addr	='$sec_postal_addr',
					sec_city		='$sec_city',
					sec_postcode	= $sec_postcode,
					sec_country		='$sec_country',
					sec_phone		='$sec_phone',
					sec_phone2		='$sec_phone2',
					sec_mobile		='$sec_mobile',
					sec_mobile2		='$sec_mobile2',
					sec_fax			='$sec_fax',
					sec_email		='$sec_email',
					sec_www			='$sec_www',
					sec_etext		='$sec_etext'
				WHERE address_id='$record'";
	}
	query($sql);
	msg("Address successfully changed/added.");
	$action="";	
}


//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: 	Erases record								//
// USES: 	coural.operator								//
//////////////////////////////////////////////////////////

if($action=="delete"){
	if($record==590){
		$ERROR = "Attempt to delete LBC denied. That would cause huge inconsistencies in the system.";
	}
	else{
		$sql = "DELETE FROM operator WHERE operator_id='$record'";
		query($sql);
		$sql = "DELETE FROM address WHERE address_id='$address_id'";
		query($sql);
		
		$MESSAGE = "Operator successfully deleted";
		$interaction_message = "Operator successfully deleted";
	}
	$action="";	
}



	
?>