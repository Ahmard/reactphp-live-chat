<?php


namespace Server\Servers;


use Server\Websocket\ConnectionInterface;
use Server\Websocket\Payload;
use Throwable;

interface SocketServerInterface
{
    /**
     * Method that will be called when message is received
     * @param ConnectionInterface $connection
     * @param Payload $payload
     * @return void
     */
    public function onMessage(ConnectionInterface $connection, Payload $payload): void;

    /**
     * Method that will be called when new connection is received
     * @param ConnectionInterface $connection
     * @return void
     */
    public function onOpen(ConnectionInterface $connection): void;

    /**
     * Message that will be called when connection is closed
     * @param ConnectionInterface $connection
     * @return void
     */
    public function onClose(ConnectionInterface $connection): void;

    /**
     * Method that will be called when an error occurred
     * @param ConnectionInterface $connection
     * @param Throwable $exception
     * @return void
     */
    public function onError(ConnectionInterface $connection, Throwable $exception): void;
}