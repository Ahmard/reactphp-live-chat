<?php


namespace App\Websocket;


use Server\Websocket\ConnectionInterface;

class UserStorage
{
    /**
     * @var ConnectionInterface[]
     */
    protected static array $clients = [];

    /**
     * Store client
     * @param int $userId
     * @param ConnectionInterface $connection
     */
    public static function add(int $userId, ConnectionInterface $connection): void
    {
        self::$clients[$userId] = $connection;
    }

    /**
     * Check if client exists
     * @param int $userId
     * @return bool
     */
    public static function exists(int $userId): bool
    {
        return array_key_exists($userId, self::$clients);
    }

    /**
     * Get client
     * @param int $userId
     * @return ConnectionInterface|null
     */
    public static function get(int $userId): ?ConnectionInterface
    {
        return self::$clients[$userId] ?? null;
    }

    /**
     * Get all clients
     * @return ConnectionInterface[]
     */
    public static function getAll(): array
    {
        return self::$clients;
    }

    /**
     * Remove client from online list
     * @param ConnectionInterface $connection
     */
    public static function remove(ConnectionInterface $connection): void
    {
        foreach (self::$clients as $userId => $client) {
            if ($client === $connection) {
                unset(self::$clients[$userId]);

                //Now, let's notify that the user is offline
                UserPresence::iamOffline($userId);
            }
        }
    }
}