<?php

namespace App;

use App\Core\ConnectionFactory;
use App\Core\ConnectionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\MessageComponentInterface;
use Voryx\WebSocketMiddleware\WebSocketConnection;
use Ratchet\RFC6455\Messaging\Message;

class WsServer
{
    protected MessageComponentInterface $handler;

    private ConnectionInterface $connection;

    public function __construct(MessageComponentInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(
        WebSocketConnection $conn,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $constructedConnection = ConnectionFactory::init($conn);
        
        $this->handler->onOpen($constructedConnection);

        $conn->on('message', function (Message $message) use($constructedConnection){
            $this->handler->onMessage($constructedConnection, $message->getPayload());
        });

        $conn->on('error', function (\Throwable $e) use($constructedConnection){
            $this->handler->onError($constructedConnection, $e);
        });

        $conn->on('close', function () use($constructedConnection){
            $this->handler->onClose($constructedConnection);
        });
    }
}
