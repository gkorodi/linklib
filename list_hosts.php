<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$linkList = queryX("SELECT SUBSTRING_INDEX(link,'/',3) AS host, count(*) AS counter FROM links GROUP BY SUBSTRING_INDEX(link,'/',3)");

$hostList = Array();
$tagsnull = [];
$tagsempty = [];
$statnotgood = [];

foreach($linkList AS $link) {
	$hostname = $link['host']; //justHostName($link['link']);
	
	/*if (array_key_exists($hostname, $hostList)) {
		$hostList[$hostname]++;
	} else {
		$hostList[$hostname] = 1;
	}

	if ($link['tags'] === null) {
		if (isset($tagsnull[$hostname])) {$tagsnull[$hostname]++;} else {
			$tagsnull[$hostname] = 1;
		}
	}

	if ($link['tags'] != null and empty($link['tags'])) {
		if (isset($tagsempty[$hostname])) {$tagsempty[$hostname]++;} else {
			$tagsempty[$hostname] = 1;
		}		
	}

	if ($link['status'] != 200) {
		if (isset($statnotgood[$hostname])) {$statnotgood[$hostname]++;} else {
			$statnotgood[$hostname] = 1;
		}		
	}*/
		$hostList[$hostname] = $link['counter'];
}

$hosts = Array();
foreach($hostList AS $hostName=>$hostTotal) {
	$hosts[] = Array(
		'name'=>substr($hostName, 0, 50), 
		'fullname'=>$hostname,
		'total'=>$hostTotal,
		'tags_null' => 0,//$tagsnull[$hostname],
		'tags_empty' => 0,//$tagsempty[$hostname],
		'status_bad' => 0,//$statnotgood[$hostname],
	);
}

uasort($hosts, function($e1, $e2) {
	return $e2['total'] - $e1['total'];
});

renderView('list_hosts.html', ['hostList' => array_slice($hosts,0,500)]);
