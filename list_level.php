<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$extra_criteria = '';

if (isset($_REQUEST['notags'])) {
	$extra_criteria .= ' AND tags IS NULL ';
}
if (isset($_REQUEST['emptytags'])) {
	$extra_criteria .= " AND tags = '' ";
}
if (isset($_REQUEST['oldestfirst'])) {
	$extra_criteria .= " ORDER BY updated_at ASC ";
} else {
	$extra_criteria .= " ORDER BY updated_at DESC ";
}

$sql='SELECT * FROM links WHERE level '.(isset($_REQUEST['level'])?' = '.$_REQUEST['level']:' IS NULL ').' '.$extra_criteria.' LIMIT 100';
$links = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo print_r($links, true);
  exit;
}

renderView('list_level.html', [ 'links' => $links, 'criteria' => $_REQUEST['level'], 'sql_query' => $sql]);
