<?php


namespace App\Core\Http\Router;


use Psr\Http\Message\ServerRequestInterface;
use QuickRoute\Route\Dispatcher as QuickRouteDispatcher;
use QuickRoute\Route\DispatchResult;

class Dispatcher
{
    private static ServerRequestInterface $request;

    private static DispatchResult $dispatchResult;


    /**
     * Initialize dispatcher
     * @param ServerRequestInterface $request
     */
    public static function setRequest(ServerRequestInterface $request)
    {
        self::$request = $request;
    }

    /**
     * Dispatch url routing
     * @return DispatchResult
     */
    public static function dispatch(): DispatchResult
    {
        $path = self::$request->getUri()->getPath();

        self::$dispatchResult = QuickRouteDispatcher::create(RouteCollector::getCollector())
            ->dispatch(self::$request->getMethod(), $path);

        return self::$dispatchResult;
    }

    /**
     * @return DispatchResult
     */
    public static function getDispatchResult(): DispatchResult
    {
        return self::$dispatchResult;
    }
}