<?php


namespace App\Core\Servers;


class SocketServer
{
    /**
     * Command listeners
     * @var array
     */
    public array $colis = [];

    /**
     * SocketServer server prefix
     * @var string
     */
    public string $prefix = '';
}