<?php

namespace App\Socket\Listeners;

use App\Core\Socket\ConnectionInterface;
use App\Core\Socket\Request;

class Listener
{
    /**
     * The current client
     * @var ConnectionInterface
     */
    public ConnectionInterface $client;

    /**
     * The current request object
     * @var Request
     */
    public Request $request;


    public function _initAndFeed_(array $objects): Listener
    {
        foreach ($objects as $objectName => $object) {
            $this->$objectName = $object;
        }

        return $this;
    }
}