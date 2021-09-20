<?php


namespace Server\Http\Middleware;


use Closure;
use Server\Http\Request;

interface MiddlewareInterface
{
    /**
     * @param Closure $next
     * @param Request $request
     * @return mixed
     */
    public function handle(Closure $next, Request $request);
}