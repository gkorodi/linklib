<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$searchresults = Array();
if (isset($_REQUEST['q'])) {
	$searchresults = queryX($_REQUEST['q']);
	if (count($searchresults) > 300) {
		$searchresults = array_slice($searchresults,0,300);
	}
}
renderView('query.html', [ 'results' => $searchresults, 'query' => $_REQUEST['q'] ]);

