<?php


namespace App\Core\Socket\Middleware;


use App\Core\Database\Connection;
use App\Core\Socket\Request;
use Closure;
use Clue\React\SQLite\Result;
use Throwable;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        return Connection::create()
            ->query('SELECT id FROM users WHERE id = ?', [\request()->auth()->userId()])
            ->then(function (Result $result) use ($next, $request) {
                if (count($result->rows) > 0) {
                    return $next($request);
                }

                if (request()->expectsJson()) {
                    return resp($request->client())->send('403');
                }

                return resp($request->client())->send('403');
            })
            ->otherwise(function (Throwable $error) use ($request) {
                return resp($request->client())->send('403');
            });
    }
}