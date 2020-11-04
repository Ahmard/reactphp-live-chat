<?php


namespace App\Core\Http\Middleware;


use Closure;
use Psr\Http\Message\ServerRequestInterface;
use function React\Promise\resolve;

class AuthMiddleware implements MiddlewareInterface
{

    public function handle(Closure $next, ServerRequestInterface $request)
    {
        if (request()->auth()->check()) {
            return resolve($next($request));
        }

        return resolve(redirect('/'));
    }
}