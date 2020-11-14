<?php

namespace App\Socket\Listeners\Chat\PublicChat;

use App\Core\Socket\ConnectionInterface;
use App\Core\Socket\Payload;
use App\Core\Socket\Request;
use App\Socket\Listeners\Listener;
use stdClass;

class ChatListener extends Listener
{
    public float $userTypingTimeout = 2000;

    public function leave(Request $request)
    {
        $this->removeUser($request->client());
    }

    public static function removeUser(ConnectionInterface $client)
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

    protected static function sendToAll(ConnectionInterface $currentClient, array $message)
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

    public function join(Request $request)
    {
        $client = $request->client();
        /**@var Payload|stdClass $message;* */
        $message = $request->payload();

        console()->write("\n[#] {$message->name}({$client->getConnectionId()}) joined {$message->room}.", 'yellow');

        /**
         * Notify users in the group that new user joined
         * @var ConnectionInterface[] $roomClients
         */
        $roomClients = chatRooms($message->room);

        foreach ($roomClients as $connectedClient) {
            resp($connectedClient)->send('chat.public.user-joined', [
                [
                    'client_id' => $client->getConnectionId(),
                    'name' => $message->name,
                ]
            ]);
        }

        //Send list of connected clients to connected user
        $roomPeople = [];
        foreach ($roomClients as $chatClient) {
            $theClient = chatClients()[$chatClient->getConnectionId()] ?? null;
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
        resp($client)->send('chat.public.joined');

        //Send user list of users in current group
        resp($client)->send('chat.public.user-joined', $roomPeople);
    }

    protected function storeClient(Request $request)
    {
        $client = $request->client();
        $message = $request->payload();

        chatClients($client, [
            'name' => $message->name,
            'room' => $message->room,
        ]);

        chatRooms($message->room, $client);
    }

    public function send(Request $request)
    {
        $message = $request->payload();
        $client = $request->client();

        $storedClient = chatClients()[$client->getConnectionId()];

        if ($storedClient) {
            self::sendToAll($client, [
                'command' => 'chat.public.send',
                'data' => [
                    'user' => $storedClient['name'],
                    'client_id' => $client->getConnectionId(),
                    'message' => $message->message
                ],
            ]);
        }
    }

    public function typing(Request $request)
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
            if($request->payload()->status !== 'typing'){
                $data['status'] = 'stopped';
            }

            self::sendToAll($client, [
                'command' => 'chat.public.typing',
                'data' => $data,
            ]);
        }
    }
}
