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
$exchange->setName('test_delay');
$exchange->setType('x-delayed-message');
$exchange->setArguments([
    'x-delayed-type' => AMQP_EX_TYPE_DIRECT
]);
$exchange->declareExchange();

try {
    //Declare Queue
    $queue = new AMQPQueue($channel);
    $queue->setName('test_delay');
    $queue->setFlags(AMQP_NOPARAM);
    $queue->declareQueue();

    //Bind queue to exchange
    $queue->bind($exchange->getName(), 'test_delay');

    while (true) {
        $queue->consume(function (AMQPEnvelope $message, AMQPQueue $q) {
            echo $message->getBody(), PHP_EOL;
            $q->ack($message->getDeliveryTag());
        });
    }
} catch (Exception $ex) {
    print_r($ex);
}
