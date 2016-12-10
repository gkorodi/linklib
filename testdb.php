<?php
require_once('_includes.php');

$sql = 'SELECT count(*) FROM links';

$conn = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
  array_push($errors, "Connect failed: %s\n", $mysqli->connect_error);
} else {
  $rs = $conn->query($sql);
  if($rs === false) {
    trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
  } else {
    $response['rowcount'] = $rs->num_rows;
  }

  $response['rows'] = Array();
  $rs->data_seek(0);
  while($row = $rs->fetch_row()){
    array_push($response['rows'], $row);
  }
  $rs->free();
  $conn->close();
}
if (count($errors)>0) {
  $response['messages'] .= implode('<br />', $errors);
}

var_dump($response);

var_dump(query($sql));

//var_dump(query('SELECT count(*) FROM links'));
?>
