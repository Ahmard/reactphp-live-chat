<?php

use App\Core\Auth\Auth;
use App\Core\Helpers\Classes\ConsoleHelper;
use App\Core\Helpers\Classes\ValidationHelper;
use App\Core\ServerStore;
use Clue\React\SQLite\Factory;
use Colors\Color;
use React\Filesystem\Filesystem;
use React\Filesystem\FilesystemInterface;
use React\Promise\PromiseInterface;

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

/**
 * Database path
 * @param null $path
 * @return string
 */
function database_path($path = null)
{
    global $root, $slash;
    return "{$root}{$slash}database{$slash}{$path}";
}

$loadedConfig = [];
function config(string $file)
{
    global $slash, $loadedConfig;
    if (array_key_exists($file, $loadedConfig)) {
        return $loadedConfig[$file];
    }

    $loaded = require root_path("config{$slash}{$file}.php");
    return $loadedConfig[$file] = $loaded;
}

$filesystem = Filesystem::create(getLoop());
/**
 * ReactPHP Filesystem
 * @return FilesystemInterface
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


function console(bool $willForceDisplay = false)
{
    $console = new ConsoleHelper();
    if ($willForceDisplay) {
        $console->forceDisplay();
    }
    return $console;
}


$factory = new Factory(getLoop());
function database()
{
    global $factory;
    return $factory;
}

/**
 * Input validation helper
 * @return ValidationHelper
 */
function validator()
{
    return new ValidationHelper();
}

/**
 * @return ServerStore
 */
function server(): ServerStore
{
    return ServerStore::getInstance();
}