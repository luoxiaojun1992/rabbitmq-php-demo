<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

define('HOST', '127.0.0.1');
define('PORT', '5672');
define('USER', 'guest');
define('PASS', 'guest');
define('VHOST', '/');

$exchange = 'delay_test';
$queue = 'test';
$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();
$channel->queue_declare($queue, false, true, false, false);
$channel->exchange_declare(
    $exchange,
    'x-delayed-message',
    false,
    true,
    false,
    false,
    false,
    [
        'x-delayed-type' => ['S', 'direct']
    ]
);
$channel->queue_bind($queue, $exchange);
$messageBody = 'test';
while (true) {
    $message = new AMQPMessage($messageBody, [
        'content_type' => 'text/plain',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'application_headers' => new \PhpAmqpLib\Wire\AMQPTable([
            'x-delay' => 5000
        ])
    ]);

    $channel->basic_publish($message, $exchange);

    echo 'Sent:' . $message->body . PHP_EOL;
}

register_shutdown_function(function () use ($channel, $connection) {
    $channel->close();
    $connection->close();
});
