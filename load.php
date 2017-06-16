<?php
// Examples from: http://www.pontikis.net/blog/how-to-use-php-improved-mysqli-extension-and-why-you-should
// and some other from: http://www.pontikis.net/blog/how-to-write-code-for-any-database-with-php-adodb

$dbservice = new DBQueryService();
$processed = 0;
$failed = 0;

//$fileList = glob("/Volumes/My Book/Links/*.webloc");
$fileList = glob(INPUT_DIR."/*.webloc");
echo "There are ".count($fileList)." files to process.";

//$filename = $fileList[0];
foreach($fileList AS $filename) {

	$raw_data['title'] = basename($filename, '.webloc');
	$file_details = stat($filename);

	$xml=simplexml_load_file($filename) or die("Error: Cannot create object from file:".$filename);
	$lurl = $xml->dict->string.'';

	$url_details = getURLInfo($lurl);

	$raw_data['link'] = $url_details['info']['url'];
	$raw_data['status'] = $url_details['info']['http_code'];
	$raw_data['last_updated'] = date('Y-m-d H:i:s', $file_details['mtime']);
	$raw_data['tags'] = getTags($filename);

	if ($dbservice->addRow($raw_data)) {
		echo date('c').' Could not insert details for '.$filename.PHP_EOL;
		$failed++;
	} else {
		$linkid = $dbservice->getInsertId();
		$dbservice->addExtraDetails(Array('details'=>json_encode($url_details['info']), 'type'=>'header', 'linkid'=>$linkid));
		unlink($filename);
	}
	$processed++;
}

$dbservice->close();
echo date('c')." Processed: ".($processed--)." Failed: ".($failed--).PHP_EOL;

?>
