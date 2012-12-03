<?php
/**
 * Handles mass article editing
 * 
 * @package imprinter
 * @author rye
 */
checkUserLogin($user);

//Redirect the user back to the previous page
function doRedirect() {
	if(isset($_REQUEST['ref'])) {
		header("Location: ".$_REQUEST['ref']);
	}
	else {
		header("Location: ".$_SERVER['HTTP_REFERER']);
	}
	die();
}

if(!$_REQUEST['pgid']) {	//Redirect the user back if no pages were specified
	Error::userNotice("No pages were selected.");
	doRedirect();
}

if(!is_array($_REQUEST['pgid'])) {	//Convert the requested page to an array if it is not an array
	$pages = array($_REQUEST['pgid']);
}
else {
	$pages = $_REQUEST['pgid'];
}

$completedCount = 0;
$failedCount = 0;
foreach($pages as $value) {
	$value = intval($value);
	if($newtime = strtotime($_POST['massedit_date'])) {
		if ($db->tableUpdate(TBL_PAGES, array("created" => $newtime), "id = $value"))
			$completedCount++;
		else
			$failedCount++;
	}
	else
		$failedCount++;
}

//Store the messages
if($completedCount == 1)
	Error::userMessage($completedCount." page has been edited successfully.");
elseif($completedCount > 1)
	Error::userMessage($completedCount." pages have been edited successfully.");

if($failedCount == 1)
	Error::userError($failedCount." page has not been edited successfully.");
elseif($failedCount > 1)
	Error::userError($failedCount." pages have not been edited successfully.");

doRedirect();	