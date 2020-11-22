<?php
require_once(__DIR__.'/../_includes.php');

$c = json_encode($_POST);

function queryByListOfQueries($sqlList) {
	$response = Array();
	$conn = new mysqli(DB_HOST.':'.DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);
	if ($conn->connect_errno) {
		$response['status'] = 'error';
		$response['message'] = $mysqli->connect_error;
	} else {
		$deleteStatement = $mysqli->prepare($conn, "DELETE FROM links WHERE id = ?");
		$updateStatement = $mysqli->prepare($conn, "UPDATE links SET level = ? WHERE id = ?");
		
		foreach($sqlList AS $se) {
			list($flag, $level, $id) = $se;
			
			echo $flag,' -- ',$level.PHP_EOL;
			/*
			if ($se[0] === 'del') {
				
				$deleteStatement->bind_param("i", $linkid);
				$deleteStatement->execute();
			
				$stmt->bind_result($district);
			
			} else {
				$updateStatement->bind_param("i", $level);
				$updateStateme$nt->bind_param("i", $linkid);
				$deleteStatement->execute();
			}
			    bind result variables
			    

			    fetch value
			    $stmt->fetch();

			    printf("%s is in district %s\n", $city, $district);
			
			*/
		}
		$deleteStatement->close();
		$updateStatement->close();
		
	}
	$conn->close();
	return $response;
}

$queryList = [];
foreach(json_decode($c) AS $l => $o) {
	list($f, $linkid) = explode("-", $l);
		
	if ($o[0] === 'del') {
		deleteLinkById($linkid);
		$queryList[] = [ 'del', $linkid, null];
	} else {
		$level = -1;
		switch($o[0]) {
			case('level1'):
			$level = 1;
			break;

			case('level2'):
			$level = 2;
			break;

			case('level3'):
			$level = 3;
			break;

			case('level4'):
			$level = 4;
			break;

			case('level5'):
			$level = 5;
			break;

			default:
			$level = 666;
			echo 'ERROR '.$l.PHP_EOL;
		}
		$queryList[] = [ 'update', $linkid, $level];
	}
}
queryByListOfQueries($queryList);

#header('Content-type: application/json');
#echo json_encode($_POST);