<?php 
$_PAGETITLE = "Edit Frontpage";
include_javascript("ckeditor");
include_javascript("jquery.autocomplete");
include_javascript("jquery.facebookbox");
include_javascript("frontpage.filter");

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
		require_once(WEB_ROOT."/views/frontpage/frontpage.php");
		$page = new Frontpage($db);
		$new = true;
	}
	
	$currdata = $page->getData();
	
	//Process the submitted array of filters
	$filters = $_POST['filter'];
	if(!$filters)
		$filters = array();
	
	//Process filter options
	$filter_options = $_POST['filter_options'];
	if(!$filter_options)
		$filter_options = array();
	
	//Change the template
	if($_POST['change_template'] == "Change" && $_POST['template'] != $currdata['template']) {
		$filters = array();
	}
	$template = $_POST['template'];
	
	foreach($filters as $contentarea => $filterlist) {
		foreach($filterlist as $unique => $filter) {
			foreach($filter as $key => $value) {
				if($key == "type" && ($value == "author" || $value == "tag" || $value == "series" || $value == "section")) {
					//Convert tag names into IDs and save to the filters array
					$tagdata = Tag::insertNonExistentTags($db, $filters[$contentarea][$unique]["value"], $value);
					$tagIds = array();
					foreach($tagdata as $tag) {
						$tagIds[] = $tag->getId();
					}
					$filters[$contentarea][$unique]["value"] = $tagIds;
				}
			}
		}
	}
	
	
	$data = array(
		"filter" => $filters,
		"template" => $template,
		"filter_options" => $filter_options,
		"allow_duplicates" => ($_POST['allow_duplicates'] == 1));
	
	$pagedata = array(
		"title" => $_POST['title'], 
		"content" => $_POST['content'], 
		"view" => "frontpage", 
		"tags" => array(),
		"data" => $data);
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
		header("Location: /admin/views/frontpage/");
		die();
	}
	
	$action = "edit";
	$data = $page->getData();
	if(!$data['filter'])
		$data['filter'] = array();
	if(!$data['filter_options'])
		$data['filter_options'] = array();
	$filters = $data['filter'];
	$filter_options = $data['filter_options'];
}
elseif($_GET['action'] == "new") {	//Requesting form to create a new page
	require_once(WEB_ROOT."/views/frontpage/frontpage.php");
	$_PAGETITLE = "Create a New Frontpage";
	$page = new Frontpage($db);
	$action = "new";
	$filters = array();
	$filter_options = array();
	$data = array("template"=>FRONTPAGE_DEFAULT);	//Use the default frontpage template
}
else {
	header("Location: ?action=new");
	die();
}

//Filters store tags as IDs; we need to change this back into names
	foreach($filters as $contentarea => $filterlist) {
		foreach($filterlist as $unique => $filter) {
			if(in_array($filters[$contentarea][$unique]["type"], array("author", "tag", "series", "section"))) {
				$filters[$contentarea][$unique]["value"] = 
					Tag::getTagNames(TagFactory::getTagsById($db, $filters[$contentarea][$unique]["value"]));
			}
		}
	}

$frontpageData = Frontpage::getFrontpageData($data['template']);

require_once(WEB_ROOT."/admin/template/header.php");
?>
<form action="?action=edit&pgid=<?php echo $pageid ?>" method="post" id="editor_form">
	<input name="title" id="form_title" value="<?php echo htmlentities($page->getTitle()) ?>" size="40"/>
	
	<fieldset>
		<legend>Frontpage Template</legend>
		<p>Change the frontpage template. Note: changing this will mean that all of the filters will be lost, and the page will start fresh.</p>
		
		<select name="template" id="form_template">
			<?php
			foreach(Theme::getFrontpageList() as $value) {
				if($value == $data['template']) {
					echo "<option value='$value' selected='selected'>$value</option>";
				}
				else {
					echo "<option value='$value'>$value</option>";
				}
			}
			?>
		</select>
		<input type="submit" name="change_template" value="Change" class="greybutton" />
	</fieldset>
	
	<input type="hidden" name="pgid" value="<?php echo $pageid ?>" />
	<input type="hidden" name="action" value="<?php echo $action ?>" />
	<input type="hidden" name="ref" value="<?php echo $ref ?>" />
	<input type="hidden" name="redir" id="form_redir" value="" />
	
<?php
foreach($frontpageData['filters'] as $value) {
	$name = $value['name'];
	echo "<fieldset id='frontpage_section_$name'>";
	echo "<legend>".$value['name']."</legend>";
	if($value['description'])
		echo "<p>".$value['description']."</p>";
	
	echo "<fieldset><legend>Filters</legend><div id='frontpage_section_filters_$name' class='frontpage_section_filter'></div>";
	echo "<a href=\"javascript:$('#frontpage_section_filters_$name').frontpageFilter('addFilter', 'frontpage_section_filters_$name')\" class='greybutton'>+ Add a Filter</a>";
	?>
	</fieldset>
	
	<fieldset>
		<legend>Other Options</legend>
		
		<table style="width: 100%">
			<tr>
				<td style="width: 200px;"><label for="frontpage_section_num_<?php echo $name?>">Number of items to show</label></td>
				<td>
					<select id="frontpage_section_num_<?php echo $name?>" name="filter_options[<?php echo $name?>][num]">
					<?php
					for($i = $value['min']; $i <= $value['max']; $i++) {
						if($filter_options[$name]['num'] == $i)
							echo "<option value='$i' selected='selected'>$i</option>";
						else
							echo "<option value='$i'>$i</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="frontpage_sort_<?php echo $name?>">Sort By</label></td>
				<td>
					<select id="frontpage_sort_<?php echo $name?>" name="filter_options[<?php echo $name?>][sort]">
					<?php
					foreach($page->valid_sort as $key => $value) {
						if($filter_options[$name]['sort'] == $value)
							echo "<option value='$value' selected='selected'>$key</option>";
						else
							echo "<option value='$value'>$key</option>";
					}
					?>
					</select>
					
					<select id="frontpage_sort_<?php echo $name?>" name="filter_options[<?php echo $name?>][sortorder]">
					<?php
					foreach(array("Descending (highest to lowest)" => "desc", "Ascending (lowest to highest)" => "asc") as $key => $value) {
						if($filter_options[$name]['sortorder'] == $value)
							echo "<option value='$value' selected='selected'>$key</option>";
						else
							echo "<option value='$value'>$key</option>";
					}
					?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<?php
	echo "</fieldset>";
}
?>
	
	<fieldset>
		<legend>General Options</legend>
		<table>
			<tr>
				<td style="text-align: right;"><input type="checkbox" name="allow_duplicates" 
					id="form_allow_duplicates" <?php if($data['allow_duplicates'] == "1") echo "checked='checked'" ?> value="1" /></td>
				<td><label for="form_allow_duplicates">Allow Duplicates</form></td>
			</tr>
		</table>
	</fieldset>
	
	<?php printStandardPageControls($ref); ?>
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
authorslist = <?php echo json_encode(utf8_encode_recursive(array_values(TagFactory::getTagNamesByType($db, "author")))); ?>;
tagslist = <?php echo json_encode(utf8_encode_recursive(array_values(TagFactory::getTagNamesByType($db, "tag")))); ?>;
sectionlist = <?php echo json_encode(utf8_encode_recursive(array_values(TagFactory::getTagNamesByType($db, "section")))); ?>;
serieslist = <?php echo json_encode(utf8_encode_recursive(array_values(TagFactory::getTagNamesByType($db, "series")))); ?>;
authors = <?php echo json_encode(utf8_encode_recursive($authors));?>;
initialfilters = <?php echo json_encode(utf8_encode_recursive($filters)); ?>

$("#editor_author_textbox").facebookbox({"form_name": "authors", "suggest" : authorslist, "initial" : authors});
$(".frontpage_section_filter").frontpageFilter(
	{
		"form_name" : "frontpage_filter",
		"suggest_tags" : tagslist,
		"suggest_authors" : authorslist,
		"suggest_series" : serieslist,
		"suggest_section" : sectionlist,
		"initial" : initialfilters,
	}
)
</script>