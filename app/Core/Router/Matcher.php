<?php


namespace App\Core\Router;


use Exception;
use App\Http\Response\NotFound;
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
    public static function match(ServerRequestInterface $request, array $routes): Response
    {
        $url = $request->getUri();

        chdir(public_path());

        $parsedUrl = parse_url($url);

        //Handle files in public dir
        $fileToCheck = public_path(substr($parsedUrl['path'], 1, strlen($parsedUrl['path'])));
        if (file_exists($fileToCheck) && is_file($fileToCheck)) {
            $fileContent = file_get_contents($fileToCheck);
            //Get file mime
            $expFile = explode('.', $fileToCheck);
            $fileMime = config('mime')[end($expFile)] ?? 'text/plain';
            //Send response with file source
            return new Response(200, [
                'Content-Type' => $fileMime,
                'Access-Control-Allow-Origin' => '*',
            ], $fileContent);
        }

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
        //Check if file exists
        if (!file_exists($controllerFile)) {
            throw new Exception("Class {$controllerFile} does not exists.");
        }
        //Check if file is readable
        if (!is_readable($controllerFile)) {
            throw new Exception("Class {$controllerFile} is not readable.");
        }

        $initializedController = (new $namespacedController())->_initAndFeed_([
            'request' => $request
        ]);

        return $initializedController->$controllerMethod();
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