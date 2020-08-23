<?php
namespace App\Core;

use Voryx\WebSocketMiddleware\WebSocketConnection;

interface ConnectionInterface
{
    public static function init(WebSocketConnection $connection): self;

    public function send($payload);

    public function close();

    public function getConnection(): WebSocketConnection;
}