<?php

use Colors\Color;
use Evenement\EventEmitter;

$root = dirname(__DIR__, 3);
$slash = DIRECTORY_SEPARATOR;

function url($url = null)
{
    global $serverConfig;
    return "http://{$serverConfig['host']}:{$serverConfig['port']}/{$url}";
}

/**
 * Root directory path
 * @param null $path
 * @return string
 */
function root_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}{$path}";
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
 * Application public directory path
 * @param null $path
 * @return string
 */
function public_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}public{$slash}{$path}";
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
    return "{$root}{$slash}app{$slash}Socket{$slash}Listeners{$slash}{$path}";
}

/**
 * Controllers path
 * @param null $path
 * @return string
 */
function controller_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}Http{$slash}Controllers{$slash}{$path}";
}

/**
 * Filesystem helper
 * @return \React\Filesystem\FilesystemInterface
 */
function filesystem()
{
    global $filesystem;
    return $filesystem;
}

function config(string $file)
{
    global $slash;
    return require root_path("config{$slash}{$file}.php");
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
