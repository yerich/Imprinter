<?php 
/**
 * Homepage for the administration panel.
 * 
 * @author rye
 * @package imprinter
 */

$_PAGETITLE = "Test Page";
include("../include/init.php");
checkUserLogin($user);

include("template/header.php");

$tags = Tag::insertNonExistentTags($db, array("Richard Ye", "Chicken Pudding"), "author");
echo $tags['authors'][1]->name;
?>

<?php
include("template/footer.php");
