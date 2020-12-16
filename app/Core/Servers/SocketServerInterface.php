<?php


namespace App\Core\Servers;


use App\Core\Socket\ConnectionInterface;
use App\Core\Socket\Payload;
use stdClass;
use Throwable;

interface SocketServerInterface
{
    /**
     * Method that will be called when message is received
     * @param ConnectionInterface $connection
     * @param Payload|stdClass $payload
     * @return mixed
     */
    public function onMessage(ConnectionInterface $connection, Payload $payload);

    /**
     * Method that will be called when new connection is received
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public function onOpen(ConnectionInterface $connection);

    /**
     * Message that will be called when connection is closed
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public function onClose(ConnectionInterface $connection);

    /**
     * Method that will be called when an error occurred
     * @param ConnectionInterface $connection
     * @param Throwable $exception
     * @return mixed
     */
    public function onError(ConnectionInterface $connection, Throwable $exception);
}