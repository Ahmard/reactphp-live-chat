<?php

namespace App\Socket;

use Exception;
use App\Core\Colis\Matcher;
use App\Core\ConnectionInterface;
use App\Core\MessageComponentInterface;
use SplObjectStorage;

class Server implements MessageComponentInterface
{
    protected SplObjectStorage $connections;

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
        $this->connections = clientStorage();

        $this->monitorClients();
    }

    public function monitorClients()
    {
        event()->on('system.pong', function($connection){
            unset($this->lastUnansweredPings[$connection->resourceId]);
        });
        
        if($_ENV['WILL_PING_CLIENTS'] == 'true'){
            setInterval($_ENV['CLIENT_PING_INTERVAL'], function ($interval) {
                static $loop = 1;
                $totalClients = count($this->connections);
                echo "\n[#] Pinging {$totalClients} clients: #round {$loop}";
                foreach (clientStorage() as $connection) {
                    //Check if current client does not reply our last pinging
                    $connectionExists = $this->lastUnansweredPings[$connection->resourceId] ?? false;
                    if($connectionExists){
                        $this->write("\n\n" . date('H:i:s'));
                        $this->write(color("\n -> Connection({$connection->resourceId}) failed to reply its last ping, so it has been disconnected.\n")->fg('light_red'));
                        
                        $this->closeAction($connection);
                        
                        //close client connection
                        $connection->close();
                    }else{
                        resp($connection)->send('system.ping');
        
                        $this->lastUnansweredPings[$connection->resourceId] = true;
                    }
                    
                }
                $loop++;
            });
        }
    }
    
    public function write($data, $condition = false)
    {
        if(is_string($condition)){
            $condition = $_ENV[$condition];
        }
        
        if($_ENV['SHOW_CLIENT_DEBUG_INFO'] == 'true'){
            if($condition) console()->write($data);
        }
        return $this;
    }

    public function onOpen(ConnectionInterface $connection)
    {
        // Store the new connection to send messages to later
        $this->connections->attach($connection);
        
        $this->currClient = $connection;
        
        //Let client know within which time interval we'll send ping message
        setTimeout(0.1, function () use($connection){
            resp($connection)->send('system.ping.interval', 10);
        });

        $this->write("\n\n" . date('H:i:s'));
        $this->write(color( "\n -> Connection({$connection->resourceId}) Established.\n")->fg('light_yellow'));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if($_ENV['SHOW_SOCKET_IMCOMING_MESSAGES'] == 'true'){
            $this->write("\n\n" . date('H:i:s'));
            $this->write(color("\n -> Message Recieved({$from->resourceId}): {$msg}\n")->fg('light_blue'));
        }
        
        $request = new Request([
            'colis' => $this->colis,
            'clients' => $this->connections,
            'client' => $from,
            'message' => $msg
        ]);

        /**
         * Check if sent command matches any provided listeners
         * If its available the command class will be executed
         */
        Matcher::match($request);
    }

    protected function closeAction(ConnectionInterface $connection){
        //remove client from list of connected clients
        $this->connections->detach($connection);
        
        event()->emit('chat.public.removeUser', [$connection]);
    }

    public function onClose(ConnectionInterface $connection)
    {
        $this->closeAction($connection);
        
        $this->write("\n\n" . date('H:i:s'));
        $this->write(color("\n -> Connection({$connection->resourceId}): disconnected.\n")->fg('light_red'));
    }

    public function onError(ConnectionInterface $connection, Exception $exception)
    {
        $this->write("\n\n" . date('H:i:s'));
        $this->write("\n[*] Error: {$exception->getMessage()} \n=> {$exception->getFile()} \n@ Line {$exception->getLine()}\n");

        $connection->close();
    }
}