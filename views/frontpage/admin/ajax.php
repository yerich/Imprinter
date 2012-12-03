<?php
if($_GET['action'] == "strtotime") {
	$time = strtotime($_GET['str']);
	if($time)
		echo $time;
}
