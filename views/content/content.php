<?php
/**
 * The content view is for static content pages on the website. These pages
 * are not articles, rather, they're for pages like "About" or "Staff" which
 * stick around for long period of time.
 * 
 * @author rye
 * @package imprinter
 */

class Content extends Page {
	function getPageTitle() {
		return $this->pagedata['title'];
	}
	
	function getPageContent() {
		return $this->pagedata['content'];
	}
	
	function getPageAuthors() {
		return implode(", ", $this->getAuthorNames());
	}
}