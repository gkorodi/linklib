<?php
session_start();

date_default_timezone_set('US/Eastern');

define('APP_ROOT','/linklib/');
define('APP_TITLE','linkLIB');
define('APP_ADDRESS', '<h4>Our Bunker</h4><div class="hline-w"></div><p>95 prince st<br/>Boston, 02113<br/>USA<br/></p>');
define('APP_SOCIAL_LINKS','<h4>Social Links</h4><div class="hline-w"></div><p>'.
	'<a href="https://www.linkedin.com/in/gaborkorodi" target="_newWindow"><i class="fa fa-linkedin"></i></a>'.
	'<a href="https://www.flickr.com/photos/crampus" target="_newWindow"><i class="fa fa-flickr"></i></a>'.
	'<a href="#"><i class="fa fa-twitter"></i></a>'.
	'</p>');
define('FEED_DIR','data');

require_once('/opt/config/vars');

if (basename($_SERVER['PHP_SELF'])  != 'login.php') {
	if (!isset($_SESSION['uid'])) {
		header("Location: login.php");
	}
}

function getLinkStatus($url) {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$json = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);

	return $info['http_code'];
}

function showRowSkinny($row) {
	?>
	<tr id="row<?php echo $row[0];?>">
		<td> </td>
		<td>
			<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
			<small><?php echo justHostName($row[1]); ?></small>
		</td>
		<td>
			<small><?php echo date('Y-m-d', strtotime($row[4]));?></small>
		</td>
		<td>
			<?php
			foreach(explode(',', $row[5]) AS $tag) { ?>
				<span class="badge"><?php echo $tag;?></span>
			<?php
			}
			?>
			<button class="btn btn-sm" onClick="checkDetails(<?php echo $row[0];?>);">
				<span class="glyphicon glyphicon-plus"> </span>
			</button>
		</td>
		<td>
			<button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
				<span class="glyphicon glyphicon-remove"> </span>
			</button>
		</td>
		<td>

			<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
				<span class="glyphicon glyphicon-ok"> </span>
			</a>
		</td>

	</tr>
	<?php
}
function showRow($row) {
	?>
	<tr id="row<?php echo $row[0];?>">
		<td> </td>
		<td>
			<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
			<small><?php echo justHostName($row[1]);?></small>
		</td>
		<td>
			<input type="text" id="tags<?php echo $row[0];?>" onChange="repairLink(<?php echo $row[0];?>, $(this).val());" value="<?php echo $row[5];?>" />
	</td>
	<td>
		<?php echo date('Y-m-d', strtotime($row[4]));?>
	</td>
	<td>
			<button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
				<span class="glyphicon glyphicon-remove"> </span>
			</button>
</td><td>
			<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
				<span class="glyphicon glyphicon-ok"> </span>
			</a>
		</td>

	</tr>
	<?php
}

function showUserRow($row) {
	?>
	<tr id="row<?php echo $row[0];?>">
		<td><?php echo justHostName($row[1]);?></small></td>
		<td>
			<h4><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a></h4><br />
			<?php
			foreach(explode(',', $row[5]) AS $tag) { echo '<span class="badge">'.$tag.'</span> ';}
			 ?></small>
		</td>
	<td>
		<?php echo date('Y-m-d', strtotime($row[4]));?>
	</td>
	</tr>
	<?php
}

function errorMessage($msg) {
	echo '<div style="color: red">'.$msg.'</div>';
}

function debugMessage($msg) {
	if (APP_DEBUG!=null && APP_DEBUG === 'on') {
		echo '<div style="color: gray">'.$msg.'</div>';
	}
}

function query($sql) {
	// Examples from: http://www.pontikis.net/blog/how-to-use-php-improved-mysqli-extension-and-why-you-should
	// and some other from: http://www.pontikis.net/blog/how-to-write-code-for-any-database-with-php-adodb
	$errors = Array();
	$response['sql'] = $sql;

	$conn = new mysqli(DB_HOST.(array_key_exists('DB_PORT', get_defined_vars())?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
	if ($conn->connect_errno) {
		array_push($errors, "Connect failed: %s\n", $mysqli->connect_error);
	} else {
		$rs = $conn->query($sql);
		if($rs === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		} else {
		  $response['rowcount'] = $rs->num_rows;
		}

		$response['rows'] = Array();
		$rs->data_seek(0);
		while($row = $rs->fetch_row()){
			array_push($response['rows'], $row);
		}
		$rs->free();
		$conn->close();
	}
	if (count($errors)>0) {
		$response['messages'] .= implode('<br />', $errors);
	}
	return $response;
}

class DBQueryService {
	var $conn;
	var $stmtUpate;
	var $stmtInsert;
	var $lastAffectedRow;

	var $debugs = Array();

	function logger($msg) {
		array_push($this->debugs, date('c')." ".$msg);
	}

	function DBQueryService() {
		$this->conn = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		$this->logger("Connected to ".DB_NAME." database.");

		if (!($this->stmtUpdate = $this->conn->prepare("UPDATE links SET link = ?, title = ?, status = ?, tags = ? WHERE id = ?"))) {
		    $this->logger("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
		}

		if (!($this->stmtInsert = $this->conn->prepare("INSERT links (link,title,status,tags,last_updated) VALUES (?,?,?,?,?)"))) {
			$this->logger("Prepare INSERT failed: (". $mysqli->errno. ") " . $mysqli->error);
		}

		if (!($this->stmtAddExtraDetails = $this->conn->prepare("INSERT raw_extras (linkid, details, type) VALUES (?,?,?)"))) {
			$this->logger("Prepare INSERT failed: (". $mysqli->errno. ") " . $mysqli->error);
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
			$row['last_updated']
			)
		) {
			$this->logger("Binding parameters ".json_encode($row)." failed: (" . $this->stmtInsert->errno . ") " . $this->stmtInsert->error);
		}

		if (!$this->stmtInsert->execute()) {
		    $this->logger("Execute failed: (" . $this->stmtInsert->errno . ") " . $this->stmtInsert->error);

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
		    $this->logger("Binding parameters failed: (" . $this->stmtUpdate->errno . ") " . $this->stmtUpdate->error);
		}

		if (!$this->stmtUpdate->execute()) {
		    $this->logger("Execute failed: (" . $this->stmtUpdate->errno . ") " . $this->stmtUpdate->error);
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

class Link {
	var $errors = Array();
	var $debugs = Array();

	var $id = '';
	var $link = '';
	var $title = '';
	var $status = '';
	var $last_updated = '';
	var $tags = '';
	var $content = '';

	var $row = Array();

	function __construct($id = null) {
		if ($id == null) {
			array_push($this->debugs, "Create an empty object. No 'id' has been specified in constructor.");
		} else {
			array_push($this->debugs, "Initialize Link object with id ".$id);
			$this->id = $id;
			array_push($this->debugs, "Initialized Link object with new id: ".$this->id);

			$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
			/* check connection */
			if ($mysqli->connect_errno) {
			    array_push($errors, "Connect failed: %s\n", $mysqli->connect_error);

			} else {
				$query = "SELECT * FROM links WHERE id = ${id}";
				if ($result = $mysqli->query($query)) {
				    /* fetch associative array */
				    while ($row = $result->fetch_assoc()) {
				        $this->link = $row['link'];
					$this->title = $row['title'];
					$this->status = $row['status'];
					$this->tags = $row['tags'];
					$this->row = $row;
				    }
				    /* free result set */
				    $result->free();
				}
				/* close connection */
				$mysqli->close();
			}

		}
	}

	function delete() {
		// Default to error, just in case.
		$status = false;

		$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($this->errors, "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);

			if ($mysqli->query("DELETE FROM links WHERE id = ".$this->id) === TRUE) {
				$status = true;
				array_push($this->debugs, "Link ".$this->id." has been successfully deleted.");
			} else {
				array_push($this->errors, "Could not execute delete statement: (" . $mysqli->errno . ") " .$mysqli->error);
			}
			$mysqli->close();
		}
		return $status;
	}

	function refresh() {
		$sql = 'SELECT * FROM links WHERE id = '.$this->id;
		$conn = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);

		$rs = $conn->query($sql);
		if($rs === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		} else {
		  $response['rowcount'] = $rs->num_rows;
		}
		$rs->data_seek(0);
		$row = $rs->fetch_row();

		$this->id = $row[0];
		$this->link = $row[1];
		$this->title = $row[2];
		$this->status = $row[3];
		$this->last_updated = $row[4];
		$this->tags = $row[5];
		$rs->free();
		$conn->close();

		return true;

	}

	function test() {
		array_push($this->debugs, 'The current link is "'.$this->link.'"');

		$ch = curl_init($this->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$json = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$this->status = $info['http_code'];
		if ($this->status == 200) {
			if ($this->update()) {
				array_push($this->debugs, "Updated the link with status of 200.");
				return true;
			} else {
				array_push($this->debugs, "Failed to update the link with 200 status.");
				return false;
			}
		} else {
			array_push($this->debugs, $info);
			return false;
		}
	}

	function getURLInfo() {
		array_push($this->debugs, 'The current link is "'.$this->link.'"');
		$this->content = '';
		$ch = curl_init($this->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$this->content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		return $info;
	}

	function update() {

		/*

	$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
	$conn->autocommit(TRUE);

	if (!($stmtUpdate = $conn->prepare("UPDATE links SET link = ?, title = ?, status = ?, tags = ? WHERE id = ?"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmtUpdate->bind_param("ssisi", $_POST['link'],$_POST['title'],$info['http_code'],$_POST['tags'], $_POST['id'])) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmtUpdate->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
		*/

		array_push($this->debugs, "Updating link ".$this->id);

		// Default to error, just in case.
		$status = false;
		$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($this->debugs, "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);

			$sqlString = "UPDATE links SET link = '".$mysqli->real_escape_string($this->link)."'";
			$sqlString .= ", title = '".$mysqli->real_escape_string($this->title)."'";
			$sqlString .= ", tags = '".$mysqli->real_escape_string($this->tags)."'";
			$sqlString .= ", status = ".$mysqli->real_escape_string($this->status);
			$sqlString .= ", last_updated = '".$mysqli->real_escape_string($this->last_updated)."'";

			$sqlString .= ' WHERE id = '.$this->id;

			array_push($this->debugs, "SQL:".$sqlString);

			if ($mysqli->query($sqlString) === TRUE) {
				$status = true;
				array_push($this->debugs, "Link ".$this->id." has been successfully updated.");
				array_push($this->debugs, "Statement was [".$sqlString."]");
			} else {
				array_push($this->debugs, "Could not execute update statement [".$sqlString."]");
				array_push($this->debugs, " (errno:" . $mysqli->errno . ", errmsg:" .$mysqli->error.")");
			}
			$mysqli->close();
		}

		$this->refresh();

		return $status;
	}

	function addLink() {
		array_push($this->debugs, "Link.addLink() Starting");

		$status = false;
		$dbservice = new DBQueryService();

		$raw_data['link'] = $this->link;
		$raw_data['title'] = $this->title;
		$raw_data['status'] = $this->status;
		$raw_data['last_updated'] = $this->last_updated;
		$raw_data['tags'] = $this->tags;
		foreach($raw_data AS $k=>$v) {
			array_push($this->debugs, "Link.addLink() field ${k} is [${v}]");
		}

		if ($dbservice->addRow($raw_data)) {
			$linkid = $dbservice->getInsertId();
			array_push($this->debugs, "Link.addLink() New link #".$linkid." has been inserted.");
			$this->id = $linkid;
			$status = true;
		} else {
			array_push($this->debugs, "Link.addLink() Could not save link.");
		}
		$dbservice->close();

		foreach($dbservice->debugs AS $dbgline) {
			array_push($this->debugs, " dbService() ".$dbgline);
		}

		array_push($this->debugs, "Link.addLink() function returning:".$status);
		return $status;
	}

	function save() {
		array_push($this->debugs, "Saving data to database.");

		$status = false;
		$dbservice = new DBQueryService();

		$raw_data['title'] = (isset($_REQUEST['title'])?$_REQUEST['title']:'');
		$raw_data['link'] = $_REQUEST['link'];
		$raw_data['status'] = ($this->status!=''?$this->status:-1);
		$raw_data['last_updated'] = date('Y-m-d H:i:s');
		$raw_data['tags'] = (isset($_REQUEST['tags'])?$_REQUEST['tags']:'');

		if ($dbservice->addRow($raw_data)) {
			$linkid = $dbservice->getInsertId();
			array_push($this->debugs, "New link, with id ".$linkid." has been inserted.");
			$this->id = $linkid;
			$status = true;
		} else {
			array_push($this->debugs, "Could not save link.");
		}
		$dbservice->close();

		foreach($dbservice->debugs AS $dbgline) {
			array_push($this->debugs, " dbService() ".$dbgline);
		}
		array_push($this->debugs, "Link.save() function returning:".$status);
		return $status;

	}

	function updateByMap($fieldmap) {
		// Default to error, just in case.
		$status = false;
		$mysqli = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($this->errors, "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$sqlString = 'UPDATE links SET last_updated = CURRENT_DATE ';

			if (isset($fieldmap['fldLink'])) {
				$sqlString .= ", link = '".$fieldmap['fldLink']."'";
			}

			if (isset($fieldmap['fldTitle'])) {
				$sqlString .= ", title = '".$fieldmap['fldTitle']."'";
			}

			if (isset($fieldmap['fldTags'])) {
				$sqlString .= ", tags = '".$fieldmap['fldTags']."'";
			}

			$sqlString .= ' WHERE id = '.$this->id;

			if ($mysqli->query($sqlString) === TRUE) {
				$status = true;
				array_push($this->debugs, "Link ".$this->id." has been successfully updated.");
				array_push($this->debugs, "Statement was [".$sqlString."]");
			} else {
				array_push($this->errors, "Could not execute update statement [".$sqlString."] (errno:" . $mysqli->errno . ", errmsg:" .$mysqli->error.")");
			}
			$mysqli->close();
		}
		return $status;
	}

	function logupdate($msg) {
		file_put_contents('/var/tmp/linklib_log.txt', $msg.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

function justHostName($url) {
	$a = explode('/', $url);
	return $a[2];
}
?>
