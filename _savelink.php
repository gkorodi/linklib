<?php

require_once('_includes.php');

function addRecord($link_row) {
  $status = false;

  if ($link_row['link'] === null || empty($link_row['link'])) {
    return false;
  }

  $dbservice = new DBQueryService();
  $raw_data['title'] = (isset($link_row['title'])?$link_row['title']:'');
  $raw_data['link'] = $link_row['link'];
  $raw_data['status'] = $link_row['status'];
  $raw_data['last_updated'] = $link_row['last_updated'];
  $raw_data['tags'] = (isset($link_row['tags'])?$link_row['tags']:'');

  if ($dbservice->addRow($raw_data)) {
    $linkid = $dbservice->getInsertId();
    $debugs[] = "New link, with id ".$linkid." has been inserted.";
    $status = true;
  } else {
    $errors[] = "Could not save link.";
    $status = false;
  }
  $dbservice->close();
  return $status;
}

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
      if(!$mysqli->query("UPDATE import2 SET status = 666 WHERE id = ".$_REQUEST['id'])) {
        $errors[] = "Could not get the old link: (" . $mysqli->errno . ") " .$mysqli->error;
      }
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
