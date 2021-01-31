<?php


namespace App\Core;


use Exception;
use Throwable;

class Error extends Exception
{
    protected Throwable $exception;


    public static function create(?Throwable $exception): Error
    {
        if ($_ENV['APP_ENVIRONMENT'] == 'development') {
            return new Error($exception);
        }

        return new Error('Server ran in to an error while processing your request.');
    }
}