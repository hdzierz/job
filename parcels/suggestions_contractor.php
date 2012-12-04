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
						$this->error("get_set(): Specified index, '$index', was not found in results.");
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
    //make sure there's text


	//create the SQL query string
	if(strlen($oData->text) > 0){
		$sQuery = "Select distinct ".$oData->fieldname." 
					from ".$oData->requesting."
					where ".$oData->fieldname." like '".$oData->text."%' 
					$where_add 
					order by ".$oData->fieldname;  

		$result = sql_get_set($sQuery,$oData->fieldname); 	
		$aSuggestions = array_keys($result);	//format: {"el":100, "e2":"myname", ... "e100":["name1", "name2",...], ...}
		//check if exist branch
		/*$has_branch = $mysql->get_value("select 1 from branch Left Join customer ON c_id = b_c_id where ".$oData->fieldname." like '".
				  $oData->text."%'");
		if($has_branch) array_push($aSuggestions, "branch:1"); */  		
	}else{
		$sQuery = "Select distinct ".$oData->fieldname." from ".$oData->requesting." Where ".$oData->fieldname." like '".
				  $oData->value."%' $where_add order by ".$oData->fieldname;  
		//$sQuery = "Select distinct truck from tpi_data where truck like 'H%' order by truck";  
			//$sQuery = "select b_branchName from branch left join customer on c_id = b_c_id where company like 'IBM' order by b_branchName asc limit 0, 10";					
		$result = sql_get_set($sQuery,$oData->fieldname); 	
		$aSuggestions = array_keys($result);	//format: {"el":100, "e2":"myname", ... "e100":["name1", "name2",...], ...}		
	}    
	
    echo($oJSON->encode($aSuggestions));
?>