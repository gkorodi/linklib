<?php
require_once('../_includes.php');

$linkList = queryX("SELECT link FROM links");

$hostList = Array();
foreach($linkList AS $link) {
	$hostname = justHostName($link['link']);
	if (array_key_exists($hostname, $hostList)) {
		$hostList[$hostname]++;
	} else {
		$hostList[$hostname] = 1;
	}
}
echo json_encode($hostList);