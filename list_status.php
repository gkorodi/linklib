<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$extra_criteria = '';
$orderBy = [];
if (isset($_REQUEST['notags'])) {
	$extra_criteria .= ' AND tags IS NULL ';
}
if (isset($_REQUEST['emptytags'])) {
	$extra_criteria .= " AND tags = '' ";
}
if (isset($_REQUEST['oldestfirst'])) {
	$orderBy[] .= "updated_at ASC ";
}
if (isset($_REQUEST['byLevel'])) {
	$orderBy[] = " level ASC ";
}

$sql='SELECT * FROM links WHERE '.'status '.(isset($_REQUEST['status'])?' = '.$_REQUEST['status']:' !=200 ').' '
	.$extra_criteria.' '
	.(count($orderBy)>0?' ORDER BY '.implode(',', $orderBy):'')
	.'LIMIT 100';
$links = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($links);
  exit;
}

renderView('list_status.html', [ 'links' => $links, 'sql_query' => $sql]);
