<?php

class DBQueryService {
	var $conn;
	var $stmtUpate;
	var $stmtInsert;
	var $lastAffectedRow;

	var $debugs = Array();
	var $errorMessage = '';

	function logger($msg) {
		array_push($this->debugs, date('c')." ".$msg);
	}

	function DBQueryService() {
		$this->conn = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		$this->logger("Connected to ".DB_NAME." database.");

		if (!($this->stmtUpdate = $this->conn->prepare("UPDATE links SET link = ?, title = ?, status = ?, tags = ? WHERE id = ?"))) {
		    $this->logger("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
		}

		if (!($this->stmtInsert = $this->conn->prepare("INSERT links (link,title,status,tags,updated_at) VALUES (?,?,?,?,?)"))) {
			$this->logger("Prepare INSERT failed: (". $mysqli->errno. ") " . $mysqli->error);
		}

		if (!($this->stmtAddExtraDetails = $this->conn->prepare("INSERT raw_extras (linkid, details, type) VALUES (?,?,?)"))) {
			$this->logger("Prepare INSERT failed: (". $mysqli->errno. ") " . $mysqli->error);
			$this->error = "Prepare INSERT failed: (". $mysqli->errno. "/" . $mysqli->error;
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
			$this->logger("Binding parameters ".print_r($row, true)." failed: (" . $this->stmtAddExtraDetails->errno . ") " . $this->stmtAddExtraDetails->error);
		}

		if (!$this->stmtAddExtraDetails->execute()) {
		    $this->logger("Execute failed: (" . $this->stmtAddExtraDetails->errno . ") " . $this->stmtAddExtraDetails->error);
		}
	}

	function addRow($row) {
		//$v1="'" . $this->conn->real_escape_string('col1_value') . "'";
		$status = false;

		$rowTitle = $this->conn->real_escape_string($row['title']);

		if (!$this->stmtInsert->bind_param("ssiss",
			$row['link'],
			$rowTitle,
			$row['status'],
			$row['tags'],
			$row['updated_at']
			)
		) {
			$this->logger("Binding parameters ".json_encode($row)." failed: (" . $this->stmtInsert->errno . ") " . $this->stmtInsert->error);
		}

		if (!$this->stmtInsert->execute()) {
			$this->error = "Execute failed: (" . $this->stmtInsert->errno . "/" . $this->stmtInsert->error;
		  $this->logger($this->error);
		} else {
			$this->lastAffectedRow = $this->stmtInsert->affected_rows;
			$this->logger("Inserted ".$this->lastAffectedRow." row successfully.");
			$status = true;
		}
		return $status;
	}

	function insertRow($mapFielValuePairs) {
		/* Prepared statement, stage 2: bind and execute */
		if (!$this->stmtUpdate->bind_param("ssisi",
			$mapFielValuePairs['link'],
			$mapFielValuePairs['title'],
			$mapFielValuePairs['status'],
			$mapFielValuePairs['tags'],
			$mapFielValuePairs['id'])) {
				$this->error = "Binding parameters failed: (" . $this->stmtUpdate->errno . "/" . $this->stmtUpdate->error;
		    $this->logger($this->error);
		}

		if (!$this->stmtUpdate->execute()) {
			$this->error = "Execute failed: (" . $this->stmtUpdate->errno . "/" . $this->stmtUpdate->error;
			$this->logger($this->error);
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

