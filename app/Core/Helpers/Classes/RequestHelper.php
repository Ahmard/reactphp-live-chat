<?php


namespace App\Core\Helpers\Classes;


use Psr\Http\Message\ServerRequestInterface;

class RequestHelper
{
    protected static ServerRequestInterface $request;

    protected static self $instance;

    public static function setRequest(ServerRequestInterface $request)
    {
        self::$request = $request;
    }

    public static function getInstance(): ServerRequestInterface
    {
        return self::$request;
    }

}