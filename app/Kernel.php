<?php

namespace App;

use App\Servers\Http\Server;
use App\Servers\Websocket\AdminServer;
use App\Servers\Websocket\PrivateChatServer;
use App\Servers\Websocket\PublicChatServer;
use Server\Http\Middleware\AuthMiddleware as HttpAuthMiddleware;
use Server\Kernel as CoreKernel;
use Server\Websocket\Middleware\AuthMiddleware as SocketAuthMiddleware;

class Kernel extends CoreKernel
{
    protected static array $servers = [
        'http' => [
            Server::class,
        ],

        'socket' => [
            AdminServer::class,
            PublicChatServer::class,
            PrivateChatServer::class,
        ]
    ];

    protected static array $middlewares = [];

    protected static array $middlewareGroups = [
        'web' => [

        ],

        'api' => [

        ],

        'socket' => [

        ],
    ];

    protected static array $RouteMiddlewares = [
        'auth' => HttpAuthMiddleware::class
    ];

    protected static array $colisMiddlewares = [
        'auth' => SocketAuthMiddleware::class
    ];
}