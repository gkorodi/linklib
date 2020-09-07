<?php
require_once('../_includes.php');

$sql = 'SELECT * FROM links AS r1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM links WHERE tags IS NULL)) AS id) AS r2 WHERE r1.id >= r2.id AND tags IS NULL ORDER BY r1.id ASC LIMIT 20';

header('Content-type: application/json');
echo json_encode(queryX($sql));