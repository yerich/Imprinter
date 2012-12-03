<?php
/**
 * The article view handles text articles (not photo galleries).
 * 
 * @author rye
 * @package imprinter
 */

class Article extends Page {
	function getPageTitle() {
		return $this->pagedata['title'];
	}
	
	function getPageContent() {
		return $this->pagedata['content'];
	}
	
	function getSubtitle() {
		return $this->pagedata['data']['subtitle'];
	}
	
	function getSection() {
		return $this->getTagNamesByType("section");
	}
	
	function getPageAuthors() {
		return implode(", ", $this->getAuthorNames());
	}
}