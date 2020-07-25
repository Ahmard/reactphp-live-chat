<?php

namespace App\Socket;

use App\Socket\InOut;
use Ratchet\ConnectionInterface;

class State
{

    public static function handlePing(ConnectionInterface $client)
    {
        self::pong($client);
    }

    public static function handlePong(ConnectionInterface $client)
    {
        //InOut::send($client, self::$pongMessage);
    }

    public function ping(ConnectionInterface $client)
    {
        resp($client)->send('system.ping', 'acknowledged');
    }

    private function pong(ConnectionInterface $client)
    {
        resp($client)->send('system.pong', 'acknowledged');
    }
}