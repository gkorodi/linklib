<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE "
	."(tags = '' OR tags IS NULL) "
	."AND (DATE(updated_at) >= now() - INTERVAL 2 DAY) "
	."AND level IS NULL "
	."ORDER BY updated_at DESC";

$raw = queryX($sql);
$rs = array_slice($raw,0,200);

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$links[] = $r;
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

renderView('curate_today.html', [
	'links' => $links, 
	'totalcount'=> count($raw), 
	'rowcount' => count($links)
]);
