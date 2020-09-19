<?php


namespace App\Core\Http\Middleware;


use App\Core\Http\Response\MethodNotAllowed;
use App\Core\Http\Response\NotFound;
use App\Core\Middleware;
use App\Core\MiddlewareInterface;
use App\Kernel;
use Closure;
use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @param Closure $next
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function handle(Closure $next, ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();

        //Remove trailing forward slash
        $lengthPath = strlen($path) - 1;
        if ($lengthPath > 1 && $path[$lengthPath] == '/') {
            $path = substr($path, 0, $lengthPath);
        }

        $routeInfo = $request->__dispatcher__->dispatch($request->getMethod(), $path);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $response = response()->with(NotFound::create());
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = response()->with(MethodNotAllowed::create());
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1]->setRequestObject($request);
                $routeData = $handler->getRoute();
                $vars = $routeInfo[2];
                //If request has middleware, run it.
                $middleware = $routeData['middleware'];
                if ($middleware !== '') {
                    $middlewares = Kernel::getMiddlewares();

                    $middleware = $middlewares['routes-middleware'][$middleware];

                    $response = Middleware::runCustom($middleware, function () use ($handler, $request, $vars) {
                        return $handler($request, $vars);
                    }, $request);
                } else {
                    $response = $handler($request, $vars);
                }
                break;
        }

        return $response ?? response()->internalServerError();
    }

}