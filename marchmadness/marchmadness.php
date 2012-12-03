<?php
session_start();
$admin_password = "imprinter468";

//User login code
if($_POST['password'] && $_POST['password'] == $admin_password) {
	$_SESSION['logged_in'] = true;
	$logged_in = true;
}
else if($_POST['password']) {
	$_SESSION['attempts'] += 1;
	if($_SESSION['attempts'] < 2)
		$error = "Password incorrect. Try again:";
	else if($_SESSION['attempts'] < 3)
		$error = "Password incorrect. Try again. Or don't.";
	else if($_SESSION['attempts'] < 4)
		$error = "Password incorrect. Try again <em>if you really want to. Sigh</em>";
	else if($_SESSION['attempts'] < 5)
		$error = "Password incorrect. Yeah. Stop trying already.";
	else if($_SESSION['attempts'] < 6)
		$error = "Password incorrect. Don't you have better things to do?";
	else if($_SESSION['attempts'] < 7)
		$error = "Password incorrect. Ok, final warning. Stop trying. This is annoying.";
	else if($_SESSION['attempts'] < 8)
		$error = "Password incorrect. <em>Really.</em> Stop.";
	else if($_SESSION['attempts'] < 9)
		$error = "Password incorrect. This is REALLY the final warning. Stop trying to cheat. CHEATER.";
	else if($_SESSION['attempts'] < 10)
		$error = "Password incorrect. One more chance. THAT'S IT.";
	else
		$error = "See! I <em>told you so</em>.";
}
else {
	if($_SESSION['attempts'] < 10)
		$_SESSION['attempts'] = 0;
}
if($_GET['logout'] == true)
	$_SESSION['logged_in'] = false;

if($_REQLOGIN == true && ($_SESSION['logged_in'] !== true && $logged_in !== true)) {
	
	
	include("header.php");
	if($_SESSION['attempts'] >= 10) {
		echo "<h2>Login Required</h2><p>Wrong password limit reached. Access Denied.</p>";
		if($error) echo "<span style='color: red'>$error</span>";
	}
	else {
	?>
		<h2>Login Required</h2>
		
		<p>A password is required to access this page.</p>
		
		<?php if($error) echo "<span style='color: red'>$error</span>"; ?>
		
		<form action='?' method='post'>Enter Password: 
		<input type='password' name='password' />
		<br /><input type='submit' value='Login' /> or <a href="./">Go Back</a></form>
	<?php
	}
	include("footer.php");
	die();
}

//Load the master roster and match data from the master file
$masterdata = unserialize(file_get_contents("saves/master.txt"));
if($masterdata) {
	//Roster of 64 teams
	$roster = $masterdata['roster'];
	
	//Results of games stored in this array
	$correct = $masterdata['match'];
	
	$lastgamept1 = $masterdata['final1'];
	$lastgamept2 = $masterdata['final2'];
	
	$contest_open = ($masterdata['contest_open'] == true);
}
else {
	$contest_open = false;
}

if(!$roster) {
	for($i = 0; $i < 64; $i++) {
		$roster[$i] = "Team $i";
	}
}
if(!$correct || !is_array($correct)) {
	for($i = 0; $i < 64; $i++) {
		$correct[$i] = "--";
	}
}


//Get the number of points that a certain game is worth
function getPoints($file, $data = false) {
	global $correct;
	
	if(!$data || !is_array($data)) {
	
		if(!$file) {
			return false;
		}
		if(!file_exists("saves/".$file.".txt")) {
			return false;
		}
		
		
		$data = unserialize(file_get_contents("saves/".$file.".txt"));
	}
	
	$points = 0;
	for($id = 0; $id < 63; $id++) {
		if($data["match"][$id] && $data["match"][$id] == $correct[$id] && $data["match"][$id] != "--") {
			if($id < 32)
				$points += 1;
			else if($id < 48)
				$points += 2;
			else if($id < 56)
				$points += 4;
			else if($id < 60)
				$points += 8;
			else if($id < 62)
				$points += 16;
			else
				$points += 32;
		}
	}
	
	return $points;
}

//Get files inside a directory
function getDirectoryList ($directory) {
    // create an array to hold directory list
    $results = array();

    // create a handler for the directory
    $handler = opendir($directory);

    // open directory and walk through the filenames
    while ($file = readdir($handler)) {

      // if file isn't this directory or its parent, add it to the results
      if ($file != "." && $file != "..") {
        $results[] = $file;
      }

    }

    // tidy up: close the handler
    closedir($handler);

    // done!
    return $results;
}
