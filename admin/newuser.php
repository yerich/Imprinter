<?php 
/**
 * This page has a form for creating a new user
 * 
 * @package imprinter
 * @
 */
$_PAGETITLE = "Create a New User";
include("../include/init.php");
checkUserLogin($user);

$username = $_POST['username'];
$email = $_POST['email'];

//Process a submitted form
if($_POST) {
	//Validation
	if(!$_POST['password']) {
		Error::userError("Please enter a password.");
		$locked = true;
	}
	elseif(!$user->validatePassword($_POST['password'])) {
		Error::userError("Your password is invalid. Passwords must be from 6 to 20 characters in length.");
		$locked = true;
	}
	elseif($_POST['password'] !== $_POST['passwordc']) {
		Error::userError("The passwords that you have entered do not match.");
		$locked = true;
	}
	
	if(!$_POST['email']) {	//Check to see if e-mail has been entered
		Error::userError("You must enter an e-mail address.");
		$locked = true;
	}
	elseif(!$user->validateEmail($_POST['email'])) {	//Validate the email address
		Error::userError("The email address that you have entered is not valid.");
		$email = $_POST['email'];
		$locked = true;
	}
	
	//Check to see if the username has been taken
	if($user->usernameTaken($_POST['username'])) {
		Error::userError("The username specified has been taken.");
		$locked = true;
	}
	elseif(strlen($_POST['username']) < 4) {
		Error::userError("The username specified is too short. Usernames must be from 4 to 20 characters long.");
		$locked = true;
	}
	elseif(strlen($_POST['username']) > 20) {
		Error::userError("The username specified is too long. Usernames must be from 4 to 20 characters long.");
		$locked = true;
	}
	elseif(!preg_match('/^\w+$/',$_POST['username'])) {
		Error::userError("The username specified users invalid characters. Usernames must only use numbers, letters, and underscores.");
	}
	
	//Check userlevel
	if(!$_POST['userlevel'] || !is_numeric($_POST['userlevel']) || $_POST['userlevel'] > 5 || $_POST['userlevel'] < 0) {
		Error::userError("Invalid User Level. User Level must be an integer value between 0 and 5.");
		$locked = true;
	}
	
	if($locked !== true) {	//No errors, proceed to save the data
		//Try to save the user data
		if($user->newUser(array('username' => $_POST['username'], 'password' => $_POST['password'], 
				'userlevel' => $_POST['userlevel'], 'email' => $_POST['email']))) {
			Error::userMessage("Your new user, \"{$_POST['username']}\", has been created sucessfully.");
			unset($username, $email);
		}
		else {
			Error::userError("There was a problem creating your user.");	//Save unsucessful
		}
	}
}

include("template/header.php");
?>

<form action="newuser.php" method="post" id="newuser_form">
	<fieldset>
		<legend>User Information</legend>
		<table class="form_layout">
			<tr>
				<td><label for="form_username">Username</label></td>
				<td><input type="text" name="username" id="form_username" value="<?php echo $username?>" /><br />
					<span class="info">Must be 4 - 20 characters long using numbers, letters and underscores only.</span></td>
			</tr>
			<tr>
				<td><label for="form_password">Password</label></td>
				<td><input type="password" name="password" id="form_password"/><br />
					<span class="info">Must be 6 - 20 characters long.</span></td>
			</tr>
			<tr>
				<td><label for="form_passwordc">Confirm Password</label></td>
				<td><input type="password" name="passwordc" id="form_passwordc"/></td>
			</tr>
			<tr>
				<td><label for="form_email">Email Address</label></td>
				<td><input type="text" name="email" id="form_email" value="<?php echo $email?>"/><br />
					<span class="info">Please provide a valid email address.</span></td>
			</tr>
			<tr>
				<td><label for="form_userlevel">User Level</label></td>
				<td>
					<select name="userlevel" id="form_userlevel">
<?php
for($i = 1; $i < 6; $i++) {
	if($userlevel == $i)
		echo "\t\t\t\t\t<option value=\"$i\" selected=\"selected\">$i</option>\n";
	else
		echo "\t\t\t\t\t<option value=\"$i\">$i</option>\n";
}
?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="i">
		<a class="bluebutton" href="javascript: void(0)" onclick="$('#newuser_form').submit()">Save</a>
		<a class="greybutton" href="./users.php">Cancel</a>
	</fieldset>
</form>

<?php 
include("template/footer.php");