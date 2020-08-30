<?php

use Colors\Color;
use Evenement\EventEmitter;
use React\Filesystem\Filesystem;
use App\Core\Helpers\Classes\ConsoleHelper;

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
 * Command path
 * @param null $path
 * @return string
 */
function command_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}Console{$slash}Commands{$slash}{$path}";
}

$loadedConfig = [];
function config(string $file)
{
    global $slash, $loadedConfig;
    if(array_key_exists($file, $loadedConfig)){
        return $loadedConfig[$file];
    }
    
    $loaded = require root_path("config{$slash}{$file}.php");
    return $loadedConfig[$file] = $loaded;
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

$filesystem = Filesystem::create(getLoop());
/**
 * ReactPHP Filesystem
 * @return Filesystem
 */
function filesystem()
{
    global $filesystem;
    return $filesystem;
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


$console = new ConsoleHelper();
function console()
{
    global $console;
    return $console;
}