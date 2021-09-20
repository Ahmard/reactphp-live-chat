<?php


namespace Server\Socket\Middleware;


use Closure;
use Server\Socket\Request;
use function React\Promise\reject;
use function React\Promise\resolve;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->auth()->check()) {
            return resolve();
        }

        resp($request->client())->send('system.response.403', [
            'action' => 'redirect',
            'uri' => '/login'
        ]);

        return reject();
    }
}