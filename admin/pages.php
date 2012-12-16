<?php 
$_PAGETITLE = "All Pages";
include("../include/init.php");
checkUserLogin($user);

include("template/header.php");
?>

<form id="content_list_form" method="post" action="./">

<div class="list_table_toolbar">
	<a href="./views/content/edit.php?action=new" class="greybutton">Create a new page</a>
</div>

<table class="list_table">
	<tr class="list_table_header">
		<th class="list_table_checkbox"><input type="checkbox" name="selectall" value="true" id="list_table_checkbox_selectall" /></th>
		<th>View</th>
		<th>Title</th>
		<th>Author</th>
		<th>Created</th>
		<th>Actions</th>
	</tr>
	<?php 
	//Get all of the pages
	$contentpages = PageFactory::loadPages($db, array("id", "title", "tags", "created", "view"), false, "title asc", 20);
	
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
		
		$editlink = "<a class=\"content_list_edit\" href=\"./views/content/edit.php?action=edit&pgid=".$id."\">Edit</a>";
		$deletelink = "<a class=\"content_list_delete redlink\" href=\"./views/content/delete.php?pgid[]=".$id."\">Delete</a>";
		
		echo "<tr><td class=\"content_list_checkbox $evenodd\">";
		echo "<input type=\"checkbox\" id=\"checkbox_".$id."\" name=\"pages[]\" value=\"".$id."\" />";
		echo "</td>";
		
		echo "<td>".$contentpages[$i]->getView()."</td>";
		echo "<td class=\"content_list_title\"><a href=\"/".$id."\">".$titlestring."</a></td>";
		echo "<td class=\"content_list_author\">".$authorstring."</td>";
		echo "<td class=\"content_list_created\">".$created."</td>";
		echo "<td class=\"content_list_actions\">$editlink | $deletelink</td>";
		echo "</tr>";
	}
	?>
</table>

</form>

<script type="text/javascript">

$("#list_table_checkbox_selectall").click(function() {
	if(this.checked) {
		$(".content_list_checkbox input").prop("checked", true);
	}
	else {
		$(".content_list_checkbox input").prop("checked", false);
	}
});

</script>

<?php 
include("template/footer.php");