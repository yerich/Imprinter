<?php
/**
 * user.php
 * 
 * This file contains all user session data.
 * It fetches user information from the
 * mySQL database.
 * 
 * This file contains functions to login, logout,
 * obtain user information, create a new user and
 * change a users information.
 * 
 * Originally written in 2009. Modified for Imprinter.
 * 
 * @author rye <yerich@gmail.com>
 * @version 1.1
 * @package imprinter
 */

require_once("database.php");
 
/**
 * A singleton class, designed to handle user logins and administration.
 * @author rye
 */
class user {
	var $username;			//Username of the current logged-in user
	var $level;				//Level of the current logged-in user
	var $id;				//ID of the current logged-in user
	var $loggedIn;			//Will be true if user is logged in
	var $info = array();	//All information pertaining to the current logged-in user
	var $db;
	
	/**
	 * Initalises the session: tells PHP to start the session,
	 * checks login status.
	 * @return null
	 */
	function __construct($db) {
		$this->db = $db;
		
		if(!isset($_SESSION))
			session_start();
		$this->loggedIn = $this->checkLogin();
	}
	
	/**
	 * Gets all of the user's information for a given username.
	 * @return array
	 * @param string $username
	 */
	function getInfo($username) {
		//Query the database for the information
		$username = mysql_real_escape_string($username);
		$result = $this->db->query("SELECT * FROM ".TBL_USERS." WHERE username = '$username' LIMIT 1");
		$resultrows = @mysql_num_rows($result);	//Check number of rows (if 0, then user doesn't exist)
		if($resultrows > 0)	//User exists, return data
			return mysql_fetch_array($result);
		else	//User doesn't exist, return false
			return false;
	}
	
	/**
	 * Checks if the current session's user is logged in.
	 * Query the database to see if the current user's
	 * credintials match the ones on record.
	 * @return boolean
	 */
	function checkLogin() {
		//Check to see if the username and password are stored in the client's cookie
		$tmp_username = null;
		if(isset($_COOKIE['username']) && isset($_COOKIE['sessid'])) {
			$tmp_username = $_COOKIE['username'];
			$tmp_sessid = $_COOKIE['sessid'];
		}
		//Check to see if the username and password are stored in the PHP session data
		elseif(isset($_SESSION['username']) && isset($_SESSION['sessid'])) {
			$tmp_username = $_SESSION['username'];
			$tmp_sessid = $_SESSION['sessid'];
		}
		
		if($tmp_username) {	//User has credintials, check validity
			$userinfo = $this->getInfo($tmp_username);	//Get userinfo
			if(is_array($userinfo)) {	//Check for the existence of the user
				//Validate the password - the sessid provided should match the one in the database
				if(strstr($userinfo['sessid'], "&".$tmp_sessid."&") && $userinfo['sessid'] != "loggedout") {
					//User is valid - update the array information
					$this->info = $userinfo;
					$this->level = $userinfo['userlevel'];
					$this->id = $userinfo['id'];
					$this->loggedIn = true;
					$this->username = $userinfo['username'];
					return true;
				}
			}
		}
		
		//This code will be excecuted if the client's credintials are not valid or the user
		//is not logged in
		$this->level = GUEST_LEVEL;
		$this->username = GUEST_NAME;
		$this->loggedIn = false;
		return false;
	}
	
	/**
	 * Check if the currently logged in user meets the login and level requirements. If not,
	 * then redirect the user appropriately.
	 * @param int $level
	 * @return bool
	 */
	function checkUser($level) {
		if($this->level < $level)
			return true;
		else
			return false;
	}
	
	/**
	 * Logs in the client based on what credientials were supplied.
	 * Validates the login via the database.
	 * @return boolean
	 * @param object $username
	 * @param object $password
	 * @param object $remember
	 */
	function login($username, $password, $remember = false) {
		if(!$username || !$password)	//No username or password entered
			return false;
		
		$userinfo = $this->getInfo($username);	//Get userinfo
		if(!is_array($userinfo))	//Username not found
			return false;
		//Check to see if the password is correct
		if($this->checkPassword($password, $userinfo)) {
			$newsessid = $this->randStr(32);	//Generate a new session id
			
			//Password is correct - log the user in
			if($remember == true) {
				setcookie("username", $username, time()+60*60*24*30, "/");
				setcookie("sessid", $newsessid, time()+60*60*24*30, "/");
			}
			else {	//User did not select "remember me", do not set cookies. Use session instead.
				$_SESSION['username'] = $username;
				$_SESSION['sessid'] = $newsessid;
			}
			
			//Set the database to match the new session id
			if($this->addUserSessid($username, $newsessid))
				return true;
			else
				die("Database Error.");
		}
		else
			return false;	//Password incorrect
	}
	
	/**
	 * Logs out the current user by clearing their session data
	 * @return boolean
	 */
	function logout($all = false) {
		//Check to see if the user is not logged in. If so, then the user is still not logged in.
		if(!$this->loggedIn)
			return true;
		//Unset session vars
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		//Unset cookies (by setting them to expire in the past)
		setcookie("username", "asdf", time()-60*60*24*30, "/");
		setcookie("sessid", "asdf", time()-60*60*24*30, "/");
		//Generate a new session id just to make sure
		if($all == true)
			return $this->updateUserSessid("asdf", $this->randStr(32));
		else
			return $this->removeUserSessid("asdf", $this->info['sessid']);
	}
	
	/**
	 * Creates a new user. The paramater is an array, this
	 * function assumes the vaildity of the parameters passed
	 * to it.
	 * @return boolean
	 * @param array $userdata
	 */
	function newUser($userdata) {
		//Get the information from the array
		$username = mysql_real_escape_string($userdata['username']);
		$email = mysql_real_escape_string($userdata['email']);
		$userlevel = intval($userdata['userlevel']);
		$salt = $this->randStr(10);	//Generate a salt
		$password = md5($userdata['password'].$salt);	//Generate the md5 hashsum of the password
		
		$query = "INSERT INTO ".TBL_USERS." (username, password, email, userlevel, salt) VALUES".
			"('$username', '$password', '$email', '$userlevel', '$salt')";
		if($this->db->query($query))
			return true;
		return false;
	}
	
	/**
	 * Edits the information for an existing user. Find a user by
	 * username and edits it based on the provided array with the
	 * new user data, which the function assumes is valid.
	 * @return boolean
	 * @param string $username
	 * @param array $userdata
	 */
	function editUser($username, $userdata) {
		//Get the new values from the array
		$username = mysql_real_escape_string($username);
		$email = mysql_real_escape_string($userdata['email']);
		$userlevel = intval($userdata['userlevel']);
		
		//Password wants to be changed as well
		if($userdata['password']) {
			$password = $userdata['password'];
			$salt = $this->randStr(10);	//Generate a new salt
			$password = md5($password.$salt);	//Generate the md5 hashsum of the password
			
			return $this->db->query("UPDATE ".TBL_USERS." SET password = '$password', email = '$email', userlevel = '$userlevel', salt = '$salt' "
				."WHERE username = '$username'");
		}
		else {	//No change to password
			return $this->db->query("UPDATE ".TBL_USERS." SET email = '$email', userlevel = '$userlevel' "
				."WHERE username = '$username'");
		}
	}
	
	/**
	 * Deletes a user based on username
	 * @return boolean
	 * @param string $username
	 */
	function deleteUser($username) {
		$username = mysql_real_escape_string($username);
		return $this->db->query("DELETE FROM ".TBL_USERS." WHERE username = '$username' LIMIT 1");
	}
	
	/**
	 * Check to see if a user exists with a specified username
	 * @return boolean
	 * @param string $username
	 */
	function usernameTaken($username) {
		$username = mysql_real_escape_string($username);
		if(mysql_num_rows($this->db->query("SELECT id FROM ".TBL_USERS." WHERE username = '$username' LIMIT 1")) == 1)
			return true;
		return false;
	}
	
	/**
	 * Changes the session id of a username to a new string
	 * @return boolean
	 * @param string $username
	 * @param string $sessid
	 */
	function updateUserSessid($username, $sessid) {
		//Query the database to change the session id of a given user.
		$username = mysql_real_escape_string($username);
		$sessid = mysql_real_escape_string($sessid);
		$result = $this->db->query("UPDATE ".TBL_USERS." SET sessid = '&$sessid&' WHERE username = '$username' LIMIT 1");
		$this->init();
		return $result;
	}
	
	/**
	 * Adds a valid session id to the database for a username
	 * @param string $username
	 * @param string $sessid
	 * @return object
	 */
	function addUserSessid($username, $sessid) {
		//Query the database to add a new session id
		$username = mysql_real_escape_string($username);
		$sessid = mysql_real_escape_string($sessid);
		return $this->db->query("UPDATE ".TBL_USERS." SET sessid = CONCAT('&$sessid&', sessid) "
			."WHERE username = '$username' LIMIT 1");
	}
	
	/**
	 * Removes 
	 * @param object $username
	 * @param object $sessid
	 * @return boolean
	 */
	function removeUserSessid($username, $sessid) {
		$username = mysql_real_escape_string($username);
		$sessid = mysql_real_escape_string($sessid);
		if (!$result = $this->db->query("UPDATE ".TBL_USERS." SET sessid = REPLACE(sessid, '&$sessid&', '') "
			."WHERE username = '$username' LIMIT 1")) return false;
		return (mysql_affected_rows() == 1);
	}
	
	/**
	 * Logs out all sessions (sets the session data to a new random string)
	 * @param string $username
	 * @return boolean
	 */
	function logoutAll($username) {
		return $this->updateUserSessid($username, $this->randStr(32));
	}
	
	/**
	 * Gets the number of current sessions for a given user
	 * @return int
	 */
	function countSessions() {
		return count(explode("&&", $this->info['sessid']));
	}
	
	/**
	 * Generates a password baased on the hashsum of the currently logged-in
	 * user's salt plus the password passed in the parameter.
	 * @return string
	 * @param string $password
	 */
	function generatePasswordHash($password) {
		return md5($password.$this->userinfo['salt']);
	}
	
	/**
	 * Checks the supplied password string against the hashsum in the database
	 * @param string $password
	 * @return string
	 */
	function checkPassword($password, $userinfo = false) {
		//Check the password
		if(!$userinfo) {
			if (md5($password.$this->userinfo['salt']) == $this->info['password'])
				return true;
			return false;
		}
		else {
			if (md5($password.$userinfo['salt']) == $userinfo['password'])
				return true;
			return false;
		}
	}
	
	/**
	 * Checks if a given password is valid (i.e. correct number of characters)
	 * @return boolean, int
	 * @param string $password
	 */
	function validatePassword($password) {
		//Only condition: password >= 6 chars and <= 20 chars in length
		if(strlen($password) < 6 || strlen($password) > 20)
			return false;
		return true;
	}
	
	/**
	 * Checks if a given e-mail is valid using regex.
	 * @return boolean
	 */
	function validateEmail($email) {
		return preg_match("%^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$%", strtolower($email));
	}
	
	/**
	 * Generates a hex-friendly string of length $length
	 * @return string
	 * @param int $len
	 */
	function randStr($len) {
		$chars = "abcdef0123456789";
		for ($i=0; $i < $len; $i++) {
		    $returnstring .= $chars[mt_rand(0, strlen($chars))];
		}
		return $returnstring;
	}
}