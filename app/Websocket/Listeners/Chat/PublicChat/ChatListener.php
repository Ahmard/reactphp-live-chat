<?php

namespace App\Websocket\Listeners\Chat\PublicChat;

use App\Websocket\Listeners\Listener;
use App\Websocket\Models\Client;
use App\Websocket\Room;
use Server\Websocket\ConnectionInterface;
use Server\Websocket\Payload;
use Server\Websocket\Request;
use Server\Websocket\Response;
use stdClass;

class ChatListener extends Listener
{
    public float $userTypingTimeout = 2000;

    public function leave(Request $request): void
    {
        $this->removeUser($request->client());
    }

    public static function removeUser(ConnectionInterface $client): void
    {
        $storedClient = chatClients()[$client->getConnectionId()] ?? null;
        if ($storedClient) {
            self::sendToAll($client, [
                'command' => 'chat.public.left',
                'data' => [
                    'client_id' => $client->getConnectionId(),
                    'name' => $storedClient['name'],
                ]
            ]);

            //Remove client from list of chat clients
            unset(chatClients()[$client->getConnectionId()]);

            console()->write("\n[#] {$storedClient['name']}({$client->getConnectionId()}) left {$storedClient['room']}.", 'light_yellow');
        }
    }

    protected static function sendToAll(ConnectionInterface $currentClient, array $message): void
    {
        $storedClient = chatClients()[$currentClient->getConnectionId()];

        if ($storedClient) {
            $clientRoom = $storedClient['room'];

            $roomClients = chatRooms($clientRoom);

            foreach ($roomClients as $roomClient) {
                if ($roomClient !== $currentClient) {
                    resp($roomClient)->send($message['command'], $message['data']);
                }
            }
        }
    }

    public function join(Request $request): void
    {
        $client = $request->client();
        /**@var Payload|stdClass $message ;* */
        $message = $request->payload()->message;

        console()->write("\n[#] {$message->name}({$client->getConnectionId()}) joined {$message->room}.", 'yellow');

        Room::send($message->room, 'chat.public.user-joined', [
            [
                'client_id' => $client->getConnectionId(),
                'name' => $message->name,
            ]
        ]);

        //Send list of connected clients to connected user
        $roomPeople = [];
        foreach (Room::all($message->room) as $chatClient) {
            $theClient = Client::get($chatClient->getConnectionId());

            if ($theClient) {
                $roomPeople[] = [
                    'client_id' => $chatClient->getConnectionId(),
                    'name' => $theClient['name'],
                ];
            }
        }

        //Add client to the clients list
        $this->storeClient($request);

        //Notify user that he joined the requested group
        Response::push($client, 'chat.public.joined');

        //Send user list of users in current group
        Response::push($client, 'chat.public.user-joined', $roomPeople);
    }

    protected function storeClient(Request $request): void
    {
        $client = $request->client();
        $message = $request->payload()->message;

        chatClients($client, [
            'name' => $message->name,
            'room' => $message->room,
        ]);

        chatRooms($message->room, $client);
    }

    public function send(Request $request): void
    {
        $message = $request->payload()->message;
        $client = $request->client();

        $storedClient = chatClients()[$client->getConnectionId()];

        if ($storedClient) {
            self::sendToAll($client, [
                'command' => 'chat.public.send',
                'data' => [
                    'user' => $storedClient['name'],
                    'client_id' => $client->getConnectionId(),
                    'message' => $message
                ],
            ]);
        }
    }

    public function typing(Request $request): void
    {
        $client = $request->client();

        $storedClient = chatClients()[$client->getConnectionId()];

        if ($storedClient) {

            $data = [
                'client_id' => $client->getConnectionId(),
                'user' => $storedClient['name'],
                'status' => 'typing',
                'timeout' => $this->userTypingTimeout,
            ];

            //Let's see if user is typing or stopped typing
            if ($request->payload()->message->status !== 'typing') {
                $data['status'] = 'stopped';
            }

            self::sendToAll($client, [
                'command' => 'chat.public.typing',
                'data' => $data,
            ]);
        }
    }
}
