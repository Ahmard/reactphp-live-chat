<?php


namespace Server\Http\Middleware;


use App\Kernel;
use Closure;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Auth\Auth;
use Server\Http\MiddlewareRunner;
use Server\Http\Request;
use Server\Http\Router\Dispatcher;
use Server\Http\Router\Matcher;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @param Closure $next
     * @param Request $request
     * @return Response|PromiseInterface
     */
    public function handle(Closure $next, Request $request)
    {
        //Dispatch http request route
        event()->emit('route.before.dispatch');
        $dispatchResult = Dispatcher::dispatch($request);
        event()->emit('route.after.dispatch');

        //Init authentication
        return Auth::handle($request->getToken())->then(function (Auth $auth) use ($request, $dispatchResult) {
            //Set auth class
            $request->init('auth', $auth);

            switch (true) {
                case $dispatchResult->isNotFound():
                    $response = $request->getResponse()->notFound();
                    break;
                case $dispatchResult->isMethodNotAllowed():
                    $response = $request->getResponse()->methodNotAllowed();
                    break;
                case $dispatchResult->isFound():
                    $routeData = $dispatchResult->getRoute();

                    //If request has middleware, run it.
                    $middleware = $routeData->getMiddleware()[0] ?? null;

                    if ($middleware) {
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
                    $response = $request->getResponse()->internalServerError();
                    break;
            }

            return $response;
        });
    }

}