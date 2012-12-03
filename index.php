<?php
/**
 * This file handles all requests to files that don't actually exist on
 * the server - all pages, articles, etc.
 * 
 * @author rye
 * @package imprinter
 */

require_once("include/init.php");

/*
 * Request handling begins here
 * The server redirects all requests to non-existent files to index.php, in the hopes that the file exists
 * somewhere on the database and can be loaded from there.
 * 
 * Example:
 * http://imprinter.example.com/agj3hgsi -> http://imprinter.example.com/index.php?_req_pg=agj3hgsi
 * [provided that http://imprinter.example.com/agj3hgsi doesn't actually exist].
 */
if(isset($_GET['_req_pg']))
	$requestPage = $_GET['_req_pg'];
else
	$requestPage = HOMEPAGE_ID;		//A request to index.php directly will load the homepage.

if($requestPage == "feed/" || $requestPage == "feeds/" || $requestPage == "feed"
	 || $requestPage == "feed/all" || $requestPage == "feed/all/" || $requestPage == "feeds/all/"
	  || $requestPage == "feeds/all") {
	$requestPage = "feeds";
	$_GET['type'] = "all";
}
	 
if(stristr($requestPage, "archives/tag/")) {	//Handle Legacy URLs from Wordpress
	$lookup_tag = explode("archives/tag/", $requestPage);
	$lookup_tag = str_replace("-", " ", $lookup_tag[1]);
	
	if($tag = TagFactory::getTagByName($db, $lookup_tag, "section")) {
		header("Location: /section/".$lookup_tag);
		die();
	}
	if($tag = TagFactory::getTagByName($db, $lookup_tag, "author")) {
		header("Location: /author/".$lookup_tag);
		die();
	}
	if($tag = TagFactory::getTagByName($db, $lookup_tag, "series")) {
		header("Location: /series/".$lookup_tag);
		die();
	}
}

//Handlers for Tag Frontpages
$req_pg = $_GET['_req_pg'];
$req_pg_parts = explode("/", $req_pg);
if($req_pg_parts[0] == "section") {
	$requestPage = FRONTPAGE_SECTION;
	$tag = TagFactory::getTagByName($db, $req_pg_parts[1], "section");
	if(!$tag)
		Error::error404();
	else
		$_GET['tagid'] = $tag->getId();
}
if($req_pg_parts[0] == "author") {
	$requestPage = FRONTPAGE_AUTHOR;
	$tag = TagFactory::getTagByName($db, $req_pg_parts[1], "author");
	if(!$tag) {
		Error::error404();
	}
	else
		$_GET['tagid'] = $tag->getId();
}
if($req_pg_parts[0] == "series") {
    $requestPage = FRONTPAGE_SERIES;
    $tag = TagFactory::getTagByName($db, $req_pg_parts[1], "series");
    if(!$tag) {
        Error::error404();
    }
    else
        $_GET['tagid'] = $tag->getId();
}
if($req_pg_parts[0] == "article") {
	$requestPage = intval($req_pg_parts[1]);
}
	

//Handlers for plugins
if(is_file(WEB_ROOT."/plugins/".$requestPage)) {
	include(WEB_ROOT."/plugins/".$requestPage);
}
elseif(is_file(WEB_ROOT."/plugins/".$requestPage."/index.php")) {
	include(WEB_ROOT."/plugins/".$requestPage."/index.php");
}
else {
	$page = loadPage($requestPage, $db, false);
	
	if(!$page) {
		unset($id);
		$query = $db->query("SELECT id FROM ".TBL_PAGES." WHERE title='".mysql_real_escape_string($requestPage)."' LIMIT 1");
		if($db->numRows($query) > 0)
			$id = $db->firstResult($query);
		
		if($id) {
			header("Location: /$id");
			die();
		}
		Error::error404();
		
	}
	
	$page->printPageHeader();
	$page->printPageBody();
	$page->printPageFooter();
}