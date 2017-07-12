<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);


echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done", "\n";
    //if this done, send ack, task won't loss if this error
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
//Fair dispatch
//This tells RabbitMQ not to give more than one message to a worker at a time.
$channel->basic_qos(null, 1, null);
//use ack => false, not use => true
//$channel->basic_consume('hello', '', false, true, false, false, $callback);
$channel->basic_consume('hello', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
