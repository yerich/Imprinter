		<div class="featured">
			<?php foreach($pages['featured'] as $value) { ?>
			<a class="featuredstory" href="<?php echo $value->getURL() ?>">
			    <div class="image-dummy" <?php $pmedia = $value->getMedia(false); if($pmedia[0]) { echo 'style="background-image:url('.$pmedia[0]->getResizedLocation(704, 1980).')"';} ?>>
			        <img src="/<?php if($pmedia[0]) echo $pmedia[0]->getResizedLocation(704, 1980); ?>" style="display: none" />
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
		
        <!--<div class="ad-banner">
            <a href="http://www.youtube.com/watch?v=oHg5SJYRHA0">
                <img src="http://theimprint.ca/wp-content/uploads/2012/03/SaH-Waterloo_Banner_final.jpg" />
            </a>
        </div>-->
	
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
				<h3><a href="<?php echo $value->getURL() ?>"><?php echo truncateText($value->getTitle(), 60); // limit titles to 60 chars (should be 2 lines) ?></a></h3>
				<?php $pmedia = $value->getMedia(false); if($pmedia[0]) echo "<a class='image' href='".$value->getURL()."'><img src='".$pmedia[0]->getResizedLocation(100, 200)."' />"; ?></a>
				<p class="snippet">
				    <?php
				    	if($pmedia[0]) { echo truncateText($value->getContent(), 119);} // Show 125 chars if there is an image. Should keep within 4 lines.
				    	else { echo truncateText($value->getContent(), 180); } // Show 180 chars if no image. Should keep within 4 lines.
				    ?>
				    <a href="<?php echo $value->getURL() ?>" class="readmore">Read&nbsp;More&nbsp;&raquo;</a></p>
			</section>
			<?php } ?>
	
<script type="text/javascript">
	$('.topstories').each(function(){
		if($(this).height() == 131) { // if the title is only one line (i.e. less than 26px high)... 
			$(this).parent().css('margin-bottom', 32); // ... then change the bottom margin to 32 px. ** NOT WORKING FOR SOME REASON **
		}
	});
</script>