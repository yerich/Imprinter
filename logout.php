<?php
/**
 * logout.php
 * 
 * Logout handler, logs out and redirects the user to the previous page.
 * 
 * @author rye
 * @package imprinter
 */
include("include/init.php");
$user->logout();

//Redirect the user and the page should give an message saying that the user was logged out
header("Location: /login.php?lo=true");