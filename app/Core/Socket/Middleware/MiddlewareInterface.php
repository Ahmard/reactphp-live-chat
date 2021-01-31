<?php


namespace App\Core\Socket\Middleware;


use App\Core\Socket\Request;
use Closure;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next);
}