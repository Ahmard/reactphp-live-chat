<?php

namespace Server\Servers\Http\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use React\Cache\ArrayCache;
use React\Promise\PromiseInterface;
use WyriHaximus\React\Http\Middleware\WebrootPreloadMiddleware;

final class StaticFileResponseMiddleware
{
    public static function create(): StaticFileResponseMiddleware
    {
        return new StaticFileResponseMiddleware();
    }

    public function __invoke(ServerRequestInterface $request, Closure $next): PromiseInterface
    {
        $url = $request->getUri();

        if ($_ENV['SHOW_HTTP_RESOURCE_REQUEST'] == 'true') {
            echo "\n" . date('H:i:s');
            echo " -> New request({$url}).\n";
        }

        $logger = new NullLogger(); // Require, PSR-3 logger for bootstrap logging
        $cache = new ArrayCache();  // Required, custom cache configuration
        return (new WebrootPreloadMiddleware(
            public_path(),
            $logger, $cache
        ))($request, $next);
    }
}