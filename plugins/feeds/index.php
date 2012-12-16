<?php
$tagtype = $_GET['tagtype'];
$tagid = $_GET['tagid'];
$type = $_GET['type'];

if($tagid) {
	$pages = array();
	$pages = PageFactory::loadFrontpagePages($db, 
		"default", array("main" => array(array("type" => "tag", "value" => array($tagid)), array("type" => "view", "value" => array("article"=>1)))), array("main" => array("num" => 20)));
	$tag = TagFactory::getTagById($db, $tagid);
	if(!$tag)
		Error::error404();
	
	$title = $tag->getName();
	$tagtype = $tag->getType();
	$url = SITE_URL.$tagtype."/".$title;
}
elseif($type=="all") {
	$pages = array();
	$pages = PageFactory::loadFrontpagePages($db, 
		"default", array("main" => array(array("type" => "view", "value" => array("article"=>1)))), array("main" => array("num" => 20)));
	
	$title = "Most Recent";
	$url = SITE_URL;
}

if($pages) {
	$time = 0;
	foreach($pages['main'] as $value) {
		$time = max($value->getCreatedTime(), $time);
	}
	
	header("Content-Type: application/rss+xml");
	echo '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
        <title><?php echo SITE_NAME ?> RSS: <?php echo $title ?></title>
        <description><?php echo SITE_NAME ?> - <?php echo $title ?></description>
        <link><?php echo $url ?></link>
        <lastBuildDate><?php echo date(DATE_RSS) ?></lastBuildDate>
        <pubDate><?php echo date(DATE_RSS, $time) ?></pubDate>
        <ttl>1800</ttl>
 <?php foreach($pages['main'] as $value) { ?>
        <item>
                <title><?php echo $value->getTitle() ?></title>
                <description><![CDATA[<?php echo truncateText($value->getContent(), 500) ?>]]></description>
                <dc:creator><?php 
                        $pauthor = $value->getTagsByType("author"); 
                        if(count($pauthor) > 0) {
                            $comma = "";
                            foreach($pauthor as $tvalue) {
                                echo "$comma".htmlspecialchars(utf8_decode($tvalue->getName()));
                                $comma = ", ";
                            } 
                        } 
                        ?></dc:creator>
                <link><?php echo SITE_URL ?><?php echo substr($value->getURL(), 1) ?></link>
                <guid isPermalink="true"><?php echo SITE_URL ?><?php echo substr($value->getURL(), 1) ?></guid>
                <pubDate><?php echo date(DATE_RSS, $value->getCreated()) ?></pubDate>
        </item>
 <?php } ?>
</channel>
</rss>
<?php
}
