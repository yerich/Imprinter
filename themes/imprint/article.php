<?php
if($_SERVER['REQUEST_URI'] != $page->getURL()) {
	header("Location: ".$page->getURL());
	die();
}
?>
	<section id="content">
        
        <article>
            <span class="article_headline"><h1><?php $page->printPageTitle() ?></h1></span>
            
            <p class="meta">
                <?php 
                $pseries = $page->getTagsByType("series"); 
                if(count($pseries) > 0) {
                    $comma = "";
                    foreach($pseries as $tvalue) {
                        echo "$comma<a href='".$tvalue->getURL()."' class='column'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                        $comma = ", ";
                    }
                } 
                ?>
                <?php echo date_smart($page->getCreatedTime()); header("Date: ".date(DATE_RFC1123, $page->getCreatedTime())) ?>
                <?php 
                $psection = $page->getTagsByType("section"); 
                if(count($psection) > 0) {
                    $comma = " &middot; ";
                    foreach($psection as $tvalue) {
                        echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                        $comma = ", ";
                    } 
                } 
                ?>
                <?php 
                $pauthor = $page->getTagsByType("author"); 
                if(count($pauthor) > 0) {
                    $comma = " &middot; By&nbsp";
                    foreach($pauthor as $tvalue) {
                        echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                        $comma = ", ";
                    } 
                } 
                ?>
            </p>

            <?php 
            $pmedia = $page->getMedia(); if(count($pmedia) > 1){ ?>
            
            <div class="article-gallery">
                <div class="galleryphoto"></div>
        
                <a class="prev"<?php if(count($pmedia) == 1) echo ' style="display: none"' ?>><img src="<?php theme_dir() ?>img/gallery-prev.png" /></a>
                <div class="gallerylistwrap"<?php if(count($pmedia) == 1) echo ' style="display: none"' ?>>
                    <ul class="gallerylist">
                        <?php 
                        foreach($pmedia as $value) {
                        ?>
                        <li>
                            <img src="<?php echo $value->getResizedLocation(704, 469) ?>" />
                            <?php $caption = $value->getCaption(); if($caption) echo "<p>$caption</p>" ?>
                            
                                <?php 
                                $mauthor = $value->getAuthor();
                                if(count($mauthor) > 0) {
                                    $comma = "<span>";
                                    foreach($mauthor as $tvalue) {
                                        echo $comma.str_replace(" ", "&nbsp;", $tvalue->getName());
                                        $comma = ", ";
                                    } 
                                    echo "</span>";
                                } 
                                ?>
                        </li>
<?php
                        } ?>
                    </ul>
                </div><!-- gallerylistwrap -->
            </div>
            <a class="next"><img src="<?php theme_dir() ?>img/gallery-next.png" /></a>
            <?php } elseif (count($pmedia) == 1 && strlen($page->getContent()) < 200) {
                $value = $pmedia[0];
?>
            <div class="article-side" style="width: 100%;">
                <div class="article-image">
                    <div class="article-image-main">
                        <a href="<?php echo $value->getLocation() ?>"><img src="<?php echo $value->getResizedLocation(692, 1000) ?>" /></a>
                        <div class="article-image-credit">
                            <?php 
                            $mauthor = $value->getAuthor();
                            if(count($mauthor) > 0) {
                                $comma = "<span>";
                                foreach($mauthor as $tvalue) {
                                    echo $comma.str_replace(" ", "&nbsp;", $tvalue->getName());
                                    $comma = ", ";
                                } 
                                echo "</span>";
                            } 
                            ?>
                        </div>
                    </div>
                    <?php $caption = $value->getCaption(); if($caption) echo "<span class=\"article-image-caption\">$caption</span>" ?>
                </div>
            </div>
            <?php } elseif (count($pmedia) == 1) {
                $value = $pmedia[0];
?>
            <div class="article-side">
                <div class="article-image">
                    <div class="article-image-main">
                        <a href="<?php echo $value->getLocation() ?>"><img src="<?php echo $value->getResizedLocation(324, 1000) ?>" /></a>
                        <div class="article-image-credit">
                            <?php 
                            $mauthor = $value->getAuthor();
						    $comma = "";
                            if(count($mauthor) > 0) {
                                foreach($mauthor as $tvalue) {
                                    echo $comma.$tvalue->getName();
                                    $comma = ", ";
                                } 
                            } 
                            ?>
                        </div>
                    </div>
                    <?php $caption = $value->getCaption(); if($caption) echo "<span class=\"article-image-caption\">$caption</span>" ?>
                </div>
            </div>
<?php
            }
            ?>
            
            <div class="body">
                <?php $subtitle = $page->getSubtitle(); if($subtitle) echo "<h2>$subtitle</h2>"?>
                
                <?php $page->printPageContent() ?>
            </div>
            <div class="social-share">
                <h4>Share this article</h4>
                <ul>
                    <li>
                        <a href="https://twitter.com/share" class="twitter-share-button" data-via="uw_imprint" data-related="uw_imprint">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </li>
                    <li>
                        <div id="fb-root"></div><script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=155670271141412";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script><div class="fb-like" data-send="false" data-layout="button_count" data-width="50" data-show-faces="false"></div>
                    </li>
                    <li>
                        <div class="g-plusone" data-size="medium"></div><script type="text/javascript">window.___gcfg = {lang: 'en-GB'};(function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;po.src = 'https://apis.google.com/js/plusone.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();</script>
                    </li>
                    <li>
                        <script type="text/javascript" src="http://www.reddit.com/static/button/button1.js"></script>
                    </li>
                    <li>
                        <a href="http://www.tumblr.com/share" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:129px; height:20px; background:url('http://platform.tumblr.com/v1/share_3.png') top left no-repeat transparent;">Share on Tumblr</a>
                    </li>
                    <li>
                        <?php
                            $share_email_subject = "Read this article on TheImprint.ca";
                            $share_email_body = $page->getPageTitle().": http://theimprint.ca".$page->getURL();
                        ?>
                        <a href="mailto:recipients@email.com?subject=<?php echo $share_email_subject ?>&amp;body=<?php echo $share_email_body ?>" title="Share via email"><img src="/themes/imprint/img/share-email.png" /></a>
                    </li>
                </ul>
            </div>
            <div class="comments">
                <h4>Discuss this article</h4>
                <p>Feel like writing a Letter to the Editor? We accept submissions of up to 250 words. <a href="/77" title="Submit a letter to the editor">Find out more &raquo;</a></p>
                <p style="font-size: 10px;">Any comments made on the Imprint website do not reflect the views and/or opinions of Imprint Publications. Imprint Publications reserves the 
                	right to moderate any comments which violate our policies. For more information, please consult 
                	<a href="http://imprintpub.org/media-tags/policies-procedures/">Policy 15</a>,
                	or <a href="http://theimprint.ca/176">contact us</a>.</p>
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                    var disqus_shortname = 'uwimprint'; // required: replace example with your forum shortname

                    /* * * DON'T EDIT BELOW THIS LINE * * */
                    (function() {
                        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
            </div>
            <div class="clear">&nbsp;</div>

        </article>

    </section><!-- content -->
    
<?php
if(DEBUG_MODE) {
    echo "<!--".print_r($page, true)."-->";
}
?>