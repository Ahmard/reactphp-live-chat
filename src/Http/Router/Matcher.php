<?php


namespace Server\Http\Router;


use Exception;
use QuickRoute\Router\DispatchResult;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Http\Request;

class Matcher
{
    /**
     * Find route that matches current route
     * @param Request $request
     * @param DispatchResult $dispatchResult
     * @return Exception|PromiseInterface|Response
     */
    public static function match(Request $request, DispatchResult $dispatchResult)
    {
        $routeData = $dispatchResult->getRoute();

        $requestParams = $dispatchResult->getUrlParameters();
        //Handle controller
        $controller = $routeData->getHandler();
        if (is_callable($controller)) {
            return call_user_func($controller, $request, $requestParams);
        }

        $explodedController = explode('@', $controller);
        $controllerClass = $explodedController[0];
        $controllerMethod = $explodedController[1];

        $namespacedController = $routeData->getNamespace() . $controllerClass;

        //Call defined method
        $instantiatedController = (new $namespacedController([
            'request' => $request,
            'params' => $requestParams
        ]));


        if (!method_exists($instantiatedController, $controllerMethod)) {
            return new Exception("Method {$namespacedController}::{$controllerMethod}() does not exists.");
        }

        return call_user_func(
            [
                $instantiatedController,
                $controllerMethod
            ],
            $request,
            $requestParams
        );
    }
}