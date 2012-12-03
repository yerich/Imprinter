<?php
$_PAGETITLE = "Edit Tag";

if(!$_GET['tagid'] || !is_numeric($_GET['tagid'])) {
	header("Location: ./");
	die();
}
$tagid = intval($_GET['tagid']);
$action = "edit";

//Process the referring page (for the cancel button)
if(isset($_REQUEST['ref']))
	$ref = $_REQUEST['ref'];
else
	$ref = $_SERVER['HTTP_REFERER'];
if(!$ref)
	$ref = "./";

//Load the tag from the database
$tag = TagFactory::getTagsById($db, array($tagid));
if(count($tag) != 1) {
	header("Location: ./");
	die();
}
$tag = $tag[0];

if($_POST['action'] == "edit") {
	$tagData = $tag->getTagData();
	$tagData['name'] = trim($_POST['name']);
	$tagData['data']['description'] = trim($_POST['description']);
	$tagData['data']['email'] = trim($_POST['email']);
	$tagData['data']['tagline'] = trim($_POST['tagline']);
	
	$tag->setTagData($tagData);
	$tag->saveTag();
	
	Error::userMessage("Tag has been saved sucessfully.");
	header("Location: $ref");
	die();
}

require_once(WEB_ROOT."/admin/template/header.php");
?>

<form action="edit.php?action=edit&tagid=<?php echo $tagid ?>" method="post" id="editor_form">
	<input type="hidden" name="tagid" value="<?php echo $tagid ?>" />
	<input type="hidden" name="action" value="<?php echo $action ?>" />
	<input type="hidden" name="ref" value="<?php echo $ref ?>" />
	
	<input name="name" id="form_title" value="<?php echo htmlentities($tag->getName()) ?>" size="40"/>
	
	<fieldset>
		<legend>Tag Information</legend>
		<table style="width: 100%;">
			<tr>
				<td style="width: 200px;">
					Tag Type
				</td>
				<td>
					<strong><?php echo $tag->getType() ?></strong><br />
					<span class="info">This cannot be changed.</span><br /><br />
				</td>
			</tr>
			<tr>
				<td>Tagline</td>
				<td>
					<input type="text" size="60" name="tagline" value="<?php echo $tag->getTagline() ?>" />
				</td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
					<textarea rows="6" cols="40" name="description"><?php echo $tag->getDescription() ?></textarea>
				</td>
			</tr>
			<tr>
				<td>Email</td>
				<td>
					<input type="text" size="60" name="email" value="<?php echo $tag->getEmail() ?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<a href="javascript:void(0)" onclick="$('#editor_form').submit()" class="bluebutton">Save</a>
		<a href="<?php echo $ref ?>"  class="greybutton">Cancel</a>
	</fieldset>

</form>

<?php
print_r($tag->getTagData());
