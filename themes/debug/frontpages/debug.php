<?php
foreach($pages['big'] as $value) {
?>
<h3><a href="/<?php echo $value->getId() ?>"><?php echo $value->getTitle() ?></a></h3>
<p><?php echo $value->getContent() ?></p>
<?php
}
?>

<h2>Raw Page Data</h2>

<pre>
<?php
print_r($pages);
?>
</pre>