<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links  WHERE (tags IS NULL OR tags = '') ORDER BY id DESC LIMIT 200";
$rs = queryX($sql);

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$r['updated_at'] = empty($r['updated_at'])?'n/a':date('Y-m-d', strtotime($r['updated_at']));
	$links[] = $r;
}

if ($_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

echo $twig->render('curate.html', ['profile' => $pageProfile, 'links' => $links]);


