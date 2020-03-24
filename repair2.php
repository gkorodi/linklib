<?php
$linecount=0;

foreach(explode("\n", file_get_contents('a.a')) AS $line) {
	if (empty($line)) { continue; }
	if ($linecount==0) { $linecount++; continue;}
	
	$fields = explode("\t", $line);
	
	$linkId = $fields[0];
	$linkURL = $fields[1];
	
	$hdrUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Safari/605.1.15';
	$cmd = 'curl --header "User-Agent: '.$hdrUserAgent.'" --output /tmp/links/'.$linkId.'.html "'.$linkURL.'"';
	echo $cmd.PHP_EOL;
	
	exec($cmd, $cmdOutput, $cmdStatus);
	if ($cmdStatus != 0) {
		echo 'ERROR '.$cmdStatus.' :'.implode("\n\t", $cmdOutput).PHP_EOL;
	}
	$linecount++;
}
echo 'LineCount: '.$linecount.PHP_EOL;