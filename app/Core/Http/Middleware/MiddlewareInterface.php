<?php


namespace App\Core\Http\Middleware;


use Closure;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    /**
     * @param Closure $next
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function handle(Closure $next, ServerRequestInterface $request);
}