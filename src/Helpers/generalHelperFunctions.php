<?php

use Carbon\Carbon;
use Clue\React\SQLite\DatabaseInterface;
use Colors\Color;
use React\EventLoop\Loop;
use React\Filesystem\Filesystem;
use React\Filesystem\FilesystemInterface;
use Server\Database\Connection;
use Server\EventEmitter;
use Server\Helpers\Classes\ConsoleHelper;
use Server\Helpers\Classes\ValidationHelper;
use Server\ServerStore;

$root = dirname(__DIR__, 2);
$slash = DIRECTORY_SEPARATOR;

function url(?string $url = null): string
{
    return "http://{$_ENV['DOMAIN']}/{$url}";
}

/**
 * Root directory path
 * @param string|null $path
 * @return string
 */
function root_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}{$path}";
}


/**
 * Application directory path
 * @param string|null $path
 * @return string
 */
function app_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}{$path}";
}

/**
 * Application public directory path
 * @param string|null $path
 * @return string
 */
function public_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}public{$slash}{$path}";
}

/**
 * Storage directory path
 * @param string|null $path
 * @return string
 */
function storage_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}storage{$slash}{$path}";
}

/**
 * Command listeners path
 * @param string|null $path
 * @return string
 */
function listener_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}Socket{$slash}Listeners{$slash}{$path}";
}

/**
 * Controllers path
 * @param string|null $path
 * @return string
 */
function controller_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}Http{$slash}Controllers{$slash}{$path}";
}

/**
 * Command path
 * @param string|null $path
 * @return string
 */
function command_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}app{$slash}Console{$slash}Commands{$slash}{$path}";
}

/**
 * Database path
 * @param string|null $path
 * @return string
 */
function database_path(?string $path = null): string
{
    global $root, $slash;
    return "{$root}{$slash}database{$slash}{$path}";
}

$loadedConfig = [];
function config(string $file): array
{
    global $slash, $loadedConfig;
    if (array_key_exists($file, $loadedConfig)) {
        return $loadedConfig[$file];
    }

    $loaded = require root_path("config{$slash}{$file}.php");
    return $loadedConfig[$file] = $loaded;
}

$filesystem = Filesystem::create(Loop::get());
/**
 * ReactPHP Filesystem
 * @return FilesystemInterface
 */
function filesystem(): FilesystemInterface
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
function color(string $text): Color
{
    global $color;
    return $color($text);
}


/**
 * An event emitter helper function
 *
 * @return EventEmitter
 */
function event(): EventEmitter
{
    return EventEmitter::getInstance();
}


function console(bool $willForceDisplay = false): ConsoleHelper
{
    $console = new ConsoleHelper();
    if ($willForceDisplay) {
        $console->forceDisplay();
    }
    return $console;
}

function database(): DatabaseInterface
{
    return Connection::get();
}

/**
 * Input validation helper
 * @return ValidationHelper
 */
function validator(): ValidationHelper
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

function carbon(): Carbon
{
    return Carbon::createFromTimestamp(time());
}