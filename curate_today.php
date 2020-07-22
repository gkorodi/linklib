<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE (tags = '' OR tags IS NULL) AND (DATE(updated_at) >= now() - INTERVAL 2 DAY) ORDER BY updated_at DESC";
$raw = queryX($sql);
$rs = queryX($sql.' LIMIT 200');

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$links[] = $r;
}

if ($_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

echo $twig->render('curate_today.html', ['profile' => $pageProfile, 'links' => $links, 'totalcount'=> count($raw), 'rowcount' => count($links)]);

