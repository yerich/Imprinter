<?php 
$_PAGETITLE = "Tags";
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
	<a href="javascript:void(0)" class="greybutton list_table_deletebutton">Delete Selected Tags</a>
</div>
	
<table class="list_table">
	<tr class="list_table_header">
		<th class="list_table_checkbox"><input type="checkbox" name="selectall" value="true" id="list_table_checkbox_selectall" /></th>
		<th>Name</th>
		<th>Type</th>
		<th>Actions</th>
	</tr>
	<?php 
	//Get the tags from the database
	$filters = array();
	if($_GET['search'])
		$filters[] = "name LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
	$contentpages = TagFactory::loadTags($db, false, $filters, "name asc", 20, $start);
	
	//Print them out in table form one row at a time
	for($i = 0; $i < count($contentpages); $i++) {
		$evenodd = ($i % 2 == 0)? "even" : "odd";
		$titlestring = $contentpages[$i]->getName();
		if($titlestring == "")
			$titlestring = "<span class=\"content_list_title_notitle\">(Untitled Tag)</span>";
		$id = $contentpages[$i]->getId();
		$type = $contentpages[$i]->getType();
		
		$editlink = "<a class=\"content_list_edit\" href=\"edit.php?action=edit&tagid=".$id."\">Edit</a>";
		$deletelink = "<a class=\"content_list_delete redlink\" href=\"delete.php?tagid[]=".$id."\">Delete</a>";
		
		echo "<tr><td class=\"content_list_checkbox $evenodd\">";
		echo "<input type=\"checkbox\" id=\"checkbox_".$id."\" name=\"pgid[]\" value=\"".$id."\" />";
		echo "</td>";
		
		echo "<td class=\"content_list_title\"><a href=\"/".$type."/".$titlestring."\">".$titlestring."</a></td>";
		echo "<td class=\"content_list_created\">".$type."</td>";
		echo "<td class=\"content_list_actions\">$editlink | $deletelink</td>";
		echo "</tr>";
	}
	?>
</table>

<script type="text/javascript">

$("#list_table_checkbox_selectall").click(function() {
	if(this.checked) {
		$(".content_list_checkbox input").prop("checked", true);
	}
	else {
		$(".content_list_checkbox input").prop("checked", false);
	}
});

$(".redlink").click(function() {
	if($(this).html() != "Sure?") {
		$(this).data("redlink_old", $(this).html());
		$(this).html("Sure?");
		
		return false;
	}
});

$(".redlink").mouseout(function() {
	if($(this).html() == "Sure?") {
		$(this).html($(this).data("redlink_old"));
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
		answer = confirm("Are you sure you want to permenantly delete the selected "+numselected+" tags?")
	else
		answer = confirm("Are you sure you want to permenantly delete the selected tag?")
	
	if(answer) {	//Confirmation was given, submit the form
		$("#content_list_form").attr("action", "./delete.php");
		$("#content_list_form").submit();
	}
});

</script>

<?php printPagination("./?search=".$_GET['search'], $page, $numperpage, $db->countTableRows(TBL_TAGS, $filters)); ?>