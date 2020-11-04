<?php

namespace App\Core\Socket\Colis;

class TheColis implements ColisInterface
{
    protected string $prefix = '';

    protected string $namespace = '';

    protected string $middleware = '';

    protected array $listeners = [];

    protected bool $isWithUsed = false;

    /**
     * @param array $withData
     * @return TheColis $this
     */
    public function with(array $withData): ColisInterface
    {
        $this->isWithUsed = true;
        $this->namespace = $withData['namespace'];
        $this->middleware = $withData['middleware'];
        $this->prefix = $withData['prefix'];
        return $this;
    }

    /**
     * @param string $prefix
     * @return TheColis $this
     */
    public function prefix(string $prefix): ColisInterface
    {
        if ($this->prefix && !$this->isWithUsed) {
            return Colis::with([
                'namespace' => $this->namespace,
                'middleware' => $this->middleware,
                'prefix' => $this->prefix
            ])->prefix($prefix);
        }

        $this->prefix .= $prefix;
        return $this;
    }

    /**
     * Group listeners
     * @param callable $closure
     * @return TheColis $this
     */
    public function group(callable $closure): ColisInterface
    {
        $closure($this);
        return $this;
    }

    /**
     * Add namespace to listener groups
     * @param string $namespace
     * @return TheColis $this
     */
    public function namespace(string $namespace): ColisInterface
    {
        if ($namespace[strlen($namespace) - 1] !== "\\") {
            $namespace .= "\\";
        }
        $this->namespace .= $namespace;
        return $this;
    }

    /**
     * Set middleware to command
     * @param string $middleware
     * @return $this
     */
    public function middleware(string $middleware)
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Listen to command
     * @param string $command
     * @param callable|string $listenerClass
     * @return TheColis $this
     */
    public function listen(string $command, $listenerClass): ColisInterface
    {
        $this->listeners[$command] = $listenerClass;
        return $this;
    }

    /**
     * Retrieve listeners defined in this object
     * @return array
     */
    public function getListeners(): array
    {
        return [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'middleware' => $this->middleware,
            'listeners' => $this->listeners,
        ];
    }
}
