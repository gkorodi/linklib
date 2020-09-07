<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE tags IS NULL AND level IS NULL ORDER BY updated_at";
$totalRS = queryX($sql);
$displayRS = queryX($sql.' LIMIT 200');

foreach($displayRS AS $key=>$value) {
    $displayRS[$key]['hostname'] = justHostName($value['link']);
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($displayRS);
	exit;
}

renderView('curate.html', ['rowCount' => count($displayRS), 'totalCount' => count($totalRS), 'links' => $displayRS]);
