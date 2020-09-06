<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$linkList = queryX("SELECT link, status, tags FROM links");

$hostList = Array();
foreach($linkList AS $link) {
	$hostname = justHostName($link['link']);
	if (array_key_exists($hostname, $hostList)) {
		$hostList[$hostname]++;
	} else {
		$hostList[$hostname] = 1;
	}
}

$hosts = Array();
foreach($hostList AS $hostName=>$hostTotal) {
	$hosts[] = Array(
	'name'=>substr($hostName, 0, 50), 
	'fullname'=>$hostname,
	'total'=>$hostTotal);
}
renderView('list_hosts.html', ['hostList' => $hosts]);
