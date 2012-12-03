<?php
require_once(WEB_ROOT . "/views/content/content.php");
require(dirname(__FILE__)."/stopwords.php");
$page = new Content($db);
$page->setPageData(array("title" => "Search"));

function getOperator($name, $query) {
    global $displayquery;
    
    if(!strstr($query, "$name:"))
        return false;
    
    $strloc = strripos("$name:", $query) + strlen($name) + 1;
    $operator_string = substr($query, $strloc)." ";
    $operator_string = str_replace("&quot;", "\"", $operator_string);
    //The operator is procedded by a quote, make it end at the next quote
    if(substr($operator_string, 0, 1) == "\"" || substr($operator_string, 0, 1) == "'") {
        $delimiter = substr($operator_string, 0, 1);
        $operator_string .= $delimiter;
        $parameter_string = substr($operator_string, 1, strpos($operator_string, $delimiter, 1) - 1);
    }
    else {
        //Not procedded by a quote, end it at the next space
        $parameter_string = substr($operator_string, 0, strpos($operator_string, ' '));
    }
    return trim($parameter_string);
}

$_GET['q'] = substr(trim($_GET['q']), 0, 100);
$displayquery = $raw_query = str_replace("\'", "'", $_GET['q']);
$displayquery = str_replace("\"", "&quot;", $displayquery);

$start = (intval($_GET['page']) - 1) * 10;
if(!$_GET['page'])
    $_GET['page'] = 1;
if(!$start || $start < 0)
    $start = 0;
if($_GET['q']) {
    //Parse the query for operators
    $escaped_query = mysql_real_escape_string(str_replace("author:", "", $_GET['q']));
    $displayquery = str_replace("author:", "", $displayquery);
    
    //Parse the query - replace spaces with AND
    $boolean_query = preg_replace("%([a-zA-Z0-9]) ([a-zA-Z0-9])%", "$1 +$2", $escaped_query);
    if(preg_match("%[a-zA-Z0-9]%", $boolean_query[0]))
        $boolean_query = "+".$boolean_query;
    
    $starttime = microtime();
	$words = explode(" ", $displayquery);   //Generate search terms based on the words given
	
    $banned_words = array();
    foreach($words as $key => $value) {
        $words[$key] = preg_replace("%\W%", "", $value);
        //Display notices for common words
        if(in_array($words[$key], $search_stopwords) && $resultrows == 0) {
            $banned_words[] = $words[$key];
			$words[$key] = "";
		}
    }
	
	//Find tags matching the query
	$i = 0;
	$matching_tags = array();
	foreach($words as $value)
		$matching_tags[$i++] = TagFactory::getTagsByPartialName($db, array($value));
	
    //Execute the query
    $query = "SELECT SQL_CALC_FOUND_ROWS *, MATCH(title, content) AGAINST ('{$escaped_query}') as relevance FROM ".TBL_PAGES." WHERE MATCH
        (title, content) AGAINST ('$boolean_query' IN
        BOOLEAN MODE) HAVING relevance > 0.0001";
	
	$matched_tag = false;
	foreach($matching_tags as $value) {
	    if(count($value) > 0) {
	    	if($matched_tag == false) {
	    		$query .= " UNION SELECT *, 0.001 FROM ".TBL_PAGES." WHERE (";
				$matched_tag = true;
			}
			
			$query .= "(";
			$and = "";
	    	foreach($value as $value2) {
	    		$query .= $and."FIND_IN_SET(".intval($value2->getId()).", tags) > 0";
	    		$and = " OR ";
	    	}
			
	    }
		if(count($value) > 0)
			$query .= ") AND ";
	}
	if($matched_tag == true)
		$query .= "1)";
    
    $query .= " ORDER BY relevance DESC LIMIT $start, 10";
    //echo $query;
    $result = $db->query($query) or die("Internal Server Error");
    $totalrows_result = $db->query("SELECT FOUND_ROWS() as rows");
    $totalrows = mysql_result($totalrows_result, 0, "rows");
    $querytime = round(microtime() - $starttime, 5);
    $resultrows = $db->numRows($result);
    
    //Calculate which rows are being displayed, in a human-friendly format
    $startrow = $start + 1;
    $endrow = $start + 10;
    if($endrow > $totalrows)
        $endrow = $totalrows;
    
    //Go through each returned result in a loop
    $search_results = array();
    for($i = 0; $i < $resultrows; $i++) {
        //Get article data
        $rows = true;
        $title = mysql_result($result, $i, 'title');
        $id = mysql_result($result, $i, 'id');
        $tags = mysql_result($result, $i, 'tags');
        $created = mysql_result($result, $i, 'created');
        $content = strip_tags(mysql_result($result, $i, 'content'));
        $content = substr($content, 0, strrpos($content, " "))."...";
        $content = str_replace(array("\r", "\n"), array(" ", ""), $content);
        //Bold matched search terms
        $count_replace = $count_title_replace = $count_author_replace = 0;
        foreach($words as $value) {
            if(trim($value) != "") {
                $content = preg_replace("%($value)%Ui", "<b>$1</b>", $content, -1, $count_replace);
                $title = preg_replace("%($value)%Ui", "<b>$1</b>", $title, -1, $count_title_replace);
                $author = preg_replace("%($value)%Ui", "<b>$1</b>", $author, -1, $count_author_replace);
            }
        }
        //Get the location and length of each highlighted word
        $display_contents = "";
        $match_places = $match_places_end = $match_places_len = array();
        $match_places = strposall($content, "<b>");
        $match_places_end = strposall($content, "</b>");
        if($match_places) {
            //Contents match - generate snippets and output
            foreach($match_places as $key => $value) {
                $match_places_len[$key] = $match_places_end[$key] - $value;
            }
            
            //Only show 5 snippets max
            if(count($match_places) > 5);
                $match_places = array_slice($match_places, 0, 5);
            
            //Display a snippet of content per matched location, the first variable controls how many characters to put after
            //the end of the matched word, the second controls how much to put before
            $snipett_len = round ((200 + count($match_places) * 40) / count($match_places));
            $snipett_rev_len = round ((100 + count($match_places) * 10) / count($match_places));
            foreach($match_places as $key => $value) {
                //If the previous match is close to the next one, don't repeat, simply append
                if($match_places_end[$key-1] + $snipett_len + 10 > $value - $snipett_rev_len) {
                    $new_snippet = substr($content, $match_places_end[$key-1] + $snipett_len, $match_places_len[$key] + $snipett_len);
                    //substr the snippet so that we don't have words cut off
                    $new_snippet = substr($new_snippet, strpos($new_snippet, " ") + 1, (strrpos($new_snippet, " ") - strpos($new_snippet, " ")));
                }
                else {  //The previous one was far away from the current one, add elipses and add the new snippet
                    $new_snippet = substr($content, $value - $snipett_rev_len, $match_places_len[$key] + $snipett_len);
                    $new_snippet = "<b>....</b> ".substr($new_snippet, strpos($new_snippet, " ") + 1, (strrpos($new_snippet, " ") - strpos($new_snippet, " ")));
                }
                $display_contents .= $new_snippet;  //Append the new snippet
            }
            $display_contents .= "<b>....</b>";
        }
        else {
            //Nohing matched in the contents, so just output a snipett of the content
            $display_contents = substr($content, 0, 200);
            $display_contents = substr($display_contents, 0, strrpos($content, " "));
            if(strlen($content) > 200)
                $display_contents .= "<b>....</b>";
        }
		$pageData = loadPage($id, $db);
        $search_results[$i] = array("content" => $display_contents, "title" => $title, "id" => $id, "created" => $created, "page" => $pageData, "authors" => $pageData->getTagsByType("author"),
			"section" => $pageData->getTagsByType("section"), "series" => $pageData->getTagsByType("series"));
    }
    //Paginiation
    $foundrows = $totalrows;
    $limit = 10;
    $maxpage = ceil($totalrows / 10);
    $href_base = "?q={$_GET['q']}";
}

require(Theme::getThemeDir()."header.php");

require(Theme::getThemeDir()."search.php");

require(Theme::getThemeDir()."footer.php");