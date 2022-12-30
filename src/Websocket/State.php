<?php

namespace Server\Websocket;

class State
{

    public static function handlePing(ConnectionInterface $client): void
    {
        (new State)->pong($client);
    }

    private function pong(ConnectionInterface $client): void
    {
        resp($client)->send('system.pong', 'acknowledged');
    }

    public static function handlePong(ConnectionInterface $client): void
    {
        //InOut::send($client, self::$pongMessage);
    }

    public function ping(ConnectionInterface $client): void
    {
        resp($client)->send('system.ping', 'acknowledged');
    }
}