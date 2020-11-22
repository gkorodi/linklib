<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new Environment($loader, array('debug' => true));

$hostname = $_REQUEST['host'];
$sql = "SELECT * FROM links WHERE link LIKE 'https://%".$hostname."/%' AND status = 200 AND tags IS NOT NULL ORDER BY updated_at DESC LIMIT 200";
$articles = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($articles);
  exit;
}

renderView('magazine.html', ['articles' => $articles]);