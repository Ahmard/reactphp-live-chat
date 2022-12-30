<?php


namespace App\Websocket\Models;


use Server\Websocket\ConnectionInterface;
use Server\Websocket\Response;

abstract class Model
{
    /**
     * @var ConnectionInterface[]
     */
    protected static array $clients = [];

    /**
     * Store client
     * @param ConnectionInterface $connection
     * @param int|null $userId
     */
    public static function add(ConnectionInterface $connection, ?int $userId = null): void
    {
        static::$clients[$userId ?? $connection->getConnectionId()] = $connection;
    }

    /**
     * Check if client exists
     * @param int $userId
     * @return bool
     */
    public static function exists(int $userId): bool
    {
        return array_key_exists($userId, static::$clients);
    }

    /**
     * Get client
     * @param int $userId
     * @return ConnectionInterface|null
     */
    public static function get(int $userId): ?ConnectionInterface
    {
        return static::$clients[$userId] ?? null;
    }

    /**
     * Get all clients
     * @return ConnectionInterface[]
     */
    public static function getAll(): array
    {
        return static::$clients;
    }

    /**
     * Send message to all clients
     *
     * @param array $payload
     * @return void
     */
    public static function send(array $payload): void
    {
        foreach (static::$clients as $client) {
            Response::push($client, $payload['command'], $payload['data']);
        }
    }

    /**
     * Remove client from online list
     * @param ConnectionInterface $connection
     * @param callable|null $callback
     */
    public static function remove(ConnectionInterface $connection, ?callable $callback = null): void
    {
        foreach (static::$clients as $userId => $client) {
            if ($client === $connection) {
                unset(static::$clients[$userId]);

                //Now, let's notify that the user is offline
                if ($callback) $callback();
            }
        }
    }
}