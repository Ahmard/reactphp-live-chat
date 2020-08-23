<?php

namespace App\Core;

interface MessageComponentInterface
{
    public function onMessage(ConnectionInterface $connection, $message);

    public function onOpen(ConnectionInterface $connection);

    public function onClose(ConnectionInterface $connection);

    public function onError(ConnectionInterface $connection, \Exception $exception);
}