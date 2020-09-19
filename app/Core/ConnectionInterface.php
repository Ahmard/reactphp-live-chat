<?php

namespace App\Core;

use Voryx\WebSocketMiddleware\WebSocketConnection;

interface ConnectionInterface
{
    /**
     * Initialize connection object
     * @param WebSocketConnection $connection
     * @return $this
     */
    public static function init(WebSocketConnection $connection): self;

    /**
     * Send message to client
     * @param $payload
     * @return mixed
     */
    public function send($payload);

    /**
     * Close client connection
     * @return mixed
     */
    public function close();

    /**
     * Get connection instance
     * @return WebSocketConnection
     */
    public function getConnection(): WebSocketConnection;
}