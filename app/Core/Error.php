<?php


namespace App\Core;


use Exception;
use Throwable;

class Error extends Exception
{
    protected Throwable $exception;

    public static function create($exception)
    {
        if ($_ENV['APP_ENVIRONMENT'] == 'development') {
            return new self($exception);
        }

        return new self('Server ran in to an error while processing your request.');
    }
}