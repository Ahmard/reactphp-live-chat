<?php


namespace Server\Websocket\Colis;


use Exception;
use Server\Websocket\Request;

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