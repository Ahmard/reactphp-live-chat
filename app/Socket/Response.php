<?php

namespace App\Socket;

use Ratchet\ConnectionInterface;

class Response
{
    protected $message;

    protected $client;

    public function __construct(ConnectionInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Send message to client
     * @param string $command
     * @param mixed $message
     * @return Response $this
     */
    public function send(string $command, $message = null): Response
    {
        $jsonMessage = json_encode([
            'command' => $command,
            'message' => $message,
            'time' => time(),
        ]);
        //Send the json encoded message to client
        $this->client->send($jsonMessage);

        return $this;
    }
}