<?php

$connection = new AMQPConnection([
    'host' => '127.0.0.1',
    'port' => 5672,
    'vhost' => '/',
    'login' => 'guest',
    'password' => 'guest'
]);

$connection->pconnect();

//Declare channel
$channel = new AMQPChannel($connection);

//Declare exchange
$exchange = new AMQPExchange($channel);
$exchange->setName('test_direct');
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->declareExchange();

try {
    //Declare Queue
    $queue = new AMQPQueue($channel);
    $queue->setName('test_direct');
    $queue->setFlags(AMQP_NOPARAM);
    $queue->declareQueue();

    //Bind queue to exchange
    $queue->bind($exchange->getName(), 'test_direct');

    $message = 'test';

    while (true) {
        $exchange->publish($message, 'test_direct');
        echo 'Sent', PHP_EOL;
    }
} catch (Exception $ex) {
    print_r($ex);
}
