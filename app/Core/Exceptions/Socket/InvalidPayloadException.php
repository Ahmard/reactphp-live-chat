<?php


namespace App\Core\Exceptions\Socket;


use Exception;

class InvalidPayloadException extends Exception
{
    public static function create(string $message): InvalidPayloadException
    {
        throw new InvalidPayloadException($message);
    }
}