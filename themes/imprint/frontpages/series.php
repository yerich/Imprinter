    <section id="content">
        <?php if($tag)  { ?>
        <div id="author-header" class="column">
            <h1><?php echo $tag->getName(); ?> <?php if($tag->row['data']['image']) echo "<img src='".theme_dir().$tag->row['data']['image']."' />" ?></h1>
                <?php if($tag->row['data']['tagline']) echo "<p class='author-header-tagline'>".$tag->row['data']['tagline']."</p>" ?>
            <div class="clear">&nbsp;</div>
            <div class="contact">
                <a href="/feeds/?tagid=<?php echo $tag->getId() ?>">RSS</a>
                <?php if($tag->row['data']['email']) { ?><a href="mailto:<?php echo $tag->row['data']['email'] ?>"><?php echo $tag->row['data']['email'] ?></a><?php } ?>
            </div>
            
            <?php if($tag->row['data']['description']) { ?><p class="author-header-description"><?php echo $tag->row['data']['description'] ?></p><?php } ?>
        </div>
        <?php } ?>
        
        <div style="clear: both"></div>
        
        <div class="seriesstories">
            <?php foreach($pages['main'] as $value) { ?>
            <section class="topstories">
                <h3><a href="/<?php echo $value->getId() ?>"><?php echo $value->getTitle() ?></a></h3>
                <p class="meta">
                    <?php 
                    $pseries = $value->getTagsByType("series"); 
                    if(count($pseries) > 0) {
                        $comma = "";
                        foreach($pseries as $tvalue) {
                            echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                            $comma = ", ";
                        }
                        echo " &middot; ";
                    } 
                    ?>
                    <?php echo date_smart($value->getCreatedTime()) ?>
                    <?php 
                    $psection = $value->getTagsByType("section"); 
                    if(count($psection) > 0) {
                        $comma = " &middot; ";
                        foreach($psection as $tvalue) {
                            echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                            $comma = ", ";
                        } 
                    } 
                    ?>
                    <?php 
                    $pauthor = $value->getTagsByType("author"); 
                    if(count($pauthor) > 0) {
                        $comma = " &middot; By&nbsp";
                        foreach($pauthor as $tvalue) {
                            echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                            $comma = ", ";
                        } 
                    } 
                    ?>
                </p>
                <?php $pmedia = $value->getMedia(false); if($pmedia[0]) echo "<a class='image' href='".$value->getURL()."'><img src='".$pmedia[0]->getResizedLocation(100, 200)."' /></a>"; ?>
                <p class="snippet">
                    <?php
                        if($pmedia[0]) { echo truncateText($value->getContent(), 120);} // Show 120 chars if there is an image. Should keep within 4 lines.
                        else { echo truncateText($value->getContent(), 180); } // Show 180 chars if no image. Should keep within 4 lines.
                    ?>
                    <a href="<?php echo $value->getURL() ?>" class="readmore">Read&nbsp;More&nbsp;&raquo;</a>
                </p>
            </section>
            <?php } ?>
            
            <div class='frontpage-pagination'><?php $page->printSectionPagination("main"); ?></div>
        </div> <!-- topstorieswrap -->
    </section> <!-- content -->