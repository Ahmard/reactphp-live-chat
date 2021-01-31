<?php


namespace App\Core\Http;


use App\Core\Http\Router\Dispatcher;
use App\Providers\HttpServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
use QuickRoute\Route\RouteData;

final class Url
{
    private static string $url;

    private static string $method;

    private static string $routeTokenPrefix = '{primaryToken}';

    private static string $routeToken = '';

    public static function init(ServerRequestInterface $request): void
    {
        self::$url = (string)$request->getUri();
        self::$method = $request->getMethod();

        if (Dispatcher::getDispatchResult()->isFound()) {
            $expPrefix = explode(
                '/',
                Dispatcher::getDispatchResult()
                    ->getRoute()
                    ->getPrefix()
            );
            $routeTokenPrefix = self::$routeTokenPrefix;

            if (class_exists(HttpServiceProvider::class)) {
                $routeTokenPrefix = HttpServiceProvider::$routeTokenPrefix;
            }

            if (end($expPrefix) == $routeTokenPrefix) {
                $expUrl = explode('/', self::$url);
                $routeToken = end($expUrl);
                $expRouteToken = explode('?', $routeToken);
                self::$routeToken = current($expRouteToken);
            } else {
                self::$routeToken = '';
            }
        }
    }

    /**
     * Get http url
     * @return string
     */
    public static function getUrl(): string
    {
        return self::$url;
    }

    /**
     * Get http request method
     * @return string
     */
    public static function getMethod(): string
    {
        return self::$method;
    }

    /**
     * Get authentication token passed to route
     * @return string
     */
    public static function getToken(): string
    {
        return self::$routeToken;
    }

    /**
     * Get current route class
     * @return RouteData
     */
    public static function getRoute(): RouteData
    {
        return Dispatcher::getDispatchResult()->getRoute();
    }
}