<?php

namespace App\Core\Servers\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use React\Cache\ArrayCache;
use WyriHaximus\React\Http\Middleware\WebrootPreloadMiddleware;

final class StaticFileResponseMiddleware
{
    public static function create()
    {
        return new static();
    }

    public function __invoke(ServerRequestInterface $request, $next)
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