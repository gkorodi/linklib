<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$criteria = '';
$searchresults = [];
if (isset($_REQUEST['q'])) {
	$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ".
		(isset($_REQUEST['fldNoTags'])?" AND tags = ''":"").
		" ORDER BY updated_at  ".(isset($_REQUEST['fldOldestFirst'])?'ASC':'DESC')." LIMIT 1000";
	$searchresults = queryX($sql);
	$criteria = $_REQUEST['q'];
}


$links = Array();
foreach($searchresults AS $r) {
	$r['hostname'] = justHostName($r['link']);
	if (empty($r['created_at'])) { $r['created_at'] = 'n/a'; } else {
		$r['created_at'] = date('Y-m-d', strtotime($r['created_at']));
	}
	$r['tags'] = explode(',', $r['tags']);
	$links[] = $r;
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode([ 'criteria' => criteria, 'links' => $links, 'result_count' => count($links) ]);
  exit;
}

renderView('search.html', ['criteria' => $criteria, 'links' => $links, 'result_count' => count($links) ]);
