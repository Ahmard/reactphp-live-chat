<?php

namespace App\Core\Router;

/**
 * Command listeners registry
 * @package App\Core\Router
 * @method static TheRouter prefix(string $prefix)
 * @method static TheRouter namespace(string $namespace)
 * @method static TheRouter group(callable $closure)
 * @method static TheRouter with(array $withDat)
 * @method static TheRouter get(string $route, string $controller)
 * @method static TheRouter post(string $route, string $controller)
 * @method static TheRouter put(string $route, string $controller)
 * @method static TheRouter patch(string $route, string $controller)
 * @method static TheRouter delete(string $route, string $controller)
 */
class Route implements Router
{
    protected static $called = [];

    protected static $theRouter;

    /**
     * @param $name
     * @param $args
     * @return TheRouter
     */
    public static function __callStatic($name, $args)
    {
           self::$theRouter = new TheRouter();

        return self::$theRouter->$name(...$args);
    }

    /**
0     * Get all registered routers
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$called;
    }

    public static function addRoute(TheRouter $route)
    {
        self::$called[] = $route;
    }
}
