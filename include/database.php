<?php
/**
 * This database class handles all interactions (queries, connections)
 * with the mySQL database. This classes is designed as a Singleton object.
 * 
 * Originally written in 2009. Modified for Imprinter.
 * 
 * @package imprinter
 * @author rye
 * @version 1.1
 */
 
class Database {
	private $dbh;
	private $queryLog = array();	//Stores a log of all queries executed by the class
	private $foundRowsOverride = false;    //Override calcFoundRows, if required.
	/**
	 * The constructor will connect to the database as defined in config.php
	 * @return void
	 */
	function __construct() {
		$this->dbh = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die("Database Connection Error");
		mysql_select_db(DB_NAME, $this->dbh) or die("Database Error");
	}
	
	public function getQueryLog() {
		return $this->queryLog;
	}
	
	/**
	 * Runs a query through the defined database connection.
	 * @param string $query
	 * @return Object
	 */
	public function query($query) {
		$result = mysql_query($query, $this->dbh);	//Query the database with the query
			
		if ($result) {
			$this->queryLog[] = $query;
			return $result;
		}
		else {	//The query failed - trigger a PHP error
			trigger_error ("A database query failed. mySQL returned the following error message: <br /><code>".mysql_error().
				"</code><br /><br />The query was: <br /><code>".$query."</code><br /><br />", E_USER_ERROR);
		}
	}
	
	/**
	 * Loads the $fields fields from the database table $table matching the $filters (with
	 * each value being a valid mySQL field) with limit $limit starting at $start.
	 * @param string $table
	 * @param array $fields optional
	 * @param array $filters optional
	 * @param int $limit optional
	 * @param int $start optional
	 * @return object
	 */
	public function tableQuery($table, $fields = false, $filters = false, $orderby = false, $limit = false, $start = false) {
		$query = "SELECT SQL_CALC_FOUND_ROWS ";
		if($fields == false)
			$query .= "* FROM $table ";
		else {
			$query .= implode(", ", $fields)." FROM ".$table." ";
		}
		
		if($filters != false) {
			if(!is_array($filters))
				$filters = array($filters);
			if(count($filters) >= 1)
				$query .= "WHERE (".implode(") AND (", $filters).") ";
		}
		
		if($orderby != false)
			$query .= "ORDER BY $orderby ";
			
		if($limit != false) {
			$query .= "LIMIT ";
			
			if($start != false) {
				$query .= intval($start).", ";
			}
			$query .= $limit;
		}
		
		return $this->query($query);
	}
	
	/**
	 * Counts the number of rows matching $filters in the $table table.
	 * @param string $table
	 * @param array $filters optional
	 * @return object
	 */
	public function countTableRows($table, $filters = false) {
		$query = "SELECT COUNT(*) FROM $table ";
		
		if($filters != false) {
			if(!is_array($filters))
				$filters = array($filters);
			$query .= "WHERE (".implode(") AND (", $filters).") ";
		}
		
		return $this->firstResult($this->query($query));
	}
	
	/**
	 * Inserts the array $data into $table table in the database. Assumes the validity of all data.
	 * 
	 * @param string $table
	 * @param array $data
	 * @param bool ignore [optional]
	 * @return bool
	 */
	public function tableInsert ($table, $data, $ignore = "") {
		if($ignore)
			$ignore = "IGNORE ";
		$query = "INSERT {$ignore}INTO $table (".implode(", ", array_keys($data)).") VALUES ('".implode("', '", $data)."')";
		return ($this->query($query) == 1);
	}
	
	/**
	 * Updates the table $table to the given array $data (assumes validity), optionally, on rows that match
	 * the conditions given in the array $filters and limited to $limit rows.
	 * 
	 * @param string $table
	 * @param array $data
	 * @param array $filters optional
	 * @param int $limit optional
	 * @return object
	 */
	public function tableUpdate ($table, $data, $filters = false, $limit = false) {
		$set = array();	//What is going to be set
		foreach($data as $key => $value) {	//Generate what is going to be set
			$set[] = "$key = '$value'";
		}
	
		$query = "UPDATE $table SET ".implode(", ", $set);	//Generate the query
		if($filters != false) {
			if(!is_array($filters))
				$filters = array($filters);
			$query .= " WHERE (".implode(") AND (", $filters).") ";
		}
		
		if($limit != false)
			$query .= " LIMIT $limit";
		
		return $this->query($query);
	}
	
	/**
	 * Deletes all rows rows from the table $table which optionally match $filters, limited to
	 * $limit rows. WARNING: calling this function with only one paramter is equivalent to TRUNCATEing it.
	 * 
	 * @param string $table
	 * @param array $filters optional
	 * @param int $limit optional
	 * @return object
	 */
	public function tableDelete ($table, $filters = false, $limit = false) {
		$query = "DELETE FROM $table";
	
		if($filters != false) {
			if(!is_array($filters))
				$filters = array($filters);
			$query .= " WHERE (".implode(") AND (", $filters).") ";
		}
		
		if($limit != false)
			$query .= " LIMIT $limit";
			
		return $this->query($query);
	}
	
	/**
	 * Returns the first row of a database query's result
	 * @param Object $result
	 * @return array
	 */
	static public function firstRow($result) {
		return mysql_fetch_array($result);
	}
	
	/**
	 * Returns the value of the first column of the first row in a result
	 * @param Object $result
	 * @return mixed
	 */
	static public function firstResult($result) {
		return mysql_result($result, 0, 0);
	}
	
	/**
	 * Returns the number of rows in a given result
	 * @param Object
	 * @return int
	 */
	static public function numRows($result) {
		return mysql_num_rows($result);
	}
	
	/**
	 * Returns the number of fields in a given result
	 * @param Object
	 * @return int
	 */
	static public function numFields($result) {
		return mysql_num_fields($result);
	}
	
	/**
	 * Returns the ID of the last-inserted row
	 * @return int
	 */
	static public function insertedId() {
		return mysql_insert_id();
	}
	
	/**
	 * Returns the number of rows found in the last query
	 * @return int
	 */
	public function calcFoundRows() {
	    if($this->foundRowsOverride != false) {
	        $t = $this->foundRowsOverride;
            $this->foundRowsOverride = false;
            return $t;
        }
		return $this->firstResult($this->query("SELECT FOUND_ROWS()"));
	}
    
    /**
     * Will set a one-time override for the result of foundRows()
     * @param int $t
     * @return null
     */
    public function setFoundRowsOverride($t) {
        $this->foundRowsOverride = $t;
    }
	
	/**
	 * Prints a table out of a given result
	 * @param resource $result The result resource from a database query
	 * @param array $rows [optional] The rows to be printed. This array values are in the following format:
	 * 	{
	 * 		"type" => "column" (Column in the Database), or "text" (arbritrary value)
	 * 		"body" =>	(This is what shows up for this column in the body of the table)
	 * 					if "type" is "column", specify a database column from the result
	 * 					if "type" is "text", any text/HTML is acceptable
	 * 		"header" => (This is what shows up for the )
	 * 					if "type" is "column", any text will do
	 * 					if "type" is "text", any text/HTML is acceptable
	 * 		"width" =>	[optional] CSS Value for the width of the of the table
	 * 	}, ...
	 * If this is left blank (or empty array), all rows will be printed with the headers being the raw database values of the columns
	 * @param array $settings [optional] Settings for the function. Currently not used.
	 * @return bool
	 */
	public function printResultTable($result, Array $columns = array(), Array $settings = array()) {
		if(!$result)
			return false;
			
		$resultrows = $this->numRows($result);
		if($resultrows != 0) {
			//Reset internal row pointer
			mysql_data_seek($result, 0);
		}
		
		$numcols = count($columns);
		if($numcols == 0) {	//No columns specified - default behaviour is to print out everything
			$i = 0;
			while($i < $this->numFields($result)) {	//Get info on each field
				$meta = mysql_fetch_field($result, $i);
				$columns[] = array("type"=>"column", "result"=>$meta->name, "header"=>$meta->name, "body" => $meta->name);
				$i++;
			}
		}
		$numcols = $i;
		
		$tableString = "";	//The table will be stored in this string.
		
		//Print out the header of the table
		$tableString = "<table class=\"list_table\">\n";
		$tableString .= "\t<thead>\n";
		$tableString .= "\t\t<tr class=\"list_table_header\">\n";
		
		foreach($columns as $value) {
			switch($value["type"]) {	//Output each header cell
				case "text" : default : case "column" :
					$tableString .= "\t\t\t<th>".$value['header']."</th>\n";
					break;
			}
		}
		
		$tableString .= "\t\t</tr>\n\t</thead>\n";	//End the table header
		$tableString .= "\t<tbody>\n";//Start the table body
		while($row = mysql_fetch_array($result)) {	//Loop through the rows
			foreach($columns as $value) {	//Loop through each column
				switch($value["type"]) {	//Print out the table cell
					case "text" :
						$tableString .= "\t\t\t<td>".$value['body']."</td>\n";
						break;
					default : case "column" :
						$tableString .= "\t\t\t<td>".$row[$value['body']]."</td>\n";
						break;
				}
			}
		}
		
		//Close the table
		$tableString .= "\t</tbody>\n</table>";
		
		echo $tableString;
		return true;
	}
}