<?php


namespace App\Core\Socket\Middleware;


use App\Core\Database\Connection;
use App\Core\Socket\Request;
use Closure;
use Clue\React\SQLite\Result;
use Throwable;
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