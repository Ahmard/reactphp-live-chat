<?php

namespace App\Socket;

use Exception;
use App\CommandLine;
use App\Core\Colis\Matcher;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class Server implements MessageComponentInterface
{
    protected SplObjectStorage $clients;

    protected array $colis;

    protected ConnectionInterface $currClient;

    protected $handler;

    protected array $lastUnansweredPings = [];


    public function __construct($colis)
    {
        $this->colis = $colis;
    }

    public function init($argv)
    {
        //Listen to any arguments or options passed fron cli
        //CommandLine::listen($argv);

        $this->clients = clientStorage();

        $this->monitorClients();
    }

    public function monitorClients()
    {
        event()->on('system.pong', function($client){
            $clientArrKey = array_search($client->resourceId, $this->lastUnansweredPings);
            unset($this->lastUnansweredPings[$clientArrKey]);
        });

        setInterval(10, function ($interval) {
            static $loop = 1;
            foreach (clientStorage() as $client) {
                resp($client)->send('system.ping');

                $this->lastUnansweredPings[] = $client->resourceId;
            }
            //echo "\n[#] Pinging clients: #{$loop}";
            $loop++;
        });
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        //Notify all users

        $this->currClient = $conn;

        echo "\n\n" . date('H:i:s');
        echo color( "\n -> Connection({$conn->resourceId}) Established.\n")->fg('light_yellow');
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "\n\n" . date('H:i:s');
        echo color("\n -> Message Recieved({$from->resourceId}): {$msg}\n")->fg('light_blue');

        $request = new Request([
            'colis' => $this->colis,
            'clients' => $this->clients,
            'client' => $from,
            'message' => $msg
        ]);

        /**
         * Check if sent command matches any provided listeners
         * If its available the command class will be executed
         */
        Matcher::match($request);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        event()->emit('chat.public.removeUser', [$conn]);

        echo "\n\n" . date('H:i:s');
        echo color("\n -> Connection({$conn->resourceId}): disconnected.\n")->fg('light_red');
    }

    public function onError(ConnectionInterface $conn, Exception $exception)
    {
        echo "\n\n" . date('H:i:s');
        echo "\n[*] Error: {$exception->getMessage()} \n=> {$exception->getFile()} \n@ Line {$exception->getLine()}\n";

        $conn->close();
    }
}