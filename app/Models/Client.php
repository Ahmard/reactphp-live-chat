<?php


namespace App\Models;


use App\Core\Socket\ConnectionInterface;

class Client
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
    public static function add(int $userId, ConnectionInterface $connection)
    {
        self::$clients[$userId] = $connection;
    }

    /**
     * Check if client exists
     * @param int $userId
     * @return bool
     */
    public static function exists(int $userId)
    {
        return array_key_exists($userId, self::$clients);
    }

    /**
     * Get client
     * @param int $userId
     * @return ConnectionInterface|null
     */
    public static function get(int $userId)
    {
        return self::$clients[$userId] ?? null;
    }

    /**
     * Get all clients
     * @return ConnectionInterface[]
     */
    public static function getAll()
    {
        return self::$clients;
    }

    /**
     * Remove client from online list
     * @param ConnectionInterface $connection
     */
    public static function remove(ConnectionInterface $connection)
    {
        foreach (self::$clients as $userId => $client){
            if($client === $connection){
                unset(self::$clients[$userId]);
            }
        }
    }
}