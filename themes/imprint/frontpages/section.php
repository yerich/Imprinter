        <div class="section-featured">
            <?php // Only show the first featured article
                $i = 0;
                foreach($pages['filmstrip'] as $value) {
                    if ($i==0) {
            ?>


                <a class="featuredstory" href="<?php echo $value->getURL() ?>">
                    <div class="image-dummy" <?php $pmedia = $value->getMedia(false); if($pmedia[0]) { echo 'style="background-image:url(\''.$pmedia[0]->getResizedLocation(704, 1980).'\')"';} ?>>
                        <img src="<?php if($pmedia[0]) echo $pmedia[0]->getResizedLocation(704, 1980); ?>" style="display: none" />
                    </div>
                    <h3><?php echo $value->getTitle() ?></h3>
                </a>
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
                <p class="snippet"><?php echo truncateText($value->getContent(), 100) ?> <a href="<?php echo $value->getURL() ?>" class="readmore">Read&nbsp;More&nbsp;&raquo;</a></p>
            <?php
                    }
                    $i++;
                }
            ?>
        </div>
        <div class="mobile_featured">
			<?php foreach($pages['filmstrip'] as $value) { ?>
			<section class="topstories">
			    <h3><a href="<?php echo $value->getURL() ?>"><?php echo $value->getTitle() ?></a></h3>
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
        </div>

    <!-- NOT USING THE FILMSTRIP
		<ul class="filmstrip">
			<?php foreach($pages['filmstrip'] as $value) { ?>
			<li>
				<div>
					<div class="imagewrap">
						<img src="<?php $pmedia = $value->getMedia(); if($pmedia[0]) echo $pmedia[0]->getResizedLocation(300, 1200); ?>" />
						<a href="/<?php echo $value->getId() ?>"> 
							<h3><?php echo $value->getTitle() ?></h3>
						</a>
					</div>
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
                        <?php echo date("H:i M j, Y", $value->getCreatedTime()) ?>
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
					<p><?php echo substr(strip_tags($value->getContent()), 0, 100) ?>... <a href="/<?php echo $value->getId() ?>" class="readmore">Read&nbsp;More&nbsp;&raquo;</a></p>
				</div>
			</li>
			<?php } ?>
		</ul>
    -->
        
        <div style="clear: both"></div>
	
		<div id="topstorieswrap">
			<?php foreach($pages['main'] as $value) { ?>
			<section class="topstories">
			    <h3><a href="<?php echo $value->getURL() ?>"><?php echo $value->getTitle() ?></a></h3>
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
		</div>
		<div style="clear: both"></div>
			
		<div class='frontpage-pagination'><?php $page->printSectionPagination("main"); ?></div>