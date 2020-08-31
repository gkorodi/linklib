<?php
require_once('../_includes.php');

$criteria = '';
$searchresults = [];
if (isset($_REQUEST['q'])) {
	$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ".
		(isset($_REQUEST['fldNoTags'])?" AND tags = ''":"").
		" ORDER BY updated_at  ".(isset($_REQUEST['fldOldestFirst'])?'ASC':'DESC')." LIMIT 1000";
	$searchresults = queryX($sql);
	$criteria = $_REQUEST['q'];
}


  header('Content-type: application/json');
  echo json_encode([ 'criteria' => criteria, 'links' => $links, 'result_count' => count($links) ]);

