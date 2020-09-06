<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new Environment($loader, array('debug' => true));

$sql = 'SELECT * FROM links AS r1 JOIN (SELECT CEIL(RAND() '.
	'* (SELECT MAX(id) FROM links WHERE tags IS NULL)) AS id) AS r2 '.
		'WHERE r1.id >= r2.id AND tags IS NULL ORDER BY r1.id ASC LIMIT '.(isset($_REQUEST['limit'])?$_REQUEST['limit']:'50');

$results = queryX($sql);

$links = Array();
foreach($results AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$links[] = $r;
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($links);
  exit;
}

renderView('random.html', [ 'results' => $links, 'result_count' => count($links) ]);

