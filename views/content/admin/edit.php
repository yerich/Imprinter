<?php 
$_PAGETITLE = "Edit Page";
include_javascript("ckeditor");
include_javascript("jquery.autocomplete");
include_javascript("jquery.facebookbox");

//Process the referring page (for the cancel button)
if(isset($_REQUEST['ref']))
	$ref = $_REQUEST['ref'];
else
	$ref = $_SERVER['HTTP_REFERER'];

if($_POST['action'] == "edit" || $_POST['action'] == "new") {	//Page has been saved
	if($_POST['action'] == "edit") {
		$page = loadPage($_REQUEST['pgid'], $db, false);
		if(!$page) {
			Error::userError("The page that you're looking to edit can't be found.");
			header("Location: /admin/pages.php");
			die();
		}
		$new = false;
	}
	else {	//New page created
		require_once(WEB_ROOT."/views/content/content.php");
		$page = new Content($db);
		$new = true;
	}
	
	//Remove authors consisting of whitespace or empty strings
	foreach ($_POST['authors'] as $key => $value) { 
		if (trim($value) == "") { 
			unset($_POST['authors'][$key]); 
		} 
	}
	
	//Seperate authors into thier names and titles
	$authornames = array();
	$authorswithtitles = array();
	foreach($_POST['authors'] as $key => $value) {		//Loop through the inputs
		$valueparts = explode(",", $value);	//Seperate it into author and title
		$authornames[] = trim($valueparts[0]);
		if(count($valueparts) > 1)	//Check to see that the title was given
			$authorswithtitles[] = trim($valueparts[0]).", ".trim($valueparts[1]);
		else
			$authorswithtitles[] = trim($valueparts[0]);
	}
	
	//Insert authors' titles into the database:
	$authors_title = Tag::insertNonExistentTags($db, $authorswithtitles, "author_title");
	//Get the authors based on form input
	$authors = Tag::insertNonExistentTags($db, $authornames, "author");
	$authorTags = $authorIDs = array();
	$tagIds = array();
	foreach($authors as $value) {
		$authorTags[] = $value->getName();
		$tagIds[] = $value->getId();
	}
	
	$display_authors = array();
	foreach($authors_title as $value) {	//Insert the authors with ttitles into the array as well, but also have them be displayed on the form afterwards
		$authorTags[] = $value->getName();
		$tagIds[] = $value->getId();
		$display_authors[] = $value->getName();
	}
	
	$pagedata = array(
		"title" => $_POST['title'], 
		"content" => $_POST['content'], 
		"view" => "content", 
		"tags" => $tagIds);
	$page->setPagedata($pagedata);
	$page->savePage($new);
	$action = "edit";
	$authors = $display_authors;
	
	if($_POST['action'] == "edit")
		$pageid = $_REQUEST['pgid'];
	else
		$pageid = mysql_insert_id();
	
	Error::userMessage("Page has been saved sucessfully.");
	if($_POST['redir'] != "") {	//Redirect if told to
		if(strstr(urldecode($_POST['redir']), "?"))
			header("Location: ".urldecode($_POST['redir']."&ref=".urlencode($ref)));
		else
			header("Location: ".urldecode($_POST['redir']."?ref=".urlencode($ref)));
		die();
	}
}
elseif($_GET['action'] == "edit") {	//Requesting an existing page
	$pageid = $_REQUEST['pgid'];
	$page = loadPage($pageid, $db, false);
	if(!$page) {
		Error::userError("The page that you're looking to edit can't be found.");
		header("Location: /admin/pages.php");
		die();
	}
	
	$action = "edit";
	$authors = $page->getAuthorNames();
}
elseif($_GET['action'] == "new") {	//Requesting form to create a new page
	require_once(WEB_ROOT."/views/content/content.php");
	$_PAGETITLE = "Create a New Page";
	$page = new Content($db);
	$action = "new";
	$authors = array();
}
else {
	header("Location: ?action=new");
	die();
}

require_once(WEB_ROOT."/admin/template/header.php");
?>
<form action="?action=edit&pgid=<?php echo $pageid ?>" method="post" id="editor_form">
	<input name="title" id="form_title" value="<?php echo htmlentities($page->getTitle()) ?>" size="40"/>
	
	<div id="form_content_wrapper">
		<textarea name="content" class="richedit" id="form_content" cols="65" rows="20"><?php echo $page->getContent() ?></textarea>
	</div>
	
	<input type="hidden" name="pgid" value="<?php echo $pageid ?>" />
	<input type="hidden" name="action" value="<?php echo $action ?>" />
	<input type="hidden" name="ref" value="<?php echo $ref ?>" />
	<input type="hidden" name="redir" id="form_redir" value="" />
	
	<fieldset id="editor_author_wrapper">
		<legend>Authors</legend>
		
		<div id="editor_author_textbox" class="facebookbox autocomplete">
		</div>

	</fieldset>
	
	<fieldset>
		<a class="bluebutton" href="javascript: void(0)" onclick="$('#editor_form').submit()">Save</a>
		<a class="greybutton" href="javascript: void(0)" onclick="$('#form_redir').val('<?php echo urlencode($ref) ?>'); $('#editor_form').submit()">Save and Return</a>
		<a class="greybutton" href="javascript: void(0)" onclick="$('#form_redir').val('<?php echo urlencode("?action=new") ?>'); $('#editor_form').submit()">Save and Create New</a>
		<a class="greybutton" href="<?php echo $ref ?>">Cancel</a>
	</fieldset>
</form>

<!--
Debugging information:
<?php print_r($page->pagedata['tags']); 
print_r($authors); ?>
<pre>
<?php print_r($_POST); ?>
</pre>
-->

<script type="text/javascript">

authorslist = <?php 
$tagData = TagFactory::getTagNamesByType($db, "author_title");
$tagDataAuthors = TagFactory::getTagNamesByType($db, "author");
echo json_encode(utf8_encode_recursive(array_values(array_unique(array_merge($tagData, $tagDataAuthors)))));
?>;
authors = <?php echo json_encode($authors);?>;

$("#editor_author_textbox").facebookbox({"form_name": "authors", "suggest" : authorslist, "initial" : authors});

//Initialize the editor
CKEDITOR.replace('form_content', {
	resize_minWidth : $("#main").width(),	//No horizontal resizing
	resize_maxWidth : $("#main").width(),
	resize_minHeight : $("#form_content").height(),
	
	toolbar :
		[
			{ name: 'styles', items : [ 'Format'] },
			{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
			{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
			{ name: 'editing', items : [ 'Find','Replace','SpellChecker'] },
		    
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight'] },
			{ name: 'links', items : [ 'Link'] },
			{ name: 'insert', items : [ 'Image','PageBreak'] },
			
			{ name: 'document', items : [ 'Source'] }
		]
});


</script>