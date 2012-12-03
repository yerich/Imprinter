<?php 
/**
 * This file handles requests to administration scripts that existt
 * for each plugin, which are located in a different folder.
 * 
 * @author rye
 * @package imprinter
 */

require_once("../../include/init.php");
checkUserLogin($user);
/*
 * Redirection Examples:
 * 
 * /admin/views/content/index.php => /views/content/admin/index.php
 */
$_GET['_req_pg'] = trim($_GET['_req_pg']);
if(!isset($_GET['_req_pg'])) {	//A page is required; if it is not provided, go to the admin homepage
	header("Location: /admin/");
	die();
}

//Get rid of trailing slash
if(substr($_GET['_req_pg'], -1) == "/" || substr($_GET['_req_pg'], -1) == "\\")
	$_GET['_req_pg'] = substr($_GET['_req_pg'], 0, -1);

//Append "index.php" onto files if only a directory is specified
if(is_dir("../../plugins/".$_GET['_req_pg']))
	$_GET['_req_pg'] .= "/index.php";

$adminpage = explode("/", $_GET['_req_pg']);	//Separate the string by directory
array_splice($adminpage, 1, 0, "admin");	//Change the directory order
$adminpage = implode("/", $adminpage);	//Glue the array back together
$adminpagedir = dirname($adminpage);	//Get the directory where the page is held in.

if(!file_exists("../../plugins/".$adminpage)) {	// Check to see if the page exists
	header("Location: /admin/");
	die();
}

if(file_exists("../../plugins/".$adminpagedir."/style.css"))	//Include the stylesheet if present
	$_HEADCONTENT[] = "<link type=\"text/css\" rel=\"stylesheet\" href=\"/plugins/".$adminpagedir."/style.css\"/>";

require_once("../../plugins/".$adminpage);	//Include the page
if($_HEADER_PRINTED == true) {		//Don't print the footer unless the header has also been printed
	require_once("../template/footer.php");
}