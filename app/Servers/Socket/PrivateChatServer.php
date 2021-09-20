<?php


namespace App\Servers\Socket;


use Server\Auth\Auth;
use Server\Servers\SocketServer;
use Server\Servers\SocketServerInterface;
use Server\Socket\Colis\Dispatcher;
use Server\Socket\ConnectionInterface;
use Server\Socket\Payload;
use Server\Socket\Request;
use Throwable;

class PrivateChatServer extends SocketServer implements SocketServerInterface
{

    public string $prefix;

    public function __construct()
    {
        $this->prefix = $_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX'];
    }

    /**
     * @inheritDoc
     */
    public function onMessage(ConnectionInterface $connection, Payload $payload): void
    {
        Auth::handle($payload->token)->then(function (Auth $auth) use ($connection, $payload) {
            $request = Request::init([
                'colis' => $this->colis,
                'client' => $connection,
                'message' => $payload,
                'payload' => $payload,
                'auth' => $auth
            ]);

            Dispatcher::dispatch($request);
        });
    }

    /**
     * @inheritDoc
     */
    public function onOpen(ConnectionInterface $connection): void
    {
        console(true)->info('New private chat connection: ' . $connection->getConnectionId());
    }

    /**
     * @inheritDoc
     */
    public function onClose(ConnectionInterface $connection): void
    {
        event()->emit('chat.private.user-left', [$connection]);
    }

    /**
     * @inheritDoc
     */
    public function onError(ConnectionInterface $connection, Throwable $exception): void
    {
        event()->emit('chat.private.user-left', [$connection]);
    }
}