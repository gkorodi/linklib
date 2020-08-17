<?php
require_once('_includes.php');

$link = new Link($_REQUEST['id']);
if ($link->delete()) {
	$resp['status'] = 'ok';
	$resp['message'] = 'Link <b>'.$link->title.'</b> has been deleted.';
} else {
	$resp['status'] = 'error';
	$resp['message'] = $link->debugs;
}
header('Content-type: application/json');
echo json_encode($resp);
?>