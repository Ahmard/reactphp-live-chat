<?php

namespace App\Core\Router;

/**
 * Command listeners registry
 * @package App\Core\Router
 * @method static TheRoute prefix(string $prefix)
 * @method static TheRoute namespace(string $namespace)
 * @method static TheRoute middleware(string $middleware)
 * @method static TheRoute group(callable $closure)
 * @method static TheRoute with(array $withDat)
 * @method static TheRoute get(string $route, $controller)
 * @method static TheRoute post(string $route, $controller)
 * @method static TheRoute put(string $route, $controller)
 * @method static TheRoute patch(string $route, $controller)
 * @method static TheRoute delete(string $route, $controller)
 */
class Route
{
    protected static array $called = [];

    protected static TheRoute $theRouter;

    /**
     * @param $name
     * @param $args
     * @return TheRoute
     */
    public static function __callStatic($name, $args)
    {
        self::$theRouter = new TheRoute();

        return self::$theRouter->$name(...$args);
    }

    /**
     * Get all registered routers
     * @return array
     */
    public static function getRoutes(): array
    {
        require(root_path('routes.php'));
        return self::$called;
    }

    public static function addRoute(TheRoute $route)
    {
        self::$called[] = $route;
    }
}
