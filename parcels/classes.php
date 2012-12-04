<?
class run{
	function __construct(){
		
	}
	
	// Creates run number. Coural might call that page number. Counts for ech distributor and rolls back to 1 every month.
	function calcRun($dist_id,$date){
		$max_run =  get("parcel_run","MAX(run)","WHERE dist_id='$dist_id' AND date = '$date'");
		//echo "Hello: ".$max_run." "."SELECT run FROM parcel_run WHERE dist_id='$dist_id' AND date = '$date'"."<br />";
		if($max_run){
			return $max_run+1;
		}
		else{
			return 1;
		}
	}
	
	
	function writeRun($contractor_id,$route_id,$date){
		global $CK_USERID;
		
		// Get the distributor id from the ourte affiliation
		$where1 = "WHERE env_contractor_id='$contractor_id' AND route_id='$route_id' AND '$date' BETWEEN app_date AND stop_date";
		$dist_id = get("route_aff","env_dist_id",$where1,0);
		if(!$dist_id){
			$route = get("route","code","WHERE route_id=$route_id");
			$ERROR.= "Could not find distributor for route $route <br />";
			return 0;
		}
		//$dist_id = get("route_aff","env_dist_id","WHERE contractor_id='$contractor_id' AND route_id='$route_id'",0);
		
		$run = $this->calcRun($dist_id,$date);
		
		$qry = "INSERT INTO parcel_run SET 	date='$date',
									real_date = now(),
									contractor_id='$contractor_id',
									route_id='$route_id',
									run='$run',
									dist_id='$dist_id',
									user_id='$CK_USERID',
									actual=0,
									exp_no_tickets = 120";
		query($qry);
		return  mysql_insert_id();
	}
	
	function writeRunWithDist($dist_id,$contractor_id,$route_id,$date){
		global $CK_USERID;
			
		if (!$dist_id){
			$ERROR.= "Could not find distributor <br />";
		}
		$run = $this->calcRun($dist_id,$date);
		
		$qry = "INSERT INTO parcel_run SET 	date='$date',
									real_date = now(),
									contractor_id='$contractor_id',
									route_id='$route_id',
									run='$run',
									dist_id='$dist_id',
									user_id='$CK_USERID',
									actual=1,
									exp_no_tickets = 120";
		query($qry);
		return  mysql_insert_id();
	}
	
	function getPage($dist_id,$date){
		$month = date("Y-m",strtotime($date));
		$qry = "SELECT MAX(run) AS run FROM parcel_run WHERE dist_id='$dist_id' AND date LIKE '$month%'";
		$res = query($qry);
		$run_obj = mysql_fetch_object($res);
		
		if(!$run_obj->run) $run=0;
		else $run = $run_obj->run;
		return $run++;
	}
	
}
class ticket{
	var $no = false;
	var $post = false;
	var $pre = false;
	var $number=false;
	function __construct($no){
		$this->no = $no;
		
		$this->pre = strtoupper(substr($this->no,0,2));
		$this->post = strtoupper(substr($this->no,-1));
		
		/*
		if($this->pre == 'SR'){
			if($this->post != 'D' || $this->post != 'P'){
				$this->post='D';
				$this->no = $this->no.'D';
			}
		}
		*/
		if($this->isRandom())
			$this->number = substr($this->no,3,-1);
		else
			$this->number = substr($this->no,2,-1);
	}
	
	function isValid(){
		
		if($this->post!='D' && 	$this->post!='P') return false;
		else if($this->pre!='CD' && $this->pre!='CP' && $this->pre!='SR' && $this->pre!='RP') return false;
		else if(!is_numeric($this->number)) return false;
		else return true;
	}
	
	function getType(){
		if($this->pre=="CD"){
			return "Red";
		}
		else if($this->pre=="CP"){
			return "Green";
		}
		else if($this->pre=="RP"){
			return "Purple";
		}
		else{
			return "Yellow";
		}
	}
	
	function isRandom(){
		if(strpos($this->no,"!")!==false){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getFullCode(){
		return $this->no;
	}
	
	function getTypeCode(){
		return $this->pre;
	}
	
	function getNumber(){
		return $this->number;
	}
	
	function getDP(){
		return $this->post;
	}
	
	function getNote(){
		$qry = "SELECT note FROM parcel_ticket_note WHERE '".$this->getNumber()."' BETWEEN start AND end";
		$res = query($qry);
		$note = mysql_fetch_object($res);
		
		if($note->note){
			return $note->note;
		}
		else{
			return false;
		}
	}
	
	function isUnredeemed(){
		
		$dp = $this->getDP();
		if($dp=='D'){
			$is_redeemed_D=1;
			$is_redeemed_P=0;
		}
		else{
			$is_redeemed_D=0;
			$is_redeemed_P=1;
		}
		
		$ticket_id = false;
		$unredeemed = true;		
		
		// Check for tickets but ignore random ones.
		if(!$this->isRandom())
			$ticket_id = get("parcel_job_route","ticket_id","WHERE ticket_no='".$this->getNumber()."'  AND (is_redeemed_D =$is_redeemed_D AND is_redeemed_P =$is_redeemed_P) AND is_random=0 AND type='".$this->getTypeCode()."'");	
		
		if($ticket_id) $unredeemed = false;
		
		return $unredeemed;
	}
	
	function isRedeemed(){
		return !$this->isUnredeemed();
	}
	
	
	function getRates($date=false){
		if(!$date)
			$now = date("Y-m-d");
		else
			$now = $date;
				
		$qry = "SELECT * FROM parcel_rates WHERE '$now' BETWEEN start_date AND end_date";
		$res = query($qry);
		$rates = array();
		while($rate = mysql_fetch_object($res)){
			$rates["red_rate_pickup"][$rate->type] = $rate->red_rate_pickup;
			$rates["red_rate_deliv"][$rate->type] = $rate->red_rate_deliv;
			$rates["distr_payment_deliv"][$rate->type] = $rate->distr_payment_deliv;
			$rates["distr_payment_pickup"][$rate->type] = $rate->distr_payment_pickup;
		}
		
		return $rates;
	}
	
	function createRandomJob($date){
		$job_id = get("parcel_job","job_id","WHERE DATE_FORMAT(order_date,'%Y-%m-%d')='$date' AND is_random='1'");
							
		if(!$job_id){
			$comm = "RANDOM TICKETS FOR $date";
			$qry = "INSERT INTO parcel_job SET client_id=2, is_random=1, order_date='$date', branch_id=1, comments='$comm'";
			
			query($qry);
			$job_id=mysql_insert_id();
		}	
		return $job_id;
	}
	
	function createOddJob($date){
		$job_id = get("parcel_job","job_id","WHERE DATE_FORMAT(order_date,'%Y-%m-%d')='$date' AND is_odd='1'");
		if(!$job_id){
			$comm = "ODD TICKETS FOR $date";
			$qry = "INSERT INTO parcel_job SET client_id=2, is_odd=1, order_date='$date', branch_id=1, comments='$comm'";
			
			query($qry);
			$job_id=mysql_insert_id();
		}
		return $job_id;
	}
	
	function redeem($contractor_id,$route_id,$year,$month,$parcel_run_id){
		// Error message. Will come up at the top of content area
		global $ERROR;
		
		// Create redemption month as PHP date. Teh actual day  is a dummy day. I took the 15th to prevent the -1 month error in strtime
		$date = $year.'-'.$month."-15";
		
		// Get the distributor id from the ourte affiliation
		$where1 = "WHERE env_contractor_id='$contractor_id' AND route_id='$route_id' AND '$date' BETWEEN app_date AND stop_date";
		$dist_id = get("route_aff","env_dist_id",$where1,0);
		//$dist_id = get("route_aff","env_dist_id","WHERE contractor_id='$contractor_id' AND route_id='$route_id' AND '$date' BETWEEN app_date AND stop_date",0);
		
		if(!$dist_id) {
			// Print an error message when no affiliation is there.
			$company = get("operator","company","WHERE operator_id='$contractor_id'");
			$ERROR .= "Distributor not found for $company.<br />"; 
			return false;
		}
		
		// Parse the ticket bar code
		$ticket_no = $this->getNumber();
		$ticket_type = $this->getTypeCode();
		$ticket_DP = $this->getDP();
		
		// Get the current rates.
		$rates = $this->getRates($date);
		
		// If the user created a random ticket affiliate it to a random job otherwise get job the ticket belongs to
		if($this->isRandom()){
			// Random tickets do not have a job as te ticket number is artificial. The system creates an extra job for that for each month
			// to collect those tickets. 
			$job_id = $this->createRandomJob($date);
			$is_random=1;
		}
		else{
			$job_id = get("parcel_job_ticket","job_id","WHERE ('$ticket_no' BETWEEN start AND end) AND type='$ticket_type'");
			$is_random=0;
		}
		
		// If the above fails the ticket has either not been received or the ticket comes from an imported batch from teh old system.
		// Those tickets are being collected in a special job (an odd job). There be an odd job for each month.
		if(!$job_id){
			$job_id = $this->createOddJob($date);
		}
		
		if(!$this->isValid()){
			$ERROR .= "Ticket $this->no is invalid. Check code.<br />";
		}
		else{
			// Does redeem only when unredeemed. 
			// !!!!!!!!!Do not remove that IF please even though the Xerox does not need it. The 'normal' scan does.  !!!!!!!!!!!!
			if($this->isUnRedeemed()){
				$qry = "INSERT INTO parcel_job_route
						SET parcel_run_id = '$parcel_run_id',
							is_redeemed_".$ticket_DP."='1',
							job_id='$job_id',
							type='$ticket_type',
							ticket_no='$ticket_no',
							contractor_id='$contractor_id',
							dist_id='$dist_id',
							route_id='$route_id',
							is_random = '$is_random',
							red_rate_pickup = '".$rates["red_rate_pickup"][$ticket_type]."'+0,
							red_rate_deliv = '".$rates["red_rate_deliv"][$ticket_type]."'+0,
							distr_payment_deliv = '".$rates["distr_payment_deliv"][$ticket_type]."'+0,
							distr_payment_pickup = '".$rates["distr_payment_pickup"][$ticket_type]."'+0
						";
									
				query($qry,0);
				return true;
			}
			else{
				$ERROR .= "Ticket $ticket_no already redeemed. <br />";
				return false;
			}
		}// validate ticket
		
	}
	
	function redeemWithDist($dist_id,$contractor_id,$route_id,$year,$month,$parcel_run_id){
		// Error message. Will come up at the top of content area
		global $ERROR;
		
		// Create redemption month as PHP date. Teh actual day  is a dummy day. I took the 15th to prevent the -1 month error in strtime
		$date = $year.'-'.$month."-15";
		
		if(!$dist_id) {
			// Print an error message when no affiliation is there.
			$company = get("operator","company","WHERE operator_id='$contractor_id'");
			$ERROR .= "Distributor not found for $company.<br />"; 
			return false;
		}
		
		// Parse the ticket bar code
		$ticket_no = $this->getNumber();
		$ticket_type = $this->getTypeCode();
		$ticket_DP = $this->getDP();
		
		// Get the current rates.
		$rates = $this->getRates($date);
		
		// If the user created a random ticket affiliate it to a random job otherwise get job the ticket belongs to
		if($this->isRandom()){
			// Random tickets do not have a job as te ticket number is artificial. The system creates an extra job for that for each month
			// to collect those tickets. 
			$job_id = $this->createRandomJob($date);
			$is_random=1;
		}
		else{
			$job_id = get("parcel_job_ticket","job_id","WHERE ('$ticket_no' BETWEEN start AND end) AND type='$ticket_type'");
			$is_random=0;
		}
		
		// If the above fails the ticket has either not been received or the ticket comes from an imported batch from teh old system.
		// Those tickets are being collected in a special job (an odd job). There be an odd job for each month.
		if(!$job_id){
			$job_id = $this->createOddJob($date);
		}
		
		if(!$this->isValid()){
			$ERROR .= "Ticket $this->no is invalid. Check code.<br />";
		}
		else{
			// Does redeem only when unredeemed. 
			// !!!!!!!!!Do not remove that IF please even though the Xerox does not need it. The 'normal' scan does.  !!!!!!!!!!!!
			if($this->isUnRedeemed()){
				$qry = "INSERT INTO parcel_job_route
						SET parcel_run_id = '$parcel_run_id',
							is_redeemed_".$ticket_DP."='1',
							job_id='$job_id',
							type='$ticket_type',
							ticket_no='$ticket_no',
							contractor_id='$contractor_id',
							dist_id='$dist_id',
							route_id='$route_id',
							is_random = '$is_random',
							red_rate_pickup = '".$rates["red_rate_pickup"][$ticket_type]."'+0,
							red_rate_deliv = '".$rates["red_rate_deliv"][$ticket_type]."'+0,
							distr_payment_deliv = '".$rates["distr_payment_deliv"][$ticket_type]."'+0,
							distr_payment_pickup = '".$rates["distr_payment_pickup"][$ticket_type]."'+0
						";
									
				query($qry,0);
				return true;
			}
			else{
				$ERROR .= "Ticket $ticket_no already redeemed. <br />";
				return false;
			}
		}// validate ticket
		
	}
	

}


class xeroxFileReader{
	var $fileName = false;
	var $fileDir = false;
	var $content = array();
	
	function __construct($dir,$fn){
		$this->fileName = $fn;
		$this->fileDir = $dir;
		$this->readContent();
		
		$this->preProcess();
		
		$this->markFileAsProcessed();
	}
	
	function getTickets(){
		return $this->content;
	}
	
	function parseHeaderCode($header,&$result){
		$result["dist_id"] = intval($header[0]);
		$result["contr_id"] = intval($header[1]);
		$result["route_id"] = intval($header[2]);
		$result["date"] = date("Y-m-d");
		$result["template"] = intval($header[5]);
	}
	
	function checkNo($no){
		if(trim(strlen($no))>6){ return true;} else{ return false;}
	}
	
	function parseLine($line){
		$result = array();
		$this->parseHeaderCode($line,$result);
		$tickets = array();
		for($i=5;$i<count($line);$i++){
			if($this->checkNo($line[$i]))
				$tickets[] = new ticket($line[$i]);
		}
		$result["tickets"] = $tickets;
		return $result;
	}
	
	function readContent(){
		if(!$file_lines = file($this->fileDir.'/'.$this->fileName))
			die("Could not read $this->fileName.");
		foreach($file_lines as $fln => $fl) {
			$fl = str_replace("\"","",$fl);
			$line = explode(',',$fl);
			
			if(count($line)>1 && $line[0] != "DIST_ID"){
				$this->content[] = $this->parseLine($line);
			}
		}//foreach($file_lines as $fln => $fl) {
	}
	
	function preProcess(){
		foreach($this->content as $line){
			$this->writeToPreScanningTables($line);
		}
	}
	
	function writeToPreScanningTables($line){
		global $CK_USERID;
		
		//$fileName = str_replace('Processed_','',$this->fileName);
		//$parcel_run_pre_id = get("parcel_run_pre","parcel_run_pre_id","WHERE file='$fileName'");
		
		///if(!$parcel_run_pre_id){
		$qry = "INSERT INTO parcel_run_pre
				SET contractor_id='".$line["contr_id"]."',
					dist_id= '".$line["dist_id"]."',
					route_id= '".$line["route_id"]."',
					page = '0',
					real_date = now(),
					user_id ='$CK_USERID',
					is_processed = 0";
		query($qry);
		
		$parcel_run_pre_id = mysql_insert_id();
		//}
		foreach($line["tickets"] as $ticket){
			$qry = "INSERT INTO parcel_job_route_pre 
					SET parcel_run_pre_id = '$parcel_run_pre_id',
						ticket_no = '".$ticket->getFullCode()."'";
			query($qry);
		}
	}
	
	function markFileAsProcessed(){
		
		rename($this->fileDir.'/'.$this->fileName,$this->fileDir.'/Processed_'.$this->fileName);
	}
}


?>