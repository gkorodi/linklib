<?php
<<<<<<< HEAD
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE (tags IS NULL OR tags = '') AND level IS NULL ORDER BY id DESC LIMIT 200";
=======
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links  WHERE (tags IS NULL OR tags = '') ORDER BY id DESC LIMIT 200";
>>>>>>> master
$rs = queryX($sql);

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$r['updated_at'] = empty($r['updated_at'])?'n/a':date('Y-m-d', strtotime($r['updated_at']));
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
echo $twig->render('set_level.html', ['profile' => $pageProfile, 'links' => $links]);
=======
echo $twig->render('curate.html', ['profile' => $pageProfile, 'links' => $links]);
>>>>>>> master


