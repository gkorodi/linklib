<?php
<<<<<<< HEAD
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE "
	."(tags = '' OR tags IS NULL) "
	."AND (DATE(updated_at) >= now() - INTERVAL 2 DAY) "
	."AND level IS NULL "
	."ORDER BY updated_at DESC";
=======
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE (tags = '' OR tags IS NULL) AND (DATE(updated_at) >= now() - INTERVAL 2 DAY) ORDER BY updated_at DESC";
>>>>>>> master
$raw = queryX($sql);
$rs = queryX($sql.' LIMIT 200');

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$links[] = $r;
}

<<<<<<< HEAD
if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
=======
if ($_REQUEST['format'] == 'json') {
>>>>>>> master
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

<<<<<<< HEAD
echo $twig->render('curate_today.html', [
	'profile' => $pageProfile, 
	'links' => $links, 
	'totalcount'=> count($raw), 
	'rowcount' => count($links)
]);
=======
echo $twig->render('curate_today.html', ['profile' => $pageProfile, 'links' => $links, 'totalcount'=> count($raw), 'rowcount' => count($links)]);
>>>>>>> master

