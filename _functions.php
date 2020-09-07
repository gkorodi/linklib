<?php
require_once('_includes.php');

if (!isset($_SESSION['uid'])) { header("Location: login.php"); exit(1); }

$resp['status'] = 'error';
$resp['message'] = 'No method has been specified';

$debugs = $_REQUEST;
function log_debug($msg) {
	global $debugs;
	$debugs[] = $msg;
}

function findMetaTags($content) {
	global $debugs;
	global $skiptagList;
	
	$arr = Array();
	$doc = new DOMDocument();
	libxml_use_internal_errors(true);
	@$doc->loadHTML($content);
	
	foreach($doc->getElementsByTagName('title') as $tag) {
		log_debug('got title');
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
	log_debug('Added '.count($arr).' tags');
	return $arr;
}

if (isset($_REQUEST['method'])) {
	log_debug('_functions.php Method '.$_REQUEST['method'].' has been specified.');

	if ($_REQUEST['method']==='deletelink') {
        try {
            $link = new Link($_REQUEST['id']);
        } catch (Exception $e) {
            die("Could not create link object with id ".$_REQUEST['id']);
        }
        if ($link->delete()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Deleted link, with id <b>'.$_REQUEST['id'].'</b>';
			log_debug(print_r($link->debugs, true));
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not delete link, with id <b>'.$_REQUEST['id'].'</b><br />'.
				'<small>'.implode('<br />', $link->errors).'</small>';
			log_debug(print_r($link->debugs, true));
		}
	} else if ($_REQUEST['method']==='warnlink') {
        try {
            $link = new Link($_REQUEST['id']);
        } catch (Exception $e) {
            die("Could not create link object with id:".$_REQUEST['id']);
        }

        if (!$link->repair()) {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not repair link. '.implode('<br />', $link->errors).'<br />Debugs:'.implode('<br />', $link->debugs);
		} else {
			$resp['status'] = 'ok';
			$resp['message'] = 'Just mirroring back the record that we had';
			$resp['content'] = <<<CONTENT
				<a href="<?=$link->link?>" target="_newWindow"><b><?=$link->title?></b></a><br />
				<small><?=$link->link?><br />
					NewStatus: <b><?=(!isset($link->status)?'n/a':$link->status)?></b> 
					NewTags: <b><?=(!isset($link->tags) || empty($row[ROW_TAGS])?'EMPTY':$row[ROW_TAGS])?></b> 
					NewCreated: <b><?=(!isset($link->curated_at) || empty($link->curated_at)?'n/a':date('Y-m-d', strtotime($link->created_at)))?></b> 
					NewUpdated: <b><?=(!isset($link->updated_at]) || empty($link->updated_at)?'n/a':date('Y-m-d', strtotime($link->created_at)))?></b>
				</small>
CONTENT;
		}
	} else if ($_REQUEST['method']==='tagCurate') {
		log_debug("tagCurate() request id ".$_REQUEST['id']);

        try {
            $lnk = new Link($_REQUEST['id']);
        } catch (Exception $e) {
            die("Could not create link object with id:".$_REQUEST['id']);
        }
        $lnk->tags = 'curate';
		if ($lnk->update()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Link tagged';
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not tag link ';
			log_debug($lnk->debugs);
		}

	} else if ($_REQUEST['method']==='setLevel') {
		
		$lnk = new Link($_REQUEST['id']);
		$resp['status'] = $lnk->setLevel($_REQUEST['level'])?'ok':'error';

	} else if ($_REQUEST['method']==='savefile') {
		$resp['status'] = 'error';
		$inputFileName = FEED_DIR.'/'.$_REQUEST['filename'].'.json';

		log_debug('savefile() Processing file:'.$inputFileName);

		if (file_exists($inputFileName)) {
			log_debug("savefile() File exists");

			try {
				$raw = file_get_contents($inputFileName);
				$newLink = new Link();
				$obj = json_decode($raw);
				$newLink->link = $obj->link.'';
				$newLink->title = $obj->title;
				$newLink->status = getLinkStatus($newLink->link);
				$newLink->updated_at = $obj->updated_at.''; //date('c', strtotime(str_replace(' at ',' ', $obj->published)));
				$newLink->tags = $obj->tags;

				log_debug('savefile() link         :'.$newLink->link);
				log_debug('savefile() title        :'.$newLink->title);
				log_debug('savefile() status       :'.$newLink->status);
				log_debug('savefile() updated_at :'.$newLink->updated_at);
				log_debug('savefile() tags         :'.$newLink->tags);
				
				if ($newLink->addLink()) {
					$resp['status'] = 'ok';
					$resp['message'] = "Imported file for curation. id:". $newLink->id;
					foreach($newLink->debugs AS $msg) {log_debug($msg); }
					if (unlink($inputFileName)) {
						log_debug( "savefile() deleted file ${inputFileName}");
					} else {
						log_debug("savefile() ERROR: could not delete file ${inputFileName}");
					}
				} else {
					$resp['message'] = "Could not import file for curation!!!!";
				}

			} catch (Exception $e) {
				$resp['message'] = "Could not import file!!!!";
				log_debug("savefile() ".print_r($e, true));
			}
		} else {
			log_debug("savefile() File does not exists.");
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
		
	} else if ($_REQUEST['method']==='updateLevelById') {
		$link = new Link($_REQUEST['id']);
		if ($link->updateLevelById($_REQUEST['value'])) {
			$resp['status'] = 'ok';
			$resp['message'] = implode('<br />', $link->debugs);
		} else {
			$resp['status'] = 'error';
			$resp['message'] = implode('<br />', $link->errors);
		}

	} else if ($_REQUEST['method']==='updateTagsById') {
		
		$link = new Link($_REQUEST['id']);
		foreach(explode(',', $_REQUEST['value']) AS $tag) {
			switch($tag) {
				case 'level1':
					$link->updateLevelById(1);
					break;
				case 'level2':
					$link->updateLevelById(2);
					break;
				case 'level3':
					$link->updateLevelById(3);
					break;
				case 'level4':
					$link->updateLevelById(4);
					break;
				case 'level5':
					$link->updateLevelById(5);
					break;
				default:
					$link->addTagForLink($tag);
			}
		}
		$resp['status'] = 'ok';
		$resp['message'] = implode('<br />', $link->debugs);
		
	} else if ($_REQUEST['method']==='updateFieldById') {

		$status = false;
		$logmessages = Array();

		$mysqli = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($logmessages, "Failed to connect to MySQL: ".
			    "(" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$queryStr = "UPDATE links SET ".$_REQUEST['field']." = '".$_REQUEST['value']."'".
					" WHERE id = ".$_REQUEST['id'];
			array_push($logmessages, "SQL Query: ".$queryStr);
			
			if ($mysqli->query($queryStr) === TRUE) {
				$status = true;
				array_push($logmessages, "Link ".$_REQUEST['id']." field `".$_REQUEST['field']."` has been updated with `".$_REQUEST['value']."`.");
			} else {
				array_push($logmessages, "Could not update `".$_REQUEST['field']."` column [".$queryStr."]: ".
					"(" . $mysqli->errno . "/" .$mysqli->error);
			}
			$mysqli->close();
		}
		$resp['status'] = ($status?'ok':'error');
		$resp['message'] = implode("\n",$logmessages);

	} else if ($_REQUEST['method']==='getheader') {
		
		$link = new Link($_REQUEST['id']);
		
		$resp['id'] = $link->id;
		$resp['link'] = $link->link;
		$resp['title'] = $link->title;
		$resp['tags'] = $link->tags;
		$resp['level'] = $link->level;
		$resp['updated_at'] = $link->updated_at;
		$resp['created_at'] = $link->created_at;
		$resp['status'] = $link->status;

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$details = findMetaTags($content);

		$resp['meta'] = $details;
		$resp['status'] = $info['http_code'];
		$resp['title'] = (empty($details['pagetitle'])?$link->title:$details['pagetitle']);

		foreach($details AS $tag=>$val) {
			switch($tag) {
				case 'twitter:title':
				case 'og:title':
					$resp['title'] = $val;
					break;
				case 'og:url':
					$resp['link'] = $val;
					break;
				case 'article:tag':
				case 'keywords':
					$resp['tags'] = $val;
					break;
				case 'article:modified_time':
				case 'og:updated_time':
					log_debug('Setting update_at to '.str_replace('-','', $val));
					$resp['updated_at'] = empty($val)?date('Y-m-d'):date('Y-m-d', strtotime(str_replace('-','', $val)));
					log_debug( 'now '.$resp['updated_at']);
					break;
				case 'article:published_time':
				case 'parsely-pub-date':
				case 'DC.date.issued':
				case 'date':
					if (strpos($val,'-')) { $val = str_replace('-','', $val); }
					if (strpos($val,'T')) { $val = str_replace('T',' ', $val); }
				
					log_debug('Setting created_at to '.$val);
					$resp['created_at'] = empty($val)?date('Y-m-d'):date('Y-m-d', strtotime($val));
					log_debug('now: '.$resp['created_at']);
					break;
			}
		}
		if ($resp['created_at'] == null || empty($resp['created_at'])) {
			$resp['created_at'] = date('Y-m-d');
		}
		if ($resp['updated_at'] == null || empty($resp['updated_at'])) {
			$resp['updated_at'] = date('Y-m-d');
		}
		
		// Find better description, if no meta tag was found
		log_debug('adding description');
		if (!isset($details['description'])) { $details['description'] = '';}
		if (isset($details['twitter:description']) && !empty($details['twitter:description'])) {
			$details['description'] .= $details['twitter:description'];
		}
		if (isset($details['og:description']) && !empty($details['og:description'])) {
			log_debug('added og:description');			
			$details['description'] .= $details['og:description'];
		}
		$resp['details'] = $details;
		$resp['message'] = 'URL Headers for '.$link->link;

	} else if ($_REQUEST['method']==='curate_done') {
		$newLink = new Link();

		$newLink->link = $_REQUEST['link'];
		$newLink->title = $_REQUEST['title'];
		$newLink->status = getLinkStatus($_REQUEST['link']);
		$newLink->updated_at = $_REQUEST['published'];
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
		
		$resp['details'] = $link->row;

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
		$content = curl_exec($ch);
		$resp['info'] = curl_getinfo($ch);
		curl_close($ch);
		
		$resp['details']['status'] = $resp['info']['http_code'];
		
		if ($resp['info']['http_code'] == '200') {
			$resp['status'] = 'ok';
			
			$details = findMetaTags($content);
			$resp['meta'] = $details;
			
			$resp['details']['title'] = $details['pagetitle'];
			
			foreach($details AS $tag => $val) {
				switch ($tag) {
					case 'og:url':
						$resp['details']['link'] = $val;
						break;
					case 'og:title':
						$resp['details']['title'] = $val;
						break;
					case 'article:modified_time':
						$resp['details']['updated_at'] = date('Y-m-d', strtotime($val));
						break;
					case 'article:published_time':
						$resp['details']['created_at'] = date('Y-m-d', strtotime($val));
						break;
					default:
					log_debug('repairlink() not processing value from tag:'.$tag);
				}
			}
			
			if ($resp['details']['updated_at'] == null || empty($resp['details']['updated_at'])) {
				$resp['details']['updated_at'] = date('Y-m-d');
			}
			
		} else {
			$resp['status'] = 'error';
		}
	} else if ($_REQUEST['method']==='getRandomTotal') {
		$sql="SELECT count(*)  FROM links WHERE tags IS NULL OR updated_at IS NULL OR tags = '' OR updated_at = ''";
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
		$r = query("SELECT * FROM links ORDER BY updated_at LIMIT 10");
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
		log_debug('Link #'.$link->id.' and ['.$link->link.']');

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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);

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
			array_push($debugs, 'Could not find _link_ attribute in request.');

			$resp['status'] = 'error';
			$resp['message'] = 'link attribute is minimally mandatory.';
		} else {
			$newlink = new Link();
			array_push($debugs, 'Created new Link object.');

			$newlink->link = $_REQUEST['link'];
			$newlink->title = (isset($_REQUEST['title'])?$_REQUEST['title']:'');
			$newlink->tags = (isset($_REQUEST['tags'])?$_REQUEST['tags']:'');
			$newlink->status = (isset($_REQUEST['status'])?$_REQUEST['status']:-1);
			$newlink->updated_at = (isset($_REQUEST['updated_at'])?$_REQUEST['updated_at']:-1);
			array_push($debugs, 'Updated fields of object.');

			if ($newlink->save()) {
				array_push($debugs, implode(PHP_EOL.'newlink debug ', $newlink->debugs));
				array_push($debugs, 'Object have been saved to persistent store.');

				$resp['status'] = 'ok';
				$resp['message'] = 'Saved link. id:'.$newlink->id;
			} else {
				array_push($debugs, $newlink->debugs);
				array_push($debugs, 'Object could not be saved');

				$resp['status'] = 'error';
				$resp['message'] = 'Could not save link.';
			}
		}

	} else if ($_REQUEST['method']==='updatelink') {
		$link = new Link($_REQUEST['id']);

		array_push($debugs,'updatelink() id:'.$link->id);

		if (isset($_REQUEST['column'])) {
			$col = $_REQUEST['column'];
			array_push($debugs,'updatelink() column:'.$col.' new value:`'.$_REQUEST['value'].'`');

			if ($col == 'link') {
				$link->link = $_REQUEST['value'];
			}

			if ($col == 'title') {
				$link->title = $_REQUEST['value'];
			}

			if ($col == 'status') {
				$link->status = $_REQUEST['value'];
			}

			if ($col == 'tags') {
				$link->tags = $_REQUEST['value'];
			}

			if ($col == 'updated_at') {
				$link->updated_at = $_REQUEST['value'];
			}

			if ($link->update()) {
				$resp['status'] = 'ok';
				$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$col.'</b>';
				array_push($debugs,'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$col.'*');
			} else {
				$resp['status'] = 'error';
				$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$col;
			}
			array_push($debugs, $link->debugs);

		} else {
			if ($link->updateByMap($_REQUEST)) {
				$resp['status'] = 'ok';
				$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b>';
				array_push($debugs, $link->debugs);
			} else {
				$resp['status'] = 'error';
				$resp['message'] = 'Could not update link, with id <b>'.$_REQUEST['id'].'</b>';
				array_push($debugs, $link->debugs);
				if (count($link->errors) > 0) { array_push($debugs, $link->errors); }
			}
		}
	} else {
		$resp['status'] = 'error';
		$resp['message'] = 'Unknown method <b>'.$_REQUEST['method'].'</b>';
	}

}
$resp['debugs'] = $debugs;

header("Content-type: application/json");
echo json_encode($resp);
