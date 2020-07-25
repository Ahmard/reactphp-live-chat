<?php

namespace App\Core\Colis;

/**
 * Command listeners registry
 * @package App\Core\Colis
 * @method static TheColis prefix(string $prefix)
 * @method static TheColis namespace(string $namespace)
 * @method static TheColis group(callable $closure)
 * @method static TheColis with(array $withDat)
 * @method static TheColis listen(string $command, string $listenerClass)
 */
class Colis
{
    protected static array $called = [];

    /**
     * @param $name
     * @param $args
     * @return TheColis
     */
    public static function __callStatic($name, $args)
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
        return self::$called;
    }
}
