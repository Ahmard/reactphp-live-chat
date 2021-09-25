<?php


namespace Server\Http\Router;


use QuickRoute\Router\Dispatcher as QuickRouteDispatcher;
use QuickRoute\Router\DispatchResult;
use Server\Http\Request;

class Dispatcher
{
    /**
     * Dispatch url routing
     * @param Request $request
     * @return DispatchResult
     */
    public static function dispatch(Request $request): DispatchResult
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $dispatchResult = QuickRouteDispatcher::create(RouteCollector::getCollector())
            ->dispatch($method, $path);

        $request->init('dispatchResult', $dispatchResult);

        return $dispatchResult;
    }
}