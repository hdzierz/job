<?


//////////////////////////////////////////////////////////
// ACTION SAVE                                       	//
// DOES: 	Saves record usually edited by action edit	//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="save"){

	if($dest=="add"){
		if(!$client_id && !$new_name){
			$ERROR="Please give name of client.";
			$action=$dest;
		}
		else if(!$publication){
			$ERROR="Please give publication of client.";
			$action=$dest;
		}
		else{
			if($new_name){
				$sql = "INSERT INTO client(name,contact,phone)
						VALUES('$new_name','$contact','$phone')";		
				query($sql);	
				$client_id = mysql_insert_id();
			}
			$sql = "INSERT INTO client_pub(client_id,publication)
					VALUES('$client_id','$publication')";
			query($sql);		
			$MESSAGE="Client successfully added.";
			$action="";				
		}
	}
	else{
		$client_id = get("client_pub","client_id","WHERE client_pub_id='$client_pub_id'");
		
		$sql = "UPDATE client 
				SET name ='$name',
					contact ='$contact',
					phone ='$phone'
				WHERE client_id='$client_id'";
		query($sql);
		$sql = "UPDATE client_pub 
				SET publication	= '$publication'
				WHERE client_pub_id='$client_pub_id'";
		query($sql);		
		$MESSAGE="Client successfully changed.";
		$action="";		
	}
}

//////////////////////////////////////////////////////////
// ACTION DELETE                                       	//
// DOES: Erases record									//
// USES: 	coural.user									//
//////////////////////////////////////////////////////////

if($action=="delete"){
	$client_id = get("client_pub","client_id","WHERE client_pub_id='$record'");
	
	$sql = "DELETE FROM client_pub WHERE client_pub_id='$record'";
	query($sql);
	$action="";	
	
	$qry = "SELECT COUNT(*) AS ct FROM client_pub WHERE client_id='$client_id'";
	$res=query($qry);
	$num = mysql_fetch_object($res);
	if($num->ct==0){
		$sql = "DELETE FROM client WHERE client_id='$record'";
		query($sql);
	}
	
	$ERROR="Client successfully deleted.";
}


	
?>