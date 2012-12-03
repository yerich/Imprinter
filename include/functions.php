<?php
/**
 * functions.php
 * 
 * Contains helper functions that don't fall within the scope of any class.
 * 
 * @author rye
 * @package imprinter
 */

/**
 * Returns the view of a page, specified by ID, from the database.
 * This function is nessesary to load the correct view class.
 * @param mixed $id
 * @param Object $db
 * @param bool[optional] $urlid
 * @return string
 */
function getPageView($id, $db, $urlid = false) {
	if(!is_numeric($id) || $urlid == true) {	//Check to see the id given represents the url field or the id field
		$id = mysql_real_escape_string($id);
		$query = "SELECT view FROM ".TBL_PAGES." WHERE url='$id' LIMIT 1";
	}
	else {
	$id = intval($id);
		$query = "SELECT view FROM ".TBL_PAGES." WHERE id='$id' LIMIT 1";
	}
	
	$result = $db->query($query);	//Query the database
	if(!$result)
		return false;
		
	$resultrows = $db->numRows($result);	//Check if the row is there or not   
	
	if($resultrows == 0)	//Page not found
		return false;
	
	return $db->firstResult($result);
}

/**
 * Pirnts out a standard pagination display
 * 
 * @param $baseurl - the base URL for the hyperlinks to the other pages. These base URLs have ?page=### or &page=### 
 * appended onto them, as appropriate.
 * @param $currpage The current page number, where 0 is the first page
 * @param @numperpage The number of items per page
 * @param The total number of pages
 * @return void
 */
function printPagination($baseurl, $currpage, $numperpage, $totalrows, $paramstr = 'page') {
    if($currpage < 0) $currpage = 0;
	$start = $currpage * $numperpage;	//Start row
	$stop = min($start + $numperpage, $totalrows);	//End row
	$disppage = $currpage + 1;	//This is the page number that is displayed to the user (since pages actually start at 0)
	$maxpage = ceil($totalrows/$numperpage);	//Highest page
	
	echo "<div class=\"pagination_wrapper\"><div class=\"pagination_left\">";
	echo "Showing ".min($start + 1, $totalrows)." to ".($stop)." of $totalrows</div>";
	if($totalrows > $start + $stop || $disppage > 1) {	//If there is more than one page, print links to other pages
		echo "<div class=\"pagination_right\"><ul>";
		
		if(strstr($baseurl, "?"))
			$baseurl .= "&$paramstr=";
		else
			$baseurl .= "?$paramstr=";
		if($disppage != 1) {	//If this isn't the first page, print out a link to the previous page
			$prevpage = $disppage - 1;
			echo "<li><a href=\"$baseurl$prevpage\" class=\"pageselector\">&laquo; prev</a></li>";
		}
		for ($i = 1; $i <= $maxpage; $i++) {	//Print links to nearby pages in a loop
			if($i == $disppage) {
				echo "<li><span class=\"pageselector active\">$i</span></li>";
			}
			elseif($i > $disppage - 4 && $i < $disppage + 4 || $i == 1 || $i == $maxpage) {
				echo "<li><a href=\"$baseurl$i\" class=\"pageselector\">$i</a></li>";
			}
			elseif($i == $disppage - 4 ||$i == $disppage + 4) {
				echo "<li class=\"noborder\"><span> ... </span></li>";
			}
		}
		if($disppage != $maxpage) {	//If this isn't the last page, print out a link to the next page
			$nextpage = $disppage + 1;
			echo "<li><a href=\"$baseurl$nextpage\" class=\"pageselector\">next &raquo;</a></li>";
		}
		echo "</ul></div>";
	}
	echo "</div>";
}

/**
 * Returns the page object for a specified page URL string.
 *
 * @param mixed $id
 * @param Object $db
 * @param bool[optional] $urlid Parse the ID as a URL
 * @return Object 
 */
function loadPage($id, $db, $urlid = false) {
	if(is_numeric($id)) {
		$pageView = getPageView($id, $db, $urlid);
	}
	if(!$pageView)
		return false;
	
	require_once(WEB_ROOT . "/views/$pageView/$pageView.php");
	$page = new $pageView($db);
	$page->getPage($id, $urlid);
	
	return $page;
}

/**
 * Prints a single HTML page with a simple layout, designed for login forms, error pages, etc.
 * @param string $title
 * @param string $content
 */
function printBasicLayout($title, $content) {
	echo "<!DOCTYPE HTML><html><head><title>$title</title><link rel=\"stylesheet\" type=\"text/css\" href=\"/themes/imprinter_basic.css\" /></head>".
		"<body><div id='header'><a href='/'>&laquo; Back to Homepage</a></div><div id='wrapper'><h1>$title</h1>$content</div>".
		"<div id=\"footer\">Powered by Imprinter. Copyright &copy; Richard Ye, Imprint Publications 2011-". date("Y")." .</div></body></html>";
}

/**
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */ 
function strposall($haystack, $needle) {
    $s = $i = 0;
    while (is_integer($i)) {
        $i = strpos($haystack,$needle,$s);
        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i+strlen($needle);
        }
    }
    if (isset($aStrPos))
        return $aStrPos;
    else
        return false;
} 

/**
 * Checks the login of a user, and redirects to the login page if 
 * @param Object $user
 */
function checkUserLogin($user, $level = 1) {
	if(!$user->loggedIn || $user->level < $level) {
		header("Location: /login.php?ref=".urlencode($_SERVER['REQUEST_URI']));
		die();
	}
}

/**
 * Puts a link into a JavaScript element on the <head> element, provided
 * that a standard content renderer is used.
 * 
 * @param $js strig
 * @return boolean
 */
function include_javascript($js) {
	global $_HEADCONTENT;	//This global variable (eww, I know) will be used by the header files
	
	switch ($js) {
		case "ckeditor" :
			$_HEADCONTENT[] = "<script type=\"text/javascript\" src=\"/scripts/ckeditor/ckeditor.js\"></script>";
			break;
		case "fileupload" :
			$_HEADCONTENT[] = "<script type=\"text/javascript\" src=\"/scripts/fileupload/jquery.fileupload.js\"></script>";
			break;
		default:
			$_HEADCONTENT[] = "<script type=\"text/javascript\" src=\"/scripts/$js.js\"></script>";
			break;
	}
	return true;
}

/**
 * Prints out a menu (defined by an array) as HTML lists
 * 
 * @param array $menu
 * @return void
 */
function printMenu($menu) {
	if(count($menu) == 0)	//Don't print anything for an empty menu
		return;
	
	echo "<ul>";
	$active = false;
	$prevactive = -1;
	$activelen = 0;
	foreach($menu as $key => $value) {	//loop through looking for active element
		//If the URL of the Link is the start of the requested URL, then the element is active
		if(strpos($_SERVER['REQUEST_URI'], $value['url']) === 0) {
			if(strlen($value['url']) > $activelen) {
				$menu[$key]['active'] = true;
				$activelen = strlen($value['url']);
				if($prevactive != -1)
					$menu[$prevactive]['active'] = false;
				$prevactive = $key;
			}
			$active = true;
		}
		
		if(isset($value['active']) && $value['active'] == true)
			$active = true;
	}
	
	foreach($menu as $key => $value) {
		echo "<li";
		//The list element has the active class if it is the default and is not overridden
		if(isset($value['default']) && $value['default'] == true && $active == false) {
			echo " class=\"active\"";
		}
		
		if(isset($value['active']) && $value['active'] == true) {
			echo " class=\"active\"";	//Give active elements the active class
		}
		echo "><a href=\"{$value['url']}\">" . $value['text'] ."</a></li>";
	}
	echo "</ul>";
	return;
}

/**
 * Removes empty array items or items consisting wholly of whitespace in an array
 * 
 * @param array $inputArray
 * @return array
 */
function trimArray(array $inputArray) {
	foreach($inputArray as $key=>$value) {
		if(trim($value) == "")
			unset($inputArray[$key]);
	}
	return $inputArray;
}

//Get files inside a directory
function getDirectoryList ($directory, $ext = false) {
	$results = array(); // create an array to hold directory list
	$handler = opendir($directory); // create a handler for the directory
	// open directory and walk through the filenames
	while ($file = readdir($handler)) {
		// if file isn't this directory or its parent, add it to the results
		if ($file != "." && $file != "..") {
			//Check extension
			if(!$ext || !is_array($ext) || in_array(pathinfo($file, PATHINFO_EXTENSION), $ext))
				$results[] = $file;
		}
	}
	
	// tidy up: close the handler
	closedir($handler);
	return $results;
}

/**
 * Prints the working directory of the active theme, for use in relative URLs for themes
 */
function theme_dir() {
	echo "/themes/".CURR_THEME."/";
}


/**
 * Here is a far better function for the atypical process of stripping tags:
 * 
 * http://www.php.net/manual/en/function.strip-tags.php#107390
 */
function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );
      
    return strip_html_tags( $text , '<b><a>' );
}

/**
 * Prints out the standard save controls for pages.
 */
function printStandardPageControls($ref = "false") {
?>
	<fieldset>
		<a class="bluebutton" href="javascript: void(0)" onclick="$('#editor_form').submit()">Save</a>
		<a class="greybutton" href="javascript: void(0)" onclick="$('#form_redir').val('<?php echo urlencode($ref) ?>'); $('#editor_form').submit()">Save and Return</a>
		<a class="greybutton" href="javascript: void(0)" onclick="$('#form_redir').val('<?php echo urlencode("?action=new") ?>'); $('#editor_form').submit()">Save and Create New</a>
		<a class="greybutton" href="<?php echo $ref ?>">Cancel</a>
	</fieldset>
<?php
}


function utf8_encode_recursive($a) {
	if(!is_array($a))
		return utf8_encode($a);
    $b = array();
    foreach($a as $key=>$value) {
        $k = utf8_encode($key);
        if(is_array($value)) {
            $b[$k] = utf8_encode_recursive($value);
        }
        else {
            $b[$k] = utf8_encode($value);
        }
    }
    return $b;
}

function utf8_decode_recursive($a) {
	if(!is_array($a))
		return utf8_decode($a);
    $b = array();
    foreach($a as $key=>$value) {
        $k = mb_check_encoding($key, 'UTF-8') ? utf8_decode($key) : $key;
        if(is_array($value)) {
            $b[$k] = utf8_decode_recursive($value);
        }
        else {
            $b[$k] = mb_check_encoding($value, 'UTF-8') ? utf8_decode($value) : $value;
        }
    }
    return $b;
}



function htmlentities_recursive($a, $flags = null, $encoding = null) {
    if(is_array($a)) {
        $b = array();
        foreach($a as $key=>$value) {
            $k = htmlentities($key, $flags, $encoding);
            $b[$k] = htmlentities_recursive($value, $flags, $encoding);
        }
        return $b;
    }
    return htmlentities($a, $flags, $encoding);
}


function truncateText($text, $len) {
    $r = substr(strip_tags(html_entity_decode($text)), 0, $len);
    if(strlen(strip_tags(html_entity_decode($text))) > $len) 
        $r .= "...";
    return $r;
}
