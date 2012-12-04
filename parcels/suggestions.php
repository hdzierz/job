<?php
//shortcut function to return an multi-dimensional array of data from a query string	
function sql_get_set($query_string, $index = false, $single_row = false)
{ 
	$result = sql_query($query_string);	//execute query using internal method
	if ($result === false) return false;	//query failed, return false 

	$return_array = array();
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0)
	{
		if ($single_row == false || mysql_num_fields($result) > 1) //more than one field was requested, return each row as an array
		{
			if ($index)	//make into associative array based on specified field index
			{
				while(($row = mysql_fetch_assoc($result)) !== false)
				{
					if (!isset($row[$index]))
					{
						die("get_set(): Specified index, '$index', was not found in results.");
						return false;
					}
					
					$return_array[$row[$index]] = $row;
				}
			}
			else	//make into numerically indexed array
			{
				while(($row = mysql_fetch_assoc($result)) !== false) {
					
					$return_array[] = $row;
				}
			}
		}
		else //return row as a simple value (cannot be indexed)
		{
			for ($cnt = 0; $cnt < $num_rows; $cnt++)
			{
				$return_array[] = mysql_result($result, $cnt);
			}
		}
		reset($return_array);
	}
	return $return_array;
}

	function sql_query($qry){
		return query($qry);
	}
	
    //plain text header
   	header("Content-Type: text/plain; charset=UTF-8");
	
	include "../includes/mysql.php";
	include_once "../includes/aid_functions.php";
	include_once "../includes/mysql_aid_functions.php";
	
	 //include JSON-PHP and instantiate the object
    require_once("../includes/JSON.php");
    $oJSON = new JSON();

    //get the data that was posted
    
    $handle = fopen("php://input","r+");
    if($handle)
		$raw_get = fgets($handle);
	else
		$raw_get="";

    //get the data that was posted
	
    //$oData = $oJSON->decode($HTTP_RAW_POST_DATA);
    //$aSuggestions = array();

	
    $oData = $oJSON->decode($raw_get);
	//echo "Hello".$raw_get;
    $aSuggestions = array();

	
	if($oData->requesting=="client") $where_add = " AND is_parcel_courier=1";
	if($oData->requesting=="operator") $where_add = " AND is_contr='Y'";
	if($oData->requesting=="name") $where_add = " AND is_contr='Y'";
    //make sure there's 


	//create the SQL query string
	if(strlen($oData->text) > 0 && $oData->requesting=="client"){
		$sQuery = "Select distinct ".$oData->fieldname."
					from $oData->requesting
					where ".$oData->fieldname." like '".$oData->text."%' 
					$where_add 
					
					order by ".$oData->fieldname;  

		$result = sql_get_set($sQuery,$oData->fieldname); 	
		$aSuggestions = array_keys($result);	
	}    
	
	else if(strlen($oData->text) > 0 && ($oData->fieldname=="company")){
		$now = date("Y-m-d");
		$sQuery = "Select CONCAT(name,', ',first_name,'--',route.code,' \[',operator.operator_id,'\]')  AS ".$oData->fieldname."
					from operator
					LEFT JOIN address
					ON address.operator_id=operator.operator_id
					LEFT JOIN route_aff
					ON route_aff.env_contractor_id=".$oData->requesting.".operator_id
					LEFT JOIN route
					ON route.route_id = route_aff.route_id
					where CONCAT(name,', ',first_name,'--',route.code) like '".$oData->text."%' 
					$where_add 
					
					AND '$now' BETWEEN app_date AND stop_date
					order by ".$oData->fieldname;  
//AND route.is_hidden<>'Y' ### removed from query as per client instructions JW
		$result = sql_get_set($sQuery,$oData->fieldname); 	
		
		$aSuggestions1 = array_keys($result);	//format: {"el":100, "e2":"myname", ... "e100":["name1", "name2",...], ...}
		$aSuggestions1[] = "---------------COMPANY---------------------------";
		
		$sQuery2 = "Select CONCAT(".$oData->fieldname.",'--',route.code,' \[',operator.operator_id,'\]')  AS ".$oData->fieldname."
					FROM operator
					
					LEFT JOIN route_aff
					ON route_aff.env_contractor_id=".$oData->requesting.".operator_id
					LEFT JOIN route
					ON route.route_id = route_aff.route_id
					WHERE company LIKE '".$oData->text."%' 
					$where_add 
					
					AND '$now' BETWEEN app_date AND stop_date
					order by ".$oData->fieldname;  
//AND route.is_hidden<>'Y' ### removed from query as per client instructions JW
		$result = sql_get_set($sQuery2,$oData->fieldname); 	
		$aSuggestions2 = array_keys($result);	
		
		$aSuggestions = array_merge($aSuggestions1,$aSuggestions2);
	}
	
	
    echo($oJSON->encode($aSuggestions));
?>