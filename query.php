<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$searchresults = Array();
if (isset($_REQUEST['q'])) {
	$searchresults = queryX($_REQUEST['q']);
	if (count($searchresults) > 300) {
		$searchresults['rows'] = array_slice($searchresults['rows'],0,300);
	}
}
renderView('query.html', ['results' => $searchresults]);

