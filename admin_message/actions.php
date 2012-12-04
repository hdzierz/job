<?
if($action=="delete"){
	$qry = "DELETE FROM message WHERE message_id=$record";
	query($qry);
	$qry = "DELETE FROM message_op WHERE message_id=$record";
	query($qry);
	$MESSAGE = "Message deleted";
	$action=null;
}

if($action == "table_action"){
//print_r($_POST); die("Hell;o");
	switch($submit){
		case "Delete Selection":
			//print_r($check);
			foreach($check as $op_id=>$on){
				if($on){
					$qry = "DELETE FROM message_op WHERE operator_id=$op_id";
					query($qry);
				}
			}
			$action = 'add_addresses';
			break;
		case "Send":
			message_send($message_id,$check,$send_type);
			$action=null;
			break;
	}
	
}

if($action=='add_addresses'){

	if($subaction=='update_message'){
		if($message_id>0){
			$qry = "UPDATE message SET title = '$title', message='$message' WHERE message_id=$message_id";
			query($qry);
		}
		else{
			$qry = "INSERT INTO message SET title = '$title', message='$message'";
			query($qry);
			$message_id = mysql_insert_id();
		}
	}
	else if($subaction=='add_operator'){
		
		$qry = "REPLACE INTO message_op(message_id,operator_id)
					(SELECT $message_id,operator_id FROM operator WHERE operator_id IS NOT NULL ";	
		$where_add = "";
		if($is_current !== "All"){	
			$where_add.= " AND is_current='$is_current'";
		}
		if($is_shareholder !== "All"){
			$where_add.= " AND is_shareholder='$is_shareholder'";
		}
		if(is_array($op_type) && !in_array("All",$op_type)){
			foreach($op_type as $type){
				$where_add.= " AND $type='Y'";
			}
		}
		if(is_array($dist) && !in_array("All",$dist)){
			$qry2 = "SELECT contractor_id FROM route_aff WHERE dist_id IN (".implode($dist,',').") AND now() BETWEEN app_date AND stop_date ";
			$res = query($qry2);
			$ids = array();
			while($c = mysql_fetch_object($res)){
				$ids[] = $c->contractor_id;
			}
			if(count($ids)>0)
				$where_add .= " AND operator_id IN (".implode($ids,',').")";
		}
		$qry .= $where_add." )";
		query($qry);
	}
}

?>