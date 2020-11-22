<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$sql = 'SELECT t.*
FROM links AS t
INNER JOIN
    (SELECT ROUND(
       RAND() * 
      (SELECT MAX(id) FROM links )) AS id
     ) AS x
WHERE
	t.level IS NULL
AND
	t.status IS NULL
AND
    t.id >= x.id
LIMIT 50';

$results = queryX($sql);

foreach($results AS $key=>$value) {
	$results[$key]['hostname'] = justHostName($value['link']);
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($results, JSON_PRETTY_PRINT).PHP_EOL;
} else {
	renderView('random.html', [ 'results' => $results, 'result_count' => count($results) ]);
}


