<?php
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$link = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $link = new Link($_POST['id']);
    } catch (Exception $e) {
        die("Could not create link object with id ".$_POST['id']);
    }
    $link->title = $_POST['title'];
    $link->link = $_POST['link'];

    $tagArray = explode(',', str_replace(' ','', strtolower($_POST['tags'])));
    $link->level = (isset($_POST['level']) && !empty($_REQUEST['level']))?$_POST['level']:0;
    if (in_array('level1', $tagArray)) { $link->level = 1; unset($tagArray[array_search('level1', $tagArray)]); }
    if (in_array('level2', $tagArray)) { $link->level = 2; unset($tagArray[array_search('level2', $tagArray)]); }
    if (in_array('level3', $tagArray)) { $link->level = 3; unset($tagArray[array_search('level3', $tagArray)]); }
    if (in_array('level4', $tagArray)) { $link->level = 4; unset($tagArray[array_search('level4', $tagArray)]); }
    if (in_array('level5', $tagArray)) { $link->level = 5; unset($tagArray[array_search('level5', $tagArray)]); }
    $link->tags = implode(',', $tagArray);

    $link->status = $_POST['status'];
    $link->created_at = empty($_POST['created_at'])?date('Y-m-d'):date('Y-m-d', strtotime($_POST['created_at']));
    $link->updated_at = date('Y-m-d');

    if (!$link->update()) {
        die("Could not update records. ".implode("<br />\n", array_merge($link->errors, $link->debugs)));
    }

} else {
    if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
        $links = Array();
        try {
            $randomQuery = "SELECT id FROM links WHERE tags IS NULL OR tags = 'curate' OR tags = 'later' LIMIT 1000";
            $links = queryX($randomQuery);
        } catch (Exception $e) {
            die('Could not run full SQL query:`'.$randomQuery.'`');
        }
        if (count($links) == 0) {
            die('There is no link available.');
        }
        $newLinkId = null;
        try {
            $newLinkId = $links[rand(1, 1000)];
            $link = new Link($newLinkId['id']);
        } catch (Exception $e) {
            die("Could not create object with id :".$newLinkId);
        }

    } else {
        try {
            $link = new Link($_REQUEST['id']);
        } catch (Exception $e) {
            die("Could not retrieve link object for id: ".$_REQUEST['id']);
        }
    }
}

if (!$link->id) {
	die('There is no link->id field!'.print_r($_REQUEST, true));
}
$errorMessage = array_merge($link->debugs, $link->errors);

if (isset($_REQUEST['format']) && $_REQUEST['format'] === 'json') {
	header('Content-type: application/json');
	echo json_encode($link);
	exit;
}

renderView('linkedit.html', ['link'=>$link, 'errorMessage' => $errorMessage]);

