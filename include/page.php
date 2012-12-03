<?php
/**
 * This class handles requests to resources that are stored in the database.
 * It calls the appropriate view to handle the type of resource.
 * 
 * @author rye
 * @package imprinter
 */

require_once("database.php");
require_once("tag.php");
 
 /* Code review November 23, 2011
  * 
  * TODO:
  * Type hinting on input params
  * field types should be in coments
  * accessor methods should be used
  * all field should be private
  * uml diagrams
  * set up require
  * 
  * create an interface that describes this subset of class
  */

abstract class Page {
	/**
	 * The link to the database, as  Database object
	 * @var Database
	 */
	protected $db;	//database handler
	/**
	 * The page's data, stored in an array
	 * @var array
	 */
	protected $pagedata = array();
	
	private $pageTagsProcessed = false;	//Whether or not the tags have been processed (that is, had their IDs looked up in the database)
	private $pageMediaProcessed = false;
    private $pageDataProcessed = false;
	
	/**
	 * This class constructor takes a database object 
	 * @param Database $db
	 */
	function __construct(Database $db) {
		$this->db = $db;
	}
	
	/**
	 * Magic functions gets a variable from the $pagedata array if available,
	 * tries to get it normally as a property of the class if not.
	 * @param unknown_type $name
	 */
	public function __get($name) {
		if(array_key_exists($name, $this->pagedata)) {
			return $this->pagedata[$name];
		}
		
		return $this->$name;
	}
	
	/**
	 * Gets the title of the page as it appears in the title tag
	 * @return string
	 */
	abstract protected function getPageTitle ();
	
	/**
	 * Prints the title of the page as it appears in the title tag
	 */
	function printPageTitle() {
		echo $this->getPageTitle();
	}
	
	/**
	 * Gets the content of the page in HTML format
	 * @return string
	 */
	abstract protected function getPageContent ();
	
	/**
	 * Prints the content of the page in HTML format
	 */
	public function printPageContent() {
		echo $this->getPageContent();
	}
	
	/**
	 * Gets the authors of a page in HTML format
	 * @return string
	 */
	abstract protected function getPageAuthors ();
	
	/**
	 * Prints the authorsof a page in HTML format
	 */
	function printPageAuthors() {
		echo $this->getPageAuthors();
	}
	
	/**
	 * Returns the title of the page
	 */
	public function getTitle() {
		return trim($this->pagedata['title']);
	}
	
	/**
	 * Returns the ID of the page
	 */
	public function getId() {
		return $this->pagedata['id'];
	}
	
	/**
	 * Returns the content of the page
	 */
	public function getContent() {
		return trim($this->pagedata['content']);
	}
	
	/**
	 * Returns the data of the page
	 */
	public function getData() {
		return $this->pagedata['data'];
	}
    
    /**
     * Sets the data of the page
     * @param mixed $a
     */
    public function setData($a) {
        $this->pagedata['data'] = $a;
    }
	
	/**
	 * Returns the biew of the page
	 */
	public function getView() {
		return $this->pagedata['view'];
	}
	
	/**
	 * Returns the tags of the page
	 * @return array
	 */
	public function getTags() {
		return $this->pagedata['tags'];
	}
	
	/**
	 * Returns the UNIX Timestamp of the page creation
	 * @return int
	 */
	public function getCreatedTime() {
		return $this->pagedata['created'];
	}
	
	/**
	 * Sets the UNIX Timestamp of the page creation
	 * @param $time int
	 * @return null
	 */
	public function setCreatedTime($time) {
		$this->pagedata['created'] = intval($time);
	}
	
	/**
	 * Returns the created date/time of the page
	 */
	public function getCreated() {
		return $this->pagedata['created'];
	}
	
	/**
	 * Returns the media data of the page. Will aos query the database and process media if $processMedia isn't false
     * @param bool $processMedia [optional] [default=true]
     * @return array Media
	 */
	public function getMedia($processMedia = true) {
	    if($processMedia == false && $pageDataProcessed == true) {
            return $this->pagedata['mediaraw'];
        }
        elseif($processMedia == false) {
            $this->processPageData();
            return $this->pagedata['mediaraw'];
        }
        
	    $this->processPageMedia();
		return $this->pagedata['media'];
	}
	
	/**
	 * Returns the authors array of the page
	 * @return array
	 */
	public function getAuthors() {
		return $this->pagedata['author'];
	}
	
	/**
	 * Returns a string giving the full absolute URL that can be used as-is by the browser to this page.
	 * @return string
	 */
	public function getURL() {
		if($this->pagedata['view'] == "article") {
			$title_words = explode(" ", $this->pagedata['title']);
			$title_words = array_slice($title_words, 0, 6);
			return "/article/".$this->pagedata['id']."-".preg_replace("%\-+$%", "", preg_replace("%[^a-z0-9\-]%", "", strtolower(implode("-", $title_words))));
		}
		return "/".$this->pagedata['id'];
	}
	
	/**
	 * Returns the permalink of the page
	 * @return string
	 */
	public function getPermalink() {
		return "/".$this->pagedata['id'];
	}
	
	/**
	 * Gets the names of all of the authors of the page, returned as an array
	 * @param notitles optional If true, will return the authors' names only, without titles [default: false]
	 * @return array
	 */
	public function getAuthorNames($notitles = false) {
		if($this->pageTagsProcessed == false)
			$this->processPageTags();
		
		$authornames = array();
		if($notitles != true) {
			if(!$this->pagedata['author_title'])
				return array();
		
			foreach($this->pagedata['author_title'] as $value) {
				$authornames[] = $value->getName();
			}
		}
		else {
			if(!$this->pagedata['author'])
				return array();
		
			foreach($this->pagedata['author'] as $value) {
				$authornames[] = $value->getName();
			}
		}
		return $authornames;
	}
	
	/**
	 * Gets the names of all of the tags of a given type for the page, returned as an array
	 * 
	 * @param string $type
	 * @return array
	 */
	public function getTagNamesByType($type) {
		if($this->pageTagsProcessed == false)
			$this->processPageTags();
		
		if(is_array($this->pagedata[$type])) {
			$tagNames = array();
			foreach($this->pagedata[$type] as $value) {
				$tagNames[] = $value->getName();
			}
			return $tagNames;
		}
		else {
			return array();
		}
	}
	
	/**
	 * Gets the all of the tags of a given type for the page, returned as an array
	 * 
	 * @param string $type
	 * @return array
	 */
	public function getTagsByType($type) {
		if($this->pageTagsProcessed == false)
			$this->processPageTags();
		
		if(is_array($this->pagedata[$type])) {
			return $this->pagedata[$type];
		}
		else {
			return array();
		}
	}
	
	/**
	 * Returns the full pagedata array of the page
	 */
	public function getPagedata($index = false) {
		if(!$index)
			return $this->pagedata;
		if(isset($this->pagedata[$index]))
			return $this->pagedata[$index];
		else
			return $this->pagedata['data'][$index];
	}
	
	/**
	 * Merges an pagedata array into the current one, overwriting the old one where applicable
	 * 
	 * @param array $pagedata
	 * @return void
	 */
	public function setPagedata(array $pagedata) {
		$this->pagedata = array_merge($this->pagedata, $pagedata);
	}
	
	/**
	 * Edits a page's data. Also assumes that the data is valid. Does not save the data to the database; use savePage() instead
	 * @return boolean
	 * @param string $id
	 * @param array $pagedata
	 */
	protected function editPage($id, array $pagedata) {
		//Call the database with the easy helper function
		return $this->db->tableUpdate(
			TBL_PAGES, 
			array("title" => $pagedata['title'], 
				"content" => $pagedata['content'], 
				"tags" => $pagedata['tags'], 
				"created" => $pagedata['created'],
				"data" => $pagedata['data'], 
				"media" => $pagedata['media']), 
			array("id = '$id'"), 1);
	}
	
	/**
	 * Adds a new page with the specified data. Assumes that the data is valid.
	 * @return boolean
	 * @param array $pagedata
	 */
	public function newPage(array $pagedata) {
		if ($this->db->tableInsert(TBL_PAGES, $pagedata)) {	//Call the database
			$this->pagedata['id'] = intval($this->db->insertedId());
		}
	} 
	
	/**
	 * Saves the current $pagedata arrry of the page into the database
	 * @param bool $new optional
	 */
	public function savePage($new = false) {
		$pagedata = array();
		//Sanitize inputs
		foreach($this->pagedata as $key => $value) {
			if(!is_array($value))
				$pagedata[$key] = mysql_real_escape_string($value);
			elseif($key == "data") {	//The data field is a serialized value
				$pagedata[$key] = serialize($value);
			}
			elseif($key == "media") {
				foreach($this->pagedata['media'] as $k => $v) {
					$pagedata['media'][$k] = $v;
					$pagedata['media'][$k]['tags'] = implode(",", Tag::getTagIds($v['tags']));
				}
			}
			elseif(is_array($key))	//All other arrays are comma seperated values
				$pagedata[$key] = mysql_real_escape_string(implode(",", $value));
		}
		//Tags are special
		$tagid = array();
		foreach($this->pagedata['tags'] as $key=>$value) {
			$tagid[] = intval($value);	//The IDs should be numeric
		}
		$pagedata["tags"] = implode(",", $tagid);	//Implode into comma-seperated values
		$pagedata["media"] = serialize($pagedata["media"]);
		
		if($new == true) {	//Create a new page - set its creation time
			if(!is_numeric($pagedata["created"]))
				$pagedata["created"] = time();
			$this->newPage($pagedata);	//Save to database
		}
		else {
			if(!is_numeric($pagedata["created"]))
				$pagedata["created"] = time();
			$this->editPage($pagedata['id'], $pagedata);	//Save to database
		}
		
		return true;
	}
	
	/**
	 * The tags are provided as a list of numeric IDs from the database.
	 * This function loads the Tag objects from the database and replaces
	 * the list of numeric IDs with an array of Tag. Furthermore, it also populates
	 * the author field.
	 * 
	 * This doesn't happen automatically because it would be computationally expensive.
	 */
	public function processPageTags() {
		if(!isset($this->pagedata['tags']))
			return false;
		if($this->pageTagsProcessed === true)
			return true;
		
		$this->pagedata['tags'] = TagFactory::getTagsById($this->db, $this->pagedata['tags']);
		//Move authors from tags to their own array
		foreach($this->pagedata['tags'] as $key => $value) {
			if($value->getType() != "tag") {	//Copy all tags which aren't actually of type "tag" to their own key
				if(!is_array($this->pagedata[$value->getType()]))	//Create it if it dosn't exist
					$this->pagedata[$value->getType()] = array();
				$this->pagedata[$value->getType()][] = $value;
				unset($this->pagedata['tags'][$key]);
			}
		}
		//Refactor the tags array
		$this->pagedata['tags'] = array_values($this->pagedata['tags']);
		
		//Process the media tags for every media file
		if(is_array($this->pagedata['media']) && $this->pageMediaProcessed) {
			foreach($this->pagedata['media'] as $key => $value) {
				$this->pagedata['media'][$key]->processMediaTags();
			}
		}
		else
			$this->pagedata['media'] = array();
		
		$this->pageTagsProcessed = true;
		return true;
	}
    
    /**
     * Processes the media of a page.
     */
    public function processPageMedia() {
        $this->processPageData();
        if($this->pageMediaProcessed == true)
            return true;
        
        if(!is_array($this->pagedata['media'])) {
            $this->pageMediaProcessed = true;
            return false;
        }
        foreach($this->pagedata['media'] as $key => $value) {
            $this->pagedata['media'][$key] = MediaFactory::loadFile($this->db, $value['location']);
            $value['tags'] = explode(",", $value['tags']);
            if($this->pagedata['media'][$key] != false)
                $this->pagedata['media'][$key]->setMediaData($value);
            else
                unset ($this->pagedata['media'][$key]);
        }
        $this->pageMediaProcessed = true;
        return true;
    }
	
	/**
	 * Takes raw database table data and processes it.
	 */
	public function processPageData() {
	    if($this->pageDataProcessed == true)
            return true;
		//Unserlize the variables
		$this->pagedata['data'] = unserialize($this->pagedata['data']);
		$this->pagedata['mediaraw'] = $this->pagedata['media'] = unserialize($this->pagedata['media']);
        if(!is_array($this->pagedata['media'])) {
            $this->pagedata['mediaraw'] = $this->pagedata['media'] = array();
        }
        foreach($this->pagedata['mediaraw'] as $key=>$value) {
            $m = new Media($this->db);
            $m->setMediaData($value);
            $this->pagedata['mediaraw'][$key] = $m;
        }
		$this->pagedata['tags'] = explode(",", $this->pagedata['tags']);
		
        $this->pageDataProcessed = true;
		return true;
	}
	
	/**
	 * This function takes a Database object and the id of a page that is being requested.
	 * It loads the page from the database by searching for the id
	 * @param Int/String
	 * @param optional urlid = 0 - force query to lookup by url instead of id
	 * @return bool
	 */
	public function getPage($id, $urlid = false) {
		if(!is_numeric($id) || $urlid == true) {
			$id = mysql_real_escape_string($id);
			$query = "SELECT * FROM ".TBL_PAGES." WHERE url='$id'";
		}
		else {
		$id = intval($id);
			$query = "SELECT * FROM ".TBL_PAGES." WHERE id='$id'";
		}
		
		$result = $this->db->query($query);
		
		if($this->db->numRows($result) == 0)
			return false;
		
		$this->pagedata = $this->db->firstRow($result);
		
		$this->processPageMedia();
		
		return true;
	}

	public function printPageHeader() {
		$page = $this;
		require(Theme::getThemeDir()."header.php");
	}
	
	
	public function printPageBody() {
		$page = $this;
		$type = $page->view;
		if($type && file_exists(Theme::getThemeDir()."/".$type.".php")) {
			require(Theme::getThemeDir()."/".$type.".php");
		}
		else
			require(Theme::getThemeDir()."/content.php");
	}
	
	public function printPageFooter() {
		$page = $this;
		require(Theme::getThemeDir()."footer.php");
	}
}

class PageFactory {
	/**
	 * Loads the $fields fields from the database for pages matching the $filters (with
	 * each value being a valid mySQL field) with limit $limit starting at $start.
	 * @param object $db
	 * @param array $fields optional
	 * @param array $filters optional
	 * @param int $limit optional
	 * @param int $start optional
     * @return array Page
	 */
	static function loadPages($db, $fields = false, $filters = false, $orderby = false, $limit = false, $start = false) {
		if($fields != false) {
			if(!is_array($fields))
				$fields = array($fields);
			if(!in_array("view", $fields))
				$fields[] = "view";		//We MUST have accessed the view column.
		}
		$pages = $db->tableQuery(TBL_PAGES, $fields, $filters, $orderby, $limit, $start);
        $db->setFoundRowsOverride($db->calcFoundRows());
		if(!$pages)
			return false;
		
		$pagesarray = array();
		$i = 0;
		while($row = mysql_fetch_assoc($pages)) {
			$pageView = $row['view'];
			require_once(WEB_ROOT . "/views/$pageView/$pageView.php");
			$pagesarray[$i] = new $pageView($db);
			
			$pagesarray[$i]->setPageData($row);
			$pagesarray[$i]->processPageData();
			$i++;
		}
		
		return $pagesarray;
	}
    
    /**
     * Loads the frontpage template $template and applies the filters defined in $filters using the options in $filter_options,
     * and returns an array of the resultant pages.
     * @param object $db
     * @param string $template
     * @param array $filters
     * @param array $filter_options
     * @return array Page
     */
    static function loadFrontpagePages($db, $template, $filters, $filter_options) {
        if(!file_exists(WEB_ROOT."/views/frontpage/frontpage.php"))
            return false;
        require_once(WEB_ROOT."/views/frontpage/frontpage.php");
        $fp = new Frontpage($db);

        $pagedata = 
            array("filter" => $filters, "filter_options" => $filter_options, "template" => $template);
        $fp->setData($pagedata);
        $fp->getFilteredPages();
        return $fp->getPages();
    }
    
    /**
     * Returns an array of up to $num of the latest articles in the $section section.
     * @param object $db
     * @param strng $section
     * @param int $num optional
     * @return array Page
     */
    static function getSectionLatest(Database $db, $section, $num = 3, $req_photos = false) {
	    $filters = array("main"=>array(
	        array("type"=>"view", "value"=>array("article"=>true)), 
	        array("type"=>"section", "value"=>array(0=>TagFactory::getTagByName($db, $section, "section")->getID()))));
	    if($req_photos == true) {
	        $filters["main"][] = array("type"=>"photos", "value"=>array("cond"=>"equal to or greater than", "num"=>1));
	    }
	    $filter_options = array("main"=>array("num"=>3, "sort"=>"created", "sortorder"=>"desc"));
	    
	    $pages = PageFactory::loadFrontpagePages($db, "default", $filters, $filter_options);
	    $pages = array_values($pages);
	    $pages = $pages[0];
	    return $pages;
	}
}