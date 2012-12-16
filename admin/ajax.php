<?php
/**
 * ajax.php
 * 
 * This file is the server-side handler for the
 * administrative ajax functions.
 * 
 * @author Richard Ye <yerich@gmail.com>
 * @version 1.0
 * @copyright Copyright (c) Richard Ye 2009. All Rights Reserved.
 */

include("../include/init.php");
checkUserLogin($user);

//Admin wants to edit a user's data
if($_GET['action'] == "edituser") {
	if($user->level < 5)	//Must be level five or above
		die("Access Denied");
	//Validate the supplied email address
	if(!$user->validateEmail($_GET['email']))
		die("Invalid Email Address");
	//Validate the userlevel
	if($_GET['userlevel'] < 0 || $_GET['userlevel'] > 5 || !$_GET['userlevel'] || !is_numeric($_GET['userlevel']))
		die("Invalid userlevel");
	//All valid, see if we can edit the user
	if($user->editUser($_GET['username'], array('email' => $_GET['email'], 'userlevel' => $_GET['userlevel'])))
		die("1");
	logError("Could not edit user");
	die("Unknown error");
}
