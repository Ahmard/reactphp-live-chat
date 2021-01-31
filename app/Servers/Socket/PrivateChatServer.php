<?php


namespace App\Servers\Socket;


use App\Core\Auth\Auth;
use App\Core\Servers\SocketServer;
use App\Core\Servers\SocketServerInterface;
use App\Core\Socket\Colis\Dispatcher;
use App\Core\Socket\ConnectionInterface;
use App\Core\Socket\Payload;
use App\Core\Socket\Request;
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