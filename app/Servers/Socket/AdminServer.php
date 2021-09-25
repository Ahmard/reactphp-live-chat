<?php


namespace App\Servers\Socket;


use Server\Servers\SocketServer;
use Server\Servers\SocketServerInterface;
use Server\Socket\Colis\Matcher;
use Server\Socket\ConnectionInterface;
use Server\Socket\Payload;
use Server\Socket\Request;
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