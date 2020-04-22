<?php

require_once('_inc.php');

foreach(glob("commands_*") AS $filename) {
	$obj = json_decode(file_get_contents($filename));
	
	
}