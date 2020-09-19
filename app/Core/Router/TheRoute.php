<?php

namespace App\Core\Router;

use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class TheRoute implements RouteInterface
{
    /**
     * Register all request methods
     */
    use RequestMethods;


    public bool $isWithUsed = false;
    private ServerRequestInterface $request;
    private string $prefix = '';
    private string $namespace = '';
    private string $middleware = '';
    private string $name = '';
    private string $method;
    private $controller;

    public function __construct(array $withData = [])
    {
        if ($withData) {
            $this->isWithUsed = true;
            $this->namespace = $withData['namespace'];
            $this->name = $withData['name'];
            $this->prefix = $withData['prefix'];
            $this->controller = $withData['controller'] ?? '';
            $this->middleware = $withData['middleware'] ?? '';

            $this->name = $withData['name'] ?? '';
            $this->method = $withData['method'] ?? '';
        }
    }

    /**
     * Set request object
     * @param ServerRequestInterface $request
     * @return $this|mixed
     */
    public function setRequestObject(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Fake route controlller
     * @param mixed ...$arguments
     * @return mixed|PromiseInterface
     */
    public function __invoke(...$arguments)
    {
        return Matcher::match($this->request, $this->getRoute());
    }

    /**
     * Retrieve controllers defined in this object
     * @return array
     */
    public function getRoute(): array
    {
        return [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'controller' => $this->controller,
            'middleware' => $this->middleware,
            'method' => $this->method,
            'name' => $this->name,
        ];
    }

    /**
     * @param string $prefix
     * @return TheRoute $this
     */
    public function prefix(string $prefix): self
    {
        if ($this->prefix && !$this->isWithUsed) {
            $newRouter = new self([
                'namespace' => $this->namespace,
                'prefix' => $this->buildPrefix($this->prefix, $prefix),
                'name' => $this->name,
                'middleware' => $this->middleware,
            ]);

            Route::addRoute($newRouter);

            return $newRouter;
        }

        $this->prefix .= $prefix;
        return $this;
    }

    protected function buildPrefix(string $prefix1, string $prefix2)
    {
        $prefix2 = $this->removeTrailingSlash($prefix2);
        if ($prefix2 && $prefix2 != '/') {
            return $prefix1 . '/' . $prefix2;
        }

        return empty($prefix1) ? '/' : $prefix1;
    }

    protected function removeTrailingSlash(string $prefix)
    {
        $totalStr = strlen($prefix) - 1;
        if ($totalStr > 0) {
            if ($prefix[$totalStr] == '/' && $totalStr != 0) {
                $prefix = substr($prefix, 0, $totalStr);
            }

            if ($prefix[0] == '/' && $totalStr != 0) {
                $prefix = substr($prefix, 1, $totalStr + 1);
            }
        }
        return $prefix;
    }

    /**
     * Group controllers
     * @param callable $closure
     * @return TheRoute $this
     */
    public function group(callable $closure): self
    {
        $closure($this);
        return $this;
    }

    /**
     * Add namespace to listener groups
     * @param string $namespace
     * @return $this
     */
    public function namespace(string $namespace): self
    {
        if ($namespace[strlen($namespace) - 1] !== "\\") {
            $namespace .= "\\";
        }
        $this->namespace .= $namespace;
        return $this;
    }

    /**
     * Add name to listener groups
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        if ($name[strlen($name) - 1] !== "\\") {
            $name .= "\\";
        }
        $this->name .= $name;
        return $this;
    }

    /**
     * Set middleware to the route
     * @param string $middleware
     * @return $this
     */
    public function middleware(string $middleware): self
    {
        $this->middleware .= $middleware;
        return $this;
    }

    /**
     * Listen to route
     * @param string $method
     * @param string $route
     * @param string $class
     * @return TheRoute $this
     */
    public function add(string $method, string $route, $controllerClass): self
    {
        $newRouter = new self([
            'namespace' => $this->namespace,
            'prefix' => $this->buildPrefix($this->prefix, $route),
            'name' => $this->name,
            'method' => $method,
            'controller' => $controllerClass,
            'middleware' => $this->middleware,
        ]);

        Route::addRoute($newRouter);

        return $newRouter;
    }

    /**
     * Register route in this class
     * @param RouteCollector $routeCollector
     * @return mixed|void
     */
    public function registerRoute(RouteCollector $routeCollector)
    {
        $prefix = $this->prefix;
        if ($prefix[0] != '/') {
            $prefix = '/' . $prefix;
        }

        $routeCollector->addRoute(strtoupper($this->method), $prefix, $this);
    }
}
