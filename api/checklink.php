<?php
require_once('../_includes.php');

$response['status'] = 'unimplemented';
$response['url'] = $_REQUEST['url'];
$sql = "SELECT * FROM links WHERE link = '".$_REQUEST['url']."' AND id != ".$_REQUEST['linkid'];
$response['sql'] = $sql;
$response['rows'] = queryX($sql);
if (count($response['rows'])===0) {
	$response['status'] = 'ok';
}
header('Content-type: application/json');
echo json_encode($response);
