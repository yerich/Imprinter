<?php

function date_smart($time) {
	if(date("H:i:s", $time)=="00:00:00")
		return date("M j, Y", $time);
	else
		return date("H:i M j, Y", $time);
}
