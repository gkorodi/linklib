<?php
require_once('class_DBQueryService.php');

class Link {
	var $errors = Array();
	var $debugs = Array();

	var $id = '';
	var $link = '';
	var $title = '';
	var $status = '';
	var $level = 0;
	var $created_at = '';
	var $updated_at = '';
	var $tags = '';
	
	var $content = '';
	var $description = '';
  	var $errorMessage = '';
	var $row = Array();
	
	private $mysqli = null;
	private $statementInsertTag = null;

	function logger($msg) {
		array_push($this->debugs, date('c')." ".$msg);
	}
	
	function addTagForLink($tagValue) {
		
		return false;
	}

	function setLevel($level) {
		$this->level = $level;
		return $this->update();
	}

	function __construct($id = null) {
		$this->mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($this->mysqli->connect_errno) {
		    die("Connect failed: ".$this->mysqli->connect_error);
		}
		if (!($this->statementInsertTag = $this->mysqli->prepare("INSERT INTO tags(link_id, tag_name) VALUES (?, ?)"))) {
		    die("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
		}
		
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
				$query = "SELECT * FROM links WHERE id = ${id}";
				if ($result = $this->mysqli->query($query)) {
				    /* fetch associative array */
				    while ($row = $result->fetch_assoc()) {
				        $this->link = $row['link'];
						$this->title = $row['title'];
						$this->status = isset($row['status'])?empty($row['status'])?-1:$row['status']:0;
						$this->tags = $row['tags'];
						$this->level = $row['level'];
						$this->updated_at = isset($row['updated_at'])?$row['updated_at']:date('Y-m-d');
						$this->created_at = isset($row['created_at'])?$row['created_at']:date('Y-m-d');
						$this->description = empty($row['description'])?'{}':$row['description'];
						$this->row = $row;
				    }
				    /* free result set */
				    $result->free();
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
	
	function getException() {
		return implode('<br />', $this->errors);
	}
	
	function updateLevelById($level) {
		$this->logger("Starting updateLevelById(${level})");
		$this->level = $level;
		if (!$this->update()) {
			$this->errors[] = 'Could not update link to level ${level}';
			return false;
		}
		$this->logger("Updated link to level ${level}");
		return true;
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
		$this->logger("update() level       :".$this->level);
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
			$sqlString .= ", level = ".$this->level;
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
?>
