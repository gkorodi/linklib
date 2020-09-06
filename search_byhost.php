<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$links = Array();
if (isset($_REQUEST['host']) && !empty($_REQUEST['host'])) {
    $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
        (isset($_REQUEST['untaggedonly'])?" AND (tags IS NULL OR tags = '')":'').
        (isset($_REQUEST['nostatus'])?" AND (status IS NULL OR status = '')":'').
        (isset($_REQUEST['badstatus'])?" AND (status != 200)":'').
        ' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
                ', link, title LIMIT 200';

    $rows = queryX($sql);
	foreach($rows AS $r) {
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

renderView('search_byhost.html', ['links' => $links, 'request' => $_REQUEST ]);