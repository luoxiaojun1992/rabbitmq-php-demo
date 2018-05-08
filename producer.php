<?php

require_once __DIR__ . '/vendor/autoload.php';

use Stomp\Client;
use Stomp\StatefulStomp;
use Stomp\Transport\Message;

$client = new Client('failover://(tcp://localhost:61613)?randomize=false');
$client->setLogin('guest', 'guest');

// make a connection
$stomp = new StatefulStomp($client);

while (true) {
    // send a message to the queue
    $stomp->send('/queue/test', new Message('test', [
        'AMQ_SCHEDULED_DELAY' => 10000,
    ]));
    echo "Sent message with body 'test'\n";
}
