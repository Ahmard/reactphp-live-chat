<?php

namespace App\Socket\Listeners\Chat\PublicChat;

use App\Core\ConnectionInterface;
use App\Core\Socket\Request;
use App\Socket\Listeners\Listener;

class ChatListener extends Listener
{
    public $userId;

    public function leave(Request $request)
    {
        $this->removeUser($request->client);
    }

    public static function removeUser(ConnectionInterface $client)
    {
        $storedClient = chatClients()[$client->resourceId] ?? null;
        if ($storedClient) {
            self::sendToAll($client, [
                'command' => 'chat.public.left',
                'data' => [
                    'client_id' => $client->resourceId,
                    'name' => $storedClient['name'],
                ]
            ]);
            //Remove client from list of chat clients
            unset(chatClients()[$client->resourceId]);

            console()->write("\n[#] {$storedClient['name']}({$client->resourceId}) left {$storedClient['room']}.", 'light_yellow');
        }
    }

    protected static function sendToAll(ConnectionInterface $currentClient, array $message)
    {
        $storedClient = chatClients()[$currentClient->resourceId];

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

    public function join(Request $request)
    {
        $client = $request->client;
        $message = $request->message;

        console()->write("\n[#] {$message->name}({$client->resourceId}) joined {$message->room}.", 'yellow');
        //Notify users in the group that new user joined
        $roomClients = chatRooms($message->room);
        foreach ($roomClients as $connectedClient) {
            resp($connectedClient)->send('chat.public.user-joined', [
                [
                    'client_id' => $client->resourceId,
                    'name' => $message->name,
                ]
            ]);
        }

        //Send list of connected clients to connected user
        $roomPeople = [];
        foreach ($roomClients as $chatClient) {
            $theClient = chatClients()[$chatClient->resourceId] ?? null;
            if ($theClient) {
                $roomPeople[] = [
                    'client_id' => $chatClient->resourceId,
                    'name' => $theClient['name'],
                ];
            }
        }

        //Add client to the clients list
        $this->storeClient($request);

        //Notify user that he joined the requested group
        resp($client)->send('chat.public.joined');

        //Send user list of users in current group
        resp($client)->send('chat.public.user-joined', $roomPeople);
    }

    protected function storeClient(Request $request)
    {
        $client = $request->client;
        $message = $request->message;

        chatClients($client, [
            'name' => $message->name,
            'room' => $message->room,
        ]);

        chatRooms($message->room, $client);
    }

    public function send(Request $request)
    {
        $message = $request->message;
        $client = $request->client;

        $storedClient = chatClients()[$client->resourceId];

        if ($storedClient) {
            self::sendToAll($client, [
                'command' => 'chat.public.send',
                'data' => [
                    'user' => $storedClient['name'],
                    'message' => $message->message
                ],
            ]);
        }
    }

    public function typing(Request $request)
    {
        $client = $request->client;

        $storedClient = chatClients()[$client->resourceId];

        if ($storedClient) {
            self::sendToAll($client, [
                'command' => 'chat.public.typing',
                'data' => [
                    'client_id' => $client->resourceId,
                    'user' => $storedClient['name'],
                    'status' => 'typing'
                ],
            ]);
        }
    }
}
