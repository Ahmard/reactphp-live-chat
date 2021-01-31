<?php


namespace App\Providers;

use App\Core\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    public static string $controllerNamespace = 'App\\Http\\Controllers\\';

    public static string $routeTokenPrefix = '{primaryToken}';

    public function boot(): void
    {
        //
    }
}