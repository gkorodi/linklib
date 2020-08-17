<?php
require_once('_includes.php');

$sql = "SELECT * FROM links WHERE status = 0";
$links = query($sql);

$hosts = [];
foreach ($links['rows'] AS $link) {
	$hostname = explode('/', $link[1])[2];
	$hosts[$hostname]++;
}
ksort($hosts);

header("Content-type: application/json");
echo json_encode($hosts);


