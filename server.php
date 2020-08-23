<?php

use App\WsServer;
use App\Socket\Server as SocketHandler;
use App\Http\Server as HttpHandler;
use App\Core\Colis\Colis;
use App\Core\Router\Route;
use React\EventLoop\Factory;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use Voryx\WebSocketMiddleware\WebSocketMiddleware;

require('vendor/autoload.php');

$loop = Factory::create();

setLoop($loop);

$exceptionHandler = function ($exception) {
    $filename = __DIR__ . '/storage/logs/' . time() . '.log';
    $logData = json_encode([
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'code' => $exception->getCode(),
    ], JSON_PRETTY_PRINT);
    //Save error log
    file_put_contents($filename, $logData);
    
    echo "\n[*] Error: {$exception->getMessage()} => {$exception->getFile()} @ Line {$exception->getLine()}\n";
};

set_exception_handler($exceptionHandler);

$serverConfig = [
    'host' => '127.0.0.1',
    'port' => 9000
];

require 'app/Core/Helpers/generalHelperFunctions.php';
require 'app/Core/Helpers/socketHelperFunctions.php';
require 'app/Core/Helpers/httpHelperFunctions.php';
require 'app/Core/event-listeners.php';

//Load command listeners
require 'colis.php';
$colis = Colis::getListeners();

//Load web routes
require 'routes.php';
$routes = Route::getRoutes();

//Initialize socket connection handler
$socketHandler = new SocketHandler($colis);
$socketHandler->init($argv);
//Initialize http handler
$httpHandler = new HttpHandler($routes);
//Create an instance of middleware that handles socket and http socket 
$websocket = new WebSocketMiddleware(['/chat'], new WsServer($socketHandler));

$server = new HttpServer(
    $loop,
    $websocket,
    $httpHandler,
);

$uri = "{$serverConfig['host']}:{$serverConfig['port']}";

$server->listen(new SocketServer($uri, $loop));

echo "\n[*] Http-Server running on http://{$uri}\n";
echo "\n[*] Socket-Server running on http://{$uri}/chat\n";

$server->on('error', $exceptionHandler);
$loop->run();