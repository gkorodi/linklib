<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

function runQueries(&$queryList) {
	foreach($queryList AS $q) {
		//$qrs = queryX($q->sql);
		$q->results = range(1, rand(1,10000)); //count($qrs);
	}
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$querylist = json_decode(file_get_contents('stats.json'));
runQueries($querylist);

$statuses = queryX("SELECT COALESCE(status,'NULL') AS status, count(*) AS counter FROM "
	."links WHERE status != 200 GROUP BY status");

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo json_encode($querylist);
  exit;
}

echo $twig->render('stats.html', [ 
	'profile' => $pageProfile, 
	'querylist' => $querylist,
	'statuses' => $statuses
]);
