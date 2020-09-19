<?php

use App\Core\ConnectionInterface;
use App\Core\Socket\Response;

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

$chatRooms = new ArrayObject();
/**
 * All created chat rooms
 * @param null $room
 * @param null $setValue
 * @return ArrayObject
 */
function chatRooms($room = null, $setValue = null)
{
    global $chatRooms;

    if (!isset($chatRooms[$room])) {
        $chatRooms[$room] = [];
    }

    if ($setValue) {
        $chatRooms[$room][] = $setValue;
    }

    return $room ? $chatRooms[$room] : $chatRooms;
}