#!/usr/local/php5/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
$connection = new AMQPStreamConnection('localhost', 32773, 'guest', 'guest');
$channel = $connection->channel();

$qName = 'injest';
$isQDurable = true;
$isMessageNoAck = false;

$channel->queue_declare($qName, false, $isQDurable, false, false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";


$callback = function($msg) {
	echo " [x] Received ", $msg->body, "\n";
	$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	echo "Done","\n";
};

$channel->basic_consume($qName, '', false, $isMessageNoAck, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>