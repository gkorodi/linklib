<?php
// Examples from: http://www.pontikis.net/blog/how-to-use-php-improved-mysqli-extension-and-why-you-should
// and some other from: http://www.pontikis.net/blog/how-to-write-code-for-any-database-with-php-adodb
date_default_timezone_set('US/Eastern');

define('INPUT_DIR',"/Users/gaborkorodi/Desktop");

define("TBL_RAW_LINKS","links");
require_once('/opt/config/vars');

class DBQueryService {
	var $conn;
	var $stmtUpate;
	var $stmtInsert;
	var $lastAffectedRow;

	function DBQueryService() {
		$this->conn = new mysqli(DB_HOST.(DB_PORT!=null?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		echo "Connected to ".DB_NAME." database.".PHP_EOL;

		if (!($this->stmtUpdate = $this->conn->prepare("UPDATE links SET link = ?, title = ?, status = ?, tags = ? WHERE id = ?"))) {
		    echo date('c')." Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error.PHP_EOL;
		}

		if (!($this->stmtInsert = $this->conn->prepare("INSERT ".TBL_RAW_LINKS." (link,title,status,tags,last_updated) VALUES (?,?,?,?,?)"))) {
			echo date('c')." Prepare INSERT failed: (". $mysqli->errno. ") " . $mysqli->error.PHP_EOL;
		}

		if (!($this->stmtAddExtraDetails = $this->conn->prepare("INSERT raw_extras (linkid, details, type) VALUES (?,?,?)"))) {
			echo date('c')." Prepare INSERT failed: (". $mysqli->errno. ") " . $mysqli->error.PHP_EOL;
		}
	}

	function getAffectedRows() {

		return $this->lastAffectedRow;
	}

	function getInsertId() {
		return mysqli_stmt_insert_id ( $this->stmtInsert );
	}

	function addExtraDetails($row) {
		if (!$this->stmtAddExtraDetails->bind_param("iss",
			$row['linkid'],
			$row['details'],
			$row['type']
			)
		) {
			echo date('c')." Binding parameters ".print_r($row, true)." failed: (" . $this->stmtAddExtraDetails->errno . ") " . $this->stmtAddExtraDetails->error;
		}

		if (!$this->stmtAddExtraDetails->execute()) {
		    echo date('c')." Execute failed: (" . $this->stmtAddExtraDetails->errno . ") " . $this->stmtAddExtraDetails->error.PHP_EOL;
		}
	}

	function addRow($row) {
		//$v1="'" . $this->conn->real_escape_string('col1_value') . "'";
		$rowTitle = $this->conn->real_escape_string($row['title']);

		if (!$this->stmtInsert->bind_param("ssiss",
			$row['link'],
			$rowTitle,
			$row['status'],
			$row['tags'],
			$row['last_updated']
			)
		) {
			echo date('c')." Binding parameters ".json_encode($row)." failed: (" . $this->stmtInsert->errno . ") " . $this->stmtInsert->error;
		}

		if (!$this->stmtInsert->execute()) {
		    echo date('c')." Execute failed: (" . $this->stmtInsert->errno . ") " . $this->stmtInsert->error.PHP_EOL;
		}

		$this->lastAffectedRow = $this->stmtInsert->affected_rows;

		/*if($this->conn->query($sql) === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
		} else {
		  $last_inserted_id = $this->conn->insert_id;
		  $affected_rows = $this->conn->affected_rows;
		}
		echo date('c')." Affected row for Insert: ".$affected_rows;
			*/
	}

	function insertRow($mapFielValuePairs) {
		/* Prepared statement, stage 2: bind and execute */
		if (!$this->stmtUpdate->bind_param("ssisi",
			$mapFielValuePairs['link'],
			$mapFielValuePairs['title'],
			$mapFielValuePairs['status'],
			$mapFielValuePairs['tags'],
			$mapFielValuePairs['id'])) {
		    echo date('c')." Binding parameters failed: (" . $this->stmtUpdate->errno . ") " . $this->stmtUpdate->error;
		}

		if (!$this->stmtUpdate->execute()) {
		    echo date('c')." Execute failed: (" . $this->stmtUpdate->errno . ") " . $this->stmtUpdate->error;
		}

		/* Prepared statement: repeated execution, only data transferred from client to server */
		/*for ($id = 2; $id < 5; $id++) {
		    if (!$stmt->execute()) {
		        echo date('c')." Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		    }
		}*/

		/* explicit close recommended */
		$this->stmtUpdate->close();
	}

	function getRows($sql) {

		$rs=$this->conn->query($sql);

		if($rs === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		} else {
		  $rows_returned = $rs->num_rows;
		}

		$rows = Array();
		/*
		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
		    echo $row['title'] . '<br>';
		}*/


		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$rows[] = $row;

			//foreach($row AS $k=>$v) {
			//	$rows[$k] = $v;
			//}
		}
		$rs->free();

		return $rows;
	}

	function close() {
		$this->conn->close();
	}
}

function getTags($fn) {
	$cmd = '/usr/local/bin/tag -lg "'.str_replace('$','\$', $fn).'"';
	exec($cmd, $output);
	array_shift($output);
	return str_replace(" ","",join(",",$output));
}


function getURLInfo($url) {
	$resp = Array();

	// Create a curl handle to a non-existing location
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	$resp['content'] = curl_exec($ch);
	$resp['info'] = curl_getinfo($ch);
	// Close handle
	curl_close($ch);

	return $resp;
}

/*
$rs=$conn->query($sql);

if($rs === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
} else {
  $rs->data_seek(0);
  $arr = $rs->fetch_array(MYSQLI_ASSOC);
}
*/



/*
UPDATE
======
$v1="'" . $conn->real_escape_string('col1_value') . "'";

$sql="UPDATE tbl SET col1_varchar=$v1, col2_number=1 WHERE id>10";

if($conn->query($sql) === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
} else {
  $affected_rows = $conn->affected_rows;
}
*/

/*
DELETE
======
$sql="DELETE FROM tbl WHERE id>10";

if($conn->query($sql) === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
} else {
  $affected_rows = $conn->affected_rows;
}
*/


/*
TRANSACTION
===========
try {
  // switch autocommit status to FALSE. Actually, it starts transaction
  $conn->autocommit(FALSE);

  $res = $conn->query($sql1);
  if($res === false) {
    throw new Exception('Wrong SQL: ' . $sql . ' Error: ' . $conn->error);
  }

  $res = $conn->query($sql2);
  if($res === false) {
    throw new Exception('Wrong SQL: ' . $sql . ' Error: ' . $conn->error);
  }

  $res = $conn->query($sql3);
  if($res === false) {
    throw new Exception('Wrong SQL: ' . $sql . ' Error: ' . $conn->error);
  }

  $conn->commit();
  echo 'Transaction completed successfully!';

} catch (Exception $e) {

  echo 'Transaction failed: ' . $e->getMessage();
  $conn->rollback();
}

//switch back autocommit status
$conn->autocommit(TRUE);
*/

function getURLFromFile($fn) {
	$xml=simplexml_load_file($fn) or die("Error: Cannot create object");
	return $xml->dict->string.'';
}


$dbservice = new DBQueryService();
$processed = 0;
$failed = 0;

//$fileList = glob("/Volumes/My Book/Links/*.webloc");
$fileList = glob(INPUT_DIR."/*.webloc");
echo "There are ".count($fileList)." files to process.";

//$filename = $fileList[0];
foreach($fileList AS $filename) {

	$raw_data['title'] = basename($filename, '.webloc');
	$file_details = stat($filename);
	$lurl = getURLFromFile($filename);
	$url_details = getURLInfo($lurl);

	$raw_data['link'] = $url_details['info']['url'];
	$raw_data['status'] = $url_details['info']['http_code'];
	$raw_data['last_updated'] = date('Y-m-d H:i:s', $file_details['mtime']);
	$raw_data['tags'] = getTags($filename);

	if ($dbservice->addRow($raw_data)) {
		echo date('c').' Could not insert details for '.$filename.PHP_EOL;
		$failed++;
	} else {
		$linkid = $dbservice->getInsertId();
		$dbservice->addExtraDetails(Array('details'=>json_encode($url_details['info']), 'type'=>'header', 'linkid'=>$linkid));
		unlink($filename);
	}
	$processed++;
}

$dbservice->close();
echo date('c')." Processed: ".($processed--)." Failed: ".($failed--).PHP_EOL;

/*
$dbservice = new DBQueryService();
$rows = $dbservice->getRows("SELECT count(*) AS total FROM raw_links WHERE status IS NULL");

foreach ($rows AS $columnName=>$columnValue) {
	var_dump($columnValue);
}
*/
?>
