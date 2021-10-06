<?php

namespace Server\Database;

use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Factory;
use React\EventLoop\Loop;

class Connection
{
    protected static Factory $connection;

    protected static DatabaseInterface $database;

    /**
     * Gets database connection
     *
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
     *
     * @return DatabaseInterface
     */
    public static function create(): DatabaseInterface
    {
        if (!isset(self::$database)) {
            self::$database = self::getFactory()->openLazy($_ENV['DB_FILE']);
        }

        return self::$database;
    }

    /**
     * Returns sqlite connection factory
     *
     * @return Factory
     */
    public static function getFactory(): Factory
    {
        if (!isset(self::$connection)) {
            return self::$connection = new Factory(Loop::get());
        }

        return self::$connection;
    }
}