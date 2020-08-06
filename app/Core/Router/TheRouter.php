<?php
namespace App\Core\Router;

use function GuzzleHttp\Psr7\str;

class TheRouter implements Router
{
    /**
     * Register all request methods
     */
    use RequestMethods;


    private $prefix = '';

    private $namespace = '';
    
    private $name = '';

    private $method;

    private $controller;

    public $isWithUsed = false;

    public function __construct(array $withData = [])
    {
        if($withData) {
            $this->isWithUsed = true;
            $this->namespace = $withData['namespace'];
            $this->name = $withData['name'];
            $this->prefix = $withData['prefix'];
            $this->controller = $withData['controller'];

            $this->name = $withData['name'] ?? '';
            $this->method = $withData['method'];
        }
    }

    /**
     * @param string $prefix
     * @return TheRouter $this
     */
    public function prefix($prefix)
    {
        if($this->prefix && !$this->isWithUsed){
            $newRouter = new self([
                'namespace' => $this->namespace,
                'prefix' => $this->buildPrefix($this->prefix, $prefix),
                'name'=> $this->name,
            ]);

            Route::addRoute($newRouter);

            return $newRouter;
        }

        $this->prefix .= $prefix;
        return $this;
    }

    /**
     * Group controllers
     * @param callable $closure
     * @return TheRouter $this
     */
    public function group(callable $closure)
    {
        $closure($this);
        return $this;
    }

    /**
     * Add namespace to listener groups
     * @param string $namespace
     * @return TheRouter $this
     */
    public function namespace($namespace)
    {
        if($namespace[strlen($namespace) - 1] !== "\\"){
            $namespace .= "\\";
        }
        $this->namespace .= $namespace;
        return $this;
    }

    /**
     * Add name to listener groups
     * @param string $name
     * @return TheRouter $this
     */
    public function name($name)
    {
        if($name[strlen($name) - 1] !== "\\"){
            $name .= "\\";
        }
        $this->name .= $name;
        return $this;
    }

    /**
     * Listen to route
     * @param string $method
     * @param string $route
     * @param string $class
     * @return TheRouter $this
     */
    public function add(string $method, string $route, string $controllerClass)
    {
        $newRouter = new self([
            'namespace' => $this->namespace,
            'prefix' => $this->buildPrefix($this->prefix, $route),
            'name'=> $this->name,
            'method' => $method,
            'controller' => $controllerClass,
        ]);

        Route::addRoute($newRouter);

        return $newRouter;
    }

    protected function buildPrefix(string $prefix1, string $prefix2)
    {
        $prefix2 = $this->removeTrailingSlash($prefix2);
        if($prefix2 && $prefix2 != '/'){
            return $prefix1 . '/' . $prefix2;
        }

        return empty($prefix1) ? '/' : $prefix1;
    }

    protected function removeTrailingSlash(string $prefix)
    {
        $totalStr = strlen($prefix) -1 ;
        if ($prefix[$totalStr] == '/' && $totalStr != 0){
            $prefix = substr($prefix, 0, $totalStr);
        }

        if ($prefix[0] == '/' && $totalStr != 0){
            $prefix = substr($prefix, 1, $totalStr + 1);
        }

        return $prefix;
    }

    /**
     * Retrieve controllers defined in this object
     * @return array
     */
    public function getRoute()
    {
        return [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'controller' => $this->controller,
            'method' => $this->method,
            'name' => $this->name,
        ];
    }
}