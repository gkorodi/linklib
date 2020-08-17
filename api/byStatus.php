<?php
require_once('_includes.php');
header('Content-type: application/json');
echo json_encode(query("SELECT status, count(*) as counter FROM links GROUP BY status"));

