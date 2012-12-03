		<div class="featured">
			<?php foreach($pages['featured'] as $value) { ?>
			<a class="featuredstory" href="/<?php echo $value->getId() ?>">
				<img src="/<?php $pmedia = $value->getMedia(); if($pmedia[0]) echo $pmedia[0]->getLocation(); ?>" />
				<h3><?php echo $value->getTitle() ?></h3>
				<p><?php echo substr(trim(strip_tags($value->getContent())), 0, 65) ?>... <span>Read&nbsp;more&nbsp;&raquo;</span></p>
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
				<span class="topstories_headline">
					<h3><a href="/<?php echo $value->getId() ?>"><?php echo $value->getTitle() ?></a></h3>
				</span>
				<p class="meta">
					<?php 
					$pseries = $value->getTagNamesByType("series"); if(count($pseries) > 0) echo '<a href="">'.implode(", ", $pseries)."</a></span> &middot; " ?>
					<?php echo date("H:i M j, Y", $value->getCreatedTime()) ?>
					<?php $psection = $value->getTagNamesByType("section"); if(count($psection) > 0) echo ' &middot; <a href="">'.implode(", ", $psection)."</a> " ?>
					<?php $pauthor = $value->getTagNamesByType("author"); if(count($pauthor) > 0) echo ' &middot; by&nbsp;<a href="">'.implode(", ", str_replace(" ", "&nbsp;", $pauthor))."</a>" ?>
					</p>
				<p><?php echo substr(strip_tags($value->getContent()), 0, 200) ?>... <a href="" class="readmore">Read&nbsp;More&nbsp;&raquo;</a></p>
			</section>
			<?php } ?>
	
<script type="text/javascript">
	// Smooth scroll to top
	$(".top").click(function(event){
		event.preventDefault(); //prevent the default action for the click event
		$('html, body').animate({scrollTop:0}, 200);
	});

	// Photo gallery
    $(".gallery li").click(function(){
    	$(this).toggleClass("expand");
    });
    $('.gallerylist li:first-child').addClass('selected');
    $('.galleryphoto').empty().append($('.gallerylist li:first-child').children().clone()).fadeIn(200);
	$('.gallerylist li').click(function () {
	    $(this).siblings().removeClass('selected');
	    $(this).addClass('selected');
	    $('.galleryphoto').empty().append($(this).children().clone()).children().hide().fadeIn(200);
	});
	$('.gallerylist').mousemove(function(e){
		var ulWidth = $('.gallerylist li:last-child')[0].offsetLeft + $('.gallerylist li:last-child').outerWidth() - $('.gallerylist li:first-child')[0].offsetLeft + 20;
		var left = (e.pageX - $('.gallerylistwrap').offset().left) * (ulWidth-$('.gallerylistwrap').width()) / $('.gallerylistwrap').width();
		$('.gallerylistwrap').stop().animate({scrollLeft: left}, 100);
	});
	$('.prev').click(function(){
		if($('.gallerylist li:first-child').hasClass('selected')){
			$('.prev').children().hide().fadeIn(200);
		}
		else {
			$('.gallerylist li.selected').removeClass('selected').prev().addClass('selected');
			$('.galleryphoto').empty().append($('.gallerylist li.selected').children().clone()).children().hide().fadeIn(200);
			var left = $('.gallerylist li.selected')[0].offsetLeft - $('.gallerylist li:first-child')[0].offsetLeft - 272;
			$('.gallerylistwrap').stop().animate({scrollLeft: left}, 100);
		}
	});
	$('.next').click(function(){
		if($('.gallerylist li:last-child').hasClass('selected')){
			$('.next').children().hide().fadeIn(200);
		}
		else {
			$('.gallerylist li.selected').removeClass('selected').next().addClass('selected');
			$('.galleryphoto').empty().append($('.gallerylist li.selected').children().clone()).children().hide().fadeIn(200);
			var left = $('.gallerylist li.selected')[0].offsetLeft - $('.gallerylist li:first-child')[0].offsetLeft - 272;
			$('.gallerylistwrap').stop().animate({scrollLeft: left}, 100);
		}
	});
</script>
