<?php


namespace App\Models;


use React\Promise\PromiseInterface;
use Server\Database\Connection;

class User extends Model
{
    public static string $username = '';
    public static string $email = '';
    public static string $token = '';
    public static string $type = '';
    public string $table = 'user';
    public int $id;

    public static function getToken(): string
    {
        return self::$token;
    }

    public static function setToken(int $userId, string $token): PromiseInterface
    {
        self::$token = $token;
        return Connection::get()
            ->query('UPDATE users SET token = ? WHERE id = ?', [$token, $userId]);
    }
}