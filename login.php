<?php 
/**
 * login.php
 * 
 * The login form.
 * 
 * @author rye
 * @package imprinter
 */

require_once("include/init.php");

$content = "<p>You need to login to continue.</p>";

if(isset($_REQUEST['lo']) && $_REQUEST['lo']=="true")
	$content .= "<p><strong>You have been logged out.</strong></p>";

if(isset($_REQUEST['li']) && $_REQUEST['li']=="false")
	$content .= "<p><strong>Your username or password is incorrect.</strong></p>";
	
$content .= <<<HEREDOC


<form action="do.php" method="post">
	<table>
		<tr>
			<td><label for="username">Username:</label></td>
			<td><input type="text" name="username" id="username" /></td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input type="password" name="password" id="password" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="checkbox" name="remember" id="input_remember" />
			<label for="input_remember">Remember Me</label></td>
		</tr>
	</table>

	<input type="hidden" name="action" value="login" />
	
	<input type="Submit" value="Login" />
HEREDOC;

if(isset($_REQUEST['ref']))
	$content .= "\n\t<input type=\"hidden\" name=\"ref\" value=\"{$_REQUEST['ref']}\" />\n";

$content .= "</form>";

printBasicLayout("Login", $content);