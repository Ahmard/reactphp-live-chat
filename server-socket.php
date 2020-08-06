<?php

use App\Socket\Server;
use App\Core\Colis\Colis;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

set_exception_handler(function ($exception) {
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

require 'vendor/autoload.php';
require 'colis.php';

$port = 10000;
$colis = Colis::getListeners();

$server = new Server($colis);
$IoServer = IoServer::factory(
    new HttpServer(
        new WsServer($server)
    ),
    $port
);

setLoop($IoServer->loop);

require 'app/core/Helpers/socketHelperFunctions.php';

echo "\n[*] Socket-Server running on port {$port}\n";

$server->init($argv);
$IoServer->run();