<?php


namespace App\Core;

use ArrayObject;


class ServerStore extends ArrayObject
{
    protected static self $instance;
    protected ArrayObject $timers;

    public function __construct()
    {
        parent::__construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator");

        $this->timers = new ArrayObject($_ENV);
    }

    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * @return ArrayObject
     */
    public function timers(): ArrayObject
    {
        return $this->timers;
    }
}