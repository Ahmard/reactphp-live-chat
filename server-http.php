<?php
error_reporting(E_ALL);

use App\Http\Server;
use App\Core\Router\Route;
use React\EventLoop\Factory;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use React\Filesystem\Filesystem;

require 'vendor/autoload.php';
require 'routes.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$serverConfig = [
    'host' => '127.0.0.1',
    'port' => 10001
];
$routes = Route::getRoutes();

$httpServer = new HttpServer($loop, new Server($routes));

//Handle all errors
$httpServer->on('error', function ($exception){
    $file = __DIR__ . '/storage/logs/' . time() . '.log';
    $log = json_encode([
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'code' => $exception->getCode(),
    ], JSON_PRETTY_PRINT);
    file_put_contents($file, $log);
    echo "\n[*] Error: {$exception->getMessage()} => {$exception->getFile()} @ Line {$exception->getLine()}\n";
});

setLoop($loop);

require 'app/core/Helpers/httpHelperFunctions.php';

echo "\n[*] Http-Server running on port {$serverConfig['port']}\n";

$socket = new SocketServer("{$serverConfig['host']}:{$serverConfig['port']}", $loop);
$httpServer->listen($socket);
$loop->run();