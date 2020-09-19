<?php

namespace App\Core;

use Voryx\WebSocketMiddleware\WebSocketConnection;

class ConnectionFactory implements ConnectionInterface
{
    public int $resourceId;
    protected WebSocketConnection $connection;

    public function __construct(WebsocketConnection $connection)
    {
        $this->connection = $connection;

        $this->resourceId = spl_object_id($connection);
    }

    public static function init(WebSocketConnection $connection): self
    {
        return new self($connection);
    }

    public function send($payload)
    {
        $this->connection->send($payload);
    }

    public function close()
    {
        $this->connection->close();
    }

    public function getConnection(): WebSocketConnection
    {
        return $this->connection;
    }
}