<?php
namespace App\Socket;

use Ratchet\ConnectionInterface;

class Request
{
    /**
     * Connected client
     * @var ConnectionInterface
     */
    public $client;

    /**
     * Received json decoded message from client
     */
    public $message;

    public function __construct($objects)
    {
        foreach ($objects as $objName => $object){
            $this->$objName = $object;
        }
    }
}