<?php

use Server\Websocket\ConnectionInterface;
use Server\Websocket\Response;

/**
 * Send message to client
 * @param ConnectionInterface $client
 * @return Response
 */
function resp(ConnectionInterface $client): Response
{
    return new Response($client);
}

$clients = new SplObjectStorage;
/**
 * All clients are stored here
 * @return SplObjectStorage
 */
function clientStorage(): SplObjectStorage
{
    global $clients;
    return $clients;
}

/**
 * @var ConnectionInterface[]
 */
$chatClients = new ArrayObject();
/**
 * Clients that joined chat rooms
 * @param null|ConnectionInterface $client
 * @param null|mixed $data
 * @return ArrayObject
 */
function chatClients($client = null, $data = null)
{
    global $chatClients;

    if ($client) {
        $chatClients[$client->getConnectionId()] = $data;
    }

    return $chatClients;
}

$chatRooms = new ArrayObject();
/**
 * All created chat rooms
 * @param null|string $room
 * @param null|mixed $setValue
 * @return ConnectionInterface[]
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