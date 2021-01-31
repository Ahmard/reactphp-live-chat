<?php

use App\Core\Helpers\Classes\ConsoleHelper;
use App\Core\Helpers\Classes\ValidationHelper;
use App\Core\ServerStore;
use Clue\React\SQLite\Factory;
use Colors\Color;
use React\Filesystem\Filesystem;
use React\Filesystem\FilesystemInterface;

$root = dirname(__DIR__, 3);
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

$filesystem = Filesystem::create(getLoop());
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


function console(bool $willForceDisplay = false): ConsoleHelper
{
    $console = new ConsoleHelper();
    if ($willForceDisplay) {
        $console->forceDisplay();
    }
    return $console;
}


$factory = new Factory(getLoop());
function database(): Factory
{
    global $factory;
    return $factory;
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