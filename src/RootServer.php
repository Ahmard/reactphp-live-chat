<?php


namespace Server;


use App\Kernel;
use App\Providers\AppServiceProvider;
use App\Providers\HttpServiceProvider;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Server\Database\Connection;
use Server\Http\Router\RouteCollector;
use Server\Servers\Http\Middleware\StaticFileResponseMiddleware;
use Server\Socket\Colis\Colis;
use Voryx\WebSocketMiddleware\WebSocketMiddleware;

class RootServer
{
    public static function run(): void
    {
        AppServiceProvider::init()->boot();

        //Server URI
        $serverUri = "{$_ENV['HOST']}:{$_ENV['PORT']}";

        //Load command listeners
        $colis = Colis::getListeners();
        //Load provided servers
        $servers = Kernel::getServers();
        $httpServers = $servers['http'];
        $socketServers = $servers['socket'];

        //Stores servers
        $instantiatedServers = [];

        //Socket servers
        foreach ($socketServers as $socketServer) {
            $instantiated = new $socketServer($colis);
            $instantiated->colis = $colis;
            $instantiatedServers[] = new WebSocketMiddleware(
                [$instantiated->prefix],
                new WsServer($instantiated)
            );
        }

        //Http servers
        foreach ($httpServers as $httpServer) {
            $instantiatedServers[] = new $httpServer();
        }

        //Boot http service provider
        HttpServiceProvider::init()->boot();
        //Collect http routes
        RouteCollector::collectRoutes();
        //Register http routes
        RouteCollector::register();

        //Start database connection
        Connection::create();

        //Init server
        $server = new HttpServer(
        //Static file response handler
            StaticFileResponseMiddleware::create(),
            //Instantiated servers
            ...$instantiatedServers
        );

        //Create servers
        $server->listen(new SocketServer($serverUri));

        console(true)->write("[*] HttpServer-Server running on http://{$serverUri}");
        console(true)->write("\n[*] Admin-SocketServer-Server running on ws://{$serverUri}{$_ENV['ADMIN_SOCKET_URL_PREFIX']}");
        console(true)->write("\n[*] Public-Chat-SocketServer-Server running on ws://{$serverUri}{$_ENV['PUBLIC_CHAT_SOCKET_URL_PREFIX']}");
        console(true)->write("\n[*] Private-Chat-SocketServer-Server running on ws://{$serverUri}{$_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX']}");

        $server->on('error', 'handleApplicationException');

        //Run event loop
        Loop::run();
    }
}