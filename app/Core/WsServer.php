<?php

namespace App\Core;

use App\Core\Servers\SocketServerInterface;
use App\Core\Socket\Payload;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ratchet\RFC6455\Messaging\Message;
use Throwable;
use Voryx\WebSocketMiddleware\WebSocketConnection;

class WsServer
{
    protected SocketServerInterface $handler;

    public function __construct(SocketServerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(
        WebSocketConnection $conn,
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $constructedConnection = ConnectionFactory::init($conn);

        $this->handler->onOpen($constructedConnection);

        $conn->on('message', function (Message $message) use ($constructedConnection) {
            $constructedPayload = Payload::init($message->getPayload());
            $this->handler->onMessage($constructedConnection, $constructedPayload);
        });

        $conn->on('error', function (Throwable $e) use ($constructedConnection) {
            $this->handler->onError($constructedConnection, $e);
        });

        $conn->on('close', function () use ($constructedConnection) {
            $this->handler->onClose($constructedConnection);
        });
    }
}
