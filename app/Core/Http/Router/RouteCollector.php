<?php


namespace App\Core\Http\Router;


use App\Providers\HttpServiceProvider;
use QuickRoute\Route\Collector;

class RouteCollector
{
    protected static Collector $collector;

    protected static string $controllerNamespace = 'App\\Http\\Controllers\\';

    public static function collectRoutes()
    {
        $controllerNamespace = self::$controllerNamespace;
        if (class_exists(HttpServiceProvider::class)) {
            $controllerNamespace = HttpServiceProvider::$controllerNamespace;
        }

        self::$collector = Collector::create();

        //Web routes
        self::$collector->collectFile(
            root_path('routes/web.php'),
            [
                'namespace' => $controllerNamespace,
            ]
        );

        //Api routes
        self::$collector->collectFile(
            root_path('routes/api.php'),
            [
                'namespace' => $controllerNamespace,
                'prefix' => 'api',
                'name' => 'api.',
            ]
        );
    }

    public static function register()
    {
        self::$collector->register();
    }

    /**
     * @return Collector
     */
    public static function getCollector(): Collector
    {
        return self::$collector;
    }
}