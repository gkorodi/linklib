<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$links = Array();
if (isset($_REQUEST['host']) && !empty($_REQUEST['host'])) {
	
	$extraLevel = ' AND level IS NULL ';
	if (isset($_REQUEST['haslevel'])) {
		$extraLevel = ' AND level IS NOT NULL ';
	}

    $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
        (isset($_REQUEST['untaggedonly'])?" AND (tags IS NULL OR tags = '')":'').
        (isset($_REQUEST['nostatus'])?" AND (status IS NULL OR status = '')":'').
        $extraLevel.
        ' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
                ', link, title LIMIT 200';

    $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
        (isset($_REQUEST['untaggedonly'])?" AND (tags IS NULL OR tags = '')":'').
        (isset($_REQUEST['nostatus'])?" AND (status IS NULL OR status = '')":'').
        $extraLevel.
        ' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
                ', link, title';

    $rows = queryX($sql);
	$rowList = array_slice($rows,0,200);
	foreach($rowList AS $r) {
		$r['hostname'] = justHostName($r['link']);
		$r['tags'] = explode(',', $r['tags']);
		$links[] = $r;
	}
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

renderView('search_byhost.html', ['links' => $links, 'total_count' => count($rows), 'request' => $_REQUEST ]);