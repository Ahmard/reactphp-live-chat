<?php

namespace App\Core\Socket;

use App\Core\ConnectionInterface;
use stdClass;

class Request
{
    /**
     * Connected client
     * @var ConnectionInterface
     */
    public $client;

    /**
     * Received json decoded message from client
     * @var stdClass
     */
    public $message;

    /**
     * Received json decoded message from client
     * @var Payload|stdClass
     */
    public $payload;

    /**
     * Command listeners
     * @var array
     */
    public array $colis;

    public function __construct($objects)
    {
        foreach ($objects as $objName => $object) {
            $this->$objName = $object;
        }
    }
}