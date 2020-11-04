<?php


namespace App\Core\Socket\Middleware;


use App\Core\Socket\Request;
use Closure;

interface MiddlewareInterface
{
    public function handle(Request $request, Closure $next);
}