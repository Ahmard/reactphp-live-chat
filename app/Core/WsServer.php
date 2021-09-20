<?php

namespace App\Core;

use App\Core\Exceptions\Socket\InvalidPayloadException;
use App\Core\Servers\SocketServerInterface;
use App\Core\Socket\ConnectionFactory;
use App\Core\Socket\Payload;
use App\Providers\SocketServiceProvider;
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
        WebSocketConnection    $connection,
        ServerRequestInterface $request,
        ResponseInterface      $response
    ): void
    {
        SocketServiceProvider::init()->boot();

        $constructedConnection = ConnectionFactory::init($connection);

        $this->handler->onOpen($constructedConnection);

        $connection->on('message', function (Message $message) use ($constructedConnection) {
            try {
                $constructedPayload = Payload::init($message->getPayload());
                $this->handler->onMessage($constructedConnection, $constructedPayload);
            } catch (InvalidPayloadException $payloadException) {
                $constructedConnection->send([
                    'command' => 'system.response.500',
                    'message' => $payloadException->getMessage()
                ]);
            }
        });

        $connection->on('error', function (Throwable $e) use ($constructedConnection) {
            $this->handler->onError($constructedConnection, $e);
        });

        $connection->on('close', function () use ($constructedConnection) {
            $this->handler->onClose($constructedConnection);
        });
    }
}
