<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$randomQuery = 'SELECT * FROM links WHERE tags IS NULL LIMIT 1';
$randomQuery = 'SELECT * FROM links AS r1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM links WHERE tags IS NULL)) AS id) AS r2 WHERE r1.id >= r2.id AND tags IS NULL ORDER BY r1.id ASC LIMIT 1';

$link = null;
if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
	$links = Array();
	try {
		$links = queryX($randomQuery);
	} catch (Exception $e) {
		throw new Exception('Could not run full SQL query:');
	}
	
	if (count($links) == 0) {
		throw new Exception('There is no link available.');
	}
	$link = new Link($links[0]['id']);

} else {
	$link = new Link($_REQUEST['id']);
}

if (!$link->id) {
	throw new Exception('There is no link->id field!'.print_r($debug, true));
}

$errorMessage = '';
if (isset($_POST['id'])) {

	$link = new Link($_POST['id']);
	$link->title = $_POST['title'];
	$link->link = $_POST['link'];

	$tagArray = explode(',', str_replace(' ','', strtolower($_POST['tags'])));
	$link->level = 0;
	if (in_array('level1', $tagArray)) { $link->level = 1; unset($tagArray[array_search('level1', $tagArray)]); }
	if (in_array('level2', $tagArray)) { $link->level = 2; unset($tagArray[array_search('level2', $tagArray)]); }
	if (in_array('level3', $tagArray)) { $link->level = 3; unset($tagArray[array_search('level3', $tagArray)]); }
	if (in_array('level4', $tagArray)) { $link->level = 4; unset($tagArray[array_search('level4', $tagArray)]); }
	if (in_array('level5', $tagArray)) { $link->level = 5; unset($tagArray[array_search('level5', $tagArray)]); }
	$link->tags = implode(',', $tagArray);

	$link->status = $_POST['status'];
	$link->created_at = empty($_POST['created_at'])?date('Y-m-d'):date('Y-m-d', strtotime($_POST['created_at']));
	$link->updated_at = date('Y-m-d');
	$link->description = empty($_POST['description'])?'{"pagetitle":"'.$link->title.'"}':$_POST['description'];
	
	$msgs = [];
	if (!$link->update()) {
		if (count($link->errors)>0) {
			foreach($link->errors AS $msg) {
				$msgs[] = '<h4>ERROR:'.$msg.'</h4>';
			}
		}
		$errorMessage = 'Could not updates link!'.'<br />'.implode('<br />', $msgs).'<br /><pre>'.implode("\n", $link->debugs)."</pre>";
	}
}
echo '<pre>';
var_dump($link);
exit;

renderView('linkedit.html', ['link'=>$link->row, 'errorMessage' => $errorMessage]);

