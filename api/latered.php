<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$sql = "SELECT * FROM links WHERE (tags = 'later') ORDER BY id DESC LIMIT 200";
$links = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($links);
  exit;
}
