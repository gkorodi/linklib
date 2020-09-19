<?php
require_once('../_includes.php');
$entityBody = file_get_contents('php://input');
$resp['status'] = 'ok';
$req = json_decode($entityBody);

$link = new Link();
$link->link = $req->URL;
$link->title = str_replace('.webloc','', $req->title);
$link->status = -1;
$link->updated_at = date("Y-m-d");
if (!$link->addLink()) {
	$resp['status'] = (!empty($link->errorMessage)?$link->errorMessage:'error');
	$resp['message'] = implode("\n", $link->errors);
} else {
	$resp['object'] = $link;
}
echo json_encode($resp);
