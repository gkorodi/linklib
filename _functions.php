<?php
session_start();
require_once('_includes.php');

if (isset($_REQUEST['method'])) {

	if ($_REQUEST['method']==='deletelink') {
		$link = new Link($_REQUEST['id']);

		if ($link->delete()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Deleted link, with id <b>'.$_REQUEST['id'].'</b>';
			$resp['debugs'] = implode('<br />', $link->debugs);
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not delete link, with id <b>'.$_REQUEST['id'].'</b><br />'.
				'<small>'.$link->getErrorListFormatted().'</small>';
		}
	} else if ($_REQUEST['method']==='getRecentPosts') {
		$resp['status'] = 'ok';
		$resp['message'] = 'recent posts are not available.';
		$resp['debugs'] = '';
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
			$resp['debugs'] = $link->debugs;
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Failed to validate the link. New status for linkid:'.$link->id.' is '.$link->status;;
			$resp['debugs'] = $link->debugs;
		}
	} else if ($_REQUEST['method']==='updatelink') {
		$link = new Link($_REQUEST['id']);

		$resp['debugs'] = Array();
		array_push($resp['debugs'], 'updatelink id:'.$link->id);
		array_push($resp['debugs'], 'updatelink link:'.$link->link);

		if (isset($_REQUEST['column'])) {
			array_push($resp['debugs'], 'updatelink column:'.$_REQUEST['column']);
			array_push($resp['debugs'], 'updatelink new value:'.$_REQUEST['value']);
			// Only update the specified column
			if ($_REQUEST['column'] == 'link') {
				$link->link = $_REQUEST['value'];
				if ($link->updateSimple()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>';
					array_push($resp['debugs'],'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>');

				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$_REQUEST['column'];
					array_push($resp['debugs'],$link->debugs);
				}
			}

			if ($_REQUEST['column'] == 'tags') {
				$link->tags = $_REQUEST['value'];
				if ($link->updateSimple()) {
					$resp['status'] = 'ok';
					$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>';
					array_push($resp['debugs'],'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>');

				} else {
					$resp['status'] = 'error';
					$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$_REQUEST['column'];
					array_push($resp['debugs'],'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$_REQUEST['column'].'</b>');

				}
			}

		} else {
			if ($link->update($_REQUEST)) {
				$resp['status'] = 'ok';
				$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b>';
				array_push($resp['debugs'],$link->debugs);
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

header("Content-type: application/json");
echo json_encode($resp);
?>
