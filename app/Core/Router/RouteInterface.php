<?php


namespace App\Core\Router;


use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    /**
     * Provide request object implementing Psr\HttpServer\Message\ServerRequestInterface
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function setRequestObject(ServerRequestInterface $request);

    /**
     * Function to be invoked
     * @param mixed ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments);

    /**
     * HttpServer request prefix
     * @param string $prefix
     * @return $this
     */
    public function prefix(string $prefix): RouteInterface;

    /**
     * Group http requests together
     * @param callable $closure
     * @return $this
     */
    public function group(callable $closure): RouteInterface;

    /** Set namespace to group of routes
     * @param string $namespace
     * @return $this
     */
    public function namespace(string $namespace): RouteInterface;

    /** Set namespace to group of routes
     * @param string $middleware
     * @return $this
     */
    public function middleware(string $middleware): RouteInterface;

    /** Set name to group of routes
     * @param string $name
     * @return $this
     */
    public function name(string $name): RouteInterface;

    /**
     *
     * @param string $route
     * @param $controller
     * @return $this
     */
    public function get(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param $controller
     * @return $this
     */
    public function post(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param $controller
     * @return $this
     */
    public function put(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param $controller
     * @return $this
     */
    public function patch(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param $controller
     * @return $this
     */
    public function delete(string $route, $controller): RouteInterface;

    /**
     * Retrieve registered routes
     * @return array
     */
    public function getRoute(): array;

    /**
     * Register route to nikita popov's router
     * @param RouteCollector $routeCollector
     * @return mixed
     */
    public function registerRoute(RouteCollector $routeCollector);
}