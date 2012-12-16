<?php

class Theme {
	static function getThemeDir() {
		return WEB_ROOT."/themes/".CURR_THEME."/";
	}
	
	static function getFrontpageList() {
		$files = getDirectoryList(Theme::getThemeDir()."frontpages", array("php"));
		$result = array();
		foreach ($files as $key => $value) {
			$result[] = pathinfo($value, PATHINFO_FILENAME);
		}
		
		sort($result);
		return $result;
	}
}
