<section id="content" class="static">
	<h1>Search</h1>
	
	<form action="/search" method="get" id="search_form">
		<input type="text" name="q" size="40" value="<?php echo $_GET['q'] ?>" />
		<input type="submit" value="Search" />
	</form>
	
	<?php if($_GET['q']) { ?>
	
	<div id="search_results"></div>
	<div id="search_results_header">
	<?php if($totalrows)  {
        echo "<span id='search_results_header_left'>Results $startrow - $endrow of $totalrows for <strong>$raw_query</strong></span>";
        //echo "<span id='search_results_header_right'>Search took $querytime seconds</span>";
	} else {
        echo "<span id='search_results_header_left'>No results for <strong>$raw_query</strong></span>";
        //echo "<span id='search_results_header_right'>Search took $querytime seconds</span>";
	} 
	echo "</div><div class='clear'></div>";
	foreach($banned_words as $value) {
		echo "<p class=\"banned\"><strong>".$value."</strong> is a common word and was not included in your search.</p>";
	}
	
	foreach($search_results as $value) {
		echo "<div class='search_result'><h2 class='search_result_title'><a href='".$value['page']->getURL()."'>";
		if(count($value['series']) > 0) {
			$comma = "";
            foreach($value['series'] as $tvalue) {
                echo "$comma".str_replace(" ", "&nbsp;", $tvalue->getName());
                $comma = ", ";
            }
			echo " &mdash; ";
        }
		echo "{$value['title']}</a></h2>";
		echo "<p class='search_result_meta'>".date("F j, Y", $value['created'])." ";
        if(count($value['section']) > 0) {
            $comma = " &middot; ";
            foreach($value['section'] as $tvalue) {
                echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                $comma = ", ";
            } 
        }
        if(count($value['authors']) > 0) {
            $comma = " &middot; By&nbsp";
            foreach($value['authors'] as $tvalue) {
                echo "$comma<a href='".$tvalue->getURL()."'>".str_replace(" ", "&nbsp;", $tvalue->getName())."</a>";
                $comma = ", ";
            } 
        }
		echo "</p>";
		echo "<div class='search_result_snippet'>".$value['content']." <a href=".$value['page']->getURL().">Read more &raquo;</a></div>";
		echo "</div>";
	}

	printPagination($href_base, $_GET['page'] - 1, $limit, $totalrows);
	?>
	
	<?php } ?>
</section>