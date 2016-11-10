<?php
require_once('_includes.php');
echo "DB_HOST:".DB_HOST;
echo "DB_PORT:".DB_PORT;
var_dump(query('SELECT count(*) FROM links'));
?>
