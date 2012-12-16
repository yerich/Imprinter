<div id="wrapper">
	<h1>Content: <?php $page->printPageTitle() ?></h1>
	
	<?php $page->printPageContent() ?>
	
	<h2>Debugging Information</h2>
	
	<pre><?php
	$page->processPageTags();
	$pagedata = $page->getPageData();
	echo htmlentities(print_r($pagedata, true));
	?>
	</pre>
	
	
</div>