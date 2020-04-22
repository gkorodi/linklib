 <?php

require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));
$links = Array();

$resultset = Array();
if (isset($_REQUEST['tag'])) {
	if ($_REQUEST['tag'] == 'empty') {
	        $sql = "SELECT * FROM links WHERE tags IS NULL ".
	      	  	(isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
	      		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
								' ORDER BY created_at '.($_REQUEST['olderfirst']?'ASC':'DESC')
		.' LIMIT 50';
	} else {
		$criteria = [];
		foreach(explode(',', $_REQUEST['tag']) AS $t) {
			$criteria[] = " UPPER(tags) LIKE '%".strtoupper($t)."%' ";
		}
	        $sql = "SELECT * FROM links WHERE ".
				implode(' AND ', $criteria)." ".
	      	  	(isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
	      		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
						' ORDER BY created_at '.(isset($_REQUEST['olderfirst'])?'ASC':'DESC')
							.' LIMIT 50'	;
	}
	$resultset = queryX($sql);
}

$links = Array();
foreach($resultset AS $r) {
	$r['hostname'] = justHostName($r['link']);
	if (empty($r['created_at'])) {
		$r['created_at'] = 'n/a';
	} else {
		$r['created_at'] = date('Y-m-d', strtotime($r['created_at']));
	}
	if (empty($r['updated_at'])) {
		$r['updated_at'] = 'n/a';
	} else {
		$r['updated_at'] = date('Y-m-d', strtotime($r['updated_at']));
	}
	$links[] = $r;
}

// Related Tags
/*
$relatedTags = Array();
$tagList = Array();
foreach($resultset AS $row) {
	if ($k === $_REQUEST['tag']) { continue; }
	if (empty($row[ROW_TAGS])) { $tagList[] = 'empty'; }
	foreach(explode(',', $row[ROW_TAGS]) AS $tag) {
		$tagList[] = $tag;
	}
}
$relatedTags = groupBy($tagList);
ksort($result);
*/

$data = ['links' => $links, 'searchTag' => $_REQUEST['tag'], 'profile' => $profile];

if (isset($_REQUEST['format']) && !empty($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($data);
	exit;
}

echo $twig->render('search_bytag.html', $data);

?>

