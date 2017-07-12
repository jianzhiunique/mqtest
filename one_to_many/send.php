<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

$data = implode(' ', array_slice($argv, 1));
empty($data) && $data = 'empty msg';
$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'logs');

echo "sent Hello World\n";

$channel->close();
$connection->close();
?>
