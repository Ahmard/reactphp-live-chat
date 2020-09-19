<?php


namespace App\Core;


use App\Core\Colis\Colis;
use App\Core\Http\Response\StaticFileResponse;
use App\Kernel;
use React\Cache\ArrayCache;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use Voryx\WebSocketMiddleware\WebSocketMiddleware;
use WyriHaximus\React\Http\Middleware\SessionMiddleware;

class RootServer
{
    public static function run()
    {
        //Load command listeners
        $colis = Colis::getListeners();

        $servers = Kernel::getServers();

        $httpServers = $servers['http'];
        $socketServers = $servers['socket'];

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

        $cache = new ArrayCache();
        $cookieHandler = new SessionMiddleware(
            $_ENV['HTTP_COOKIE_NAME'],
            $cache,
            [
                0, // expiresAt, int, default
                '', // path, string, default
                '', // domain, string, default
                false, // secure, bool, default
                false // httpOnly, bool, default
            ],
        );

        $server = new HttpServer(
            getLoop(),
            $cookieHandler,
            new StaticFileResponse(),
            ...$instantiatedServers,
        );

        $uri = "{$_ENV['HOST']}:{$_ENV['PORT']}";

        $server->listen(new SocketServer($uri, getLoop()));

        console(true)->write("[*] HttpServer-Server running on http://{$uri}");
        console(true)->write("\n[*] Admin-SocketServer-Server running on ws://{$uri}{$_ENV['ADMIN_SOCKET_URL_PREFIX']}");
        console(true)->write("\n[*] Public-Chat-SocketServer-Server running on ws://{$uri}{$_ENV['PUBLIC_CHAT_SOCKET_URL_PREFIX']}");
        console(true)->write("\n[*] Private-Chat-SocketServer-Server running on ws://{$uri}{$_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX']}");

        $server->on('error', 'exceptionHandler');

        //Run event loop
        getLoop()->run();
    }
}