<?php 
/**
 * Homepage for the administration panel.
 * 
 * @author rye
 * @package imprinter
 */

$_PAGETITLE = "Adminstration";
include("../include/init.php");
checkUserLogin($user);

include("template/header.php");
?>

<h2>Recently Posted</h2>
<div class="list_table_toolbar">
	<a href="/admin/views/article/edit.php?action=new" class="bluebutton">Create a new article</a>
</div>

<table class="list_table">
	<tr class="list_table_header">
		<th class="list_table_checkbox"><input type="checkbox" name="selectall" value="true" id="list_table_checkbox_selectall" /></th>
		<th>Title</th>
		<th>Section</th>
		<th>Author</th>
		<th>Created</th>
		<th>Actions</th>
	</tr>
<?php 
//Get all of the pages
$filters = array("view = 'article'");
if($_GET['search'])
	$filters[] = "title LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
$contentpages = PageFactory::loadPages($db, array("id", "title", "tags", "created"), $filters, "created desc", 20, $start);

//Print them out in table form one row at a time
for($i = 0; $i < count($contentpages); $i++) {
	$contentpages[$i]->processPageTags();
	$evenodd = ($i % 2 == 0)? "even" : "odd";
	$authors = $contentpages[$i]->getAuthorNames(true);
	if(count($authors) > 2) {
		$restauthors = "<span title=\"".implode(", ", array_slice($authors, 2))."\"><em>and ".(count($authors) - 2)." more</em></span>";
		$authors = array_slice($authors, 0, 2);
		$authors[] = $restauthors;
	}
	$authorstring = implode(", ", $authors);
	//Replace empty titles and authors with nicer text informing the user of such
	if($authorstring == "")
		$authorstring = "<span class=\"content_list_author_noauthor\">No author</span>";
	$titlestring = $contentpages[$i]->getTitle();
	if($titlestring == "")
		$titlestring = "<span class=\"content_list_title_notitle\">(Untitled Page)</span>";
	$id = $contentpages[$i]->getId();
	$created = date("M. j, Y, g:ia", $contentpages[$i]->getCreatedTime());
	$section = implode(", ", $contentpages[$i]->getSection());
	
	$editlink = "<a class=\"content_list_edit\" href=\"/admin/views/article/edit.php?action=edit&pgid=".$id."\">Edit</a>";
	$deletelink = "<a class=\"content_list_delete redlink\" href=\"/admin/delete.php?pgid[]=".$id."\">Delete</a>";
	
	echo "<tr><td class=\"content_list_checkbox $evenodd\">";
	echo "<input type=\"checkbox\" id=\"checkbox_".$id."\" name=\"pgid[]\" value=\"".$id."\" />";
	echo "</td>";
	
	echo "<td class=\"content_list_title\"><a href=\"/".$id."\">".$titlestring."</a></td>";
	echo "<td class=\"content_list_section\">$section</td>";
	echo "<td class=\"content_list_author\">".$authorstring."</td>";
	echo "<td class=\"content_list_created\">".$created."</td>";
	echo "<td class=\"content_list_actions\">$editlink | $deletelink</td>";
	echo "</tr>";
}
?>
</table>

<?php 
include("template/footer.php");