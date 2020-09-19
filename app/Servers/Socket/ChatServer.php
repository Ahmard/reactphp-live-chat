<?php

namespace App\Servers\Socket;

use App\Core\Colis\Matcher;
use App\Core\ConnectionInterface;
use App\Core\Servers\SocketServer;
use App\Core\Servers\SocketServerInterface;
use App\Core\Socket\Payload;
use App\Core\Socket\Request;
use React\EventLoop\TimerInterface;
use SplObjectStorage;
use Throwable;

class ChatServer extends SocketServer implements SocketServerInterface
{
    public array $colis;
    public string $prefix = '/chat/public';
    protected SplObjectStorage $connections;
    protected ConnectionInterface $currClient;

    protected array $lastUnansweredPings = [];

    public function __construct(array $colis)
    {
        $this->colis = $colis;

        $this->connections = clientStorage();

        $this->monitorClients();
    }

    public function monitorClients()
    {
        event()->on('system.pong', function ($connection) {
            unset($this->lastUnansweredPings[$connection->resourceId]);
        });

        if ($_ENV['WILL_PING_CLIENTS'] == 'true') {
            setInterval($_ENV['CLIENT_PING_INTERVAL'], function (TimerInterface $interval) {
                static $loop = 1;
                $totalClients = count($this->connections);

                $this->write(
                    "\n[#] Pinging {$totalClients} clients: #round {$loop}",
                    $_ENV['SHOW_CLIENT_PING_MESSAGE']
                );

                foreach (clientStorage() as $connection) {
                    //Check if current client does not reply our last pinging
                    $connectionExists = $this->lastUnansweredPings[$connection->resourceId] ?? false;
                    if ($connectionExists) {
                        $this->write("\n\n" . date('H:i:s'), $_ENV['SHOW_CLIENT_PING_MESSAGE']);
                        $this->write(
                            color("\n -> Connection({$connection->resourceId}) failed to reply its last ping, so it has been disconnected.\n")
                                ->fg('light_red')
                        );

                        $this->closeAction($connection);

                        //close client connection
                        $connection->close();
                    } else {
                        resp($connection)->send('system.ping');

                        $this->lastUnansweredPings[$connection->resourceId] = true;
                    }

                }
                $loop++;
            });
        }
    }

    /**
     * Display message to console if certain condition returns true
     * @param $data
     * @param false $condition
     * @return $this
     */
    public function write($data, $condition = false)
    {
        if ($condition == 'true') {
            console(true)->write($data);
        }
        return $this;
    }

    protected function closeAction(ConnectionInterface $connection)
    {
        //remove client from list of connected clients
        $this->connections->detach($connection);

        event()->emit('chat.public.removeUser', [$connection]);
    }

    public function onOpen(ConnectionInterface $connection)
    {
        // Store the new connection to send messages to later
        $this->connections->attach($connection);

        $this->currClient = $connection;

        //Let client know within which time interval we'll send ping message
        setTimeout(0.1, function () use ($connection) {
            resp($connection)->send('system.ping.interval', $_ENV['CLIENT_PING_INTERVAL']);
        });

        $this->write("\n" . date('H:i:s'));
        $this->write(color("\n -> Connection({$connection->resourceId}) Established.\n")->fg('light_yellow'));
    }

    public function onMessage(ConnectionInterface $connection, Payload $payload)
    {
        if ($_ENV['SHOW_SOCKET_INCOMING_MESSAGES'] == 'true') {
            //We don't want to display unnecessary logs
            if (
                $payload->command != 'system.ping'
                && $payload->command != 'system.pong'
                && $payload->command != 'system.ping.interval'
            ) {
                $this->write("\n" . date('H:i:s'), true);
                $this->write(color(" -> Message Received({$connection->resourceId}): {$payload->getOriginalPayload()}")->fg('light_blue'), true);
            }
        }

        $request = new Request([
            'colis' => $this->colis,
            'clients' => $this->connections,
            'client' => $connection,
            'message' => $payload,
            'payload' => $payload,
        ]);

        /**
         * Check if sent command matches any provided listeners
         * If its available the command class will be executed
         */
        Matcher::match($request);
    }

    public function onClose(ConnectionInterface $connection)
    {
        $this->closeAction($connection);

        $this->write("\n\n" . date('H:i:s'));
        $this->write(color("\n -> Connection({$connection->resourceId}): disconnected.\n")->fg('light_red'));
    }

    public function onError(ConnectionInterface $connection, Throwable $exception)
    {
        $this->write("\n\n" . date('H:i:s'));
        $this->write("\n[*] Error: {$exception->getMessage()} \n=> {$exception->getFile()} \n@ Line {$exception->getLine()}\n");

        $connection->close();
    }
}