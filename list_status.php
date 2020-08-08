<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$extra_criteria = '';

if (isset($_REQUEST['notags'])) {
	$extra_criteria .= ' AND tags IS NULL ';
}
if (isset($_REQUEST['emptytags'])) {
	$extra_criteria .= " AND tags = '' ";
}
if (isset($_REQUEST['oldestfirst'])) {
	$extra_criteria .= " ORDER BY updated_at ASC ";
}

$sql='SELECT * FROM links WHERE '.'status '.(isset($_REQUEST['status'])?' = '.$_REQUEST['status']:' !=200 ').' '.$extra_criteria.' LIMIT 100';
$links = queryX($sql);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($links);
  exit;
}

echo $twig->render('list_status.html', [ 
	'profile' => $pageProfile, 
	'links' => $links,
	'sql_query' => $sql
]);
