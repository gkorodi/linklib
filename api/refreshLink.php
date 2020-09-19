<?php

function getMetaTags($content) {
    $arr = Array();

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    @$doc->loadHTML($content);

    foreach($doc->getElementsByTagName('title') as $tag) {
        $arr['title'] = $tag->nodeValue;
    }

    foreach($doc->getElementsByTagName('meta') as $metatag) {
        if ($metatag->getAttribute('name') != null) {
            $tn = $metatag->getAttribute('name');
        }
        if ($metatag->getAttribute('property') != null) {
            $tn = $metatag->getAttribute('property');
        }

        if (isset($tn)) {
            $arr[$tn] = $metatag->getAttribute('content');
        }
    }
    return $arr;
}

function cleanURL($urlString) {
    $pos = strpos($urlString, '?utm');
    if ($pos) {
        return substr($urlString,0, $pos);
    }
    return $urlString;
}

function getURLDetails($url) {

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $content = curl_exec($ch);
    $info = curl_getinfo($ch);
    $info['cleanurl'] = cleanURL($info['url']);
    $tags = getMetaTags($content);
    curl_close($ch);

	$responses = Array();
	foreach( array_merge($info, $tags) AS $k=>$v) {
		$responses[] = [ 'tag'=> $k, 'value'=>$v];
	}
    return $responses;
}
require_once('../_includes.php');
$entityBody = file_get_contents('php://input');
$req = json_decode($entityBody);

header("Content-Type: application/json");
echo json_encode(getURLDetails($req->url)).PHP_EOL;
exit;