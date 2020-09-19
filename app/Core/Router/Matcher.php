<?php


namespace App\Core\Router;


use App\Core\Helpers\Classes\FormHelper;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class Matcher
{
    protected static string $controllerNamespace = 'App\\Http\\Controllers\\';


    /**
     * Find route that matches current route
     * @param ServerRequestInterface $request
     * @param array $routeData
     * @return PromiseInterface
     */
    public static function match(ServerRequestInterface $request, array $routeData)
    {
        //Handle controller
        $controller = $routeData['controller'];
        if (is_callable($controller)) {
            return call_user_func($controller, $request);
        }

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
        return $ctrlFileP->exists()->then(function () use ($namespacedController, $request, $controllerMethod) {
            //Initialize form helpers
            FormHelper::setRequest($request);
            //Call defined method
            return (new $namespacedController())->_initAndFeed_([
                'request' => $request
            ])->$controllerMethod();
        }, function () use ($controllerFile) {
            throw new Exception("Class {$controllerFile} does not exists.");
        });
    }
}