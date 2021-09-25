<?php

namespace Server\Socket;

use Voryx\WebSocketMiddleware\WebSocketConnection;

/**
 * Interface ConnectionInterface
 * @package Server\Socket
 */
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
     * @param mixed $payload
     * @return mixed
     */
    public function send($payload);

    /**
     * Close client connection
     * @return mixed
     */
    public function close();

    /**
     * Unique identifier to this connection
     * @return int
     */
    public function getConnectionId(): int;

    /**
     * Get connection instance
     * @return WebSocketConnection
     */
    public function getConnection(): WebSocketConnection;
}