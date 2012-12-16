<?php
/**
 * config.php
 *
 * Contains links to scripts which edit configuration files.
 *
 * Written By: Richard Ye
 */
$_PAGETITLE="Site Configuration";

include("../include/init.php");
checkUserLogin($user);

$printed_fields = array();

function echoConfigField($key, $nicename = '') {
	global $printed_fields, $_CONFIG;
	
	$key = strtoupper($key);
	
	if(!defined($key))
		return false;
	
	$printed_fields[] = $key;
	
	if(!$_CONFIG[$key]['type'])
		$_CONFIG[$key]['type'] = "varchar";
	$value = constant($key);	
	$type = $_CONFIG[$key]['type'];
	$description = $_CONFIG[$key]['description'];
	
	if(!$nicename)
		$nicename = $key;
	
	
	if($type == "bool") {
		if($value)
			echo "<tr><td class=\"alignright\" style='width: 200px;'><input type=\"checkbox\" name=\"constant[$key]\" value=\"1\" checked=\"$checked\" />";
		else
			echo "<tr><td class=\"alignright\" style='width: 200px;'><input type=\"checkbox\" name=\"constant[$key]\" value=\"1\" />";
	}
	else
		echo "<tr><td style='width: 200px;'><label for=\"constant[$key]\">$nicename</label><span class=\"info technical_name\">$key</span>";
	
	echo "</td><td>";
	if($type == "text") {
		echo "<textarea name=\"constant[$key]\" rows=\"5\" cols=\"30\" class=\"width-95percent\">$value</textarea>";
	}
	elseif($type == "html") {
		echo "<textarea name=\"constant[$key]\" rows=\"10\" cols=\"40\" style=\"width:100%\" class=\"width-95percent mceAdv\">$value</textarea>";
	}
	elseif($type == "bool") {
		echo "<label for=\"constant[$key]\">$nicename</label><span class=\"info technical_name\">$key<br /></span>";
		if($description)
			echo "<span class=\"info\">$description</span>";
	}
	else {
		echo "<input type=\"text\" name=\"constant[$key]\" value=\"$value\" class=\"width-95percent\" size=\"40\" />";
	}
	if($description && $type != "bool")
		echo "<br /><span class=\"info\">$description</span>";
	
	echo "<input type=\"hidden\" name=\"description[$key]\" value=\"$description\" />";
	echo "<input type=\"hidden\" name=\"type[$key]\" value=\"$type\" />";
	echo "</td></tr>\n";
	
	return true;
}

if($_POST['constant']) {
	foreach($_CONFIG as $key=>$v) {
		if($_CONFIG[$key]['value'] != $_POST['constant'][$key] && ($_CONFIG[$key]['value'] || $_POST['constant'][$key])) {
			$value = $_POST['constant'][$key];
			if($_CONFIG[$key]['type'] == "bool" && $_POST['constant'][$key] === "1")
				$value = 1;
			elseif($_CONFIG[$key]['type'] == "bool")
				$value = 0;
			
			if($_CONFIG[$key]['type'] == "int")
				$value = intval($value);
			
			$query = "UPDATE ".TBL_CONFIG." SET `value` = '$value' WHERE `key` = '$key' LIMIT 1";
			$db->query($query);
		}
	}
	
	header("Location: ?saved=1");
	die();
}

include("template/header.php");

if($_GET['saved'])
	$sucessful[] = "Configuration saved sucessfully.";

?>
<style type="text/css">
.technical_name {display: none;}
label { display: block;}
td { padding-bottom: 10px;}
</style>
<p>Use this page to change the configuration of the website. <strong>Warning:</strong> incorrect configuration may break this website, including this administration interface.
	Always have a way to revert your changes (i.e. phpMyAdmin database access) in case something goes wrong.</p>
<a href="javascript: void(0)" onclick="$('.technical_name').slideDown(200)">Show Technical Names</a></p>

<form action="editconfig.php" method="post" style="font-size: 10pt;">
	<fieldset>
		<legend>Edit Variables</legend>
		
		<table style="width: 100%;">
			<?php 
			echoConfigField("SITE_NAME", "Site Name");
			echoConfigField("SITE_URL", "Site URL");
			echoConfigField("SHORT_URL", "Shortened URL");
			echoConfigField("ADMIN_EMAIL", "Website Administrator Email");
			?>
		</table>
		
		<table style="width: 100%;">
			<?php 
			echoConfigField("FRONTPAGE_DEFAULT", "Default Frontpage");
			echoConfigField("FRONTPAGE_SECTION", "Default Section Frontpage ID");
			echoConfigField("FRONTPAGE_AUTHOR", "Default Author Frontpage ID");
			echoConfigField("FRONTPAGE_SERIES", "Default Series Frontpage ID");
			
			foreach($_CONFIG as $key => $value) {
				if(!in_array($value['key'], $printed_fields)) {
					echoConfigField($value['key']);
				}
			}
			
			?>
		</table>
		
	</fieldset>

	<fieldset class="i">
		<input type="submit" value="Submit" />
	</fieldset>
</form>

<?php require("template/footer.php"); ?>