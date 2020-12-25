<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$linkList = queryX("SELECT link, status, tags FROM links");

$hostList = Array();
$tagsnull = [];
$tagsempty = [];
$statnotgood = [];

foreach($linkList AS $link) {
	$hostname = justHostName($link['link']);
	
	if (array_key_exists($hostname, $hostList)) {
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
	}
}

$hosts = Array();
foreach($hostList AS $hostName=>$hostTotal) {
	$hosts[] = Array(
		'name'=>substr($hostName, 0, 50), 
		'fullname'=>$hostname,
		'total'=>$hostTotal,
		'tags_null' => $tagsnull[$hostname],
		'tags_empty' => $tagsempty[$hostname],
		'status_bad' => $statnotgood[$hostname],
	);
}

uasort($hosts, function($e1, $e2) {
	return $e2['total'] - $e1['total'];
});

renderView('list_hosts.html', ['hostList' => array_slice($hosts,0,500)]);
