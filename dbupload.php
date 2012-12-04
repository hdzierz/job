<?php
include 'includes/mysql.php';
function PostRequest($url, $referer, $_data) {

	// convert variables array to string:
    $data = array();    
    while(list($n,$v) = each($_data)){
        $data[] = "$n=$v";
    }    
    $data = implode('&', $data);
    // format --> test1=a&test2=b etc.

   // parse the given URL
    $url = parse_url($url);
    if ($url['scheme'] != 'http') { 
        die('Only HTTP request are supported !');
    }
    
   	// extract host and path:
    $host = $url['host'];
    $path = $url['path'];
    //echo $host."<br />";
	//echo $path."<br />";
    // open a socket connection on port 80
    $fp = fsockopen($host, 80);
    
    // send the request headers:
    fputs($fp, "POST $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp, "Referer: $referer\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);
    
    $result = ''; 
    while(!feof($fp)) {
        // receive the results of the request
        $result .= fgets($fp, 128);
    }

    // close the socket connection:
    fclose($fp);
    
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
    
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
    
    // return as array:
    return array($header, $content);
}


$COUNTER=5;

function AddressInsert($data,$start){
	global $COUNTER;
	$qry = "SELECT * FROM address LIMIT $start, 100";
	$res = mysql_query($qry);
	
	$str = "INSERT INTO `e107_coural_address` (
							  `address_id`,
							  `operator_id`,
							  `sort`,
							  `salutation`,
							  `first_name`,
							  `name`,
							  `salutation2`,
							  `first_name2`,
							  `name2`,
							  `address`,
							  `address2`,
							  `postal_addr`,
							  `city`,
							  `postcode`,
							  `country`,
							  `phone`,
							  `phone2`,
							  `mobile` ,
							  `mobile2` ,
							  `fax`,
							  `email` ,
							  `etext`,
							  `bank_num` ,
							  `gst_num`,
							  `type` ,
							  `mail_type`,
							  `card_id` ,
							  `alt_mail_type`,
							  `alt_email`,
							  `alt_fax`)
	";
	if(mysql_num_rows($res)>0){
		while($address = mysql_fetch_object($res)){
				$mobile = $address->mobile;
				if(strpos($mobile,'#') === false){	
					$buffer = explode('#',$mobile);
					$mobile = $buffer[0];
				}
				$mobile2 = $address->mobile2;
				if(strpos($mobile2,'#') === false){
					$buffer = explode('#',$mobile2);
					$mobile2 = $buffer[0];
				}
				
				$COUNTER++;
				$qry = $str." VALUES(".
				  "'".$address->address_id."',".
				  "'".$address->operator_id."',".
				  "'".$address->sort."',".
				  "'".$address->salutation."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->first_name))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->name))."',".
				  "'".$address->salutation2."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->first_name2))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->name2))."',".
				  "'".mysql_escape_string($address->address)."',".
				  "'".mysql_escape_string($address->address2)."',".
				  "'".mysql_escape_string($address->postal_addr)."',".
				  "'".mysql_escape_string($address->city)."',".
				  "'".$address->postcode."',".
				  "'".mysql_escape_string($address->country)."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->phone))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->phone2))."',".
				  "'".mysql_escape_string(str_replace('&','and',$mobile ))."',".
				  "'".mysql_escape_string(str_replace('&','and',$mobile2 ))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->fax))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->email ))."',".
				  "'".$address->etext."',".
				  "'".$address->bank_num ."',".
				  "'".$address->gst_num."',".
				  "'".$address->type ."',".
				  "'".$address->mail_type."',".
				  "'".$address->card_id ."',".
				  "'".$address->alt_mail_type."',".
				  "'".$address->alt_email."',".
				  "'".$address->alt_fax."')";
				 
				  $data["querya".$address->address_id] = $qry;
				  //echo $qry."<br />";
		}
	}
	return $data;
}

function OperatorInsert($data, $start){
	global $COUNTER;
	$qry = "SELECT * FROM operator  LIMIT $start, 100";
	$res = mysql_query($qry);
	
	$str = "INSERT INTO `e107_coural_operator` (
							  `operator_id`,
							  `company`,
							  `is_dist`,
							  `is_subdist`,
							  `is_dropoff`,
							  `is_contr`,
							  `circ_drop`,
							  `shares`,
							  `alias`,
							  `contract`,
							  `agency`,
							  `date_started`,
							  `date_left`,
							  `deliv_notes`,
							  `env_deliv_notes`,
							  `do_address`,
							  `do_city`,
							  `latest_dep` ,
							  `is_current` ,
							  `is_shareholder`,
							  `share_bought` ,
							  `share_sold`,
							  `share_notes` ,
							  `is_alt_dropoff`,
							  `subdist_seq` ,
							  `is_hauler_ni`,
							  `is_hauler_si`)
	";
	if(mysql_num_rows($res)>0){
		while($address = mysql_fetch_object($res)){
				$COUNTER++;
				$qry = $str." VALUES(".
				  "'".$address->operator_id."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->company))."',".
				  "'".$address->is_dist."',".
				  "'".$address->is_subdist."',".
				  "'".$address->is_dropoff."',".
				  "'".$address->is_contr."',".
				  "'".$address->circ_drop."',".
				  "'".$address->shares."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->alias))."',".
				  "'".$address->contract."',".
				  "'".$address->agency."',".
				  "'".$address->date_started."',".
				  "'".$address->date_left."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->deliv_notes))."',".
				"'".mysql_escape_string(str_replace('&','and',$address->env_deliv_notes))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->do_address))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->do_city))."',".
				"'".$address->latest_dep."',".
				  "'".$address->is_current."',".
				  "'".$address->is_shareholder ."',".
				  "'".$address->share_bought ."',".
				  "'".$address->share_sold."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->share_notes)) ."',".
				  "'".$address->is_alt_dropoff."',".
				  "'".$address->subdist_seq ."',".
				  "'".$address->is_hauler_ni."',".
				  "'".$address->is_hauler_si."')";
				 
				  $data["queryo".$address->operator_id] = $qry;
				  //echo $qry."<br />";
		}
	}
	return $data;
}

function RouteInsert($data, $start){
	global $COUNTER;
	$qry = "SELECT * FROM route  LIMIT $start, 100";
	$res = mysql_query($qry);
	
	$str = "INSERT INTO `e107_coural_route` (
							  `route_id`,
							  `island`,
							  `region`,
							  `area`,
							  `code`,
							  `description`,
							  `external`,
							  `no_ticket_header`,
							  `pmp_areacode`,
							  `pmp_runcode`,
							  `num_lifestyle`,
							  `num_farmers`,
							  `num_dairies`,
							  `num_sheep`,
							  `num_beef`,
							  `num_sheepbeef`,
							  `num_dairybeef`,
							  `num_hort` ,
							  `seq_region` ,
							  `seq_area`,
							  `seq_code` ,
							  `is_hidden`,
							  `num_nzfw` ,
							  `rmt`,
							  `rm_rr` ,
							  `rm_f`,
							  `rm_d`,
							  `code_base`,
							  `code_rd`)
	";
	
	if(mysql_num_rows($res)>0){
		while($address = mysql_fetch_object($res)){
				$COUNTER++;
				$qry = $str." VALUES(".
				  "'".$address->route_id."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->island))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->region))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->area))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->code))."',".
				  "'".mysql_escape_string(str_replace('&','and',$address->description))."',".
				  "'".$address->external."',".
				  "'".$address->no_ticket_header."',".
				  "'".$address->pmp_areacode."',".
				  "'".$address->pmp_runcode."',".
				  "'".$address->num_lifestyle."',".
				  "'".$address->num_farmers."',".
				  "'".$address->num_dairies."',".
				  "'".$address->num_sheep."',".
				  "'".$address->num_beef."',".
				  "'".$address->num_sheepbeef."',".
				  "'".$address->num_dairybeef."',".
				  "'".$address->num_hort ."',".
				  "'".$address->seq_region ."',".
				  "'".$address->seq_area."',".
				  "'".$address->seq_code ."',".
				  "'".$address->is_hidden."',".
				  "'".$address->num_nzfw ."',".
				  "'".$address->rmt."',".
				  "'".$address->rm_rr."',".
				  "'".$address->rm_f."',".
				  "'".$address->rm_d."',".
				  "'".$address->code_base."',".
				  "'".$address->code_rd."')";
				 
				  $data["queryr".$address->route_id] = $qry;
			 // echo $qry."<br />";
		}
	}
	return $data;
}

function RouteAffInsert($data, $start){
	global $COUNTER;
	$qry = "SELECT * FROM route_aff  LIMIT $start, 100";
	$res = mysql_query($qry);
	
	$str = "INSERT INTO `e107_coural_route_aff` (
							  `route_aff_id`,
							  `route_id`,
							  `dist_id`,
							  `subdist_id`,
							  `contractor_id`,
							  `dropoff_id`,
							  `app_date`,
							  `stop_date`,
							  `env_dist_id`,
							  `env_contractor_id`,
							  `pc_dist_id`,
							  `env_subdist_id`,
							  `pc_contractor_id`,
							  `env_dropoff_id`)
	";
	
	if(mysql_num_rows($res)>0){
		while($address = mysql_fetch_object($res)){
				$COUNTER++;
				$qry = $str." VALUES(".
				  "'".$address->route_aff_id."',".
				  "'".$address->route_id."',".
				  "'".$address->dist_id."',".
				  "'".$address->subdist_id."',".
				  "'".$address->contractor_id."',".
				  "'".$address->dropoff_id."',".
				  "'".$address->app_date."',".
				  "'".$address->stop_date."',".
				  "'".$address->env_dist_id."',".
				  "'".$address->env_contractor_id."',".
				  "'".$address->pc_dist_id."',".
				  "'".$address->env_subdist_id."',".
				  "'".$address->pc_contractor_id."',".
				  "'".$address->env_dropoff_id."')";
				 
				  $data["queryra".$COUNTER] = $qry;
				  //echo $qry."<br />";
		}
	}
	return $data;
}

$log = fopen("dbupload.log","a+");
$message = "DB SYNC PROCESS STARTED (".date("Y-m-d").")<br />\n\r";
fwrite($log,$message,strlen($message));

$data1 = array(

    'sql' => 'TRUNCATE e107_coural_address'
);

$data2 = array(
    'sql' => 'TRUNCATE e107_coural_operator'
);


$data3 = array(
	'sql' => 'TRUNCATE e107_coural_route'
);


$data4 = array(
    'sql' => 'TRUNCATE e107_coural_route_aff'
);



$num_add=0;
$num_rou=0;
$num_roa=0;
$num_ope=0;

for($i=0;$i<15;$i++){
	usleep(100);
	if($i>0){
		$data1 = array(

		    'sql' => 'DELETE FROM  e107_coural_address WHERE address_id=-1'
		);
		
		$data2 = array(
		    'sql' => 'DELETE FROM  e107_coural_operator  WHERE operator_id=-1'
		);
		
		
		$data3 = array(
			'sql' => 'DELETE FROM  e107_coural_route  WHERE route_id=-1'
		);
		
		
		$data4 = array(
		    'sql' => 'DELETE FROM  e107_coural_route_aff  WHERE route_aff_id=-1'
		);
	}
	
	$start = $i*100;
	// submit these variables to the server:
	
	$data1 = AddressInsert($data1,$start);
	$num_add += count($data1);
	$message = "Num1: ".count($data1)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	$data2 = OperatorInsert($data2,$start);
	$num_ope += count($data2);
	$message = "Num2: ".count($data2)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	$data3 = RouteInsert($data3,$start);
	$num_rou += count($data3);
	$message = "Num3: ".count($data3)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	$data4 = RouteAffInsert($data4,$start);
	$num_roa += count($data4);
	$message = "Num3: ".count($data4)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	// send a request to example.com (referer = jonasjohn.de)
	list($header, $content) = PostRequest(
	    "http://coural.co.nz/modules/SP_coural/receiver.php",
	    "http://www.coural.co.nz/",
	    $data1
	);
	
	$message = print_r($content,true)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	list($header, $content) = PostRequest(
	    "http://coural.co.nz/modules/SP_coural/receiver.php",
	    "http://www.coural.co.nz/",
	    $data2
	);
	
	$message = print_r($content,true)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	list($header, $content) = PostRequest(
	    //"http://coural.spdev.co.nz/modules/SP_coural/receiver.php",
		"http://coural.co.nz/modules/SP_coural/receiver.php",
	    "http://www.coural.co.nz/",
	    $data3
	);
	
	$message = print_r($content,true)."<br />\n\r";
	fwrite($log,$message,strlen($message));
	
	list($header, $content) = PostRequest(
	    //"http://coural.spdev.co.nz/modules/SP_coural/receiver.php",
		"http://coural.co.nz/modules/SP_coural/receiver.php",
	    "http://www.coural.co.nz/",
	    $data4
	);
	
	//$result  = 
	

	// print the result of the whole request:
	$message = print_r($content,true)."<br />\n\r";
	fwrite($log,$message,strlen($message));	

}

$message = print_r($header,true)."<br />\n\r";
fwrite($log,$message,strlen($message));
// print $header; --> prints the headers
fclose($log);
echo "Success <a href='index.php'>&lt&ltBack</a>";
die();

/****************************************
 * THIS SECTION SHOWS THE PHP SCRIPT CALLED BY THE ABOVE
 * 
 
require_once("../../class2.php");
require_once(e_HANDLER."form_handler.php");
$form = new form();
$SP_coural;

////////////////////////////////////////////
//Execute query---------------------------//
////////////////////////////////////////////
if(isset($_POST['sql'])):
$_POST = strip_if_magic($_POST);
foreach($_POST as $k => $v){
    echo $k." - ".$sql->db_Select_gen($v)."<br />";
}
exit();
endif;

////////////////////////////////////////////
//default---------------------------------//
////////////////////////////////////////////
$SP_coural.= $form->form_open("post", e_SELF).$form->form_hidden("sql", "1");
$SP_coural.= "<table class='fborder' style='width:100%;'>";
$SP_coural.= "<tr><td class='forumheader'>".$form->form_textarea("query1", 50, 10)."</td></tr>";
$SP_coural.= "<tr><td class='forumheader'>".$form->form_textarea("query2", 50, 10)."</td></tr>";
$SP_coural.= "<tr><td class='forumheader'>".$form->form_textarea("query3", 50, 10)."</td></tr>";
$SP_coural.= "<tr><td class='forumheader'>".$form->form_button("submit", "", "Submit")."</td></tr>";
$SP_coural.= "</table>";
$SP_coural.= $form->form_close();

require_once(HEADERF);
$ns->tablerender("Receiver", $SP_coural);
require_once(FOOTERF);
*/
?>