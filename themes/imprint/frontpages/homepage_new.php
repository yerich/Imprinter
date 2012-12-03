        <div class="featured">
            <?php foreach($pages['featured'] as $value) { ?>
            <a class="featuredstory" href="<?php echo $value->getURL() ?>">
                <div class="image-dummy" <?php $pmedia = $value->getMedia(false); if($pmedia[0]) { echo 'style="background-image:url(\''.$pmedia[0]->getResizedLocation(704, 1980).'\')"';} ?>>
                    <img src="/<?php if($pmedia[0]) echo $pmedia[0]->getResizedLocation(704, 1980); ?>" style="display: none" />
                </div>
                <h3><?php echo $value->getTitle() ?></h3>
                <p> <?php echo truncateText($value->getContent(), 65) ?> <span>Read&nbsp;more&nbsp;&raquo;</span></p>
            </a>
            <?php } ?>
        </div>
        <div class="mobile_featured">
			<?php foreach($pages['featured'] as $value) { ?>
			<section class="topstories">
				<p class="meta">
					<?php 
					$psection = $value->getTagsByType("section"); 
					if(count($psection) > 0) {
						foreach($psection as $tvalue) {
							echo "<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
							$comma = ", ";
						} 
					} 
					?>
				</p>
				<h3><a href="<?php echo $value->getURL() ?>"><?php echo $value->getTitle(); ?></a></h3>
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
        <div style="clear: both;"></div>
        <div class="featured-nav">
            <a class="featured-buttons featured-active" href="#"></a>
            <?php
            for($i = 1; $i < count($pages['featured']); $i++) {
                echo '<a class="featured-buttons" href="#"></a>';
            }
            ?>
        </div>
    
        <div id="topstorieswrap">
            <?php
            $adspace = 0; // define the $adspace variable.
            foreach(array($pages['main1'], $pages['main2'], $pages['main3'], $pages['main4'], $pages['main5'], $pages['main6']) as $subsection) {
            if($adspace % 10 == 0){
            } // We want an ad after every two sections, so we will include an ad only if this number is odd.
            $adspace++; // increase the value of $adspace by 1. ?>
            <section class="topstories">
                <?php if($subsection[0]) { ?>
                <p class="meta">
                    <?php 
                    $psection = $subsection[0]->getTagsByType("section"); 
                    if(count($psection) > 0) {
                        foreach($psection as $tvalue) {
                            echo "<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                            $comma = ", ";
                        } 
                    } 
                    ?>
                </p>
                <h3><a href="<?php echo $subsection[0]->getURL() ?>"><?php echo $subsection[0]->getTitle(); ?></a></h3>
                <?php $pmedia = $subsection[0]->getMedia(false); if($pmedia[0]) echo "<a class='image' href='".$subsection[0]->getURL()."'><img src='".$pmedia[0]->getResizedLocation(100, 200)."' /></a>"; ?>
                <p class="snippet">
                    <?php
                        if($pmedia[0]) { echo truncateText($subsection[0]->getContent(), 120);} // Show 120 chars if there is an image. Should keep within 4 lines.
                        else { echo truncateText($subsection[0]->getContent(), 180); } // Show 180 chars if no image. Should keep within 4 lines.
                    ?>
                    <a href="<?php echo $subsection[0]->getURL() ?>" class="readmore">Read&nbsp;More&nbsp;&raquo;</a>
                </p>
                <?php } 
                $i = 0; foreach($subsection as $value) { $i++; if($i == 1) continue; ?>
                <p class="snippet"><a href="<?php echo $value->getURL() ?>"><?php echo $value->getTitle(); ?></a></p>
                <?php } if(count($psection) > 0) {?>
                <p class="snippet snippet_more"><a href="<?php echo $psection[0]->getURL() ?>">More <?php echo ucfirst($psection[0]->getName()); ?> &raquo;</a></p><?php } ?>
            </section>
            <?php } ?>
