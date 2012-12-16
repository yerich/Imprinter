<?php
/**
 * The frontpage view prints out a list of other pages. This is meant from everything from the homepage
 * to individual author's pages.
 * 
 * @author rye
 * @package imprinter
 */

class Frontpage extends Page {
	var $valid_sort = array("Title" => "title", "Creation Date" => "created", "Relevance" => "FLOOR(created/604800) DESC, LENGTH(content)", "Article Length" => "LENGTH(content)");
    var $pages = array();   //Array of pages generated via the filters
    var $totalpages = array();  //Number of total pages that match each filter
	
	//Handling of Tag Frontpages
	function processFrontpageTag() {
		if($this->frontpageTagProcessed == true) {
			return true;
		}
		
		if(in_array($this->getId(), array(FRONTPAGE_SECTION, FRONTPAGE_AUTHOR, FRONTPAGE_TAG, FRONTPAGE_SERIES))) {
			if(!$_GET['tagid'] || !is_numeric($_GET['tagid'])) {
				Error::error404();
			}
			$tagid = intval($_GET['tagid']);
			$tag = TagFactory::getTagById($this->db, $tagid);
			if(!$tag) {
				Error::error404();
			}
			
			$this->pagedata['title'] = $tag->getName();
			$this->tag = $tag;
		}
		
		$this->frontpageTagProcessed = true;
		return true;
	}
	
	/**
	 * Returns the title of the page
	 * @return string
	 */
	function getPageTitle() {
		$this->processFrontpageTag();
		
		return $this->pagedata['title'];
	}
	
	/**
	 * Returns the authors of the Frontpage. But Frontpages don't have any authors, so this will return array()
	 * @return array()
	 */
	function getPageAuthors() {
		return array();
	}
    
    function getPages() {
        return $this->pages;
    }
	
	function getPageContent() { return false; }
    
    /**
     * Runs the filters and caches the resultant pages
     * @return bool
     */
    function getFilteredPages() {
        $this->processFrontpageTag();
        $pages = array();   //This will store the pages that are returned
        
        $frontpageData = Frontpage::getFrontpageData($this->data['template']);  //Get page data
        $filters = $this->data['filter'];
        $filter_options = $this->data['filter_options'];
        $where = array();   //Array representing the WHERE clause of the query
        
        //Loop through the sections of the frontpage
        foreach($frontpageData['filters'] as $key => $value) {
            $name = $value['name'];
            
            if($this->pages[$name]) {   //Read from cache
                $pages[$name] = $this->pages[$name];
                continue;
            }
            
            if(!is_array($filters[$name]))  //Validation, just in case
                $filters[$name] = array("1");
            if(!is_array($filter_options[$name]))
                $filter_options[$name] = array();
            
            //Handling of Tag Frontpages
            if(in_array($this->getId(), array(FRONTPAGE_SECTION, FRONTPAGE_AUTHOR, FRONTPAGE_TAG, FRONTPAGE_SERIES))) {
                $tag = $this->tag;
                $filters[$name][] = array("type" => "author", "value" => array($tag->getId()));
                $this->pagedata['title'] = $tag->getName();
            }
            
            $queryData = Frontpage::generateFilters($filters[$name], $filter_options[$name]);   //Generate the query clauses from the page data
            
            //Pagination
            $start = max((intval($_GET['page'][$name]) - 1), 0) * $queryData['limit'];
            
            //Get the pages
            $pages[$name] = PageFactory::loadPages($this->db, false, array_merge($where, $queryData['where']), $queryData['orderby'], $queryData['limit'], $start);
            
            if($this->data['allow_duplicates'] != "1") {    //Collect IDs so that pages already outputted won't be returned again
                foreach($pages[$name] as $rpage) {  //Loop through the pages to collect their IDs
                    $where[] = "ID != ".intval($rpage->getId());    //Add the caluse to future queries so that the page isn't returned again
                }
            }
            
            $this->pages[$name] = $pages[$name];       //Cache the result
            
            $this->totalpages[$name] = $this->db->calcFoundRows();
        }
    }
	
	/**
	 * Prints the content of the frontpage, using the template specified in the page's data.
	 * @return null
	 */
	function printPageContent() {
	    $this->getFilteredPages();
        $frontpageData = Frontpage::getFrontpageData($this->data['template']);
        $filters = $this->data['filter'];
        $filter_options = $this->data['filter_options'];
        $pages = $this->pages;
        $page = $this;
        $db = $this->db;
        
        //Handling of Tag Frontpages
        if(in_array($this->getId(), array(FRONTPAGE_SECTION, FRONTPAGE_AUTHOR, FRONTPAGE_TAG, FRONTPAGE_SERIES))) {
            $tag = $this->tag;
            $filters[$name][] = array("type" => "author", "value" => array($tag->getId()));
            $this->pagedata['title'] = $tag->getName();
        }
        
        foreach($pages as $key=>$value) {
            foreach($pages[$key] as $k => $v) {
                $pages[$key][$k]->processPageData();
            } 
        }
        
		//Load the frontpage template file from disk
		$themefile = Theme::getThemeDir()."frontpages/".$this->data['template'].".php";
		if(file_exists($themefile))
			require($themefile);
		else //Fallback to default frontpage
			require(Theme::getThemeDir()."frontpages/".FRONTPAGE_DEFAULT.".php");
		
		return null;
	}
    
    /**
     * Prints pagination for a given section
     */
    function printSectionPagination($section) {
        $this->getFilteredPages();
        $frontpageData = Frontpage::getFrontpageData($this->data['template']);  //Get page data
        $filters = $this->data['filter'];
        $filter_options = $this->data['filter_options'];
        $queryData = Frontpage::generateFilters($filters[$section], $filter_options[$section]);
        if(!$this->pages[$section])
            return;
        
        $baseurl = preg_replace("%\?(page\[$section\]|_req_pg)=[^ \?\&]*\&%", "?", $_SERVER["REQUEST_URI"]);
        $baseurl = preg_replace("%(\&|\?)(page\[$section\]|_req_pg)=[^ \?\&]*%", "", $baseurl);
        printPagination($baseurl, intval($_GET['page'][$section] - 1), $queryData['limit'], $this->totalpages[$section], "page[$section]");
    }
	
	/**
	 * Loads and returns an array of data for the frontpage template, from the data file comtained with the current theme
	 * @param $frontpage string -- the name of the frontpage template
	 * @return array(name, min, max, description)
	 */
	static function getFrontpageData($frontpage) {
		//Load the data file
		$filecontent = file_get_contents(Theme::getThemeDir()."frontpages/$frontpage.dat.txt");
		if(!$filecontent)
			return false;
		
		$result = array();
		$filecontent = str_replace("\r", "", $filecontent);
		$lines = explode("\n", $filecontent);
		foreach($lines as $key=>$value) {
			if(!$value)
				continue;
			if($value[0] == "#")
				continue;
			
			$data = explode(" ", $value, 4);
			$result['filters'][] = array("name"=>$data[0], "min"=>intval($data[1]), "max"=>intval($data[2]), "description"=>$data[3]);
		}
		
		return $result;
	}
	
	/**
	 * Generates a list of filters for the PageFactory::loadPages function from an array of filters as defined by the
	 * frontpage content type.
	 * @param object $filters
	 * @return array - an array of mySQL caluses that can be used in PageFactory::loadPages()
	 */
	function generateFilters($filterlist, $optionslist) {

		$flist = array(); 	//This will store the array we will return
		$orderby = false;
		$limit = false;
		if(!is_array($filterlist))
            $filterlist = array();
        
		foreach($filterlist as $value) {
			if(!is_array($value) || !$value['type'] || !$value['value'])
				continue;
		
			switch ($value['type']) {
				case "view" :
					foreach($value['value'] as $k => $v) {
						$value['avalue'][] = mysql_real_escape_string($k);
					}
					$flist[] = "view = '".implode($value['avalue'], "' OR view = '")."'";
					break;
				case "tag" : case "author" :  case "series" :  case "section" : 
					$flist[] = "FIND_IN_SET(".intval($value['value'][0]).", tags) > 0";
					break;
				case "title" :
					$flist[] = "title LIKE '".mysql_real_escape_string($value['value'])."'";
					break;
					
				case "photos" :
					$operator = "=";
					switch ($value['value']['cond']) {
						case "less than" :
						$operator = "<"; break;
						case "equal to or less than" :
						$operator = "<="; break;
						case "greater than" :
						$operator = ">"; break;
						case "equal to or greater than" :
						$operator = ">="; break;
						case "equal to" : default :
						$operator = "="; break;
					}
					
					if(!$value['value']['num'])
						$value['value']['num'] == 0;
					
					$flist[] = "SUBSTRING(media, 3) $operator ".intval($value['value']['num']);
					break;
					
				case "date" :
					$operator = "<";
					if($value['value']['cond'] == "created after")
						$operator = ">";
					
					if($value['value']['date'] !== "")
					$flist[] = "created $operator ".intval($value['value']['date']);
					break;
					
				case "age" :
					$operator = ">";
					if($value['value']['cond'] == "created earlier than")
						$operator = "<";
					
					$time = time();
					$time -= intval($value['value']['weeks']) * 60*60*24*7;
					$time -= intval($value['value']['days']) * 60*60*24;
					$time -= intval($value['value']['hours']) * 60*60;
					$time -= intval($value['value']['minutes']) * 60;
					$flist[] = "created $operator $time";
					break;
			}
		}
		
		$optionslist['num'] = intval($optionslist['num']);
		if($optionslist['num'] > 0)
			$limit = $optionslist['num'];
		
		if(in_array($optionslist['sort'], $this->valid_sort))
			$orderby = $optionslist['sort'];
		else
			$orderby = "created";
		
		if(in_array($optionslist['sortorder'], array("asc", "desc")))
			$orderby .= " ".$optionslist['sortorder'];
		else
			$orderby .= " desc";
		
		return array("where" => $flist, "orderby" => $orderby, "limit" => $limit);
	}
}