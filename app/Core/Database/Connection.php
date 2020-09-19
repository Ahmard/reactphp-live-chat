<?php

namespace App\Core\Database;

use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Factory;
use Clue\React\SQLite\Io\LazyDatabase;

class Connection
{
    protected static $connection;

    protected static $database;

    /**
     * Create database connection
     * @return DatabaseInterface|LazyDatabase
     */
    public static function create()
    {
        if (!self::$connection) {
            self::$connection = new Factory(getLoop());
        }

        if (!self::$database) {
            self::$database = self::$connection->openLazy($_ENV['DB_FILE']);
        }

        return self::$database;
    }

    /**
     * Get database connection
     * @return DatabaseInterface
     */
    public static function get()
    {
        return self::$database;
    }
}