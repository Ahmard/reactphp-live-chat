<?php

namespace App\Websocket;

use Server\Websocket\ConnectionInterface;
use Server\Websocket\Response;

class Room
{
    protected static array $rooms = [];


    public static function addClient(string $roomId, ConnectionInterface $connection): void
    {
        self::$rooms[$roomId][] = $connection;
    }

    /**
     * @param string $roomId
     * @return ConnectionInterface[]
     */
    public static function all(string $roomId): array
    {
        return self::$rooms[$roomId] ?? [];
    }

    public static function send(string $roomId, string $command, array $data): void
    {
        foreach (self::all($roomId) as $client) {
            Response::push($client, $command, $data);
        }
    }

    /**
     * @return array<int, ConnectionInterface[]>
     */
    public static function getRooms(): array
    {
        return self::$rooms;
    }
}