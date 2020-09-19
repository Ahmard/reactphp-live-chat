<?php

namespace App;

use App\Core\Kernel as CoreKernel;
use App\Http\Middleware\AuthMiddleware;
use App\Servers\Http\Server;
use App\Servers\Socket\AdminServer;
use App\Servers\Socket\ChatServer;

class Kernel extends CoreKernel
{
    protected static array $servers = [
        'http' => [
            Server::class,
        ],

        'socket' => [
            AdminServer::class,
            ChatServer::class,
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