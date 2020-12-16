<?php


namespace App\Core;


use Evenement\EventEmitter as EvenementEventEmitter;
use Evenement\EventEmitterInterface;

class EventEmitter extends EvenementEventEmitter implements EventEmitterInterface
{
    protected static self $instance;
    protected EventEmitterInterface $socketEvent;
    protected EventEmitterInterface $httpEvent;

    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new self();
    }

    /**
     * SocketServer event
     * @return EventEmitter|EventEmitterInterface
     */
    public function socket()
    {
        if (!isset($this->socketEvent)) {
            return $this->socketEvent = new EventEmitter();
        }

        return $this->socketEvent;
    }

    /**
     * HttpServer event
     * @return EventEmitter|EventEmitterInterface
     */
    public function http()
    {
        if (!isset($this->socketEvent)) {
            return $this->socketEvent = new EventEmitter();
        }

        return $this->socketEvent;
    }
}