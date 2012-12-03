<?php
$pageSection = $page->getTagNamesByType("section");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php $page->printPageTitle() ?></title>
	<link href="<?php theme_dir() ?>css/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/scripts/jquery.js"></script>
	<!--[if lt IE 9]><script src="<?php theme_dir() ?>js/html5shiv.js"></script><![endif]-->
</head>
<body>
<header>
	<div>
		<p class="nav2">
			<a href="">Archives</a>
			<a href="">Campus Bulletin</a>
			<a href="">Letters to the Editor</a>
			<a href="">Contact Us</a>
			<a href="">Advertise</a>
			<a href="">Board of Directors</a>
		</p>
		<a class="logo" href="index.php" title="Imprint &raquo; Home">
			<img src="<?php theme_dir() ?>img/logo.png" alt="Imprint &raquo; Home" />
		</a>
		<h1 class="section-title"><?php echo $pageSection[0] ?></h1>
		<p class="social-links">
			<a href="http://twitter.com/uw_imprint" title="#uwaterloo, #grtfail, and everything in between"><img src="<?php theme_dir() ?>img/twitter.png" /></a>&nbsp;
			<a href="http://www.youtube.com/user/ImprintUW" title="Do you like us?"><img src="<?php theme_dir() ?>img/facebook.png" /></a>&nbsp;
			<a href="http://www.youtube.com/user/ImprintUW" title="More than just cat videos"><img src="<?php theme_dir() ?>img/youtube.png" /></a>&nbsp;
			<a href="" title="Feed me"><img src="<?php theme_dir() ?>img/rss.png" /></a>
		</p>
		<div class="clear"></div>
	</div>
</header>
<nav>
	<div>
		<ul>
			<li <?php if($pageSection[0]=="News") {echo "class=\"current\"";} ?>> <a href="news.php">News</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="news.php">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="features") {echo "class=\"current\"";} ?>> <a href="features.php">Features</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="opinion") {echo "class=\"current\"";} ?>> <a href="opinion.php">Opinion</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="arts") {echo "class=\"current\"";} ?>> <a href="arts.php">Arts</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="science") {echo "class=\"current\"";} ?>> <a href="science.php">Science</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="sports") {echo "class=\"current\"";} ?>> <a href="sports.php">Sports</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="columns") {echo "class=\"current\"";} ?>> <a href="columns.php">Columns</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="distractions") {echo "class=\"current\"";} ?>> <a href="distractions.php">Distractions</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
			<li <?php if($pageSection[0]=="comics") {echo "class=\"current\"";} ?>> <a href="comics.php">Comics</a>
				<ul>
					<li><a href="">Arts symposium features local talent</a></li>
					<li><a href="">Fresh wave of goose attacks near V1</a></li>
					<li><a href="">BlackBerry hosts recruitment event on campus, nobody cares</a></li>
					<li><a href="">More...</a></li>
				</ul>
			</li>
		</ul>
		<div id="search">
			<input type="text" />
		</div>
		<div class="clear"></div>
	</div>
</nav>

<a class="top" href="#"><img src="img/top.png"></a>