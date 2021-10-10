<?php

namespace Server\Websocket;

class Response
{
    protected ConnectionInterface $client;

    public function __construct(ConnectionInterface $client)
    {
        $this->client = $client;
    }

    public function internalServerError(): Response
    {
        return resp($this->client)->send('system.500', 'Internal server error');
    }

    /**
     * Send message to client
     * @param string $command
     * @param mixed $message
     * @return $this
     */
    public function send(string $command, $message = null): Response
    {
        $jsonMessage = json_encode([
            'command' => $command,
            'message' => $message,
            'time' => time(),
        ]);

        if ($_ENV['SHOW_SOCKET_OUTGOING_MESSAGES'] == 'true') {
            if (
                //We don't want to display unnecessary logs
                (
                    $command != 'system.ping'
                    && $command != 'system.pong'
                    && $command != 'system.ping.interval'
                )
                //If we don't want to display system logs
                && (
                $_ENV['SILENCE_SYSTEM_MESSAGES'] == 'true' ?:
                    substr($command, 0, 6) != 'system'
                )
            ) {
                console(true)->write("\n" . date('H:i:s'));
                console(true)->write(" -> Sending({$jsonMessage}) to UserStorage({$this->client->getConnectionId()}).\n");
            }
        }

        //Send the json encoded message to client
        $this->client->send($jsonMessage);

        return $this;
    }
}