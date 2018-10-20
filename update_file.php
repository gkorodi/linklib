<?php
date_default_timezone_set('US/Eastern');
header("Content-type: application/json");

$id = uniqid();
$response['id'] = $id;
$response['status'] = 'OK';
$response['message'] = 'The record has been created, with id '.$id;

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array
$response['details'] = $input;
file_put_contents("/tmp/${id}.json", $inputJSON);

echo json_encode($response);

?>