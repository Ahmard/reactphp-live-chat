<?php


namespace App\Core\Http\Middleware;


use App\Core\Auth\Auth;
use App\Core\Helpers\Classes\RequestHelper;
use App\Core\Http\MiddlewareRunner;
use App\Core\Http\Router\Dispatcher;
use App\Core\Http\Router\Matcher;
use App\Core\Http\Url;
use App\Kernel;
use Closure;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @param Closure $next
     * @param ServerRequestInterface $request
     * @return Response|PromiseInterface
     * @throws Exception
     */
    public function handle(Closure $next, ServerRequestInterface $request)
    {
        //Dispatch http request route
        event()->emit('route.before.dispatch');
        $dispatchResult = Dispatcher::dispatch();
        event()->emit('route.after.dispatch');

        //Init url Helper
        Url::init($request);

        //Init authentication
        return Auth::handle(Url::getToken())->then(function (Auth $auth) use ($request, $dispatchResult) {
            //Set auth class
            RequestHelper::setAuth($auth);

            switch (true) {
                case $dispatchResult->isNotFound():
                    $response = response()->notFound();
                    break;
                case $dispatchResult->isMethodNotAllowed():
                    $response = response()->methodNotAllowed();
                    break;
                case $dispatchResult->isFound():
                    $routeData = $dispatchResult->getRoute();

                    //If request has middleware, run it.
                    $middleware = $routeData->getMiddleware();

                    if ($middleware !== '') {
                        $middlewares = Kernel::getMiddlewares();
                        $middleware = $middlewares['routes-middleware'][$middleware];

                        $response = MiddlewareRunner::runCustom($middleware, function () use ($request, $dispatchResult) {
                            return Matcher::match($request, $dispatchResult);
                        }, $request);
                    } else {
                        $response = Matcher::match($request, $dispatchResult);
                    }
                    break;
                default:
                    $response = \response()->internalServerError();
                    break;
            }

            return $response;
        });
    }

}