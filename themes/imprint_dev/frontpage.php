<div id="wrap">

	<section id="content">
		<!--
		<div id="ad-banner">
			<a href="http://www.youtube.com/watch?v=oHg5SJYRHA0">
				<img src="<?php theme_dir() ?>img/ad1.png" />
			</a>
		</div>
		-->
		
			<?php $page->printPageContent() ?>
			
		<div class="page-content" style="clear: both;">
			<h2 id="debugging">Debugging: Raw Page Data</h2>
			
			<pre><?php
			$page->processPageTags();
			$pagedata = $page->getPageData();
			echo htmlentities(print_r($pagedata, true));
			?>
			</pre>
			
			<h2 id="queries">Database Queries for this Request</h2>
			<pre><?php echo implode("\n\n", $page->db->getQueryLog()) ?></pre>
		</div>

	</section><!-- content -->