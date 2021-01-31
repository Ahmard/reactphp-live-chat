<?php


namespace App\Core\Socket\Colis;


use App\Core\Socket\Request;
use Exception;

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