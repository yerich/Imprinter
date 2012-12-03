<?php 
$_PAGETITLE = "Edit Article";
include_javascript("ckeditor");
include_javascript("jquery.autocomplete");
include_javascript("jquery.facebookbox");
include_javascript("jquery.ui.widget");
include_javascript("jquery.iframe-transport");
include_javascript("fileupload");
include_javascript("jquery.mediaupload");

//Process the referring page (for the cancel button)
if(isset($_REQUEST['ref']))
	$ref = $_REQUEST['ref'];
else
	$ref = $_SERVER['HTTP_REFERER'];

if($_POST['action'] == "edit" || $_POST['action'] == "new") {	//Page has been saved
	if($_POST['action'] == "edit") {
		$page = loadPage($_REQUEST['pgid'], $db, false);
		if(!$page) {
			Error::userError("The article that you're looking to edit can't be found.");
			header("Location: /admin/pages.php");
			die();
		}
		$new = false;
	}
	else {	//New page created
		require_once(WEB_ROOT."/views/article/article.php");
		$page = new Article($db);
		$new = true;
	}
	
	//Remove authors consisting of whitespace or empty strings
	$_POST['authors'] = trimArray($_POST['authors']);
	
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
	foreach($authors_title as $value) {	//Insert the authors with titles into the array as well, but also have them be displayed on the form afterwards
		$authorTags[] = $value->getName();
		$tagIds[] = $value->getId();
		$display_authors[] = $value->getName();
	}
	
	//Process Tags
	$tags = Tag::insertNonExistentTags($db, $_POST['tags'], "tag");
	$display_tags = array();
	foreach($tags as $value) {
		$tagIds[] = $value->getId();
		$display_tags[] = $value->getName();
	}
	
	//Process Series
	$series = Tag::insertNonExistentTags($db, $_POST['series'], "series");
	$display_series = array();
	foreach($series as $value) {
		$tagIds[] = $value->getId();
		$display_series[] = $value->getName();
	}
	
	//Process Sections
	$section = Tag::insertNonExistentTags($db, $_POST['section'], "section");
	$display_section = array();
	foreach($section as $value) {
		$tagIds[] = $value->getId();
		$display_section[] = $value->getName();
	}
	
	//Process Media
	if(!is_array($_POST['file_location']))
		$_POST['file_location'] = array();
	$media = MediaFactory::loadFiles($db, $_POST['file_location']);
	
	//Generate Media String to put in the database
	$mediadata = array();
	$i = 0;
	foreach($media as $key => $value) {
		if($value->isNew() == true) {	//New media has just been uploaded, so set the caption, author and tags as the default instead of just for the article.
			//TODO
		}
		$authors = Tag::insertNonExistentTags($db, $_POST["media_author"][$_POST["file_unique"][$i]], "media_author");
		$tags = Tag::insertNonExistentTags($db, $_POST["media_tag"][$_POST["file_unique"][$i]], "media_tag");
		$mediadata[] = array(
			"id" => $value->getID(), 
			"location" => $value->getLocation(), 
			"caption" => $_POST['media_caption'][$i], 
			"tags" => array_merge($tags, $authors),
			"url" => $value->getLocation(),
			"name" => substr(strrchr($value->getLocation(), "/"), 1),
			"orig_authors" => $authors,
			"orig_tags" => $tags,
			);
		$i++;
	}
	
	//Trim the content
	$content = trim($_POST['content']);
	$content = preg_replace('#^<p[^>]*>(?:\s+|(?:&nbsp;)+|(?:<br\s*/?>)+)*</p>#', '', $content);
	
	$pagedata_tmp = $pagedata = array(
		"title" => $_POST['title'], 
		"content" => $content, 
		"view" => "article", 
		"tags" => $tagIds,
		"media" => $mediadata,
		"created" => strtotime($_POST['created']),
		"data" => array(
			"subtitle" => $_POST['subtitle'],
		));
	//We don't want to store the originally entered tags and authors, but we need to use them later in the script
	foreach ($pagedata['media'] as $key => $value) {	
		unset ($pagedata['media'][$key]['orig_authors']);
		unset ($pagedata['media'][$key]['orig_tags']);
	}
	
	//Check creation time to see if valid. If not, default to now.
	if(!$pagedata['created'])
		$pagedata['created'] = $pagedata_tmp['created'] = time();
	
	$page->setPagedata($pagedata);
	$page->savePage($new);	//Save the page
	$action = "edit";
	$authors = $display_authors;
	$tags = $display_tags;
	$series = $display_series;
	$section = $display_section;
	
	$mediadata = array(); 
	foreach($pagedata_tmp['media'] as $key => $value) {	//Retore the old form values for display to the user
		$mediadata[] = array("name" => $value['name'], 
		"url" => $value['url'], 
		"unique" => $value['id'], 
		"caption" => $value['caption'], 
		"author" => Tag::getTagNames($value['orig_authors']),
		"tags" => Tag::getTagNames($value['orig_tags']));
	}
	
	if($_POST['action'] == "edit")
		$pageid = $_REQUEST['pgid'];
	else
		$pageid = mysql_insert_id();
	
	Error::userMessage("Your article has been saved sucessfully. <a href='".$page->getURL()."'>Preview Article</a>");
	if($_POST['redir'] != "") {	//Redirect if told to
		if(strstr(urldecode($_POST['redir']), "?"))
			header("Location: ".urldecode($_POST['redir']."&ref=".urlencode($ref)));
		else
			header("Location: ".urldecode($_POST['redir']."?ref=".urlencode($ref)));
		die();
	}
}
elseif($_GET['action'] == "edit") {	//Requesting an existing article
	$pageid = $_REQUEST['pgid'];
	$page = loadPage($pageid, $db, false);
	if(!$page) {
		Error::userError("The article that you're looking to edit can't be found.");
		header("Location: /admin/pages.php");
		die();
	}
	
	$action = "edit";
	$authors = $page->getAuthorNames();
	$section = $page->getTagNamesByType("section");
	$series = $page->getTagNamesByType("series");
	$tags = $page->getTagNamesByType("tags");
	
	$mediadata = array(); 
	foreach($page->pagedata['media'] as $value) 
		$mediadata[] = $value->getJSONData(array("resize"=>true, "width"=>250, "height"=>175));
}
elseif($_GET['action'] == "new") {	//Requesting form to create a new article
	require_once(WEB_ROOT."/views/article/article.php");
	$_PAGETITLE = "Create a New Article";
	$page = new Article($db);
	$page->setCreatedTime(time());
	$action = "new";
	$authors = array();
	$mediadata = array();
}
else {
	header("Location: ?action=new");
	die();
}

require_once(WEB_ROOT."/admin/template/header.php");
?>
<form action="?action=edit&pgid=<?php echo $pageid ?>" method="post" id="editor_form">
	<fieldset>
		<table style="width: 100%;">
			<tr>
				<td colspan="4"><input name="title" type="text" id="form_title" value="<?php echo htmlentities($page->getTitle()) ?>" size="40"/></td>
			</tr>
			<tr>
				<td style="width: 70px;"><label for="form_subtitle">Subtitle</label></td>
				<td colspan="3"><input name="subtitle" id="form_subtitle" type="text" value="<?php echo htmlentities($page->getSubtitle()) ?>" size="40"/></td>
			</tr>
			<tr>
				<td><label for="editor_section_textbox">Section</label></td>
				<td style="padding-right: 10px; width: 250px;"><div id="editor_section_textbox" class="facebookbox autocomplete"></div></td>
				<td style="width: 50px; text-align: right; padding-right: 10px;"><label for="editor_series_textbox">Series</label></td>
				<td><div id="editor_series_textbox" class="facebookbox autocomplete"></div></td>
			</tr>
		</table>
	</fieldset>
	
	<div id="form_content_wrapper">
		<textarea name="content" class="richedit" id="form_content" cols="65" rows="20"><?php echo $page->getContent() ?></textarea>
	</div>
	
	<input type="hidden" name="pgid" value="<?php echo $pageid ?>" />
	<input type="hidden" name="action" value="<?php echo $action ?>" />
	<input type="hidden" name="ref" value="<?php echo $ref ?>" />
	<input type="hidden" name="redir" id="form_redir" value="" />
	
	<fieldset id="editor_author_wrapper">
		<table style="width: 100%">
			<tr>
				<td style="width: 90px;"><label for="editor_author_textbox">Authors</label></td>
				<td><div id="editor_author_textbox" class="facebookbox autocomplete"></div></td>
			</tr>
			<tr>
				<td><label for="editor_tags_textbox">Tags</label></td>
				<td><div id="editor_tags_textbox" class="facebookbox autocomplete"></div></td>
			</tr>
			<tr>
				<td><label for="editor_tags_textbox">Created</label></td>
				<td><input name="created" id="form_created" type="text" value="<?php echo date("Y/m/d h:ia", $page->getCreatedTime()) ?>" size="40"/></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>Media</legend>
		
		<input id="fileupload" type="file" name="media[]" multiple> or Drag and Drop
		
		<div id="media_dropzone" class="form_dropzone">
			Drag and drop your files here to upload.
		</div>
		
		<div id="media_result">

		</div>
	</fieldset>
	
	<?php printStandardPageControls($ref); ?>
</form>

<!--
Debugging information:

Page information:
<?php print_r($page); ?>

Form POST values:
<?php print_r($_POST); ?>

-->

<script type="text/javascript">
authorslist = <?php 
$tagData = TagFactory::getTagNamesByType($db, "author_title");
$tagDataAuthors = TagFactory::getTagNamesByType($db, "author");
echo json_encode(utf8_encode_recursive(array_values(array_unique(array_merge($tagData, $tagDataAuthors)))));
?>;

tagslist = <?php echo json_encode(array_values(TagFactory::getTagNamesByType($db, "tag"))); ?>;
sectionlist = <?php echo json_encode(array_values(TagFactory::getTagNamesByType($db, "section"))); ?>;
serieslist = <?php echo json_encode(array_values(TagFactory::getTagNamesByType($db, "series"))); ?>;
mediaauthorslist = <?php echo json_encode(array_values(TagFactory::getTagNamesByType($db, "media_author"))) ?>;
mediatagslist = <?php echo json_encode(array_values(TagFactory::getTagNamesByType($db, "media_tag"))) ?>;
authors = <?php echo json_encode(utf8_encode_recursive($authors));?>;
tags = <?php echo json_encode(utf8_encode_recursive($tags));?>;
series = <?php echo json_encode(utf8_encode_recursive($series));?>;
section = <?php echo json_encode(utf8_encode_recursive($section));?>;

$("#editor_author_textbox").facebookbox({"form_name": "authors", "suggest" : authorslist, "initial" : authors});
$("#editor_tags_textbox").facebookbox({"form_name": "tags", "suggest" : tagslist, "initial" : tags});
$("#editor_series_textbox").facebookbox({"form_name": "series", "suggest" : serieslist, "initial" : series});
$("#editor_section_textbox").facebookbox({"form_name": "section", "suggest" : sectionlist, "initial" : section});
$("#media_1_author").facebookbox({"form_name": "media_author", "suggest" : [], "initial" : []});
$("#media_1_tags").facebookbox({"form_name": "media_author", "suggest" : [], "initial" : []});

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

$(function () {
    $('#fileupload').mediaupload({
    	index : 0,
    	upload_url: './media_upload.php',
    	media_result: $("#media_result"),
    	dropzone: $("#media_dropzone"),
    	tagslist : mediatagslist,
    	authorslist : mediaauthorslist,
    	initial : <?php echo json_encode(utf8_encode_recursive($mediadata));    	?>
    });
});

</script>

