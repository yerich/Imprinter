<?php 
/**
 * do.php
 * 
 * All user actions are routed through this page: login, logout, etc.
 * 
 * @author rye
 * @package ralphy
 */

require_once("include/init.php");

if($_REQUEST['action'] == "login") {
	$ref = $_SERVER['HTTP_REFERER'];	//user will get redirected to this page
	if(isset($_REQUEST['ref']))		//redirect override by form
		$ref = $_REQUEST['ref'];
	if(strstr($ref, "login.php"))	//Don't redirect back to the login form
		$ref = "/admin/";
		
	if($user->loggedIn == true) {
		if($_GET['li'] != "true") {
			$_SESSION['message'] = "You are already logged in.";
			header("Location: /");
		}
		else
			header("Location: /?li=true");
		die();
	}
	
	if(isset($_POST['username']) && isset($_POST['password'])) {
		//Try to login
		if($user->login($_POST['username'], $_POST['password'], $_POST['remember'])) {	//Login sucessful
			//The "?li=true" appended to the URL interferes with other parameters, so check to see that it doesn't.
			if(strstr($ref, "?")) {
				//Redirect the user
				header("Location: ".$ref."&li=true");
				exit;
			}
			else {
				header("Location: ".$ref."?li=true");
				exit;
			}
		}
	}
	//No username/password provided - display a form
	else {
		header("Location: /login.php?ref=".urlencode($ref));
		die();
	}
	//login unsucessful code goes here
	//Redirect the user and the page should give an error message.
	//Redirect the user
	header("Location: /login.php?ref=".$ref."&li=false");
	exit;
}

header("Location: /");
die();