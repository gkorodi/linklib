<?php
require_once('_includes.php');

$link = new Link($_REQUEST['id']);
if ($link->delete()) {
	// Deleted entry
} else {
	// Could not delete entry
}
header('Location: '.$_SERVER['HTTP_REFERER']);
?>