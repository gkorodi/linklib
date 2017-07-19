<?php
require_once('_includes.php');

$resp['status'] = 'error';
$resp['message'] = 'No method has been specified';
$debugs = $_REQUEST;

function findMetaTags($content) {
	global $debugs;

	$endpos = null;
	$arr = Array();

	$more_meta_tag = strpos($content, '<meta');
	while ($more_meta_tag) {
		$endpos = strpos($content, '>', $more_meta_tag);
		$tagvalues = substr($content,$more_meta_tag,$endpos-$more_meta_tag);

		//$xml = simplexml_load_string($tagvalues.'>');
		//array_push($debugs, print_r($xml, true));
		//$arr[$xml['name']] =  $xml['content'];

		if (strpos($tagvalues, 'content=')) {
			$parts = explode('"', $tagvalues);
			$arr[str_replace(':','',$parts[1])] =  $parts[3];
		}
		$more_meta_tag = strpos($content, '<meta', $endpos++);
	}
	return $arr;
}

if (isset($_REQUEST['method'])) {
	array_push($debugs, 'Method '.$_REQUEST['method'].' has been specified.');

	if ($_REQUEST['method']==='deletelink') {
		$link = new Link($_REQUEST['id']);
		if ($link->delete()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Deleted link, with id <b>'.$_REQUEST['id'].'</b>';
			array_push($debugs, implode('<br />', $link->debugs));
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not delete link, with id <b>'.$_REQUEST['id'].'</b><br />'.
				'<small>'.$link->getErrorListFormatted().'</small>';
		}
	} else if ($_REQUEST['method']==='delfile') {
		$resp['status'] = 'error';
		if (file_exists($_REQUEST['filename'])) {
			array_push($debugs, "File exists");
			try {
				$s = unlink($_REQUEST['filename']);
				$resp['message'] = ($s?
					"File '".$_REQUEST['filename']."' has been deleted.":
					"File '".$_REQUEST['filename']."' could not be deleted."
				);
				$resp['status'] = 'ok';
			} catch (Exception $e) {
				$resp['message'] = "Could not delete file!!!!";
				array_push($debugs, print_r($e, true));
			}
		} else {
			array_push($debugs, "File does not exists.");
		}

} else if ($_REQUEST['method']==='getfilecount') {
		$resp['status'] = 'ok';
		$resp['value'] = count(glob('data/*.json'));

	} else if ($_REQUEST['method'] === 'savefeeditem') {
		$debugs[] = 'saving new feed item.';

		$link = new Link();
		$link->title = $_REQUEST['title'];
		$link->link = $_REQUEST['link'];
		$link->last_updated = $_REQUEST['last_updated'];

		if ($link->addLink()) {
			$resp['status'] = 'ok';
			$resp['message'] = "Imported details.";
			foreach($link->debugs AS $msg) { $debugs[] = "${msg}"; }
		} else {
			$resp['status'] = 'error';
			$resp['message'] = "Could not import feed item.";
		}


	} else if ($_REQUEST['method']==='savefile') {
		$resp['status'] = 'error';
		$inputFileName = FEED_DIR.'/'.$_REQUEST['filename'].'.json';

		$debugs[] = 'savefile() Processing file:'.$inputFileName;

		if (file_exists($inputFileName)) {
			$debugs[] = "savefile() File exists";

			try {
				$raw = file_get_contents($inputFileName);
				$newLink = new Link();
				$obj = json_decode($raw);
				$newLink->link = $obj->link.'';
				$newLink->title = $obj->title;
				$newLink->status = getLinkStatus($newLink->link);
				$newLink->last_updated = $obj->last_updated.''; //date('c', strtotime(str_replace(' at ',' ', $obj->published)));
				$newLink->tags = $obj->tags;

				$debugs[] = 'savefile() link         :'.$newLink->link;
				$debugs[] = 'savefile() title        :'.$newLink->title;
				$debugs[] = 'savefile() status       :'.$newLink->status;
				$debugs[] = 'savefile() last_updated :'.$newLink->last_updated;
				$debugs[] = 'savefile() tags         :'.$newLink->tags;

				if ($newLink->addLink()) {
					$resp['status'] = 'ok';
					$resp['message'] = "Imported file for curation. id:". $newLink->id;
					foreach($newLink->debugs AS $msg) { $debugs[] = $msg; }
					if (unlink($inputFileName)) {
						$debugs[] = "savefile() deleted file ${inputFileName}";
					} else {
						$debugs[] = "savefile() ERROR: could not delete file ${inputFileName}";
					}
				} else {
					$resp['message'] = "Could not import file for curation!!!!";
				}

			} catch (Exception $e) {
				$resp['message'] = "Could not import file!!!!";
				$debugs[] = "savefile() ".print_r($e, true);
			}
		} else {
			$debugs[] = "savefile() File does not exists.";
		}

	} else if ($_REQUEST['method']==='curate_del') {

		$status = false;
		$logmessages = Array();

		$mysqli = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($logmessages, "Failed to connect to MySQL: ".
			    "(" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$queryStr = "DELETE FROM import1 WHERE link = '".$_REQUEST['linkUrl']."'";
			if ($mysqli->query($queryStr) === TRUE) {
				$status = true;
				array_push($logmessages, "Link ".$_REQUEST['linkUrl']." has been successfully deleted.");
			} else {
				array_push($logmessages, "Could not execute delete statement [".$queryStr."]: ".
					"(" . $mysqli->errno . ") " .$mysqli->error);
			}
			$mysqli->close();
		}
		$resp['status'] = ($status?'ok':'error');
		$resp['message'] = implode("\n",$logmessages);

	} else if ($_REQUEST['method']==='curate_tag') {

		$status = false;
		$logmessages = Array();

		$mysqli = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($logmessages, "Failed to connect to MySQL: ".
			    "(" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$queryStr = "UPDATE import1 SET tags = '".$_REQUEST['tags']."', ".
					"status = ".getLinkStatus($_REQUEST['linkUrl']).
						" WHERE link = '".$_REQUEST['linkUrl']."'";
			if ($mysqli->query($queryStr) === TRUE) {
				$status = true;
				array_push($logmessages, "Link ".$_REQUEST['linkUrl']." has been tagged.");
			} else {
				array_push($logmessages, "Could not update 'tags' column [".$queryStr."]: ".
					"(" . $mysqli->errno . ") " .$mysqli->error);
			}
			$mysqli->close();
		}
		$resp['status'] = ($status?'ok':'error');
		$resp['message'] = implode("\n",$logmessages);

	} else if ($_REQUEST['method']==='getheader') {
		$link = new Link($_REQUEST['id']);

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$content = curl_exec($ch);

		$resp['meta'] = findMetaTags($content);

		$resp['status'] = 'ok';
		$resp['message'] = 'URL Headers for '.$link->link;
		//$resp['info'] = curl_getinfo($ch);
		curl_close($ch);

	} else if ($_REQUEST['method']==='curate_done') {
		$newLink = new Link();

		$newLink->link = $_REQUEST['link'];
		$newLink->title = $_REQUEST['title'];
		$newLink->status = getLinkStatus($_REQUEST['link']);
		$newLink->last_updated = $_REQUEST['published'];
		$newLink->tags = $_REQUEST['tags'];

		if ($newLink->addLink()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'New link has been created, with id '.$newLink->id;
		} else {
			$resp['status'] = 'error';
			$resp['message'] = $newLink->message;
		}
	} else if ($_REQUEST['method']==='repairlink') {
		$link = new Link($_REQUEST['id']);
		$resp['message'] = 'Repair options for link '.$link->id;
		$resp['id'] = $link->id;
		$resp['title'] = $link->title;
		$resp['link'] = $link->link;
		$resp['state'] = $link->status;
		$resp['last_updated'] = $link->last_updated;
		$resp['tags'] = $link->tags;

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$content = curl_exec($ch);
		$resp['info'] = curl_getinfo($ch);
		curl_close($ch);
		if ($resp['info']['http_code'] == '200') {
			$resp['status'] = 'ok';
		}
		$resp['meta'] = findMetaTags($content);

	} else if ($_REQUEST['method']==='repair') {
		$resp['status'] = 'ok';

		$link = new Link($_REQUEST['id']);
		$resp['message'] = 'Repair options for link '.$link->id;
		$resp['id'] = $_REQUEST['id'];
		$resp['title'] = $link->title;
		$resp['link'] = $link->link;
		$resp['last_updated'] = date('Y-m-d H:i:s');
		$resp['tags'] = '';

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$content = curl_exec($ch);
		$resp['info'] = curl_getinfo($ch);
		curl_close($ch);

		if ($resp['info']['http_code']!=200) {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not get the expected webpage!'.print_r($resp['info'], true);
		} else {

		$startpos = strpos($content, '>', strpos($content, '<title'));
		$endpos = strpos($content, '</title', $startpos);
		$resp['title'] = substr($content, $startpos+1, $endpos-$startpos-1);

		$editorLink = '<div class="form-group">'.
		'<label for="link">Link</label>'.
		'<input type="text" class="form-control" id="link" name="link" placeholder="URL for the link " value="'.$resp['link'].'">'.
		'</div>';

		$editorTitle = '<div class="form-group">'.
		'<label for="title">Title</label>'.
		'<input type="title" class="form-control" id="title" name="title" '.
			'placeholder="Enter the title of the link" value="'.$resp['title'].'">'.
		'</div>';

		$editorTags = '<div class="form-group">'.
		'<label for="tags">Tags</label>'.
		'<input type="text" class="form-control" id="tags" name="tags" placeholder="Comma separated list of tags." value="'.$resp['tags'].'">'.
		'</div>';

		$editorDate = '<div class="form-group">'.
		'<label for="last_updated">Last Updated</label>'.
		'<input type="text" class="form-control" id="last_updated" name="last_updated" placeholder="Last time updated" value="'.$resp['last_updated'].'">'.
		'</div>';

		$resp['body'] = '<form method="POST" action="linkedit.php?id='.$_REQUEST['id'].'">'.
			'<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />'.
			$editorTitle.
			$editorLink.
			$editorTags.
			$editorDate.
			'<button type="submit" class="btn btn-default">Submit</button>'.
		'</form>';
	}

	} else if ($_REQUEST['method']==='getRandomTotal') {
		$sql="SELECT count(*)  FROM links WHERE tags IS NULL OR last_updated IS NULL OR tags = '' OR last_updated = ''";
		$raw_rs = query($sql);
		$resp['status'] = 'ok';
		$resp['count'] = 123;

	} else if ($_REQUEST['method']==='getRandomList') {
		//$sql = 'SELECT count(*) AS rowcount FROM links WHERE status != 200';
		//$query_response = query($sql);

		$resp['status'] = 'ok';
		$extra_criteria = (isset($_REQUEST['status'])?'AND status='.$_REQUEST['status']:'');
		if (isset($_REQUEST['notags'])) {
			$extra_criteria .= ' AND tags IS NULL ';
		}
		$sql='SELECT * FROM links WHERE '.'status != 200 '.$extra_criteria.' LIMIT 100';

		$resp['linklist'] = query($sql);

	} else if ($_REQUEST['method']==='getAllTags') {
		$categories['empty'] = 0;
		$categories['NULL'] = 0;
		$r = query("SELECT tags FROM links GROUP BY tags");
		foreach ($r['rows'] AS $row) {
			if ($row[0]===null) { $categories['NULL']++; continue;}
			if ($row[0]==='') { $categories['empty']++; continue;}
			$cats = explode(',', $row[0]);
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
		$resp['alltags'] = $categories;

	} else if ($_REQUEST['method']==='getLinkTags') {
		$resp['status'] = 'ok';
		$resp['linktaglist'] = query('SELECT tags FROM links WHERE id = '.$_REQUEST['id']);

	} else if ($_REQUEST['method']==='getSearchResults') {
		$resp['status'] = 'ok';
		$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ORDER BY title DESC";
		$resp['searchresults'] = query($sql);


	} else if ($_REQUEST['method']==='getRecentPosts') {
		$r = query("SELECT * FROM links ORDER BY last_updated LIMIT 10");
		$resp['status'] = 'ok';
		$resp['message'] = 'recent posts are not available.';

	} else if ($_REQUEST['method']==='getStatusList') {
		$resp['status'] = 'ok';
		$query_response = query('SELECT status, count(*) FROM links WHERE status != 200 GROUP BY status ORDER BY count(*) DESC');
		$resp['status_list'] = $query_response['rows'];

	} else if ($_REQUEST['method']==='getStats') {
		$resp['status'] = 'ok';

		$categories['empty'] = 0;
		$categories['NULL'] = 0;
		$r = query("SELECT tags FROM links GROUP BY tags");
		foreach ($r['rows'] AS $row) {
			if ($row[0]===null) { $categories['NULL']++; continue;}
			if ($row[0]==='') { $categories['empty']++; continue;}
			$cats = explode(',', $row[0]);
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
		$resp['categories'] = $categories;

		$r = query("SELECT tags, count(*) AS counter FROM links GROUP BY tags ORDER BY counter DESC LIMIT 20");
		$resp['category_combinations'] = $r['rows'];


	} else if ($_REQUEST['method']==='getHostList') {
		$resp['status'] = 'ok';
		$query_response = query('SELECT link FROM links WHERE status != 200');

		$hostList = Array();
		foreach($query_response['rows'] AS $row) {
			$lst = split('/',  $row[0]);
			if (isset($lst[2])) {
				$hostList[$lst[2]]++;
			} else {
				$hostList[$lst[2]] = 1;
			}
		}
		asort($hostList);

		$resp['hostlist'] = $hostList;
	} else if ($_REQUEST['method']==='testurl') {

		$link = new Link($_REQUEST['id']);
		array_push($debugs, 'Link #'.$link->id.' and ['.$link->link.']');

		if ($link->test()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Link is valid.';
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Invalid link.';
		}
		foreach($link->debugs AS $msg) { array_push($debugs, $msg);}

	} else if ($_REQUEST['method']==='testlink') {
		// ***** Test a possible new link ******
		array_push($debugs, 'The current link is <pre>'.$_REQUEST['link'].'</pre>');

		$ch = curl_init($_REQUEST['link']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$json = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		if ($info['http_code'] == 200) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Link is valid.';
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Invalid link.';
		}
		array_push($debugs, print_r($info, true));
		// ***** End of new link test ******

	} else if ($_REQUEST['method']==='insertlink') {
		array_push($debugs, 'Processing new link request.');

		if (!isset($_REQUEST['link'])) {
			$debugs << 'Could not find _link_ attribute in request.';

			$resp['status'] = 'error';
			$resp['message'] = 'link attribute is minimally mandatory.';
		} else {
			$newlink = new Link();
			$debugs << 'Created new Link object.';

			$newlink->link = $_REQUEST['link'];
			$newlink->title = (isset($_REQUEST['title'])?$_REQUEST['title']:'');
			$newlink->tags = (isset($_REQUEST['tags'])?$_REQUEST['tags']:'');
			$newlink->status = (isset($_REQUEST['status'])?$_REQUEST['status']:-1);
			$newlink->last_updated = (isset($_REQUEST['last_updated'])?$_REQUEST['last_updated']:-1);
			$debugs << 'Updated fields of object.';

			if ($newlink->save()) {
				$debugs << implode(PHP_EOL, $newlink->debugs);
				$debugs << 'Object have been saved to persistent store.';

				$resp['status'] = 'ok';
				$resp['message'] = 'Saved link. id:'.$newlink->id;
			} else {
				$debugs << $newlink->debugs;
				$debugs << 'Object could not be saved';

				$resp['status'] = 'error';
				$resp['message'] = 'Could not save link.';
			}
		}

	} else if ($_REQUEST['method']==='updatelink') {
		$link = new Link($_REQUEST['id']);

		array_push($debugs,'updatelink id:'.$link->id);
		array_push($debugs,'updatelink link:'.$link->link);

		if (isset($_REQUEST['column'])) {
			array_push($debugs,'updatelink column:'.$_REQUEST['column']);
			array_push($debugs,'updatelink new value:'.$_REQUEST['value']);

			$col = $_REQUEST['column'];
			array_push($debugs,'updatelink Column to update:'.$col);

			// Only update the specified column
			if ($col == 'link') {
				$link->link = $_REQUEST['value'];
				if ($link->update()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>';
					array_push($debugs,'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$_REQUEST['column'].'*');
				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$_REQUEST['column'];
					array_push($debugs, implode("\n", $link->debugs));
				}
			}

			if ($col == 'title') {
				$link->title = $_REQUEST['value'];
				if ($link->update()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>';
					array_push($debugs,'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$_REQUEST['column'].'*');
				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$_REQUEST['column'];
					array_push($debugs, implode("\n", $link->debugs));
				}
			}

			if ($col == 'status') {
				$link->status = $_REQUEST['value'];
				if ($link->update()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>';
					array_push($debugs,'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$_REQUEST['column'].'*');
				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$_REQUEST['column'];
					array_push($debugs, implode("\n", $link->debugs));
				}
			}

			if ($col == 'tags') {
				$link->tags = $_REQUEST['value'];
				if ($link->update()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$col.'*';
					array_push($debugs,'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column *'.$col.'*');

				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$col;
					array_push($debugs,'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$col.'*');

				}
			}

			if ($col == 'last_updated') {
				$link->last_updated = $_REQUEST['value'];
				if ($link->update()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$col.'*';
					array_push($debugs, implode($link->debugs));
					array_push($debugs,'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column *'.$col.'* with value `'.$_REQUEST['value'].'`');

				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$col;
					array_push($debugs,'Could not update link, with id *'.$_REQUEST['id'].'* and column *'.$col.'*');
				}
				array_push($debugs, $link->debugs);
			}
		} else {
			if ($link->update($_REQUEST)) {
				$resp['status'] = 'ok';
				$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b>';
				array_push($debugs, implode("\n", $link->debugs));
			} else {
				$resp['status'] = 'error';
				$resp['message'] = 'Could not update link, with id <b>'.$_REQUEST['id'].'</b>'.
					(count($link->errors)>0?implode('<br />', $link->errors):'').implode('<br />', $link->debugs);
			}
		}
	} else {
		$resp['status'] = 'error';
		$resp['message'] = 'Unknown method <b>'.$_REQUEST['method'].'</b>';
	}

} else {

}

$resp['debugs'] = $debugs; //implode("<br />", $debugs);

header("Content-type: application/json");
echo json_encode($resp);
?>
