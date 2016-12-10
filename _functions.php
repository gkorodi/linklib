<?php
session_start();
require_once('_includes.php');

$resp['status'] = 'error';
$resp['message'] = 'No method has been specified';
$debugs = Array(print_r($_REQUEST, true));

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
	} else if ($_REQUEST['method']==='testlink') {
		$link = new Link($_REQUEST['id']);
		if ($link->test()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'New status for linkid:'.$link->id.' is '.$link->status;
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Failed to validate the link. New status for linkid:'.$link->id.' is '.$link->status;;
		}
		$debugs << $link->debugs;
		
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
			$debugs << 'Updated fields of object.';
		
			if ($newlink->save()) {
				array_push($debugs, implode(PHP_EOL, $newlink->debugs));
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
		array_push($debugs,'updatelink id:'.$link->id);
		array_push($debugs,'updatelink link:'.$link->link);

		if (isset($_REQUEST['column'])) {
			array_push($debugs,'updatelink column:'.$_REQUEST['column']);
			array_push($debugs,'updatelink new value:'.$_REQUEST['value']);
			
			$col = $_REQUEST['column'];
			
			// Only update the specified column
			if ($col == 'link') {
				$link->link = $_REQUEST['value'];
				if ($link->updateSimple()) {
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
$resp['debugs'] = implode("\n", $debugs);

header("Content-type: application/json");
echo json_encode($resp);
?>
