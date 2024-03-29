<?php

namespace App\Servers\Websocket;

use Exception;
use React\EventLoop\Loop;
use Server\Servers\SocketServer;
use Server\Servers\SocketServerInterface;
use Server\Websocket\Colis\Dispatcher;
use Server\Websocket\ConnectionInterface;
use Server\Websocket\Payload;
use Server\Websocket\Request;
use SplObjectStorage;
use Throwable;

class PublicChatServer extends SocketServer implements SocketServerInterface
{
    public array $colis;

    public string $prefix;

    protected SplObjectStorage $connections;
    protected ConnectionInterface $currClient;

    protected array $lastUnansweredPings = [];

    public function __construct(array $colis)
    {
        $this->prefix = $_ENV['PUBLIC_CHAT_SOCKET_URL_PREFIX'];

        $this->colis = $colis;

        $this->connections = clientStorage();

        $this->monitorClients();
    }

    public function monitorClients(): void
    {
        if ($_ENV['WILL_PING_CLIENTS'] == 'true') {
            Loop::addPeriodicTimer($_ENV['CLIENT_PING_INTERVAL'],  function () {
                static $loop = 1;
                $totalClients = count($this->connections);

                $this->write(
                    "\n[#] Pinging {$totalClients} clients: #round {$loop}",
                    $_ENV['SHOW_CLIENT_PING_MESSAGE']
                );

                foreach (clientStorage() as $connection) {
                    //Check if current client does not reply our last pinging
                    $connectionExists = $this->lastUnansweredPings[$connection->getConnectionId()] ?? false;
                    if ($connectionExists) {
                        $this->write("\n\n" . date('H:i:s'), $_ENV['SHOW_CLIENT_PING_MESSAGE']);
                        $this->write(
                            color("\n -> Connection({$connection->getConnectionId()}) failed to reply its last ping, so it has been disconnected.\n")
                                ->fg('light_red')
                        );

                        $this->closeAction($connection);

                        //close client connection
                        $connection->close();
                    } else {
                        resp($connection)->send('system.ping');

                        $this->lastUnansweredPings[$connection->getConnectionId()] = true;
                    }

                }
                $loop++;
            });
        }
    }

    /**
     * Display message to console if certain condition returns true
     * @param mixed $data
     * @param bool $condition
     * @return $this
     */
    public function write($data, bool $condition = false): PublicChatServer
    {
        if ($condition == 'true') {
            console(true)->write($data);
        }

        return $this;
    }

    protected function closeAction(ConnectionInterface $connection): void
    {
        //remove client from list of connected clients
        $this->connections->detach($connection);

        event()->emit('chat.public.removeUser', [$connection]);
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        // Store the new connection to send messages to later
        $this->connections->attach($connection);

        $this->currClient = $connection;

        //Let client know within which time interval we'll send ping message
        Loop::addTimer(1, function () use ($connection) {
            resp($connection)->send('system.ping.interval', $_ENV['CLIENT_PING_INTERVAL']);
        });

        $this->write("\n" . date('H:i:s'));
        $this->write(color("\n -> Connection({$connection->getConnectionId()}) Established.\n")->fg('light_yellow'));
    }

    public function onMessage(ConnectionInterface $connection, Payload $payload): void
    {
        if ($_ENV['SHOW_SOCKET_INCOMING_MESSAGES'] == 'true') {
            //We don't want to display unnecessary logs
            if (
                $payload->command != 'system.ping'
                && $payload->command != 'system.pong'
                && $payload->command != 'system.ping.interval'
            ) {
                $this->write("\n" . date('H:i:s'), true);
                $this->write(color(" -> Message Received({$connection->getConnectionId()}): {$payload->getOriginalPayload()}")->fg('light_blue'), true);
            }
        }

        if ('system.pong' == $payload->command) {
            unset($this->lastUnansweredPings[$connection->getConnectionId()]);
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
        try {
            Dispatcher::dispatch($request);
        } catch (Exception $e) {
            handleApplicationException($e);
        }
    }

    public function onClose(ConnectionInterface $connection): void
    {
        $this->closeAction($connection);

        $this->write("\n\n" . date('H:i:s'));
        $this->write(color("\n -> Connection({$connection->getConnectionId()}): disconnected.\n")->fg('light_red'));
    }

    public function onError(ConnectionInterface $connection, Throwable $exception): void
    {
        $this->write("\n\n" . date('H:i:s'));
        $this->write("\n[*] Error: {$exception->getMessage()} \n=> {$exception->getFile()} \n@ Line {$exception->getLine()}\n");

        $connection->close();
    }
}