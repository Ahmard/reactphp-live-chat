<?php

namespace Server\Socket;

use Voryx\WebSocketMiddleware\WebSocketConnection;

class ConnectionFactory implements ConnectionInterface
{
    protected int $resourceId;

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
        if (!is_string($payload)) {
            $payload = json_encode($payload);
        }

        $this->connection->send($payload);
    }

    public function close()
    {
        $this->connection->close();
    }

    public function getConnectionId(): int
    {
        return $this->resourceId;
    }

    public function getConnection(): WebSocketConnection
    {
        return $this->connection;
    }
}