<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$hostname = $_REQUEST['host'];
$sql = "SELECT * FROM links WHERE link LIKE 'https://%".$hostname."/%' AND status = 200 AND tags IS NOT NULL ORDER BY updated_at DESC LIMIT 200";
$articles = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($articles);
  exit;
}

echo $twig->render('magazine.html', [ 
	'profile' => $pageProfile, 
	'articles' => $articles,
]);
