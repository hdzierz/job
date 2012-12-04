<?


	include "../../includes/mysql.php";
	include "../../includes/mysql_aid_functions.php";
	include "../functions.php";
	include "../classes.php";
	
	$tickets = $_POST["tickets"];
	$run = $_POST["run"];
	$result="\n";
	$first_note = true;
	if(is_array($tickets)){
		foreach($tickets as $ticket){
			$t = new ticket($ticket);
			
			$ticket_no = $t->getNumber();
			
			$ticket_id = $t->isRedeemed();
			if($ticket_id) $result.="Already Redeemed:\t".$t->getFullCode()."\n";
			
			if($t->getNote()){
				$result.= "Note: \t\t\t".$t->getNumber().": ".$t->getNote()."\n";
				$first_note=false;
			}
		}
	}

	
	
	echo $result;
	
?>