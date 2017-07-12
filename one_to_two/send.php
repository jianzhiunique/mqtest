<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

//$channel->queue_declare('hello', false, false, false, false);
//prevent task from rabbitmq crashing, if mq server alreading runnning, this operation useless
//but new queue can solve
$channel->queue_declare('hello', false, true, false, false);

$data = implode(' ', array_slice($argv, 1));
//persistent
$msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

$channel->basic_publish($msg, '', 'hello');

echo "sent $data\n";

$channel->close();
$connection->close();
?>

