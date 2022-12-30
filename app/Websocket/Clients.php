<?php

namespace App\Websocket;

use Server\Websocket\ConnectionInterface;
use Server\Websocket\Request;

class Clients
{
    /**
     * @var ConnectionInterface[] $clients
     */
    protected static array $clients = [];

    /**
     * @param Request $request
     * @return void
     */
    public static function add(Request $request): void
    {
        self::$clients[$request->client()->getConnectionId()] = $request->client();
    }

    /**
     * @param int $connId
     * @return bool
     */
    public static function exists(int $connId): bool
    {
        return array_key_exists($connId, self::$clients);
    }

    /**
     * @param int $connId
     * @return ConnectionInterface|null
     */
    public static function get(int $connId): ?ConnectionInterface
    {
        return self::$clients[$connId] ?? null;
    }
}