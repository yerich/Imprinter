<?php
/**
 * Handles page deletion.
 * 
 * @package imprinter
 * @author rye
 */
include("../include/init.php");
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
	Error::userNotice("No pages were deleted.");
	doRedirect();
}

if(!is_array($_REQUEST['pgid'])) {	//Convert the requested page to an array if it is not an array
	$pages = array($_REQUEST['pgid']);
}
else {
	$pages = $_REQUEST['pgid'];
}

$deletedCount = 0;
$failedCount = 0;
foreach($pages as $value) {
	$value = intval($value);
	if ($db->tableDelete(TBL_PAGES, "id = $value", 1))
		$deletedCount++;
	else
		$failedCount++;
}

//Store the messages
if($deletedCount == 1)
	Error::userMessage($deletedCount." page has been deleted successfully.");
elseif($deletedCount > 1)
	Error::userMessage($deletedCount." pages have been deleted successfully.");

if($failedCount == 1)
	Error::userError($failedCount." page has not been deleted successfully.");
elseif($failedCount > 1)
	Error::userError($failedCount." pages have not been deleted successfully.");

doRedirect();	