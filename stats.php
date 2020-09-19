<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$querylist = json_decode(file_get_contents('stats.json'));
foreach($querylist AS $key=>$query) {
	$rs = queryX($query->sql);
    $queryList[$key]->result = $rs;
}

$statuses = queryX("SELECT COALESCE(status,'NULL') AS status, count(*) AS counter FROM "
	."links WHERE status != 200 GROUP BY status");

$latestlinks = queryX("SELECT * FROM links WHERE status = 200 and tags IS NOT NULL AND (level>0 AND level<6) ORDER BY updated_at DESC LIMIT 10");
if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($querylist);
  exit;
}

renderView('stats.html', ['querylist' => $querylist, 'statuses' => $statuses, 'latestlinks' => $latestlinks]);
