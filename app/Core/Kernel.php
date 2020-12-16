<?php


namespace App\Core;

use App\Core\Http\Middleware\RootMiddleware;
use App\Core\Http\Middleware\RouteMiddleware;

class Kernel
{
    /**
     * Application servers
     * @var array|array[]
     */
    protected static array $servers = [
        'http' => [

        ],

        'socket' => [

        ]
    ];

    /**
     * Application middlewares
     * @var array
     */
    protected static array $middlewares = [];

    /**
     * Route middlewares
     * @var array
     */
    protected static array $RouteMiddlewares = [];

    /**
     * Colis middlewares
     * @var array
     */
    protected static array $colisMiddlewares = [];

    /**
     * Application grouped middlewares
     * @var array
     */
    protected static array $middlewareGroups = [];

    /**
     * Application default middlewares
     * @var array|string[]
     */
    protected static array $defaultMiddlewares = [
        RootMiddleware::class,
    ];

    /**
     * Application default route middlewares
     * @var array|string[]
     */
    protected static array $defaultRouteMiddleware = [
        RootMiddleware::class,
    ];

    /**
     * Application default colis middlewares
     * @var array|string[]
     */
    protected static array $defaultColisMiddleware = [
        RootMiddleware::class,
    ];

    /**
     * Application default middleware groups
     * @var array
     */
    protected static array $defaultMiddlewareGroups = [
        'web' => [
            RouteMiddleware::class,
        ],

        'api' => [

        ],

        'socket' => [

        ],
    ];

    /**
     * Get registered middlewares
     * @return array
     */
    public static function getMiddlewares(): array
    {
        return [
            'middlewares' => array_merge(
                static::$middlewares,
                self::$defaultMiddlewares
            ),
            'middleware-groups' => array_merge(
                static::$middlewareGroups,
                self::$defaultMiddlewareGroups
            ),
            'routes-middleware' => array_merge(
                static::$RouteMiddlewares,
                self::$defaultRouteMiddleware,
            ),
            'colis-middleware' => array_merge(
                static::$colisMiddlewares,
                self::$defaultColisMiddleware,
            ),
        ];
    }

    /**
     * Get registered servers
     * @return array|array[]
     */
    public static function getServers()
    {
        return static::$servers;
    }
}