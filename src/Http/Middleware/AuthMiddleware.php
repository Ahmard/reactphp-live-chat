<?php


namespace Server\Http\Middleware;


use Closure;
use Server\Http\Request;
use function React\Promise\resolve;

class AuthMiddleware implements MiddlewareInterface
{

    public function handle(Closure $next, Request $request)
    {
        if ($request->auth()->check()) {
            return resolve($next($request));
        }

        return resolve($request->getResponse()->redirect('/'));
    }
}