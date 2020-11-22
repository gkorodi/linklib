<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$queryList = json_decode(file_get_contents('stats.json'));
foreach($queryList AS $key=>$query) {
    $rs = queryX($query->sql);
	$queryList[$key]->counts = $rs[0]['counter'];
}

$statuses = queryX("SELECT COALESCE(status,'NULL') AS status, count(*) AS counter FROM "
	."links WHERE status != 200 GROUP BY status");

$latestlinks = queryX("SELECT * FROM links WHERE status = 200 and tags IS NOT NULL AND (level>0 AND level<6) ORDER BY updated_at DESC LIMIT 10");

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($queryList);
  exit;
}

renderView('stats.html', ['queryList' => $queryList, 'statuses' => $statuses, 'latestlinks' => $latestlinks]);
