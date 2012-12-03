	<?php include 'sidebar.php'; ?>
	
</div><!-- wrap -->

<footer>
	<ul class="links">
		<li><a href="">Archives</a></li>
		<li><a href="">Campus Bulletin</a></li>
		<li><a href="">Letters to the Editor</a></li>
		<li><a href="">Contact Us</a></li>
		<li><a href="">Advertise</a></li>
		<li><a href="">Board of Directors</a></li>
	</ul>
	<p>&copy; <?php echo date('Y'); ?> Imprint Publications</p>
	<p>Student Life Centre, University of Waterloo</p>
	<div class="clear"></div>
</footer>


<!-- Slider -->
<script type="text/javascript" src="<?php theme_dir() ?>js/jquery.slider.min.js"></script>
<script type="text/javascript">
	$('.featured').liteSlider({
		content : '.featuredstory',		// The panel selector.
		width : 704,			// Width of the slider
		height: 400,
		autoplay : true,		// Autoplay the slider. Values, true & false
		delay : 6,			// Transition Delay. Default 3s
		buttonsClass : 'featured-buttons',	// Button's class
		activeClass : 'featured-active',		// Active class
		controlBt : '.featured-control',		// Control button selector
		playText : 'Play',		// Play text
		pauseText : 'Stop'		// Stop text
	});
</script>

<!-- Twitter -->
<script type="text/javascript" src="<?php theme_dir() ?>js/jquery.tweet.js"></script>
<script type="text/javascript">
    jQuery(function($){
        $("#tweetstream").tweet({
        	count: 7,
        	username: ["uw_imprint","bruvark","dksan","bgolz","chantastique","samnabi"],
        	loading_text: "Loading tweets",
			filter: function(t){ return ! /^@\w+/.test(t.tweet_raw_text); }, // remove @replies
        	template: "{user}: {text}<br />{time}{retweet_action} {reply_action} {favorite_action}"
        });
    }).bind("loaded", function(){
        $(this).find("a.tweet_action").click(function(ev) {
          window.open(this.href, "Retweet",
                      'menubar=0,resizable=0,width=550,height=420,top=200,left=400');
          ev.preventDefault();
        });
    });

	window.setInterval(function(){
		$('.tweet_list li:first-child').hide('fast');
		$('.tweet_list li:first-child').clone().appendTo('.tweet_list');
		setTimeout(function () {
			$('.tweet_list li:first-child').remove();
			$('.tweet_list li').show();
		}, 1000);
	}, 10000);
</script>


<script type="text/javascript">
	// Sticky header
	var stickerTop = parseInt($('nav').offset().top);
	$(window).scroll(function()
	{
		// When the header reaches top of viewport, make it stick there
	    $("nav").css((parseInt($(window).scrollTop())+parseInt($("nav").css('margin-top')) > stickerTop) ? {position:'fixed',top:'0px',width:'100%','z-index':'9'} : {position:'relative'});
	    // Since header is removed from flow, we need to compensate for its height by adding a top margin to the body that is equal to the header's height
	    $("body").css((parseInt($(window).scrollTop())+parseInt($("nav").css('margin-top')) > stickerTop) ? {'margin-top':'25px'} : {'margin-top':'0'});
	    // Show the 'return to top' link when you have scrolled 5 times the height of the header
	    $(".top").css((parseInt($(window).scrollTop())+parseInt($("nav").css('margin-top')) > stickerTop * 15) ? {'display':'block'} : {'display':'none'});
	});

	$(document).ready(function(){

		// Smooth scroll to top
		$(".top").click(function(event){
			//prevent the default action for the click event
			event.preventDefault();
			//go to top of page
			$('html, body').animate({scrollTop:0}, 200);
		});

	});
</script>

</body>
</html>