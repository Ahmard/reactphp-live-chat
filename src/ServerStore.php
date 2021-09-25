<?php


namespace Server;

use ArrayObject;


final class ServerStore extends ArrayObject
{
    protected static self $instance;
    protected ArrayObject $timers;

    public function __construct()
    {
        parent::__construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator");

        $this->timers = new ArrayObject($_ENV);
    }

    public static function getInstance(): ServerStore
    {
        if (isset(ServerStore::$instance)) {
            return ServerStore::$instance;
        }

        return ServerStore::$instance = new ServerStore();
    }

    /**
     * @return ArrayObject
     */
    public function timers(): ArrayObject
    {
        return $this->timers;
    }
}