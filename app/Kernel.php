<?php

namespace App;

use App\Core\Http\Middleware\AuthMiddleware;
use App\Core\Kernel as CoreKernel;
use App\Servers\Http\Server;
use App\Servers\Socket\AdminServer;
use App\Servers\Socket\PrivateChatServer;
use App\Servers\Socket\PublicChatServer;

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
        'auth' => AuthMiddleware::class
    ];
}