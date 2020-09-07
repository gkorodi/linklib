<?php
require_once('../_includes.php');
//require_once(__DIR__.'/vendor/autoload.php');
//$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader, array('debug' => true));

$sql="SELECT YEAR(created_at), MONTH(created_at), count(*) "
	."FROM links "
	."GROUP BY YEAR(created_at), MONTH(created_at) "
	."ORDER BY YEAR(created_at), MONTH(created_at)";
$rs = queryX($sql);

header('Content-type: application/json');
echo json_encode($rs);
