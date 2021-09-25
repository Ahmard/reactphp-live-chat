<?php


namespace Server\Socket\Middleware;


use Closure;
use Server\Socket\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next);
}