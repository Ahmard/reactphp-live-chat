<?php


namespace App\Core\Auth;


use App\Core\Database\Connection;
use Clue\React\SQLite\Result;
use React\Promise\PromiseInterface;
use React\Promise\RejectedPromise;
use Throwable;
use function React\Promise\reject;
use function React\Promise\resolve;

class Auth
{
    protected static bool $isAuthenticated = false;

    protected static self $instance;

    /**
     * Retrieve instance
     * @return static
     */
    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        Auth::handle();

        return static::$instance = new static();
    }

    /**
     * Init checker
     * @return PromiseInterface|RejectedPromise
     */
    public static function handle()
    {
        if (!session()->get('user_id')) {
            static::$isAuthenticated = false;
            return reject(false);
        }

        return Connection::create()
            ->query('SELECT id, password FROM users WHERE id = ?', [session()->get('user_id')])
            ->then(function (Result $result) {
                static::$isAuthenticated = true;
                return resolve(true);
            })
            ->otherwise(function (Throwable $error) {
                static::$isAuthenticated = false;
                return reject(false);
            });
    }

    /**
     * Check if user is authenticated
     * @return bool
     */
    public static function check()
    {
        return self::$isAuthenticated;
    }
}