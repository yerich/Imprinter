<?php 
$_PAGETITLE = "Manage Users";
include("../include/init.php");
include("../include/admin.php");
checkUserLogin($user, 5);

if($_POST['users']) {
	foreach($_POST['users'] as $key => $value)
		$deleteusers[] = $key;
	if(Admin::massDeleteUsers($deleteusers, $db)) {
		if(count($_POST['users'] == 1))
			Error::userMessage ("1 user has been deleted sucessfully.");
		else
			Error::userMessage (count($_POST['users'])." users have been deleted sucessfully.");
	}
}

include("template/header.php");
?>

<script type="text/javascript">
<!--
function editUser(id) {
	$("#useredit_"+id).html("<a href=\"javascript:void(0)\" onclick=\"saveUser("+id+")\">"
		+"<img src=\"/images/icons/disk-black.png\" alt=\"Save\" title=\"Save\"/></a>");
	currlevel = $("#userlevel_"+id).html();
	levelhtml = "<select id=\"userlevelselect_"+id+"\">";
	for(i = 1; i <= 5; i = i+1) {
		if(i == currlevel)
			levelhtml += "<option value=\""+i+"\" selected=\"selected\">"+i+"</option>";
		else
			levelhtml += "<option value=\""+i+"\">"+i+"</option>";
	}
	levelhtml += "</select>";
	$("#userlevel_"+id).html(levelhtml);
	emailhtml = "<input type=\"text\" id=\"useremailinput_"+id+"\" value=\""+$("#useremail_"+id).html()+"\"/>";
	$("#useremail_"+id).html(emailhtml);
}

function saveUser(id) {
	newlevel = $("#userlevelselect_"+id).val();
	newemail = $("#useremailinput_"+id).val();
	$("#userlevel_"+id).html(newlevel);
	$("#useremail_"+id).html(newemail);
	$("#useredit_"+id).html("<a href=\"javascript:void(0)\" onclick=\"editUser("+id+")\">"
		+"<img src=\"/images/icons/pencil.png\" alt=\"Edit\" title=\"Edit\"/></a>");
	
	username = $("#username_"+id).html();
	$.get("ajax.php", {action : "edituser", username : username, userlevel : newlevel, email : newemail}, function(response) {
		if(response !== "1") {
			alert("Error:\n"+response);
		}
	});
}
//-->
</script>

<form action="users.php" method="post" id="usersform">
<div class="list_table_toolbar">
	<a href="./newuser.php" class="bluebutton">Create a New User</a>
	<a href="javascript: void(0)" class="list_table_toolbar_delete greybutton">Delete Checked Users</a>
</div>

<table class="list_table">
	<tr class="list_table_header">
		<th style="width: 20px;"></th>
		<th>Username</th>
		<th style="width: 70px">Level</th>
		<th>Email</th>
		<th style="width: 20px;"><img src="/images/icons/pencil.png" alt="Edit" title="Edit"/></th>
	</tr>

<?php
//Get all users from the database
$query = "SELECT * FROM ".TBL_USERS." ORDER BY userlevel DESC, username";
$result = $db->query($query);
$resultrows = mysql_num_rows($result);

for($i = 0; $i <$resultrows; $i++) {	//Loop through each returned row
	$id = mysql_result($result, $i, "id");
	$username = mysql_result($result, $i, "username");
	$userlevel = mysql_result($result, $i, "userlevel");
	$email = mysql_result($result, $i, "email");
	
	if($username != $user->username) {
		//only have checkbox and edit link for users who are not the current user.
		$checkbox = "<input type=\"checkbox\" name=\"users[".$username."]\" class=\"form_users_checkbox\" />";
		$editlink = "<a href=\"javascript:void(0)\" onclick=\"editUser($id)\"><img src=\"/images/icons/pencil.png\" alt=\"Edit\" title=\"Edit\"/></a>";
	}
	else {
		$checkbox = "";
		$editlink = "<a href=\"edituser.php\"/><img src=\"/images/icons/pencil.png\" alt=\"Edit\" title=\"Edit\"/></a>";
	}
	//Print the row
	echo "<tr id=\"user_$id\"><td>$checkbox</td><td id=\"username_$id\">$username</td><td id=\"userlevel_$id\">$userlevel</td><td id=\"useremail_$id\">$email</td>"
		."<td id=\"useredit_$id\">$editlink</td></tr>\n";
}
?>
</table>
<div class="list_table_toolbar">
	<a href="./newuser.php" class="bluebutton">Create a New User</a>
	<a href="javascript: void(0)" class="list_table_toolbar_delete greybutton">Delete Checked Users</a>
</div>
</form>

<script type="text/javascript">
$(".list_table_toolbar_delete").click(function() {
	numselected = $(".form_users_checkbox:checked").length;
	if(numselected == 0) {
		alert("No users were selected.");
		return false;
	}
	answer = false;
	if(numselected > 1)
		answer = confirm("Are you sure you want to permenantly delete the selected "+numselected+" users?")
	else
		answer = confirm("Are you sure you want to permenantly delete the selected user?")
	
	if(answer)
		$("#usersform").submit();
});
</script>

<?php 
include("template/footer.php");