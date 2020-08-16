<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql = 'SELECT * FROM links WHERE tags IS NULL AND level IS NULL ORDER BY updated_at ASC LIMIT 100';
$resultset = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($resultset);
  exit;
}

echo $twig->render('list.html', [ 
	'profile' => $pageProfile, 
	'links' => $resultset
]);
