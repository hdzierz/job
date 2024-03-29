<?

//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.operator								//
//////////////////////////////////////////////////////////

if($action=="save"){
	if($makeclient=="Add To Parcel Client List"){
		$name = addslashes($name);
		$first_name = addslashes($first_name);
		$deliv_notes = addslashes($deliv_notes);
		$address = addslashes($address);
		$address2 = addslashes($address2);
		$postal_addr = addslashes($postal_addr);
		
		$qry = "INSERT INTO client(name,contact,phone,email,contact_details,is_parcel_courier,is_operator) 
				VALUES('$first_name $name','$address, $city $postcode','$phone $mobile','$email','$first_name $name\n$address\n$city $postcode\n$phone\n$mobile',1,1)";
		query($qry);
		
		$id = mysql_insert_id();
		$qry = "UPDATE address SET client_id=$id WHERE address_id=$record";
		query($qry);
		
		$cancel="Cancel";
		$MESSAGE = "Adress added to client list.";
	}
	else if($makeclient=="Remove From Parcel Client List"){
		$client_id = get("address","client_id","WHERE address_id=$record");
		
		$qry = "DELETE FROM client WHERE client_id=$client_id";
		query($qry);
		
		$qry = "UPDATE address SET client_id=NULL WHERE address_id=$record";
		query($qry);
		$cancel="Cancel";
		$MESSAGE = "Adress Removed from client list.";
	}

	if(!$name){
		$ERROR="Please give surname! (Minimum requirement)";
		$action=$dest;
	}
	else{
		$name = addslashes($name);
		$first_name = addslashes($first_name);
		$deliv_notes = addslashes($deliv_notes);
		$address = addslashes($address);
		$address2 = addslashes($address2);
		$postal_addr = addslashes($postal_addr);
        if(!$is_rural) $is_rural=0;		
		if(!$postcode) $postcode=0;
		
		if(!$cancel=="Cancel"){
			$address_id=$record;
			if($dest=="add"){
				$sql = "INSERT INTO address(type,
											card_id,
											salutation,
											first_name,
											name,
											salutation2,
											first_name2,
											name2,
											bank_num,
											gst_num,
											address,
											address2,
											postal_addr,
                                            suburb,
                                            building_name,
											city,
											postcode,
											country,
											phone,
											phone2,
											mobile,
											mobile2,
											fax,
											email,
											etext,
                                            netpass,
											mail_type,
                                            is_rural,
											alt_mail_type,
											alt_email,
											alt_fax
                                            )
							
					VALUES(	'$type',
							'$card_id',
							'$salutation',
							'$first_name',
							'$name',
							'$salutation2',
							'$first_name2',
							'$name2',
							'$bank_num',
							'$gst_num',
							'$address',
							'$address2',
							'$postal_addr',
                            '$suburb',
                            '$building_name',
							'$city',
							'$postcode',
							'$country',
							'$phone',
							'$phone2',
							'$mobile',
							'$mobile2',
							'$fax',
							'$email',
							'$etext',
                            '$password',
							'$mail_type',
                            '$is_rural',
							'$alt_mail_type',
							'$alt_email',
							'$alt_fax')";
				query($sql);						
				$address_id=mysql_insert_id();
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
						SET	type		='$type',
							card_id		='$card_id',
							salutation	='$salutation',
							first_name	='$first_name',
							name		='$name',
							salutation2	='$salutation2',
							first_name2	='$first_name2',
							name2		='$name2',
							bank_num	= '$bank_num',
							gst_num		= '$gst_num',
							address		='$address',
							address2	='$address2',
							postal_addr	='$postal_addr',
                            suburb      = '$suburb',
                            building_name = '$building_name',
							city		='$city',
							postcode	= $postcode,
							country		='$country',
							phone		='$phone',
							phone2		='$phone2',
							mobile		='$mobile',
							mobile2		='$mobile2',
							fax			='$fax',
							email		='$email',
							etext		='$etext',
                            netpass     ='$password',
							mail_type	='$mail_type',
                            is_rural    ='$is_rural',
							alt_mail_type = '$alt_mail_type',
							alt_email   ='$alt_email',
							alt_fax		='$alt_fax'
						WHERE address_id='$record'";
				query($sql);					
			
				$client_id = get("address","client_id","WHERE address_id=$record");
				if($client_id){
					$qry = "UPDATE client
						SET name		= '$first_name $name',
							contact		= '$address, $city',
							phone		= '$phone $mobile',
							email		= '$email',
							contact_details = '$first_name $name\n$address\n$city\n$phone\n$mobile'
						WHERE client_id=$client_id";
					query($qry);
				}
			}
            $qry = "SELECT * FROM auth_user WHERE username='$etext'";
            $res = query($qry);
            $u = mysql_fetch_object($res);
            if($u  && $etext && $password){
                $qry = "UPDATE auth_user SET password=md5('$password'), first_name='$first_name', last_name='$name', email='$email'  WHERE username='$etext'";
                query($qry);
            }
            else if($etext && $password){
                $qry = "INSERT INTO  auth_user SET 
                            username='$etext', 
                             first_name='$first_name', 
                             last_name='$name', 
                             email='$email',
                             is_staff=0,       
                             is_active=1,       
                             date_joined=now(),     
                             is_superuser=0,        
                             password=md5('$password') ";
                query($qry);
                $auth_user_id = mysql_insert_id();      
                $qry = "INSERT INTO auth_user_groups(user_id, group_id) VALUES($auth_user_id,1)";      
                query($qry);
            }

			$action="save_operator";	
		}
		else{
			$action="";
			$MESSAGE = "Action cancelled.";
		}		
	}
}



if($action=="save_operator"){
	if($alias) $company=$alias;
	else $company=$name."-".$first_name;
	
	$date_started = make_empty_null($date_started);
	$date_left	  = make_empty_null($date_left);
	if($is_dist!="Y")$is_dist="N";
	if($is_ioa!="Y")$is_ioa="N";
	if($is_subdist!="Y")$is_subdist="N";
	if($is_dropoff!="Y")$is_dropoff="N";
	if($is_contr!="Y")$is_contr="N";
    if($is_pallet!='Y') $is_pallet='N';
	if($parcel_send_di!='Y') $parcel_send_di = 'N';
	$is_hauler_ni!="Y" ? $is_hauler_ni="0" : $is_hauler_ni="1";
	$is_hauler_si!="Y" ? $is_hauler_si="0" : $is_hauler_si="1";
	if($is_shareholder!="Y")$is_shareholder="N";
	if($distributor=="") $distributor=$name;
	if($postcode=="") $postcode=0;
	if($latest_dep=="") $latest_dep="0:00";
	if(!$shares||$shares=='') $shares=0;
    if(!$shares_start||$shares_start=='') $shares_start=0;
    if(!$shares_end||$shares_end=='') $shares_end=0;
    if(!$send_contr_sheet) $send_contr_sheet = 'N';
    if(!$has_gst) $has_gst = 0;
	
	if($same_as_add=='Y'){
		$do_address = $address;
		$do_city 	= $city;
	}
	$company 			= addslashes($company);
	$alias 				= addslashes($alias);
	$deliv_notes 		= addslashes($deliv_notes);
	$env_deliv_notes 	= addslashes($env_deliv_notes);
	$share_notes 		= addslashes($share_notes);
	$rate_red_fact      = 1+$rate_red_fact/100;

	$operator_id = get("address","operator_id"," WHERE address_id=$address_id");
	if($dest=="add"){
		$sql = "INSERT INTO operator(is_current,
									 company,
									 is_dist,
									 is_subdist,
									 is_contr,
                                     is_pallet,
									 is_dropoff,
									 is_alt_dropoff,
									 is_hauler_ni,
									 is_hauler_si,
									 alias,
									 is_shareholder,
									 shares,
                                     shares_start,
                                     shares_end,
									 share_bought,
									 share_sold,
									 share_notes,
									 date_started,
									 date_left,
									 contract,
									 agency,
									 do_address,
                                     do_suburb,
                                     do_building_name,
									 do_city,   
                                     do_postcode,
									 deliv_notes,
									 env_deliv_notes,
									 latest_dep,
									 subdist_seq,
									 rate_red_fact,
									 parcel_send_di,
                                     send_contr_sheet,
                                     linehaul_a,
                                     linehaul_a_type,
                                     linehaul_a_bin,
                                     linehaul_b,
                                     linehaul_b_type,
                                     linehaul_b_bin,
                                     ph_desk,
                                     depot_rent,
                                     scanner_no1,
                                     scanner_no2,
                                     scanner_no3,
                                     scanner_no4,
                                     scanner_phone_no1,
                                     scanner_phone_no2,
                                     scanner_phone_no3,
                                     scanner_phone_no4,
                                     scanner_email,
                                     scanner_charge,
                                     mobile_pay,
                                     has_gst,
                                     rate_code)
				VALUES('$is_current',
					 '$company',
					 '$is_dist',
					 '$is_subdist',
					 '$is_contr',
                     '$is_pallet',
					 '$is_dropoff',
					 '$is_alt_dropoff',
					 '$is_hauler_ni',
					 '$is_hauler_si',
					 '$alias',
					 '$is_shareholder',
					 '$shares',
                     '$shares_start',
                     '$shares_end',
					 '$share_bought',
					 '$share_sold',
					 '$share_notes',
					  $date_started,
					  $date_left,
					 '$contract',
					 '$agency',
					 '$do_address',
                     '$do_suburb',
                     '$do_building_name',
					 '$do_city',
                     '$do_postcode',
					 '$deliv_notes',
					 '$env_deliv_notes',
					 '$latest_dep',
					 '$subdist_seq'+0,
					 '$rate_red_fact'+0,
					 '$parcel_send_di',
                     '$send_contr_sheet',
                     '$linehaul_a',
                     '$linehaul_a_type',
                     '$linehaul_a_bin',
                     '$linehaul_b',
                     '$linehaul_b_type',
                     '$linehaul_b_bin',
                     '$ph_desk',
                     '$depot_rent',
                     '$scanner_no1',
                     '$scanner_no2',
                     '$scanner_no3',
                     '$scanner_no4',
                     '$scanner_phone_no1',
                     '$scanner_phone_no2',
                     '$scanner_phone_no3',
                     '$scanner_phone_no4',
                     '$scanner_email',
                     '$scanner_charge',
                     '$mobile_pay',
                     '$has_gst',
                     '$rate_code')";
		query($sql);
		$operator_id=mysql_insert_id();					 
		$qry = "UPDATE address SET operator_id=$operator_id WHERE address_id=$address_id";
		query($qry);
	}
	else{
		$do_address=addslashes($do_address);
		$sql = "UPDATE operator 
				SET	 is_current		='$is_current',
					 company		='$company',
					 is_dist		='$is_dist',
					 is_subdist		='$is_subdist',	
					 is_dropoff		='$is_dropoff',	
					 is_alt_dropoff		='$is_alt_dropoff',	
					  is_hauler_ni		='$is_hauler_ni',	
					   is_hauler_si		='$is_hauler_si',	
					 is_contr		='$is_contr',
                     is_pallet      = '$is_pallet',
					 alias			='$alias',
					 is_shareholder = '$is_shareholder',
					 shares			='$shares',
                     shares_start   ='$shares_start',
                     shares_end     ='$shares_end',
					 share_bought	='$share_bought',
					 share_sold		='$share_sold',
					 share_notes	='$share_notes',
					 date_started	= $date_started,
					 date_left		= $date_left,
					 contract		= '$contract',
					 agency			= '$agency',
					 do_address		= '$do_address',
                     do_suburb      = '$do_suburb',
                     do_building_name = '$do_building_name',
					 do_city		= '$do_city',
                     do_postcode    = '$do_postcode',
					 deliv_notes	= '$deliv_notes',
					 env_deliv_notes= '$env_deliv_notes',
					 latest_dep		= '$latest_dep',
					 subdist_seq	= '$subdist_seq'+0,
					 rate_red_fact  = '$rate_red_fact',
					 parcel_send_di = '$parcel_send_di',
                     send_contr_sheet = '$send_contr_sheet',
                     linehaul_a     = '$linehaul_a',
                     linehaul_a_type = '$linehaul_a_type',
                     linehaul_a_bin = '$linehaul_a_bin',
                     linehaul_b = '$linehaul_b',
                     linehaul_b_type = '$linehaul_b_type',
                     linehaul_b_bin = '$linehaul_b_bin',
                     ph_desk = '$ph_desk',
                     depot_rent = '$depot_rent',
                     scanner_no1 = '$scanner_no1',
                     scanner_no2 = '$scanner_no2',
                     scanner_no3 = '$scanner_no3',
                     scanner_no4 = '$scanner_no4',
                     scanner_phone_no1 = '$scanner_phone_no1',
                     scanner_phone_no2 = '$scanner_phone_no2',
                     scanner_phone_no3 = '$scanner_phone_no3',
                     scanner_phone_no4 = '$scanner_phone_no4',
                     scanner_email = '$scanner_email',
                     scanner_charge = '$scanner_charge',
                     mobile_pay = '$mobile_pay',
                     has_gst = '$has_gst',
                     rate_code = '$rate_code'
				WHERE operator_id='$operator_id'";
			query($sql);
	}

    if($etext){      
        $qry = "SELECT * FROM auth_user WHERE username='$etext'";      
        $res = query($qry);        
        $u = mysql_fetch_object($res);     
        if($u){        
            $qry = "UPDATE operator SET user_id={$u->id} WHERE operator_id='$operator_id'";        
            query($qry);       
        }      
    }	

	$qry = "UPDATE job_route SET subdist_rate_red = '$rate_red_fact' WHERE subdist_id = '$operator_id'";
	query($qry,0);
	
	$MESSAGE = "Address successfully changed/added.";
	$action="";
}



//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: 	Erases record								//
// USES: 	coural.operator								//
//////////////////////////////////////////////////////////

if($action=="delete"){
	$operator_id = get("address","operator_id"," WHERE address_id=$record");
	$sql = "DELETE FROM operator WHERE operator_id='$operator_id'";
	query($sql);
	
	
	$sql = "DELETE FROM address WHERE address_id='$record'";
	query($sql);
	
	$ERROR =  "Address successfully deleted";
	$action="";	
}


	
?>
