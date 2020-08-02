<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$page['profile'] = $pageProfile;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$links = Array();
if (isset($_REQUEST['host']) && !empty($_REQUEST['host'])) {
    $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
        (isset($_REQUEST['untaggedonly'])?" AND (tags IS NULL OR tags = '')":'').
        (isset($_REQUEST['nostatus'])?" AND (status IS NULL OR status = '')":'').
        (isset($_REQUEST['badstatus'])?" AND (status != 200)":'').
        ' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
                ', link, title LIMIT 200';
	$page['sql'] = $sql;
	
    $rows = queryX($sql);
	foreach($rows AS $r) {
		$r['hostname'] = justHostName($r['link']);
		$r['tags'] = explode(',', $r['tags']);
		$links[] = $r;
	}
}
$page['links'] = $links;
$page['request'] = $_REQUEST;

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($page);
	exit;
}

echo $twig->render('search_byhost.html', $page);

?>