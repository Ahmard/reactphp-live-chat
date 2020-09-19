<?php


namespace App\Core;


use Closure;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    public function handle(Closure $next, ServerRequestInterface $request);
}