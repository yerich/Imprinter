	<?php include 'sidebar.php'; ?>
	
</div><!-- wrap -->

<footer>
	<ul class="links">
		<li><a target="_blank" href="http://issuu.com/uw_imprint">Archives</a></li>
		<li><a href="/section/campus+bulletin/">Campus Bulletin</a></li>
        <li><a href="/176">Contact Us</a></li>
		<li><a href="/77">Letters to the Editor</a></li>
		<li><a href="/76">Advertise</a></li>
		<li><a target="_blank" href="http://imprintpub.org">Board of Directors</a></li>
	</ul>
    <div class="contact">
    	<p class="address">
            Student Life Centre, Room 1116<br />
            University of Waterloo<br />
            Waterloo, ON, N2L 3G1<br />
            &nbsp;
        </p>
        <p class="phone"><strong>Phone</strong>: 519-888-4048</p>
        <p class="email"><strong>Email</strong>: <a class="grey" href="mailto:editor@imprint.uwaterloo.ca">editor@imprint.uwaterloo.ca</a></p>
    </div>

    <p class="notice">&copy; 2011-<?php echo date('Y'); ?> Imprint Publications and contributors. All rights reserved.
     </p><div style="font-size: 10px"><p style="line-height: 1.5em; margin-top: 1.2em;">
        <em>Imprint</em> is the official student newspaper of the University of Waterloo. It is an editorially independent newspaper published by Imprint Publications, Waterloo, a corporation without share capital.</p>
        	<p style="line-height: 1.6em; margin-top: 0.8em;"><strong>Ron Kielstra, Editor-in-chief</strong>; Laurie Tigert-Dumas, Advertising &amp; Production Manager; Catherine Bolger, General Manager.
        		<strong>Board of Directors: </strong>Cameron Winterink, President; 
        	Eric Evenchick, Vice-president; David Birnbaum, Treasurer; Anupriya Sadhukan, Secretary; Mike Soares, Staff liaison.</p>
        	<p style="line-height: 1.6em; margin-top: 0.8em;">This website was built by Ron Kielstra, Richard Ye, Chantal Jandard and Sam Nabi.</p></div>
	<div class="clear"></div>
</footer>
<!-- 
<p class="stickyfooter">What do you think of the new website? <a target="_blank" href="https://docs.google.com/spreadsheet/viewform?fromEmail=true&formkey=dElnNU5ONXBsTVI5UmlmSkVZSl9GaVE6MQ">Give us your feedback</a>.</p>
-->
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
            count: 10,
            query: "uwaterloo from:uw_imprint OR from:bruvark OR from:bgolz OR from:chantastique OR from:samnabi",
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
    // Toggle button for mobile menu
    $('nav > div > a.nav-button').click(function(){
        $(this).toggleClass('selected');
        $('nav > div > ul').slideToggle(150);
    });

    // Hide menu if using mobile version
    if($(window).width() < 959) {
        $('nav > div > ul').hide();
    } else {
        $('nav > div > ul').show();
    }

    // Like above, but recalculates when window resizes
    $(window).resize(function(){
        if($(window).width() < 959) {
            $('nav > div > ul').hide();
        } else {
            $('nav > div > ul').show();
        }
    });


    // Sticky header
    var stickerTop = parseInt($('nav').offset().top);
    $(window).scroll(function(){
        // When the header reaches top of viewport, make it stick there
        $("nav").css((parseInt($(window).scrollTop())+parseInt($("nav").css('margin-top')) > stickerTop) ? {position:'fixed',top:'0px',width:'100%','z-index':'9'} : {position:'relative'});
        // Since header is removed from flow, we need to compensate for its height by adding a top margin to the body that is equal to the header's height
        $("body").css((parseInt($(window).scrollTop())+parseInt($("nav").css('margin-top')) > stickerTop) ? {'margin-top':'25px'} : {'margin-top':'0'});
        // Show the 'return to top' link when you have scrolled 5 times the height of the header
        $(".top").css((parseInt($(window).scrollTop())+parseInt($("nav").css('margin-top')) > stickerTop * 15) ? {'display':'block'} : {'display':'none'});
    });

    // Smooth scroll to top
    $(".top").click(function(event){
        event.preventDefault(); //prevent the default action for the click event
        $('html, body').animate({scrollTop:0}, 200);
    });

    // Photo gallery
    $('.gallerylist li:first-child').addClass('selected');
    
    function adjustGalleryDimensions(ele) {
        w = $(ele).width();
        h = $(ele).height();
        maxw = 704;
        maxh = 400;
        
        if(w > maxw) {
            h = h * (maxw/w);
            w = maxw;
        }
        if(h > maxh) {
            w = w * (maxh/h);
            h = maxh;
        }
        $(ele).width(w);
        $(ele).height(h);
    }
    
    $('.gallerylist li').click(function () {
        $(this).siblings().removeClass('selected');
        $(this).addClass('selected');
        $('.galleryphoto').empty().append($(this).children().clone()).children().hide().fadeIn(200);
    });
    $('.gallerylist').mousemove(function(e){
        var ulWidth = $('.gallerylist li:last-child')[0].offsetLeft + $('.gallerylist li:last-child').outerWidth() - $('.gallerylist li:first-child')[0].offsetLeft + 20;
        var left = (e.pageX - $('.gallerylistwrap').offset().left) * (ulWidth-$('.gallerylistwrap').width()) / $('.gallerylistwrap').width();
        $('.gallerylistwrap').stop(true, true).animate({scrollLeft: left}, 100);
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
    
    $('.gallerylist li').first().click();

</script>

<?php 
if(DEBUG_MODE) {
	echo "<!-- DEBUGGING OUTPUT FOLLOWS \n".print_r($page->db->getQueryLog(), true)."\n-->";
}
?>

</body>
</html>