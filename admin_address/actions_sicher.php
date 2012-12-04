<?
//////////////////////////////////////////////////////////
// ACTION EDIT	                                       	//
// DOES:	Edits record of user on the same page as 	//
//			table.										//
// USES: 	coural.user									//
// REURNS:	Form.										//
//////////////////////////////////////////////////////////
if($action=="edit"||$action=="add"){
	edit_address($record,$action,$dest);
}

//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="save"){
	if($dest=="add"){
		$sql = "INSERT INTO address(operator_id,
			salutation,first_name,name,salutation2,first_name2,name2,,bank_num,gst_num,
			
			address,address2,postal_addr,city,postcode,country,phone,phone2,mobile,mobile2,fax,email,www,etext,

			sec_address,sec_address2,sec_postal_addr,sec_city,sec_postcode,sec_country,sec_phone,sec_phone2,sec_mobile,
					sec_mobile2,sec_fax,sec_email,sec_www,sec_etext)
					
			VALUES(NULL,
			'$salutation','$first_name','$name','$salutation2','$first_name2','$name2','$bank_num','$gst_num',
			
			'$address','$address2','$postal_addr','$city','$postcode','$country','$phone','$phone2','$mobile','$mobile2','
				$fax','$email','$www','$etext',

			'$sec_address','$sec_address2','$sec_postal_addr','$sec_city','$sec_postcode','$sec_country','$sec_phone',
				'$sec_phone2','$sec_mobile','$sec_mobile2','$sec_fax','$sec_email','$sec_www','$sec_etext')";
	}
	else{
		if($postcode=="") $postcode="NULL";
		else $postcode="'".$postcode."'";
		if($sec_postcode=="") $sec_postcode="NULL";
		else $sec_postcode="'".$sec_postcode."'";
		
		$sql = "UPDATE address 
				SET	salutation	='$salutation',
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
// DOES: Erases record									//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="delete"){
	$sql = "DELETE FROM address WHERE address_id='$record'";
	query($sql);
	msg("Address successfully deleted.");
	$action="";	
}

//////////////////////////////////////////////////////////
// ACTION DEFAULT                                      	//
// DOES: 	Create table with content of user table    	//
//			using class MySQLTable.                    	//
// RETURNS: Table										//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="" || !isset($action)){
	if($letter){	
		$where = " WHERE op.company LIKE('$letter%') OR address.name LIKE('$letter%') ";
	}
	else if($query){
		$where = " WHERE op.company LIKE('%$query%') OR address.name LIKE('%$query%') ";
	}
	else{
		$where = " WHERE op.company LIKE('a%') OR address.name LIKE('a%') ";
	}
	if($record) $where=" WHERE address_id='$record' ";
	$sql = "SELECT 	address_id AS Record,
			op.company	AS Operator,
			salutation 	AS Salutation,
			first_name 	AS 'First Name',
			address.name AS Name,
			salutation2	AS Salutation2,
			first_name2	AS 'First Name 2',
			name2 		AS 'Name 2',
			bank_num	AS 'Bank Acccount No.',
			gst_num		AS 'GST No.',
			
			address		AS Address,
			address2	AS 'Address 2',
			postal_addr	AS 'Postal Address',
			city		AS City,
			postcode 	AS Postcode,
			country 	AS Country,
			phone		AS Phone,
			phone2		AS 'Phone 2',
			mobile		AS 'Mobile',
			mobile2		AS 'Mobile 2',
			fax			AS 'Facsimile',
			email		AS 'Email',
			etext		AS Etext,

			sec_address		AS '2nd Address',
			sec_address2	AS '2nd Address 2',
			sec_postal_addr	AS '2nd Postal Address',
			sec_city		AS '2nd City',
			sec_postcode 	AS '2nd Postcode',
			sec_country 	AS '2nd Country',
			sec_phone		AS '2nd Phone',
			sec_phone2		AS '2nd Phone 2',
			sec_mobile		AS '2nd Mobile',
			sec_mobile2		AS '2nd Mobile 2',
			sec_fax			AS '2nd Facsimile',
			sec_email		AS '2nd Email',
			sec_etext		AS '2nd Etext'		
			FROM address
			LEFT JOIN
			operator op
			ON op.operator_id=address.operator_id
			$where
			ORDER BY op.company,address.name";
	$clientTab = new MySQLTable("admin_address.php",$sql);
	$clientTab->showRec  = 0;
	$clientTab->wrap1    = "GST No.";
	$clientTab->wrap2    = "Etext";
	$clientTab->captionF = "Operator";
	$clientTab->caption  = "Operator: ";
	$clientTab->writeList();		
}

	
?>