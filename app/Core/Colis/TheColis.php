<?php

namespace App\Core\Colis;

class TheColis implements ColisInterface
{
    public $prefix = '';

    public $namespace = '';

    public $listeners = [];

    public $isWithUsed = false;

    /**
     * @param $withData
     * @return TheColis $this
     */
    public function with(array $withData): ColisInterface
    {
        $this->isWithUsed = true;
        $this->namespace = $withData['namespace'];
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
     * Listen to command
     * @param string $command
     * @param $listenerClass
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
            'listeners' => $this->listeners,
        ];
    }
}
