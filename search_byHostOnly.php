<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$links = Array();
if (isset($_REQUEST['host'])) {
    $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
        (isset($_REQUEST['untaggedonly'])?" AND (tags IS NULL OR tags = '')":'').
        (isset($_REQUEST['nostatus'])?" AND (status IS NULL OR status = '')":'').
        (isset($_REQUEST['badstatus'])?" AND (status != 200)":'').
        ' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
                ', link, title LIMIT 50';
    echo $sql;

    $links = queryX($sql)['rows'];
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	# header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

$request = [ 'isOldestFirst' => true ];
echo $twig->render('search_byhost.html', [ 'profile' => $pageProfile, 'req' => $request, 'links' => $links]);

?>
