<?php


namespace App\Http;


use App\Core\Router\Matcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Server
{
    protected $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if($_ENV['SHOW_HTTP_RESOURCE_REQUEST'] == 'true'){
            echo "\n" . date('H:i:s');
            echo " -> New request({$request->getUri()}).\n";
        }
        
        return Matcher::match($request, $this->routes);
    }
}