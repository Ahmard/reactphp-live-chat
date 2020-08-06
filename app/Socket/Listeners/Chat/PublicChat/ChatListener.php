<?php
namespace App\Socket\Listeners\Chat\PublicChat;

use App\Socket\Listeners\Listener;
use App\Socket\Request;

class ChatListener extends Listener
{
    public $userId;
    
    
    public function join(Request $request)
    {
        $client = $request->client;
        $message = $request->message;
        
        $this->storeClient($request);
       
        //Notify user
        resp($client)->send('chat.public.joined');
        
        echo "[#] {$message->name} joined {$message->room}\n";
        //Notify users in the group
        foreach (chatRooms($message->room) as $connectedClient){
            if($connectedClient !== $client){
                resp($connectedClient)->send('chat.public.ujoined', [
                    'name' => $message->name,
                    'message' => "{$message->name} joined."
                ]);
            }
        }
    }
    
    public function send(Request $request)
    {
        $message = $request->message;
        $client = $request->client;

        $storedClient = chatClients()[$client->resourceId];

        if($storedClient){
            $clientRoom = $storedClient['room'];
            $clientName = $storedClient['name'];
            
            $roomClients = chatRooms($clientRoom);
            
            foreach ($roomClients as $roomClient){
                if($roomClient !== $client){
                    resp($roomClient)->send('chat.public.send', [
                        'user' => $clientName,
                        'message' => $message->message
                    ]);
                }
            }
        }
    }
    
    protected function storeClient(Request $request)
    {
        $client = $request->client;
        $message = $request->message;

        chatClients($client, [
            'name' => $message->name,
            'room' => $message->room
        ]);
        
        chatRooms($message->room, $client);
    }
}