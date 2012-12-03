<?php
require_once(WEB_ROOT . "/views/content/content.php");

$list_type = $_GET['type'];
if(!$list_type) {
	header("Location: /");
	die();
}

$tags = TagFactory::getTagsByType($db, $list_type, "name asc");

if(count($tags) == 0) {
	Error::error404();
	die();
}

$page = new Content($db);
$page->setPageData(array("title" => ucfirst($list_type)." Listing"));

$content = "<ul>";
foreach($tags as $value) {
	$content .= "<li><a href='".$value->getURL()."'>".$value->getName()."</a></li>";
}
$content .= "</ul>";

$page->setPageData(array("content" => $content));

require(Theme::getThemeDir()."header.php");

require(Theme::getThemeDir()."content.php");

require(Theme::getThemeDir()."footer.php");