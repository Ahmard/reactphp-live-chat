<?php

namespace Server\Websocket\Colis;

/**
 * Command listeners registry
 * @package Server\Colis
 * @method static TheColis prefix(string $prefix)
 * @method static TheColis namespace(string $namespace)
 * @method static TheColis middleware(string $middleware)
 * @method static TheColis group(callable $closure)
 * @method static TheColis with(array $withDat)
 * @method static TheColis listen(string $command, $listenerClass)
 */
class Colis
{
    protected static array $called = [];

    /**
     * @param string $name
     * @param array $args
     * @return TheColis
     */
    public static function __callStatic(string $name, array $args)
    {
        $colis = new TheColis();
        self::$called[] = $colis;

        return $colis->$name(...$args);
    }

    /**
     * Get all registered listeners
     * @return array
     */
    public static function getListeners(): array
    {
        require(root_path('routes/websocket.php'));
        return self::$called;
    }
}
