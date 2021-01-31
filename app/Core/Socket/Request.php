<?php

namespace App\Core\Socket;

use App\Core\Auth\Auth;
use App\Core\Socket\Colis\TheColis;
use stdClass;

final class Request
{
    private ConnectionInterface $client;

    private Payload $payload;

    private array $colis;

    private Auth $auth;

    public function __construct(array $objects)
    {
        foreach ($objects as $objName => $object) {
            $this->$objName = $object;
        }
    }

    public static function init(array $objects): Request
    {
        return new Request($objects);
    }

    /**
     * Authentication class
     * @return Auth* @var Auth $auth
     */
    public function auth(): Auth
    {
        return $this->auth;
    }

    /**
     * Command listeners
     * @return TheColis[]
     */
    public function colis(): array
    {
        return $this->colis;
    }

    /**
     * Received json decoded message from client
     * @return Payload|stdClass
     */
    public function payload()
    {
        return $this->payload;
    }

    /**
     * Connected client
     * @return ConnectionInterface
     */
    public function client(): ConnectionInterface
    {
        return $this->client;
    }
}