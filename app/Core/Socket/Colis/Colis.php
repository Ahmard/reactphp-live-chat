<?php

namespace App\Core\Socket\Colis;

/**
 * Command listeners registry
 * @package App\Core\Colis
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
    public static function getListeners()
    {
        require(root_path('routes/socket.php'));
        return self::$called;
    }
}
