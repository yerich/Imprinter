	<section id="content">
<?php
foreach($pages['main'] as $value) {
?>
<div id="topstorieswrap">
<h2><a href="/<?php echo $value->getId() ?>"><?php echo $value->getTitle() ?></a></h2>
<p><?php echo substr(strip_tags($value->getContent()), 0, 1000) ?></p>
<?php
}
?>
</div>
</section>