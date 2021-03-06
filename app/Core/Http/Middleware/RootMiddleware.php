<?php


namespace App\Core\Http\Middleware;


use Closure;
use Psr\Http\Message\ServerRequestInterface;

class RootMiddleware implements MiddlewareInterface
{

    public function handle(Closure $next, ServerRequestInterface $request)
    {
        return $next($request);
    }
}