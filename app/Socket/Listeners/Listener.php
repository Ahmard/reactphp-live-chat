<?php

namespace App\Socket\Listeners;

use App\Core\ConnectionInterface;
use App\Core\Socket\Request;

class Listener
{
    /**
     * The current client
     * @var ConnectionInterface
     */
    public $client;

    /**
     * The current request object
     * @var Request
     */
    public $request;


    public function _initAndFeed_(array $objects)
    {
        foreach ($objects as $objectName => $object) {
            $this->$objectName = $object;
        }

        return $this;
    }
}