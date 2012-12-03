<?php
/**
 * User-configurable settings, stored in the database and loaded with every request.
 * 
 * @package imprinter
 * @author rye
 */

class Settings {
	/**
	 * Given a database connection, this will load the configuration values from the relevant table
	 * and load them as constants.
	 * 
	 * @param Object Database
	 * @return boolean
	 */
	static function getDatabaseConfig($database) {
		global $_CONFIG;
		$query = "SELECT * FROM ".TBL_CONFIG." WHERE 1";
		$_CONFIG = array();
		
		$result = $database->query($query);
		if(!$result)
			return false;
		
		while(1) {
			$row = mysql_fetch_array($result);
			if(!$row) break;
			
			$_CONFIG[$row['key']] = $row;
			define(strtoupper($row['key']), $row['value']);
		}
		return true;
	}
}