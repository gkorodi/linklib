#!/usr/local/php5/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';

define('STAGING_DIR', '/Users/Shared/staging');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 32773, 'guest', 'guest');
$channel = $connection->channel();

$qName = 'injest';
$isQDurable = true;

$channel->queue_declare($qName, false, $isQDurable, false, false);

foreach(glob(STAGING_DIR.'/*.webloc') AS $filename) {
	$r['title'] = basename($filename,'.webloc');
	$xml=simplexml_load_file($filename);
	$r['link'] = $xml->dict->string.'';
	
	$msg = new AMQPMessage(
		json_encode($r),
		array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
	);
	
	$channel->basic_publish($msg, '', $qName);
	#echo "Processed ".print_r($r, true).PHP_EOL;
}
$channel->close();
$connection->close();
?>
