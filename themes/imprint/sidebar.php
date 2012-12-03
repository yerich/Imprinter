<div id ="sidebar">
    <h4><a href="http://twitter.com/uw_imprint"><img class="twitter" src="/themes/imprint/img/twitterbird.png" />Twitter</a></h4>
    <div id="tweetstream"></div>

	<br /><br /><script type="text/javascript"><!--
	google_ad_client = "ca-pub-5067918159247477";
	/* Imprint Ads */
	google_ad_slot = "4617749517";
	google_ad_width = 200;
	google_ad_height = 200;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script><br /><br />

    <!--<a class="ad-sidebar square" href="http://www.youtube.com/watch?v=oHg5SJYRHA0"> <img src="<?php theme_dir() ?>img/saul.png" /> </a>-->
	<?php
	$ads = array(
		//'<a class="ad-sidebar" href="http://www.hauntedhouse.ca/"> <img src="/images/Oct15HauntedHouse.jpg" /> </a>',
		//'<a class="ad-sidebar" href="http://www.allstarwingsandribs.com/"><img src="/uploads/2012/10/allstarwings.jpg" /></a>',
		'<a class="ad-sidebar" href="http://www.futon-fashions.ca/"> <img src="/uploads/2012/08/futonfashions.jpg" /> </a>'
	);
	
	for ($i=0; $i<2; $i++) {
	    $random = array_rand($ads);  # one random array element number
	    $get_it = $ads[$random];    # get the letter from the array
	    echo $get_it;
	
	    unset($ads[$random]);
	}

	?>
	<!--
    <div id="sidebarstories">
        <h4><a href="/section/news/">News</a></h4>
        <?php $pages = PageFactory::getSectionLatest($page->db, "news", 3, true);
    
        foreach($pages as $value2) {
            echo "<a href='".$value2->getURL()."'>";
            $pmedia = $value2->getMedia(); 
            if($pmedia[0]) 
                echo "<div class='img-wrapper'><img src='".$pmedia[0]->getResizedLocation(66, 66)."' /></div>";
            echo "<span>".$value2->getTitle()."</span></a>";
        }
        ?>
        <div class="clear">
            &nbsp;
        </div>
        <h4><a href="/section/features/">Features</a></h4>
        <?php $pages = PageFactory::getSectionLatest($page->db, "features", 3, true); 
    
        foreach($pages as $value2) {
            echo "<a href='".$value2->getURL()."'>";
            $pmedia = $value2->getMedia(); 
            if($pmedia[0]) 
                echo "<div class='img-wrapper'><img src='".$pmedia[0]->getResizedLocation(66, 66)."' /></div>";
            echo "<span>".$value2->getTitle()."</span></a>";
        }
        ?>
        <div class="clear">
            &nbsp;
        </div>
    </div>
	-->
    
	<!--
    <a class="ad-sidebar" href="http://www.youtube.com/watch?v=oHg5SJYRHA0"> <img src="<?php theme_dir() ?>img/saul2.png" /> </a>-->
	<br /><br /><script type="text/javascript"><!--
	google_ad_client = "ca-pub-5067918159247477";
	/* Imprint Ads */
	google_ad_slot = "4617749517";
	google_ad_width = 200;
	google_ad_height = 200;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>
<div class="clear">
    &nbsp;
</div>