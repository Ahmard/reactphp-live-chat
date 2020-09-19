<?php


namespace App\Servers\Http;

use App\Core\Helpers\Classes\RequestHelper;
use App\Core\Helpers\Classes\SessionHelper;
use App\Core\Middleware;
use App\Core\Router\Route;
use App\Core\Servers\HttpServer;
use App\Core\Servers\HttpServerInterface;
use App\Kernel;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use function FastRoute\simpleDispatcher;

class Server extends HttpServer implements HttpServerInterface
{
    private Dispatcher $dispatcher;

    public function __construct()
    {
        //Load web routes
        $this->dispatcher = simpleDispatcher(function (RouteCollector $routeCollector) {
            //Retrieve routes
            $routes = Route::getRoutes();
            //Register routes
            foreach ($routes as $route) {
                $route->registerRoute($routeCollector);
            }
        });
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $request->__dispatcher__ = $this->dispatcher;

        RequestHelper::setRequest($request);
        SessionHelper::setRequest($request);

        //session()->set('user_id', 1);

        //Auth::handle();

        $registeredMiddlewares = Kernel::getMiddlewares();

        $middlewares = $registeredMiddlewares['middlewares'];
        $groupedMiddlewares = $registeredMiddlewares['middleware-groups'];

        ob_start();

        //Run general middlewares
        $response = Middleware::run($request, $middlewares);

        //Run grouped middlewares
        $response = Middleware::run($request, $groupedMiddlewares, $response, 'web');

        $html = ob_get_contents();

        ob_end_clean();

        if ($html) {
            $response = response()->ok($html);
        }

        if (gettype($response) == 'string') {
            $response = response()->ok($response);
        }

        return $response;
    }

}