<?php

function test($link) {
	$ch = curl_init($link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$r['body'] = curl_exec($ch);
	$r['info'] = curl_getinfo($ch);
	curl_close($ch);
	return $r;
}

foreach(glob('*.webloc') AS $filepath) {

	$resp['title'] = basename($filepath, '.webloc');
	$resp['published_date'] = date('c', stat($filepath)['mtime']);

	$xmlObject = simplexml_load_string(file_get_contents($filepath));
	$resp['link'] = $xmlObject->dict->string.'';

	$testResponse = test((string) $xmlObject->dict->string);
	$resp['body'] = $testResponse['body'];
	$resp['info'] = $testResponse['info'];

	echo json_encode($resp).PHP_EOL;
}


