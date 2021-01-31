<?php


namespace App\Servers\Socket;


use App\Core\Servers\SocketServer;
use App\Core\Servers\SocketServerInterface;
use App\Core\Socket\Colis\Matcher;
use App\Core\Socket\ConnectionInterface;
use App\Core\Socket\Payload;
use App\Core\Socket\Request;
use Throwable;

class AdminServer extends SocketServer implements SocketServerInterface
{

    public array $colis;

    public string $prefix = '/ws/admin';


    public function __construct(array $colis)
    {
        $this->colis = $colis;
    }

    /**
     * @inheritDoc
     */
    public function onMessage(ConnectionInterface $connection, Payload $payload): void
    {
        $request = new Request([
            'colis' => $this->colis,
            'client' => $connection,
            'message' => $payload,
            'payload' => $payload,
        ]);

        /**
         * Check if sent command matches any provided listeners
         * If its available the command class will be executed
         */
        Matcher::match($request);
    }

    /**
     * @inheritDoc
     */
    public function onOpen(ConnectionInterface $connection): void
    {
        console(true)->info('Admin connection opened');
    }

    /**
     * @inheritDoc
     */
    public function onClose(ConnectionInterface $connection): void
    {
        console(true)->comment('Admin connection closed');
    }

    /**
     * @inheritDoc
     */
    public function onError(ConnectionInterface $connection, Throwable $exception): void
    {
        // TODO: Implement onError() method.
    }
}