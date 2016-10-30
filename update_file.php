<?php
date_default_timezone_set('US/Eastern');

header("Content-type: application/json");

$id = -1;
$response['status'] = 'OK';
$response['message'] = 'The record has been created, with id '.$id;
$response['fields'] = $_POST;

$uid = time();
$new_file_name = '/var/tmp/'.$uid.'.json';
file_put_contents($new_file_name, json_encode($response));
$response['log'][] = 'File has been moved to '.$new_file_name;

echo json_encode($response);

?>