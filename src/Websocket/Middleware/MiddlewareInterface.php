<?php


namespace Server\Websocket\Middleware;


use Closure;
use Server\Websocket\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next);
}