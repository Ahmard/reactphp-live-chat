<?php


namespace App\Core\Socket\Colis;


interface ColisInterface
{
    /**
     * Command prefix to listen to
     * @param string $prefix
     * @return $this
     */
    public function prefix(string $prefix): ColisInterface;

    /**
     * Group command listeners together
     * @param callable $callback
     * @return $this
     */
    public function group(callable $callback): ColisInterface;

    /**
     * Register command listener namespace
     * @param string $namespace
     * @return $this
     */
    public function namespace(string $namespace): ColisInterface;

    /**
     * Listen to command
     * @param string $command command to listen to
     * @param callable $listenerClass command handler
     * @return $this
     */
    public function listen(string $command, callable $listenerClass): ColisInterface;

    /**
     * Retrieve list of registered commands
     * @return array
     */
    public function getListeners(): array;
}