<?php
/*
$tt = pg_connect("host=localhost dbname=cc_nzsti user=admin password=inkl67z");
pg_query($tt, "INSERT INTO test(test) VALUES(2)");
$res = pg_query($tt, "SELECT test FROM test");
$obj = pg_fetch_object($res);
echo $obj->test;
*/
class MySQL{
	var $db = "coural";
	var $host;
	var $db_conn;
	var $deb=false;
	var $mode;
	var $fCon 	 = "mysql_connect";
	var $fSelDB  = "mysql_select_db";
	var $fError  = "mysql_error";
	var $fQuery  = "mysql_query";
	var $ffetchOB= "mysql_fetch_object";
	var $ffetchAS= "mysql_fetch_assoc";
	var $ffetchAR= "mysql_fetch_array";
	var $fNumRows= "mysql_num_rows";
	
	// Contructor
	function MySQL($hostI,$dbI,$mode="MySQL"){
		$this->db 	= $dbI;
		$this->host = $hostI;
		$this->mode = $mode;
		
		if($mode=="PostgreSQL"){
			$this->fCon 	 = "pg_connect";
			$this->fSelDB  	 = "pg_select_db";
			$this->fError    = "pg_result_error";
			$this->fQuery    = "pg_query";
			$this->ffetchOB  = "pg_fetch_object";
			$this->ffetchAS  = "pg_fetch_assoc";
			$this->ffetchAR  = "pg_fetch_array";
			$this->fNumRows  = "pg_num_rows";
		}
	}
	

	function select_db($db, $db_connect){
		return call_user_func($this->fSelDB,$db, $db_connect);
	}	
	
	function connectDB($host, $db, $passwd){
		return call_user_func($this->fCon,$host, $db, $passwd);
	}		
	
	function error(){
		if($this->mode=="MySQL")
			return call_user_func($this->fError);
		else
			return call_user_func($this->fError,$this->db_conn);
	}		
	
	function connect($user,$passwd){
		if($this->mode=="MySQL"){
			$db_connect = $this->connectDB($this->host, $user, $passwd);
			$this->db_conn=$this->select_db($this->db, $db_connect); 
			if (!$this->db_conn) die("System could not connect to the database.".$this->error());
		}
		else{
			$this->db_conn = $this->connectDB("host=$this->host dbname=$this->db user=$user password=$passwd");
			if (!$this->db_conn) die("System could not connect to the database.".$this->error());
		}
	}
	
	function debug($str){
		echo nl2br($str);	
	}
	
	function query($qry){
		
		if($this->deb){
			$this->debug($qry);	
		}
		if($this->mode=="MySQL"){
			if(!$res = call_user_func($this->fQuery,$qry)){
				$message = "ERROR in Query: ".$qry."ERROR: ".$this->error();
				die($message);
			}
		}
		else{
			if(!$res = call_user_func($this->fQuery,$this->conn,$qry)){
				$message = "ERROR in Query: ".$qry."ERROR: ".$this->error();
				die($message);
			}			
		}
		return $res;		
	}
	
	
	function get_set($query_string, $index = false, $single_row = false)
	{ 
		$result =& $this->query($query_string);	//execute query using internal method
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
					while(($row = mysql_fetch_assoc($result)) !== false) $return_array[] = $row;
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
	
	function fetchObject($res){
		if(!$res){
			$message = "Could not access SQL Result set in fetchObject.";
			return false;
		}
		$obj = call_user_func($this->ffetchOB,$res);
		return $obj;		
	}

	function fetchArray($res){
		if(!$res){
			$message = "Could not access SQL Result set in fetchArray.";
			return false;
		}
	
		$array = call_user_func($this->ffetchAR,$res);
		return $array;		
	}
	
	function fetchAssoc($res){
		if(!$res){
			$message = "Could not access SQL Result set in fetchAssoc.";
			return false;
		}	
		call_user_func($this->ffetchAS,$res);
		return $array;		
	}
		
	function queryFetchObject($qry){
		if(!$res = mysql_query($qry)){
			$message = "ERROR in Query: ".$sql."ERROR: ".$this->error();
			die($message);
		}	
		return $this->fetchObject($res);		
	}
	
	function hasRecord($table,$field,$record){
		$qry = "SELECT * FROM $table WHERE $field='$record'";
		$res = $this->query($qry);
		return call_user_func($this->fNumRows,$res)>0;
	}
	
	function getMax($table,$field){
		$qry = "SELECT MAX($field) AS max FROM $table";
		$obj = $this->queryFetchObject($qry);
		return $obj->max;
	}
	
	function getMin($table,$field){
		$qry = "SELECT MIN($field) AS ct FROM $table";
		$obj = $this->queryFetchObject($qry);
		return $obj->min;
	}
	
	function getCount($table,$field,$where){
		$qry = "SELECT COUNT($field) AS ct FROM $table WHERE $where";
		$obj = $this->queryFetchObject($qry);
		return $obj->ct;
	}		
	
	function get($table,$field,$where){
		$qry = "SELECT $field FROM $table WHERE $where";
		$obj = $this->queryFetchObject($qry);
		return $obj->$field;
	}	
	
	function getFieldAtt($table,$field,$target='Type'){
		$qry = "SHOW FULL COLUMNS FROM $table WHERE `Field` = '$field'";
		$obj = $this->queryFetchObject($qry);
		return $obj->$target;
	}
	
	
	// checks whether a field is numeric.
	function isNumeric($table,$field){
		
		$type = $this->getFieldAtt($table,$field);
		$type = strtolower($type);

		$num_types[] = "int";
		$num_types[] = "double";
		$num_types[] = "real";
		$num_types[] = "numeric";
		$num_types[] = "float";
		
		foreach($num_types as $t){
			if(strpos($type,$t)!==false){
				return true;
			}
		}
		return false;
	}
		
		
	// The next two functions return field aliases. The field aliases are stored in the description of the field with a seperator.
	// This is NZSTI specific but I intend to use it more frequently for MySQL tables. Very handy.
	function get_table_alias_from_description_by_ref($table,&$alias,$sep="::"){
		$qry = "SHOW FULL COLUMNS FROM $table";
		$res = $this->query($qry);

		while($fie = $this->fetchObject($res)){
			$com = explode($sep,$fie->Comment);
			if($com[1]) $alias[$fie->Field]=$com[1];
		}
	}
			
	// The next two functions return field aliases. The field aliases are stored in the description of the field with a seperator.
	// This is NZSTI specific but I intend to use it more frequently for MySQL tables. Very handy.
	function get_table_alias_from_description($table,$alias="",$sep="::"){
		$qry = "SHOW FULL COLUMNS FROM $table";
		$res = $this->query($qry);
		if($alias=="") $alias=array();
		while($fie = $this->fetchObject($res)){
			$com = explode($sep,$fie->Comment);
			if($com[1]) $alias[$fie->Field]=$com[1];
		}
		if(count($alias)>0){
			return $alias;
		}
		else
			return false;		
	}
	

	function get_field_alias_from_description($table,$field,$sep="::"){
		$qry = "SHOW FULL COLUMNS FROM $table WHERE Field='$field'";
		$fie = $this->queryFetchObject($qry);
		$com = explode($sep,$fie->Comment);
		if(!$com[1]){
			return false;
		}
		else{
			$alias[$fie->Field] = $com[1];
			return $alias;
		}
	}

	function get_table_descriptions($table,$descr="",$sep="::"){
		$qry = "SHOW FULL COLUMNS FROM $table";
		$res = $this->query($qry);
		if($descr=="") $descr=array();
		while($fie = $this->fetchObject($res)){
			$com = explode($sep,$fie->Comment);
			if($com[0]) $descr[$fie->Field]=$com[0];
		}
		if(count($descr)>0){
			return $descr;
		}
		else
			return false;		
	}
	

}

$db = 'coural';
if(strpos($_SERVER['REQUEST_URI'],'job_test') !== false){
	$db = 'coural_test';
}

if($_SERVER['DOCUMENT_ROOT'] == "/Applications/XAMPP/xamppfiles/htdocs"){
	$MYSQL = new MySQL('localhost', $db);
	$MYSQL->connect('root','inkl67z');
}
else if($_SERVER['DOCUMENT_ROOT'] == "/var/www/html"){
	$MYSQL = new MySQL('localhost', $db);
        $MYSQL->connect('root','inkl67z');
}
else{
	$MYSQL = new MySQL('192.168.100.23:3306', $db);
	$MYSQL->connect('admin','zt90undr');
}


	




?>
