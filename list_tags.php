<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$categories = Array();
$rows = queryX("SELECT tags FROM links");
foreach ($rows AS $row) {
	 $cats = explode(',', $row['tags']);
	 foreach($cats AS $category) {
		 $c = trim($category);
		 if (isset($categories[$c])) {
			 $categories[$c]++;
		 } else {
			 $categories[$c] = 1;
		 }
	 }
}
arsort($categories);

$tags = Array();
foreach($categories AS $category => $count) {
	$tags[] = [ 'tag' => $category, 'count' => $$count ];
}

if (isset($_REQUEST['format']) && $_REQUEST === 'json') {
	header('Content-type: application/json');
	echo json_encode($tags);
	exit;
}

renderView('link_tags.html', [ 'tags' => $tags ]);

