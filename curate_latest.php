<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT * FROM links WHERE (tags IS NULL OR tags = '') AND level IS NULL ORDER BY id DESC LIMIT 200";
$rs = queryX($sql);

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$r['updated_at'] = empty($r['updated_at'])?'n/a':date('Y-m-d', strtotime($r['updated_at']));
	$links[] = $r;
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}
renderView('set_level.html', ['links' => $links]);
