<?php
date_default_timezone_set('US/Eastern');

define('ROW_ID',0);
define('ROW_LINK',1);
define('ROW_TITLE',2);
define('ROW_STATUS',3);
define('ROW_TAGS',4);
define('ROW_CREATED_AT',5);
define('ROW_UPDATED_AT',6);
define('ROW_DESCRIPTION',7);

require_once('/opt/config/vars');


function LoggerDebug($msg) {
	//echo $msg.PHP_EOL;
}
function getLinkStatus($url) {
	LoggerDebug("getLinkStatus(${url}) Starting");
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$r['content'] = curl_exec($ch);
	$r['info'] = curl_getinfo($ch);
	curl_close($ch);
	LoggerDebug('getLinkStatus() Finished.');
	return $r;
}

function getMetaTags($pageContent) {
	$tagList = [];
	LoggerDebug('getMetaTags() Starting. Content-size:'.strlen($pageContent));
	$arr = Array();
	$doc = new DOMDocument();
	libxml_use_internal_errors(true);
	@$doc->loadHTML($pageContent); // loads your HTML

	foreach($doc->getElementsByTagName('title') as $tag) {
		$tagList['pagetitle'] = $tag->nodeValue;
	}
	
	foreach($doc->getElementsByTagName('meta') as $metatag) {
		$tagName = '';
		
		if ($metatag->getAttribute('name') != null) {
			$tagName = $metatag->getAttribute('name');
		}
	
		if ($metatag->getAttribute('property') != null) {
			$tagName = $metatag->getAttribute('property');
		}
	
		if (!empty($tagName)) {
			$tagList[$tagName] = $metatag->getAttribute('content');
		}
	}
	LoggerDebug('getMetaTags() Finished. There are '.count($tagList).' tags.');
	return $tagList;
}

function getNewStatus($linkRecord) {
	return $linkRecord['info']["http_code"];
}

function getDescription($linkDetails) {
	return $linkDetails['metatags']['description'];
}

function getDetails($linkRecord) {
	
	$r = getLinkStatus($linkRecord[ROW_LINK]);
	
	if ($r['info']["http_code"]!=200) {
		LoggerDebug("ERROR! Error loading page from ".$linkRecord[ROW_LINK]);
		return false;
	}
	$metaTags = getMetaTags($r['content']);

	$resp['content'] = $r['content'];
	$resp['info'] = $r['info'];
	$resp['metatags'] = $metaTags;
	
	return $resp;
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

?>
