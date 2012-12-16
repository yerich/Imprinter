<?php
/**
 * Handles tag deletion.
 * 
 * @package imprinter
 * @author rye
 */

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

if(!$_REQUEST['tagid']) {	//Redirect the user back if no pages were specified
	Error::userNotice("No pages were deleted.");
	doRedirect();
}

if(!is_array($_REQUEST['tagid'])) {	//Convert the requested page to an array if it is not an array
	$tags = array($_REQUEST['tagid']);
}
else {
	$tags = $_REQUEST['tagid'];
}

$deletedCount = 0;
$failedCount = 0;
foreach($tags as $value) {
	$value = intval($value);
	$tag = TagFactory::getTagById($db, $value);
	if(!$tag) {
		$failedCount++;
	}
	else {
		if($tag->deleteTag($id)) {
			$deletedCount ++;
		}
		else {
			$failedCount++;
		}
	}
}

//Store the messages
if($deletedCount == 1)
	Error::userMessage($deletedCount." tag has been deleted successfully.");
elseif($deletedCount > 1)
	Error::userMessage($deletedCount." tags have been deleted successfully.");

if($failedCount == 1)
	Error::userError($failedCount." tag has not been deleted successfully.");
elseif($failedCount > 1)
	Error::userError($failedCount." tags have not been deleted successfully.");

doRedirect();	