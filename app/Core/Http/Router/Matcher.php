<?php


namespace App\Core\Http\Router;


use App\Core\Helpers\Classes\FormHelper;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use QuickRoute\Route\DispatchResult;

class Matcher
{
    /**
     * Find route that matches current route
     * @param ServerRequestInterface $request
     * @param DispatchResult $dispatchResult
     * @return Exception|PromiseInterface|Response
     */
    public static function match(ServerRequestInterface $request, DispatchResult $dispatchResult)
    {
        $routeData = $dispatchResult->getRoute();

        $requestParams = $dispatchResult->getUrlParameters();
        //Handle controller
        $controller = $routeData['controller'];
        if (is_callable($controller)) {
            return call_user_func($controller, $request, $requestParams);
        }

        $explodedController = explode('@', $controller);
        $controllerClass = $explodedController[0];
        $controllerMethod = $explodedController[1];

        $namespacedController = $routeData['namespace'] . $controllerClass;

        //Initialize form helpers
        FormHelper::setRequest($request);

        //Call defined method
        $instantiatedController = (new $namespacedController())->_initAndFeed_([
            'request' => $request,
            'params' => $requestParams
        ]);


        if (!method_exists($instantiatedController, $controllerMethod)) {
            return new Exception("Method {$namespacedController}::{$controllerMethod}() does not exists.");
        }

        $response = call_user_func(
            [
                $instantiatedController,
                $controllerMethod
            ],
            $request,
            $requestParams
        );

        return $response;
    }
}