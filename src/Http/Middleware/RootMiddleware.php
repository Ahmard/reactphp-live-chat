<?php


namespace Server\Http\Middleware;


use Closure;
use Server\Http\Request;

class RootMiddleware implements MiddlewareInterface
{

    public function handle(Closure $next, Request $request)
    {
        return $next($request);
    }
}