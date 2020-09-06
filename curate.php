<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE ".
	"tags IS NULL AND level IS NULL ".
	"ORDER BY updated_at LIMIT 200";
$rs = queryX($sql);

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

renderView('curate.html', ['rowcount' => count($rs), 'links' => $links]);
