<?php
/**
 * admin.php
 * 
 * Singleton wrapper class for adminitration-related functions
 * 
 * @package imprinter
 * @author rye <yerich@gmail.com>
 */

class Admin {
	/**
	 * Changes a User's userlevel
	 * @return boolean
	 * @param string $username
	 * @param int $newlevel
	 */
	function changeUserLevel($username, $newlevel, $database) {
		//Validation
		if (!$userinfo = $user->getInfo($username)) {	//Check for user
			Error::userError ("Username does not exist.");
			$locked = true;
		}
		//Check for valid userlevel - by default, between 0 and 5.
		if($newlevel > MAX_LEVEL || $newlevel < 0 || !is_numeric($newlevel)) {
			Error::userError ("The userlevel that you have entered is invalid.");
			$locked = true;
		}
		if($locked !== true) {	//No errors, proceed
			$userinfo['userlevel'] = $newlevel;	//Change the userlevel
			if($user->editUser($username, $userlevel))	//Save changes
				return true;
		}
		return false;
	}
	
	/**
	 * Mass deletes an array of users using a custom mySQL query
	 * @return boolean
	 * @param array $users
	 */
	static function massDeleteUsers($users, $database) {
		//Check to see if only one username given as a string
		if(!is_array($users) && is_string($users))
			$users = array($users);
		elseif(!is_array($users))	//No users given, return false
			return false;
		
		//Generate the mySQL query
		$query = "DELETE FROM ".TBL_USERS." WHERE";
		$i = 0;
		foreach($users as $value) {
			$query .= " username = '{$value}'";
			if($i != count($users) - 1)
				$query .= " OR";
			$i++;
		}
		return $database->query($query);
	}
}