<?php
include("functions.php");
include("ads.php");
$pageSection = $page->getTagNamesByType("section");
if(in_array($page->getId(), array(FRONTPAGE_SECTION, FRONTPAGE_AUTHOR, FRONTPAGE_TAG, FRONTPAGE_SERIES))) {
	$page->processFrontpageTag();
	$pageSection[0] = $page->tag->getName();
}
?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/" itemscope itemtype="http://schema.org/Article" lang="en">
<head>
	<title><?php $page->printPageTitle() ?> - Imprint</title>
	<link rel="shortcut icon" href="<?php theme_dir() ?>img/favicon.ico" />
	<link media="screen and (min-width:960px)" href="<?php theme_dir() ?>css/style.css" rel="stylesheet" type="text/css" />
	<link media="screen and (max-width:959px)" href="<?php theme_dir() ?>css/mobile.css" rel="stylesheet" type="text/css" />
	<meta property="fb:admins" content="603076325" />
	<link rel="bookmark" href="<?php echo $page->getPermalink() ?>" />
	<script type="text/javascript" src="/scripts/jquery.js"></script>
	<!--[if lt IE 9]>
		<script src="<?php theme_dir() ?>js/html5shiv.js"></script>
		<link media="screen" href="<?php theme_dir() ?>css/style.css" rel="stylesheet" type="text/css" />
	<![endif]-->

<?php
	$pmedia = $page->getMedia(); 
	if (count($pmedia) > 0) {
?>
	<!-- Open Graph (Facebook) -->
	<meta property="og:title" content="<?php $page->printPageTitle() ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:image" content="http://imprint.yerich.net<?php echo $pmedia[0]->getResizedLocation(704, 469) ?>" />
	<meta property="og:site_name" content="Imprint" />

	<!-- Schema (Google+) -->
	<meta itemprop="name" content="<?php $page->printPageTitle() ?>">
	<meta itemprop="image" content="http://imprint.yerich.net<?php echo $pmedia[0]->getResizedLocation(704, 469) ?>">
<?php } ?>

	<script type="text/javascript">//<![CDATA[
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount','UA-27238097-1']);
	_gaq.push(['_setAllowLinker',true],['_setDomainName','.theimprint.ca'],['_trackPageview'],['_trackPageLoadTime']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	//]]></script>

</head>
<body<?php if($pageSection[0]) { ?> id="page-<?php echo preg_replace("%[^ a-z]%", "", strtolower($pageSection[0]))."\""; }?>>

<!--[if lte IE 7]> <center><div style=' clear: both; height: 59px; padding:0 0 0 15px; position: relative;'> <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div></center><![endif]-->

<header>
	<div>
		<ul class="nav2">
			<li><a href="/1905">About</a></li>
			<li><a target="_blank" href="http://issuu.com/uw_imprint">Archives</a></li>
			<li><a href="/76">Advertise</a></li>
			<li><a href="/section/campus+bulletin/">Campus Bulletin</a></li>
			<li><a href="/176">Contact Us &#x25BE;</a>
				<ul>
					<li><a href="/77">Letters to the Editor</a></li>
					<li><a href="/volunteer">Volunteer</a></li>
					<li><a target="_blank" href="http://imprintpub.org">Board of Directors</a></li>
				</ul>
			</li>
		</ul>
		<a class="logo" href="/" title="Imprint &raquo; Home">
			<img src="<?php theme_dir() ?>img/logo.png" alt="Imprint &raquo; Home" />
		</a>
		<p class="social-links">
			<a href="http://twitter.com/uw_imprint" title="#uwaterloo, #grtfail, and everything in between"><img src="<?php theme_dir() ?>img/twitter.png" alt="Twitter" /></a>&nbsp;
			<a href="http://www.youtube.com/user/ImprintUW" title="Do you like us?"><img src="<?php theme_dir() ?>img/facebook.png" alt="Facebook" /></a>&nbsp;
			<a href="http://www.youtube.com/user/ImprintUW" title="More than just cat videos"><img src="<?php theme_dir() ?>img/youtube.png" alt="YouTude" /></a>&nbsp;
			<a href="/feeds/all" title="Imprint RSS: Most Recent"><img src="<?php theme_dir() ?>img/rss.png" alt="RSS" /></a>
		</p>
		<div class="clear"></div>
	</div>
</header>
<nav>
	<div>

		<a class="nav-button">Menu</a>

		<ul>
			
			<?php $pageid = strtolower($pageid); ?>

			<li <?php if($pageSection[0]=="News") {echo "class=\"current\"";} ?>> <a href="/section/news/">News<span></span></a>
				<?php if($pageSection[0]=="News") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
			    <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "news", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/news/">More News &raquo;</a></li>
                </ul>
			</li>
			<li <?php if($pageSection[0]=="Features") {echo "class=\"current\"";} ?>> <a href="/section/features/">Features<span></span></a>
				<?php if($pageSection[0]=="Features") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
                <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "features", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/features/">More Features &raquo;</a></li>
                </ul>
			</li>
			<li <?php if($pageSection[0]=="Arts") {echo "class=\"current\"";} ?>> <a href="/section/arts/">Arts<span></span></a>
				<?php if($pageSection[0]=="Arts") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
                <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "arts", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/arts/">More Arts &raquo;</a></li>
                </ul>
			</li>
			<li <?php if($pageSection[0]=="Science") {echo "class=\"current\"";} ?>> <a href="/section/science/">Science<span></span></a>
				<?php if($pageSection[0]=="Science") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
                <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "science", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/science/">More Science &raquo;</a></li>
                </ul>
			</li>
			<li <?php if($pageSection[0]=="Opinion") {echo "class=\"current\"";} ?>> <a href="/section/opinion/">Opinion<span></span></a>
				<?php if($pageSection[0]=="Opinion") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
                <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "opinion", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/opinion/">More Opinion &raquo;</a></li>
                </ul>
			</li>
			<li <?php if($pageSection[0]=="Sports") {echo "class=\"current\"";} ?>> <a href="/section/sports/">Sports<span></span></a>
				<?php if($pageSection[0]=="Sports") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
                <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "sports", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/sports/">More Sports &raquo;</a></li>
                </ul>
			</li>
			<li <?php if($pageSection[0]=="Distractions") {echo "class=\"current\"";} ?>> <a href="/section/distractions/">Distractions<span></span></a>
				<?php if($pageSection[0]=="Distractions") {echo "<img class=\"arrow\" src=\"/themes/imprint/img/nav-arrow.png\" />";} ?>
                <ul>
                    <?php $pages = PageFactory::getSectionLatest($page->db, "distractions", 6); 
                    foreach($pages as $value2) {
                        echo "<li><a href='".$value2->getURL()."'>".$value2->getTitle()."</a>";
                    }
                    ?>
                    <li class="more"><a href="/section/distractions/">More Distractions &raquo;</a></li>
                </ul>
			</li>

		<script type="text/javascript">
			var w = $('.current > a').width()+13;
			$('.arrow').css('width', w);
		</script>

		</ul>
		<div id="search">
			<form action="/search" method="get">
				<input type="text" name="q" size="20"/>
			</form>
		</div>
		<div class="clear"></div>
	</div>
</nav>

<div class="debug"></div>

<a class="top" href="#"><img src="<?php theme_dir() ?>img/top.png"></a>

<div id="wrap">
    <div class="ad-banner">
        <?php top_ad();  ?>
    </div>