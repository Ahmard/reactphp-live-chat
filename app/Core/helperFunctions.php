<?php

use App\Socket\Response;
use Colors\Color;
use Evenement\EventEmitter;
use Ratchet\ConnectionInterface;

$root = dirname(__DIR__, 2);
$slash = DIRECTORY_SEPARATOR;

/**
 * Root directory path
 * @param null $path
 * @return string
 */
function root_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}";
}

/**
 * Application directory path
 * @param null $path
 * @return string
 */
function app_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}{$path}";
}

/**
 * Storage directory path
 * @param null $path
 * @return string
 */
function storage_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}storage{$slash}{$path}";
}

/**
 * Command listeners path
 * @param null $path
 * @return string
 */
function listener_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}Listeners{$slash}{$path}";
}

function loop()
{
    global $IoServer;

    return $IoServer->loop;
}

$event = new EventEmitter;
/**
 * Event object
 * @return EventEmitter
 */
function event()
{
    global $event;
    return $event;
}

/**
 * Send message to client
 * @param ConnectionInterface $client
 * @return Response
 */
function resp(ConnectionInterface $client)
{
    return new Response($client);
}

$clients = new \SplObjectStorage;
/**
 * All clients are stored here
 * @return SplObjectStorage
 */
function clientStorage()
{
    global $clients;
    return $clients;
}

$chatClients = new \ArrayObject();
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
 * @param $room
 * @param null $setValue
 * @return array|mixed
 */
function chatRooms($room, $setValue = null)
{
    static $chatRooms = [];

    if (!isset($chatRooms[$room])) {
        $chatRooms[$room] = [];
    }

    if ($setValue) {
        $chatRooms[$room][] = $setValue;
    }
    return $chatRooms[$room];
}

/**
 * Console color
 * @param string $text
 * @return Color
 */
$color = new Color();
function color($text)
{
    global $color;
    return $color($text);
}
