<?php
require_once('_constants.php');
require_once('class_Link.php');

$entityBody = file_get_contents('php://input');

$resp['status'] = 'ok';
$resp['req'] = json_decode($entityBody);

$link = new Link();
$link->link = $resp['req']->URL;
$link->title = str_replace('.webloc','', $resp['req']->title);
$link->updated_at = date("Y-m-d");

if (!$link->addLink()) {
	$resp['status'] = (!empty($link->errorMessage)?$link->errorMessage:'error');
	$resp['message'] = implode("\n", $link->errors);
} else {
	$resp['object'] = $link;
}
$resp['debugs'] = $link->debugs;

echo json_encode($resp, JSON_PRETTY_PRINT);