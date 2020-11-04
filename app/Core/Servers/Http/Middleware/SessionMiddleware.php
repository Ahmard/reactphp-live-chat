<?php


namespace App\Core\Servers\Http\Middleware;


use Niko9911\React\Middleware\Session\Id\Random;
use Psr\Http\Message\ServerRequestInterface;
use React\Cache\ArrayCache;
use WyriHaximus\React\Http\Middleware\SessionMiddleware as WHSessionMiddleware;

final class SessionMiddleware
{
    public static ArrayCache $arrayCache;

    public static function create()
    {
        self::$arrayCache = new ArrayCache();
        return new static();
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $serverHost = $request->getHeaderLine('Host');
        $session = new \Niko9911\React\Middleware\SessionMiddleware(
            'PHPSESS',
            self::$arrayCache,
            7200,
            '/',
            '',
            false,
            false,
            new Random()
        );

        return $session($request, $next);

        return (new WHSessionMiddleware(
            $_ENV['HTTP_COOKIE_NAME'],
            self::$arrayCache,
            [
                '', // expiresAt, int, default
                '', // path, string, default
                'yy', // domain, string, default
                false, // secure, bool, default
                false // httpOnly, bool, default
            ],
        ))($request, $next);
    }
}