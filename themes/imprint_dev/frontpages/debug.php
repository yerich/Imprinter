<?php
foreach($pages['main'] as $value) {
?>
<h2><a href="/<?php echo $value->getId() ?>"><?php echo $value->getTitle() ?></a></h2>
<p><?php echo substr(strip_tags($value->getContent()), 0, 1000) ?></p>
<?php
}
?>

<h2>Raw Page Data</h2>

<pre>
<?php
print_r($pages);
?>
</pre>