<?php
/**
 * This class represents a single media file and handles its loading, caching and resizing.
 * 
 * @author: rye
 * @package: Imprinter
 */

class Media {
	private $location = "";
	private $db;
	private $abspath = "";
	private $mediadata = array();
	private $mediaTagsProcessed = false;
	
	//Constructor function loads file from database. If the file is not in the database, put it in the database.
	function __construct($dbh, $location = false) {
		$this->db = $dbh;
        if(!$location)
            return;
        
		$this->location = $location;
        if($location[0] == "/")
            $location = substr($location, 1);
		$this->abspath = WEB_ROOT."/".$this->location;
		if(!file_exists($this->abspath)) {
			throw new Exception("File does not exist at $abspath.");
		}
		
		$mediaResult = $this->getMediaInfo();
		if(!$mediaResult) {
			$this->saveMediaInfo(true);
			$mediaResult = $this->getMediaInfo();
			$this->mediadata = $mediaResult;
			$this->mediadata['new'] = true;
		}
		else {
			$this->mediadata = $mediaResult;
			$this->mediadata['new'] = false;
		}
		
		$this->mediadata['tags'] = explode(",", $this->mediadata['tags']);
	}
	
	/**
	 * Queries the database for the data associated with a media file
	 * @return array The data in the database (false if the file wqas not found in the database)
	 */
	private function getMediaInfo() {
		$loc = $this->location;
		if(!$loc)
			return false;
		
		$loc = mysql_real_escape_string($loc);
		$query = "SELECT * FROM ".TBL_MEDIA." WHERE location='$loc' LIMIT 1";
		
		$result = $this->db->query($query);
		if(!$result || mysql_num_rows($result) != 1)
			return false;
		return $this->db->firstRow($result);
	}
	
	/**
	 * Saves the media information, either by upadting it in the database or creating it as new
	 * @param bool optional $new [default: false]
	 * @return database result object
	 */
	private function saveMediaInfo($new = false) {
		if(!$this->mediadata['id'])
			$new = true;
		$loc = $this->location;
		$loc = mysql_real_escape_string($loc);
		$caption = mysql_real_escape_string("");
		if($mediaTagsProcessed == true)
			$tags = implode(",", array_merge(Tag::getTagIds($this->mediadata['media_tag']), Tag::getTagIds($this->mediadata['media_author'])));
		else
			$tags = $this->mediadata['tags'];
		if($new == true) {	//A new media entry in the database
			$query = "INSERT INTO ".TBL_MEDIA." (location, tags, caption) VALUES ('$loc', '$tags', '$caption')";
			return $this->db->query($query);
		}
		else {	//Update an existing entry
			$id = intval($this->mediadata['id']);
			$query = "UPDATE ".TBL_MEDIA." SET location = '$loc', tags = '$tags', caption = '$caption' WHERE id = '$id' LIMIT 1";
			return $this->db->query($query);
		}
	}
	
	function processMediaTags() {
		if($this->mediaTagsProcessed == true)
			return true;
		
		$this->mediadata['tags'] = TagFactory::getTagsById($this->db, $this->mediadata['tags']);
		$this->mediadata['authors'] = array();
		$this->mediadata['media_tag'] = array();
		$this->mediadata['media_author'] = array();
		//Move other types of tags from [tags] to their own array
		foreach($this->mediadata['tags'] as $key => $value) {
			if($value->getType() != "tag") {
				if(!is_array($this->mediadata[$value->getType()]))
					$this->mediadata[$value->getType()] = array();
				$this->mediadata[$value->getType()][] = $value;
				unset($this->mediadata['tags'][$key]);
			}
		}
		//Refactor the tags array
		$this->mediadata['tags'] = array_values($this->mediadata['tags']);
		$this->mediaTagsProcessed = true;
		return true;
	}
	
	/**
	 * Sets the media data
	 * @param $info array
	 * @return void
	 */
	public function setMediaData(array $info) {
		foreach($info as $key => $value)
			$this->mediadata[$key] = $value;
        if($info['location']) {
            $this->location = $info['location'];
            if($info['location'][0] == "/")
                $info['location'] = substr($info['location'], 1);
            $this->abspath = WEB_ROOT."/".$info['location'];
        }
	}
	
	/**
	 * Returns the absolute location of the file, for URLs
	 * @return string
	 */
	function getLocation() {
		return preg_replace("%\/+%", "/", "/".$this->location);
	}
    
    /**
     * Returns the absolute location of the file with max-width $width and max-height $height, for URLs
     * @param int $width
     * @param int $height
     * @return string
     */
    function getResizedLocation($width, $height) {
        if(!$width) $width = 100;
        if(!$height) $height = 100;
        $loc = explode("/", substr(str_replace("//", "/", "/".$this->location), 1));
        array_splice($loc, 1, 0, array("thumbs", intval($width)."x".intval($height)));
        return "/".implode("/", $loc);
    }
    
    /**
     * Run getimagesize() on the image, resized with max-width $width and max-height $height
     * @param int $width
     * @param int $height
     * @return array
     */
    function getResizedSize($width, $height) {
        if(!$width) $width = 100;
        if(!$height) $height = 100;
        $loc = explode("/", str_replace("//", "/", $this->location));
        array_splice($loc, 1, 0, array("thumbs", intval($width)."x".intval($height)));
        $loc = "/".implode("/", $loc);
        return getimagesize(WEB_ROOT.$loc);
    }
    
    /**
     * Run getimagesize() on the image, resized with max-width $width and max-height $height, return the third index,
     * which is the string directly usable in the <img> tag (.e. width="xxx", height="xxx")
     * @param int $width
     * @param int $height
     * @return string
     */
    function getResizedSizeString($width, $height) {
        
        $imagesize = $this->getResizedSize($width, $height);
        return $imagesize[3];
    }
	
	/**
	 * Returns the caption of the file
	 * @return string
	 */
	function getCaption() {
		return $this->mediadata['caption'];
	}
	
	/**
	 * Returns the file name of the file
	 * @return string
	 */
	function getFilename() {
		$pathinfo = pathinfo($this->getLocation());
		return $pathinfo['filename'];
	}
	
	/**
	 * Returns the file data needed by the mediaupload plugin, in JSON format
	 * @return string JSON
	 */
	function getJSONData($options = array()) {
	    if(!$options || !is_array($options) || $options['resize'] != true) {
    		return array("name" => $this->getFilename(), 
    			"url" => $this->getLocation(), 
    			"thumb_url" => $this->getLocation(), 
    			"unique" => $this->mediadata['id'], 
    			"caption" => $this->mediadata['caption'], 
    			"author" => Tag::getTagNames($this->mediadata['media_author']),
    			"tags" => Tag::getTagNames($this->mediadata['media_tag']));
        }
        else {
            return array("name" => $this->getFilename(), 
                "url" => $this->getLocation(), 
                "thumb_url" => $this->getResizedLocation($options['width'], $options['height']), 
                "unique" => $this->mediadata['id'], 
                "caption" => $this->mediadata['caption'], 
                "author" => Tag::getTagNames($this->mediadata['media_author']),
                "tags" => Tag::getTagNames($this->mediadata['media_tag']));
        }
	}
    
    /**
     * Returns the authors of the media file
     * @return array Tag
     */
    function getAuthor() {
        $this->processMediaTags();
        return $this->mediadata['media_author'];
    }
	
	/**
	 * Returns the file's ID
	 * @return int
	 */
	function getID() {
		return intval($this->mediadata['id']);
	}
	
	/**
	 * Returns the absolute URL of the file
	 * @return string
	 */
	function getURL() {
		return "/".$this->location;
	}
	
	/**
	 * Returns whether or not the media has just been added to the database
	 * @return bool
	 */
	function isNew() {
		return $this->mediadata['new'];
	}
}

class MediaFactory {
	/**
	 * Returns a Media object from a file location.
	 * @param Database $db the database handler
	 * @param string $location The Location of the file, relative to the web root
	 * @return Media (flase if file not found)
	 */
	static function loadFile(Database $db, $location) {
		if(!file_exists(WEB_ROOT.$location)) {
			echo "<!--".WEB_ROOT.$location."-->";
			return false;
		}
		else {
			$media = new Media($db, $location);
			return $media;
		}
	}

	/**
	 * Returns an array of Media objects from an array of file locations.
	 * @param Database $db the database handler
	 * @param array[string] $location The Location of the file, relative to the web root
	 * @return array[Media] (false if file not found)
	 */
	static function loadFiles(Database $db, array $locations) {
		$mediaarray = array();
		foreach($locations as $key => $value) {
			$media = MediaFactory::loadFile($db, $value);
			if($media != false)
				$mediaarray[] = $media;
		}
		return $mediaarray;
	}
}