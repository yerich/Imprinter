<?php 
$_PAGETITLE = "Manage Articles";
include_javascript("jquery.adminfunc");
require_once(WEB_ROOT."/admin/template/header.php");
$page = 0;
$numperpage = 20;
if($_GET['page'])
	$page = max(intval($_GET['page']) - 1, 0);
$start = $page * $numperpage;
?>

<form id="content_search_form" method="get" action="./">
	<fieldset>
		<legend>Search</legend>
		<input type="text" size="40" name="search" value="<?php echo $_GET['search'] ?>"/>
		 <a class="greybutton" href="#" onclick="$('#content_search_form').submit();">Search</a>
	</fieldset>
</form>

<form id="content_list_form" method="post" action="./">

<div class="list_table_toolbar">
	<a href="javascript:void(0)" class="greybutton list_table_deletebutton">Delete Selected Articles</a>
	<a href="javascript:void(0)" class="greybutton list_table_editbutton">Edit Selected Articles</a>
	<a href="edit.php?action=new" class="bluebutton">Create a new article</a>
</div>

<div id="content_form_massedit" style="display: none;">
	<fieldset>
		<legend>Mass Edit</legend>
	    <table style="width: 100%">  
	        <tr>
	            <td style="width: 150px;">Creation Date:</td>
	            <td><input type="text" size="40" name="massedit_date" /></td>
	        </tr>
	    </table>
	    <a class="bluebutton" href="#" onclick="$('#content_list_form').attr('action', 'massedit.php'); $('#content_list_form').submit();">Save</a>
	    <a class="greybutton" href="#" onclick="$('#content_form_massedit').hide();">Cancel</a>
    </fieldset>
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
		
		$editlink = "<a class=\"content_list_edit\" href=\"edit.php?action=edit&pgid=".$id."\">Edit</a>";
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

<?php printPagination("./?search=".$_GET['search'], $page, $numperpage, $db->countTableRows(TBL_PAGES, $filters)); ?>

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

$(".list_table_deletebutton").click(function() {
	numselected = $(".content_list_checkbox input:checked").length;
	if(numselected == 0) {
		alert("No pages were selected.");
		return false;
	}
	answer = false;
	if(numselected > 1)
		answer = confirm("Are you sure you want to permenantly delete the selected "+numselected+" pages?")
	else
		answer = confirm("Are you sure you want to permenantly delete the selected page?")
	
	if(answer) {	//Confirmation was given, submit the form
		$("#content_list_form").attr("action", "/admin/delete.php");
		$("#content_list_form").submit();
	}
});

$(".list_table_editbutton").click(function() {
    numselected = $(".content_list_checkbox input:checked").length;
    if(numselected == 0) {
        alert("No pages were selected.");
        return false;
    }
    
    $("#content_form_massedit").show();
    $("#content_list_form").attr("action", "/admin/massedit.php");
});

</script>

<?php 
require_once(WEB_ROOT."/admin/template/footer.php");