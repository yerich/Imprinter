<?php 
/**
 * init.php
 * 
 * Essentially a header file. Includes the classes needed for loading any page on the website.
 * 
 * @author rye
 * @package imprinter
 */
ob_start();
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once("config.php");
require_once("error.php");
require_once("settings.php");

define("WEB_ROOT", "/nfs/c07/h04/mnt/107536/domains/theimprint.ca/html");	//WEB_ROOT is the root folder of what's visible on the website.
date_default_timezone_set('America/Toronto');	//TODO Move Timezone settings to configuration file

require_once("database.php");
require_once("user.php");
require_once("page.php");
require_once("tag.php");
require_once("functions.php");
require_once("upload.php");
require_once("media.php");
require_once("theme.php");

set_error_handler(array('Error', 'phpError'));	//Set the error handler to use the error-handling class

$db = new Database;		//Create a new database object - connect and initialize

Settings::getDatabaseConfig($db);	//Load the site configuration for the database

$user = new User($db);	//Check user login status
