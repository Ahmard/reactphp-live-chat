<?php

namespace Server\Database;

use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Factory;

class Connection
{
    protected static Factory $connection;

    protected static DatabaseInterface $database;

    /**
     * Get database connection
     * @return DatabaseInterface
     */
    public static function get(): DatabaseInterface
    {
        if (!isset(self::$database)) {
            return self::create();
        }

        return self::$database;
    }

    /**
     * Create database connection
     * @return DatabaseInterface
     */
    public static function create(): DatabaseInterface
    {
        if (!isset(self::$connection)) {
            self::$connection = new Factory(getLoop());
        }

        if (!isset(self::$database)) {
            self::$database = self::$connection->openLazy($_ENV['DB_FILE']);
        }

        return self::$database;
    }
}