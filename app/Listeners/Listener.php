<?php
namespace App\Listeners;

use App\Socket\Request;
use Ratchet\ConnectionInterface;

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


    public function _initAndFeed_($objects)
    {
        foreach ($objects as $objectName => $object){
            $this->$objectName = $object;
        }
        
        return $this;
    }
}