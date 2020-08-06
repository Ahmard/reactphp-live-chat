<?php

use App\Socket\Response;
use Colors\Color;
use Evenement\EventEmitter;
use Ratchet\ConnectionInterface;

require 'generalHelperFunctions.php';

/**
 * Send message to client
 * @param ConnectionInterface $client
 * @return Response
 */
function resp(ConnectionInterface $client)
{
    return new Response($client);
}

$clients = new SplObjectStorage;
/**
 * All clients are stored here
 * @return SplObjectStorage
 */
function clientStorage()
{
    global $clients;
    return $clients;
}

$chatClients = new ArrayObject();
/**
 * Clients that joined chat rooms
 * @param null $client
 * @param null $data
 * @return ArrayObject
 */
function chatClients($client = null, $data = null)
{
    global $chatClients;

    if ($client) {
        $chatClients[$client->resourceId] = $data;
    }
    return $chatClients;
}

/**
 * All created chat rooms
 * @param null $room
 * @param null $setValue
 * @return array|splObjectStorage
 */
function chatRooms($room = null, $setValue = null)
{
    static $chatRooms = [];

    if (!isset($chatRooms[$room])) {
        $chatRooms[$room] = [];
    }

    if ($setValue) {
        $chatRooms[$room][] = $setValue;
    }

    return $room ? $chatRooms[$room] : $chatRooms;
}