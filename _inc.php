<?php
if (isset($_SERVER['PHP_SELF'])) {
	session_start();
	if (!in_array(basename($_SERVER['PHP_SELF']), explode(',','login.php,index.php')) && !isset($_SESSION['uid'])) {
		header("Location: login.php");
		exit;
	}
} else {
  die('Please <a href="login.php">log in</a>');
}

date_default_timezone_set('US/Eastern');

define('APP_ROOT','/linklib/');
define('APP_TITLE','linkLIB');

require_once('conf/vars');

$pageProfile['server'] = $_SERVER;
$pageProfile['session'] = $_SESSION;
$pageProfile['request'] = $_REQUEST;

$skiptagList = Array(
	'og:image:height',
	'og:image:width',
	'msapplication-TileColor',
	'fb:app_id',
	'fb:pages',
	'og:locale',
	'og:site_name',
	'og:image:secure_url',
	'og:image',
	'twitter:site',
	'twitter:card',
	'og:type',
	'twitter:image',
	'rating',
	'apple-mobile-web-app-title',
	'bt:body'
);


function validToken($token) {
	return true;
}

if (function_exists('getallheaders')) {
	foreach (getallheaders() as $name => $value) {
	    switch($name) {
	    case 'Authorization':
			$a = explode(' ', $value);
			if (validToken($a[1])) {
				$_SESSION['uid'] = $a[1];
			}
			break;
		default:
	    }
	}
}


function queryX($sql) {
	$response = Array();
	$conn = new mysqli(DB_HOST.':'.DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);
	if ($conn->connect_errno) {
		$response['status'] = 'error';
		$response['message'] = $mysqli->connect_error;
	} else {
		$rs = $conn->query($sql);
		if($rs && $rs->num_rows>0){
		    $response = $rs->fetch_all(MYSQLI_ASSOC);
		}
		$rs->free();
	}
	$conn->close();
	return $response;
}

function getLevel($tags) {
	if (empty($tags)) { return 0;}
	if (strpos(strtolower($tags),'evel1')>0) { return 1;}
	if (strpos(strtolower($tags),'evel2')>0) { return 2;}
	if (strpos(strtolower($tags),'evel3')>0) { return 3;}
	if (strpos(strtolower($tags),'evel4')>0) { return 4;}
	if (strpos(strtolower($tags),'evel5')>0) { return 5;}
	return "?";
}

function getRowDescription($record) {
	$obj = json_decode($record[ROW_DESCRIPTION]);
	if (isset($obj->{'og:description'})) return $obj->{'og:description'};
	return 'No Description';
}


function groupBy($arr) {
	$ret = Array();
	foreach($arr AS $e) {
		if (isset($ret[$e])) {
			$ret[$e]++;
		} else {
			$ret[$e] = 1;
		}
	}
	$taglist = [];
	foreach($ret AS $k=>$v) {
		$tagList[] = ["tag" => $k, "count" => $v];
	}
	return $tagList;
}

function getLinkStatus($url) {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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
			$this->errorMessage = $this->stmtInsert->errno . "/" . $this->stmtInsert->error;
			
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

class Link {
	var $errors = Array();
	var $debugs = Array();

	var $id = '';
	var $link = '';
	var $title = '';
	var $status = '';
	var $created_at = '';
	var $updated_at = '';
	var $tags = '';
	var $content = '';
	var $description = '';
  var $errorMessage = '';
	var $row = Array();

	function logger($msg) {
		array_push($this->debugs, date('c')." ".$msg);
	}

	function __construct($id = null) {
		if ($id == null) {
			$this->logger("Create an empty object. No 'id' has been specified in constructor.");
		} else {
			if (gettype($id) == 'array') {
				list($linkid,$link,$title,$status,$tags,$created_at,$updated_at,$description) = $id;
				
				if ($linkid == null || $link == null) {
					throw new Exception('Invalid id or link value. Cannot create Link object with either of them being null!');
				}
				// This constructor is to find an object!
				$this->id = $linkid;
				$this->link = $link;
				$this->title = $title;
				$this->status = $status;
				$this->tags = $tags;
				$this->updated_at = $updated_at;
				$this->created_at = $created_at;
				$this->description = $description;
				
			} else {
				$this->logger("Initialize Link object with id ".$id);
				$this->id = $id;
				$this->logger("Initialized Link object with new id: ".$this->id);

				$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
				/* check connection */
				if ($mysqli->connect_errno) {
				    $this->logger("Connect failed: %s\n", $mysqli->connect_error);
				} else {
					$query = "SELECT * FROM links WHERE id = ${id}";
					if ($result = $mysqli->query($query)) {
					    /* fetch associative array */
					    while ($row = $result->fetch_assoc()) {
					        $this->link = $row['link'];
									$this->title = $row['title'];
									$this->status = isset($row['status'])?empty($row['status'])?-1:$row['status']:0;
									$this->tags = $row['tags'];
									$this->updated_at = isset($row['updated_at'])?$row['updated_at']:date('Y-m-d');
									$this->created_at = isset($row['created_at'])?$row['created_at']:date('Y-m-d');
									$this->description = empty($row['description'])?'{}':$row['description'];
									
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
	}
	
	function getMetaTags($content) {
		global $skiptagList;
		$this->logger("getMetaTags() starting");
		
		$arr = Array();
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		@$doc->loadHTML($content); // loads your HTML

		foreach($doc->getElementsByTagName('title') as $tag) {
			$this->logger('getMetaTags() got page title');
			$arr['pagetitle'] = $tag->nodeValue;
		}

		foreach($doc->getElementsByTagName('meta') as $metatag) {
			if ($metatag->getAttribute('name') != null) {
				$tn = $metatag->getAttribute('name');
			}
	
			if ($metatag->getAttribute('property') != null) {
				$tn = $metatag->getAttribute('property');
			}
	
			if (isset($tn)) {
				if (!in_array($tn,$skiptagList)) { $arr[$tn] = $metatag->getAttribute('content'); }
			}
		}
		$this->logger('getMetaTags() Added '.count($arr).' tags');
		return $arr;
	}
	
	function repair() {
		$this->logger("repair() starting...");
		
		$this->logger("repair() title:".$this->title.'<br />link: '.$this->link);
		
		$ch = curl_init($this->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		$this->status = $info['http_code'];
		if ($info['http_code']!=200) {
			$this->errors[] = 'HTTP status is '.$info['http_code'];
			
			// Update `status` field with new status
			if ($this->status != $info['http_code']) {
				if ($this->update()) {
					$this->errors[] = 'Status has been updated with '.$info['http_code'];
				}
			} else {
				$this->errors[] = 'Status is still invalid. '.$info['http_code'];
			}
			return false;
		} else {
			$this->errors[] = 'HTTP status is OK';
			// Remove querystring, if any
			$newUrl = substr($info['url'],0, strpos($info['url'], '?'));
			
			// Check if the URL is a duplicate
			$isDuplicate = false;
			try {
				$sql = 'SELECT * FROM links WHERE link = "'.$newUrl.'" AND id != '.$this->id;
				$conn = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
				$rs = $conn->query($sql);
				if($rs === false) {
				  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
				} else {
					$isDuplicate = ($rs->num_rows>0);
					$rs->data_seek(0);
					$r = $rs->fetch_row();
					$dupeLink = '<a href="linkedit.php?id='.$r[ROW_ID].'" target="dupelinkWin">'.$r[ROW_TITLE].'</a>';

				}
				$rs->free();
				$conn->close();
			} catch (Exception $ex) {
				$this->errors[] = 'Exception '.$ex->getMessage();
			}
			
			if ($isDuplicate) {
				$this->errors[] = 'This would be a duplicate. '.$dupeLink .' Please delete';
				return false;
			} else {
				
				$this->link = $newUrl;
				$this->errors[] = 'New URL: '.$this->link;
				
				$columnDescription = $this->getMetaTags($content);
				$this->description = json_encode($columnDescription);
				$this->logger('repair() description:'.$columnDescription);
				
				if (isset($this->description) && !empty($this->description)) {
					$metaTags = json_decode($this->description);
					if (isset($metaTags['pagetitle'])) {
						$this->title = $metaTags['pagetitle'];
					}
				}
				
				if ($this->update()) {
					$this->errors[] = 'Updated the whole record with new information';
				} else {
					$this->errors[] = 'Could not updated the whole record :(';
				}
			}
			// TODOs
			// parse metaTags for `created_at` and `tags` field
			// Update fields if they are different from before ($this)
			
			return false;
		}
		
		return false;
	}
	
	function getLastError() {
		return implode(',', $this->errors);
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
		$this->debugs[] = "refresh() starting...";
		
		$sql = 'SELECT * FROM links WHERE id = '.$this->id;
		$conn = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);

		$rs = $conn->query($sql);
		if($rs === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		} else {
		  $response['rowcount'] = $rs->num_rows;
		}
		$rs->data_seek(0);
		$this->debugs[] = "refresh() got first record";
		
		$row = $rs->fetch_row();

		$this->id = $row[0];
		$this->link = $row[1];
		$this->title = $row[2];
		$this->status = $row[3];
		$this->tags = $row[4];
		$this->created_at = $row[5];
		$this->updated_at = $row[6];
		$this->description = $row[7];
		
		$rs->free();
		$conn->close();
		$this->debugs[] = "refresh() finished";
		return true;

	}

	function test() {
		array_push($this->debugs, 'The current link is "'.$this->link.'"');

		$ch = curl_init($this->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$this->content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		return $info;
	}

	function update() {
		$this->logger("update() starting...");
		
		
		$this->logger("update() id:".$this->id);
		$this->logger("update() link        :".$this->link);
		$this->logger("update() title       :".$this->title);
		$this->logger("update() tags        :".$this->tags);
		$this->logger("update() status      :".(isset($this->status)?(empty($this->status)?-1:$this->status):0));
		$this->logger("update() updated_at  :".(isset($this->updated_at)?$this->updated_at:date('Y-m-d')));
		$this->logger("update() created_at  :".(isset($this->created_at)?$this->created_at:date('Y-m-d')));
		$this->logger("update() description :".(empty($this->description)?'empty':$this->description));

		// Default to error, just in case.
		$status = false;
		$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    $this->logger("update() Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$this->logger("link.update()   tags        :".$this->tags);
		
			$sqlString = "UPDATE links SET link = '".$mysqli->real_escape_string($this->link)."'";
			$sqlString .= ", title = '".$mysqli->real_escape_string($this->title)."'";
			$sqlString .= ", tags = '".$mysqli->real_escape_string($this->tags)."'";
			$sqlString .= ", status = ".$mysqli->real_escape_string($this->status);
			if (empty($this->created_at)) {
				$this->created_at = date('Y-m-d');
			}
			$sqlString .= ", created_at = '".$mysqli->real_escape_string(date('Y-m-d', strtotime($this->created_at)))."'";
			$sqlString .= ", description = '".$mysqli->real_escape_string($this->description)."'";
			$sqlString .= ' WHERE id = '.$this->id;

			$return_status = $mysqli->query($sqlString);
			$this->logger("update() statement: ".$sqlString);
				
			if ( $return_status === TRUE) {
				$status = true;
				$this->logger("update() Link ".$this->id." has been successfully updated. AffectedRows:".$mysqli->affected_rows);
				
			} else {
				if ($mysqli->errno==1062) {
					$this->errors[] = 'Duplicate link detected';
					$this->logger("update() (status:". $mysqli->errno . "/" .$mysqli->error.")");
				} else {
					$this->logger("update() (status:".print_r($return_status, true)." errno:" . $mysqli->errno . ", errmsg:" .$mysqli->error.")");
				}
				
			}
			$mysqli->close();
		}
		return $status;
	}

	function addLink() {
		$this->logger("addLink() starting");

		$status = false;
		$dbservice = new DBQueryService();

		$raw_data['link'] = $this->link;
		$raw_data['title'] = $this->title;
		$raw_data['status'] = $this->status;
		$raw_data['created_at'] = $this->created_at;
		$raw_data['updated_at'] = $this->updated_at;
		$raw_data['tags'] = $this->tags;
		$raw_data['description'] = $this->description;
		
		foreach($raw_data AS $k=>$v) {
			$this->logger("addLink() field ${k} is [${v}]");
		}

		if ($dbservice->addRow($raw_data)) {
			$linkid = $dbservice->getInsertId();
			$this->logger("Link.addLink() New link #".$linkid." has been inserted.");
			$this->id = $linkid;
			$status = true;
		} else {
			$this->errorMessage = $dbservice->errorMessage;
			$this->logger("Link.addLink() Could not save link.");
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
		$raw_data['updated_at'] = date('Y-m-d');
		$raw_data['created_at'] = date('Y-m-d');
		$raw_data['tags'] = (isset($_REQUEST['tags'])?$_REQUEST['tags']:'');
		$raw_data['description'] = $this->description;

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
		array_push($this->debugs, "Link.updateByMap() fieldmap:.");
		foreach($fieldmap AS $k=>$v) {
			array_push($this->debugs, "Link.updateByMap() ${k} = '${v}'");
		}
		// Default to error, just in case.
		$status = false;
		$mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($this->errors, "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$sqlString = 'UPDATE links '.
				'SET updated_at = '.(isset($fieldmap['updated_at'])?"'".date('Y-m-d', strtotime($fieldmap['updated_at']))."'":'CURRENT_DATE')." ";

			if (isset($fieldmap['link'])) {
				$sqlString .= ", link = '".$fieldmap['link']."'";
			}

			if (isset($fieldmap['title'])) {
				$sqlString .= ", title = '".$fieldmap['title']."'";
			}

			if (isset($fieldmap['tags'])) {
				$sqlString .= ", tags = '".$fieldmap['tags']."'";
			}

			if (isset($fieldmap['description'])) {
				$sqlString .= ", description = '".$fieldmap['description']."'";
			}

			$sqlString .= ' WHERE id = '.$this->id;
			
			foreach(explode(',', $sqlString) AS $sstr) {
				array_push($this->debugs, "Link.updateByMap() SQL: ".$sstr);
			}

			if ($mysqli->query($sqlString) === TRUE) {
				$status = true;
				array_push($this->debugs, "Link.updateByMap() Link ".$this->id." has been successfully updated.");
			} else {
				array_push($this->debugs, "Link.updateByMap() Could not execute SQL statement.");
				array_push($this->debugs, "Link.updateByMap() SQLException " . $mysqli->errno . "/" .$mysqli->error);
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
