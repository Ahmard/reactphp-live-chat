<?php


namespace App\Http\Middleware;


use App\Core\Database\Connection;
use App\Core\MiddlewareInterface;
use Closure;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class AuthMiddleware implements MiddlewareInterface
{

    public function handle(Closure $next, ServerRequestInterface $request)
    {
        return Connection::create()
            ->query('SELECT id FROM users WHERE id = ?', [session()->get('user_id')])
            ->then(function (Result $result) use ($next, $request) {
                if (count($result->rows) > 0) {
                    return $next($request);
                }

                return response()->redirect([
                    'You are not authorised'
                ]);
            })
            ->otherwise(function (Throwable $error) {
                return response()->internalServerError();
            });
    }
}