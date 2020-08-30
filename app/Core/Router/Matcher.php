<?php


namespace App\Core\Router;


use Exception;
use App\Http\Response\NotFound;
use App\Http\Response\StaticFileResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Filesystem\Filesystem;

class Matcher
{
    protected static $controllerNamespace = 'App\\Http\\Controllers\\';


    /**
     * Find route that matches current route
     * @param ServerRequestInterface $request
     * @param array $routes
     * @return Response
     * @throws Exception
     */
    public static function match(ServerRequestInterface $request, array $routes)
    {
        $url = $request->getUri();

        //Check if route exists
        $needle = self::findNeedle($url->getPath(), $routes);
        if (!$needle) {
            return response()->with(NotFound::create());
        }

        //Handle controller
        $routeData = $needle->getRoute();
        $controller = $routeData['controller'];
        $explodedController = explode('@', $controller);
        $controllerClass = $explodedController[0];
        $controllerMethod = $explodedController[1];

        $namespacedController = self::$controllerNamespace . $routeData['namespace'] . $controllerClass;

        $controllerFile = controller_path($routeData['namespace'] . $controllerClass . '.php');
        $controllerFile = str_replace('\\', DIRECTORY_SEPARATOR, $controllerFile);
        //Check if file is readable
        /*if (!is_readable($controllerFile)) {
            throw new Exception("Class {$controllerFile} is not readable.");
        }*/
        
        //Check if file exists
        $ctrlFileP = filesystem()->file($controllerFile);
        return $ctrlFileP->exists()->then(function() use($namespacedController, $request, $controllerMethod){
            return (new $namespacedController())->_initAndFeed_([
                'request' => $request
            ])->$controllerMethod();
        }, function() use($controllerFile){
            throw new Exception("Class {$controllerFile} does not exists.");
        });
    }

    /**
     * @param string $path
     * @param array $routes
     * @return TheRouter|null
     */
    public static function findNeedle(string $path, array $routes)
    {
        foreach ($routes as $route) {
            if ($route->getRoute()['prefix'] == $path) {
                return $route;
            }
        }

        return null;
    }
}