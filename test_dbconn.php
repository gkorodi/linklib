<?php
require_once('_includes.php');
$result_set = query('SELECT count(*) FROM links');
var_dump($result_set);
?>

