<?php
/**
 * This class represents an tag in the database.
 * 
 * All tags get a row created for them in the tags table in the database
 */

require_once("database.php");
 
class Tag {
	protected $db;	//database handler
	protected $row;	//tag row data

	function __construct($db) {
		$this->db = $db;
	}
	
	/**
	 * Magic functions gets a variable from the $row array if available,
	 * tries to get it normally as a property of the class if not.
	 * @param unknown_type $name
	 */
	public function __get($name) {
		if(array_key_exists($name, $this->row)) {
			return $this->row[$name];
		}
		
		return $this->$name;
	}
	
	/**
	 * Returns the title of the tag
	 * @return string
	 */
	function getName() {
		return $this->row['name'];
	}
	
	/**
	 * Returns the ID of the tag
	 * @return int
	 */
	function getId() {
		return $this->row['id'];
	}
	
	/**
	 * Returns the type of the tag
	 * @return string
	 */
	function getType() {
		return $this->row['type'];
	}
	
	/**
	 * Returns the description of the tag
	 * @return string
	 */
	function getDescription() {
		return $this->row['data']['description'];
	}
	
	/**
	 * Returns the tagline of the tag
	 * @return string
	 */
	function getTagline() {
		return $this->row['data']['tagline'];
	}
	
	/**
	 * Returns the email
	 * @return string
	 */
	function getEmail() {
		return $this->row['data']['email'];
	}
	
	/**
	 * Sets the tag data
	 * @param array $row
	 * @return null
	 */
	function setTagData($row) {
		$this->row = $row;
	}
	
	/**
	 * Gets the tag data
	 */
	function getTagData() {
		return $this->row;
	}
	
	/**
	 * Gets the user-accessible URL of the tag
	 */
	function getURL() {
		return "/".$this->row['type']."/".urlencode(strtolower($this->row['name']))."/";
	}
	
	
	/**
	 * Finds an tag by ID and puts its data into the class
	 * 
	 * @aparam int $id
	 * @return bool
	 */
	function getTag($id) {
		$id = intval($id);
		$query = "SELECT * FROM ".TBL_TAGS." WHERE id = '$id' LIMIT 1";
		$result = $this->db->query($query);
		
		if(!$result)
			return false;
		
		$this->row = $this->db->firstRow($result);
		if($this->row['data']) {
			$this->row['data'] = unserialize($this->row['data']);
		}
		else
			$this->row['data'] = array();
		return $true;
	}
	
	/**
	 * Edits an tag's data. Also assumes that the data is valid
	 * 
	 * @return boolean
	 * @param string $id
	 * @param array $row
	 */
	function editTag($id, $row) {
		
		if($this->row['data'])
			$data = mysql_real_escape_string(serialize($this->row['data']));
		else
			$data = serialize(array());
		//Generate the database query
		return $this->db->tableUpdate(
			TBL_TAGS, 
			array(
				"name" => $row['name'], 
				"data" => $data,
				"num_articles" => $pagedata['num_articles']), 
			array("id = '$id'"), 1);
	}
	
	/**
	 * Adds a new tag with the specified data. Assumes that the data is valid.
	 * @return boolean
	 * @param array $row
	 */
	function newTag($row) {
		$rowdata = $row;
		if($rowdata['data']) {
			$rowdata['data'] = mysql_real_escape_string(serialize($rowdata['data']));
		}
		else
			$rowdata['data'] = serialize(array());
		return $this->db->tableInsert(TBL_TAGS, $rowdata);
	}
	
	/**
	 * Saves the current $row array of the tag into the database,
	 * @param bool $new optional
	 */
	function saveTag($new = false) {
		$row = array();
		//Sanitize inputs
		foreach($this->row as $key => $value) {
			if(is_string($value))
				$row[$key] = mysql_real_escape_string($value);
		}
		
		if($new == true)
			$this->newTag($row);
		else
			$this->editTag($row['id'], $row);
	}
	
	/**
	 * Deletes a tag, and will also delete the tag from all associated pages and media
	 */
	public function deleteTag() {
		$id = intval($this->row['id']);
		$query = "UPDATE ".TBL_PAGES." AS p, ".TBL_MEDIA." AS m SET p.tags = REPLACE(p.tags, ',$id', ''), m.tags = REPLACE(m.tags, ',$id', '')";
		$this->db->query($query);
		$query = "UPDATE ".TBL_PAGES." AS p, ".TBL_MEDIA." AS m SET p.tags = REPLACE(p.tags, '$id,', ''), m.tags = REPLACE(m.tags, '$id,', '')";
		$this->db->query($query);
		$query = "UPDATE ".TBL_PAGES." AS p, ".TBL_MEDIA." AS m SET p.tags = REPLACE(p.tags, '$id', ''), m.tags = REPLACE(m.tags, '$id', '')";
		$this->db->query($query);
		
		$query = "DELETE FROM ".TBL_TAGS." WHERE id='$id' LIMIT 1";
		return $this->db->query($query);
	}
	
	/**
	 * Goes through the list of names in the array $tags and inserts
	 * those that don't already exist in into the database.
	 * @param Database $db
	 * @param Array $tags
	 * @param string $type
	 * @return array Tag - A complete array of the tags, including the ones that were already there
	 */
	static function insertNonExistentTags(Database $db, Array $names, $type) {
		$names = trimArray($names);	//Trim empty tags
		foreach($names as $key => $value) {
			$type = mysql_real_escape_string($type);
			$value = mysql_real_escape_string($value);
			//Insert it into the table ignoring the query 
			$db->tableInsert(TBL_TAGS, array("type"=>$type, "name"=>$value), true);
		}
		return TagFactory::getTagsByName($db, $names, $type);
	}
	
	/**
	 * Returns a list of Ids from a list of tags
	 * @param array $tags
	 * @return array
	 */
	static function getTagIds(array $tags) {
		$ids = array();
		foreach($tags as $value) {
			$ids[] = $value->getId();
		}
		return $ids;
	}
	
	/**
	 * Returns a list of Names from a list of Tags
	 * @param array $tags
	 * @return array
	 */
	static function getTagNames(array $tags) {
		$ids = array();
		foreach($tags as $value) {
			$ids[] = $value->getName();
		}
		return $ids;
	}
}

class TagFactory {
	/**
	 * Loads the $fields fields from the database for tags matching the $filters (with
	 * each value being a valid mySQL field) with limit $limit starting at $start.
	 * @param object $db
	 * @param array $fields optional
	 * @param array $filters optional
	 * @param int $limit optional
	 * @param int $start optional
	 */
	static function loadTags(Database $db, $fields = false, $filters = false, $orderby = false, $limit = false, $start = false) {
		$tags = $db->tableQuery(TBL_TAGS, $fields, $filters, $orderby, $limit, $start);
		if(!$tags)
			return false;
		
		$tagsarray = array();
		$i = 0;
		while($row = mysql_fetch_assoc($tags)) {
			$tagsarray[$i] = new Tag($db);
			if($row['data'])
				$row['data'] = unserialize($row['data']);
			else
				$row['data'] = array();
			$tagsarray[$i]->setTagData($row);
			$i++;
		}
		
		return $tagsarray;
	}
	
	/**
	 * Gets the tags from the database with the most articles, sorted from highest to lowest, optionally
	 * limited to $limit (default 50)
	 * @param object $db
	 * @param int $limit optional (default 50)
	 * @return array of Tag
	 */
	static function getPopularTags(Database $db, $limit = 50) {
		return TagFactory::loadTags($db, false, false, "num_articles DESC", $limit, false);
	}
	
	/**
	 * Gets an tag by a name
	 * @param object $db
	 * @param string $name
	 * @return Tag
	 */
	static function getTagByName(Database $db, $name, $type = false) {
		$name = mysql_real_escape_string($name);
		$and = "";
		if($type)
			$and = " AND type='".mysql_real_escape_string($type)."'";
		$tag = TagFactory::loadTags($db, false, "name = '$name'$and", false, 1, 0);
		if(!$tag || count($tag) == 0)
			return false;
		return $tag[0];
	}
	
	/**
	 * Gets many tags by name
	 * @param object $db
	 * @param array $name
	 * @return array Tags
	 */
	static function getTagsByName(Database $db, Array $names, $type = false) {
		foreach($names as $key => $value)
			$names[$key] = mysql_real_escape_string($value);
		$filter = array("name = '".implode("' OR name = '", $names)."'");
		if($type != false)
			$filter[] = "type = '".mysql_real_escape_string($type)."'";
		return TagFactory::loadTags($db, false, $filter);
	}
	
	/**
	 * Gets many tags by partially matching the name
	 * @param object $db
	 * @param array $name
	 * @return array Tags
	 */
	static function getTagsByPartialName(Database $db, Array $names, $type = false) {
		foreach($names as $key => $value)
			$names[$key] = strtoupper(mysql_real_escape_string($value));
		$filter = array("UPPER(name) REGEXP '[[:<:]]".implode("[[:>:]]' OR UPPER(name) REGEXP '[[:<:]]", $names)."[[:>:]]'");
		if($type != false)
			$filter[] = "type = '".mysql_real_escape_string($type)."'";
		return TagFactory::loadTags($db, false, $filter);
	}
	
	/**
	 * Gets all tags of a given type
	 * @param Database $db
	 * @param string $type
	 * @return array Tag
	 */
	static function getTagsByType(Database $db, $type, $orderby = false, $limit = false, $start = false) {
		return TagFactory::loadTags($db, false, "type = '$type'", $orderby, $limit, $start);
	}
	
	/**
	 * Gets all of the names of the tags of a given type
	 * @param Database $db
	 * @param string $type
	 * @return array String
	 */
	static function getTagNamesByType (Database $db, $type) {
		$allTags = TagFactory::getTagsByType($db, $type);
		$tagData = array();
		foreach($allTags as $value)
			$tagData[] = $value->getName();
		return $tagData;
	}
	
	/**
	 * Returns tags based on the format of the page editor's output
	 * This input is an array of author strings, which consist of a name and a title
	 * seperated by a comma.
	 * @param Database $db
	 * @param array $input
	 * @return array
	 */
	static function getAuthorsByFormInput(Database $db, Array $input) {
		$authors = array();
		$authorswithtitles = array();
		foreach($input as $key => $value) {		//Loop through the inputs
			$valueparts = explode(",", $value);	//Seperate it into author and title
			$authors[] = trim($valueparts[0]);
			if(count($valueparts) > 1)	//Check to see that the title was given
				$authorwithtitles[] = trim($valueparts[0]).", ".trim($valueparts[1]);
		}
		
		return array(
			"authors" => TagFactory::getTagsByName($db, $authors, "author"),
			"authors_title" => TagFactory::getTagsByName($db, $authorswithtitles, "author_title")
		);
	}
	
	/**
	 * Gets an array of Tag from an array of IDs
	 * @param object $db
	 * @retur array
	 */
	static function getTagsById(Database $db, array $id) {
		if(count($id) == 0) return array();
		foreach($id as $key => $value)
			$id[$key] = intval($value);
		$filter = array("id = ".implode(" OR id = ", $id));
		return TagFactory::loadTags($db, false, $filter);
	}
	
	/**
	 * Gets one Tag by an ID
	 * @param object $db
	 * @param int $id
	 * @return Tag
	 */
	static function getTagById(Database $db, $id) {
		if(!is_numeric($id)) return false;
		$id = intval($id);
		$filter = array("id = $id");
		$tags = TagFactory::loadTags($db, false, $filter);
		return $tags[0];
	}
}