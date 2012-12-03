		<div class="featured">
			<?php foreach($pages['featured'] as $value) { ?>
			<a class="featuredstory" href="<?php echo $value->getURL() ?>">
			    <div class="image-dummy" <?php $pmedia = $value->getMedia(false); if($pmedia[0]) { echo 'style="background-image:url(\''.$pmedia[0]->getResizedLocation(704, 1980).'\')"';} ?>>
			        <img src="<?php if($pmedia[0]) echo $pmedia[0]->getResizedLocation(704, 1980); ?>" style="display: none" />
			    </div>
				<h3><?php echo $value->getTitle() ?></h3>
				<p> <?php echo truncateText($value->getContent(), 65) ?> <span>Read&nbsp;more&nbsp;&raquo;</span></p>
			</a>
			<?php } ?>
		</div>
		<div class="featured-nav">
			<a class="featured-buttons featured-active" href="#"></a>
			<?php
			for($i = 1; $i < count($pages['featured']); $i++) {
				echo '<a class="featured-buttons" href="#"></a>';
			}
			?>
		</div>
	
		<div id="topstorieswrap">
			<?php foreach($pages['main'] as $value) { ?>
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