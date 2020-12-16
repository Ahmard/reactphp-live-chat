<?php

namespace App\Core\Socket;

class State
{

    public static function handlePing(ConnectionInterface $client)
    {
        self::pong($client);
    }

    private function pong(ConnectionInterface $client)
    {
        resp($client)->send('system.pong', 'acknowledged');
    }

    public static function handlePong(ConnectionInterface $client)
    {
        //InOut::send($client, self::$pongMessage);
    }

    public function ping(ConnectionInterface $client)
    {
        resp($client)->send('system.ping', 'acknowledged');
    }
}