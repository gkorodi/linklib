<?php

require_once('_includes.php');

$resp['status'] = 'unknown';
$resp['message'] = 'Not implemented';

if (!isset($_REQUEST['id'])) {
  $resp['message'] = 'Missing id, don`t know what to delete';
}

if (isset($_REQUEST['table']) && $_REQUEST['table'] === 'import2') {
    $debugs = Array();
    $errors = Array();

    $mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        $errors[] = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {
      $mysqli->autocommit(true);
      if ($mysqli->query("DELETE FROM ".$_REQUEST['table']." WHERE id = ".$_REQUEST['id']) === TRUE) {
      } else {
        $errors[] = "Could not execute delete statement: (" . $mysqli->errno . ") " .$mysqli->error;
      }
      $mysqli->close();
    }

    if (count($errors) > 0) {
      $resp['status'] = 'error';
      $resp['message'] = implode("\n", $errors);
    } else {
      $resp['status'] = 'ok';
      $resp['message'] = 'Link '.$_REQUEST['id'].' deleted from '.$_REQUEST['table'];
    }
}
echo json_encode($resp);
?>
