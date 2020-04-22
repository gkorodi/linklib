<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$searchresults = [];
if (isset($_REQUEST['q'])) {
	$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ".
		(isset($_REQUEST['fldNoTags'])?" AND tags = ''":"").
		" ORDER BY updated_at  ".(isset($_REQUEST['fldOldestFirst'])?'ASC':'DESC')." LIMIT 1000";
	$searchresults = queryX($sql);
}

$links = Array();
foreach($searchresults AS $r) {
	$r['hostname'] = justHostName($r['link']);
	if (empty($r['created_at'])) { $r['created_at'] = 'n/a'; } else {
		$r['created_at'] = date('Y-m-d', strtotime($r['created_at']));
	}
	$r['level'] = getLevel($r['tags']);
	$r['tags'] = explode(',', $r['tags']);
	$links[] = $r;
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode([ 'profile' => $profile, 'criteria' => $_REQUEST['q'], 'links' => $links, 'result_count' => count($links) ]);
  exit;
}

echo $twig->render('search.html', [ 'profile' => $profile, 'criteria' => $_REQUEST['q'], 'links' => $links, 'result_count' => count($links) ]);
