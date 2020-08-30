<?php

namespace App\Socket;

use App\Core\ConnectionInterface;

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
        
        if($_ENV['SHOW_SOCKET_OUTGOING_MESSAGES'] == 'true'){
            console()->write("\n" . date('H:i:s'));
            console()->write(" -> Sending({$message}) to Client({$this->client->resoureId}).\n");
        }
        
        //Send the json encoded message to client
        $this->client->send($jsonMessage);

        return $this;
    }
}