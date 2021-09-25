<?php


namespace Server\Socket\Colis;


use Exception;
use Server\Socket\Request;

class Dispatcher
{
    public static function dispatch(Request $request): void
    {
        //Listen to ready to call listener
        try {
            Matcher::match($request);
        } catch (Exception $e) {
            handleApplicationException($e);
        }
    }
}