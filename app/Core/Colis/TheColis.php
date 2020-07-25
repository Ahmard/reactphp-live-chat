<?php
namespace App\Core\Colis;

class TheColis
{
    public $prefix = '';
    
    public $namespace = '';
    
    public $listeners = [];
    
    public $isWithUsed = false;

    /**
     * @param $withData
     * @return TheColis $this
     */
    public function with(array $withData)
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
    public function prefix($prefix)
    {
        if($this->prefix && !$this->isWithUsed){
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
    public function group(callable $closure)
    {
        $closure($this);
        return $this;
    }

    /**
     * Add namespace to listener groups
     * @param string $namespace
     * @return TheColis $this
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
     * Listen to command
     * @param string $command
     * @param string $class
     * @return TheColis $this
     */
    public function listen($command, $listenerClass)
    {
        $this->listeners[$command] = $listenerClass;
        return $this;
    }

    /**
     * Retrieve listeners defined in this object
     * @return array
     */
    public function getListeners()
    {
        return [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'listeners' => $this->listeners,
        ];
    }
}
