<?
$_HEADER_PRINTED = true;	//Header has been printed -- this is needed later on to trigger printing the footer

$adminMenu = array(
	array("text" => "Dashboard", "url" => "/admin/", "default" => true),
	array("text" => "Content", "url" => "/admin/views/content/"),
	array("text" => "Articles", "url" => "/admin/views/article/"),
	array("text" => "Frontpages", "url" => "/admin/views/frontpage/"),
	array("text" => "Tags", "url" => "/admin/views/tag/"),
	array("text" => "Configuration", "url" => "/admin/editconfig.php"),
	array("text" => "Ads", "url" => "/admin/plugins/ads"),
	array("text" => "Users", "url" => "/admin/users.php"),
	)
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<meta name="generator" content="Imprinter v0.1-alpha" />
	
	<title><?php echo $_PAGETITLE ?> - Imprint</title>
	
	<link rel="stylesheet" type="text/css" href="/admin/template/style.css" />
	<script type="text/javascript" src="/scripts/jquery.js"></script>
	<?php
	if(isset($_HEADCONTENT) && is_array($_HEADCONTENT)) {
		echo implode("\n\t", $_HEADCONTENT);
	}
	else if(isset($_HEADCONTENT)) {
		echo $_HEADCONTENT;
	}
	?>
</head>

<body>
<div id="wrapper">
	<div id="header">
		<div id="headerright">Hello, <strong><?php echo $user->username; ?></strong><br /><a href="/logout.php" id="header_logout">Logout &raquo;</a></div>
	
		<div id="title">Administration: <?php echo SHORT_URL ?> <a id="previewlink" class="bluebutton" href="/">View site &raquo;</a></div>
		<div id="nav">
			<?php printMenu($adminMenu) ?>
		</div>
	</div>
	
	<div id="main">
		<h1 class="pagetitle"><?php echo $_PAGETITLE ?></h1>
		<noscript>
			<div class="error messagebox">You need to have JavaScript enabled in your browser to use this website.</div>
		</noscript>
		<?php Error::printMessages(); ?>