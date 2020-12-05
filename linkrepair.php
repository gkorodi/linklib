<?php
require_once('_includes.php');

function getScrewedUpLinks() {
	$rows = [];
	$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
	if (!$mysqli->connect_errno) {
		$result = $mysqli->query("SELECT * FROM links WHERE link LIKE '%?utm%'");
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) { $rows[] = $row;}
		$result->close();
		$mysqli->close();
	}
	return $rows;	
}


function findOtherLinks($currentId, $newURL) {
	$rows = [];
	$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
	if (!$mysqli->connect_errno) {
		$sql = "SELECT * FROM links WHERE link = '".$newURL."' AND id != ".$currentId;
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) { $rows[] = $row;}
		$result->close();
		$mysqli->close();
	}
	return $rows;	
}

$leftovers = [];
$screwedUpLinks = getScrewedUpLinks();
echo 'There are '.count($screwedUpLinks).' screwed up links.<br />';

$linksToDelete = [];
$linksToUpdate = [];
foreach($screwedUpLinks AS $linkRow) {
	$wouldbenewurl = substr($linkRow['link'], 0, strpos($linkRow['link'], '?'));
	#$leftovers[] = substr($linkRow['link'], strpos($linkRow['link'], '?'));
	
	$otherRows = findOtherLinks($linkRow['id'], $wouldbenewurl);
	if (count($otherRows)>0) {
		$linksToDelete[] = $linkRow['id'];
	} else {
		$linkRow['newlink'] = $wouldbenewurl;
		$linksToUpdate[] = $linkRow;
	}
}

echo 'Would delete '.count($linksToDelete).'<br />';
echo 'Would update '.count($linksToUpdate).'<br />';

echo print_r($linksToUpdate[0], true);