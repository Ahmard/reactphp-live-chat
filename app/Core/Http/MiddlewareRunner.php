<?php


namespace App\Core\Http;


use Closure;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class MiddlewareRunner
{
    /**
     * @param ServerRequestInterface $request
     * @param array $middlewares
     * @param string|null $response
     * @param string|null $group
     * @return PromiseInterface|\React\Http\Message\Response|mixed
     */
    public static function run(
        ServerRequestInterface $request,
        array                  $middlewares,
        ?string                $response = null,
        ?string                $group = null
    )
    {
        if ($group) {
            $middlewares = $middlewares[$group];
        }

        $totalMiddlewares = count($middlewares);
        if ($totalMiddlewares > 1) {
            for ($i = 0; $i < $totalMiddlewares; $i++) {
                $nextMiddleware = $middlewares[$i + 1] ?? fn() => $response;
                $objMiddleware = new $middlewares[$i]();
                $response = $objMiddleware->handle($nextMiddleware, $request);

                //If response is returned, we break out from the cycle of middlewares
                if ($response instanceof Response) {
                    break;
                }
            }
        } else {
            $objMiddleware = new $middlewares[0]();

            $response = $objMiddleware->handle(function (ServerRequestInterface $request) {

            }, $request);
        }

        return $response;
    }

    public static function runCustom(
        string                 $middleware,
        Closure                $next,
        ServerRequestInterface $request
    ): PromiseInterface
    {
        return (new $middleware())->handle(function () use ($next, $request) {
            return $next($request);
        }, $request);
    }
}