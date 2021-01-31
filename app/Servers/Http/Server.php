<?php


namespace App\Servers\Http;

use App\Core\Helpers\Classes\RequestHelper;
use App\Core\Http\MiddlewareRunner;
use App\Core\Http\Response\ResponseInterface;
use App\Core\Http\Router\Dispatcher;
use App\Core\Servers\HttpServer;
use App\Core\Servers\HttpServerInterface;
use App\Kernel;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Throwable;
use function response;

class Server extends HttpServer implements HttpServerInterface
{
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        RequestHelper::setRequest($request);
        Dispatcher::setRequest($request);

        $registeredMiddlewares = Kernel::getMiddlewares();

        $middlewares = $registeredMiddlewares['middlewares'];
        $groupedMiddlewares = $registeredMiddlewares['middleware-groups'];
        $html = null;

        ob_start();

        //Run general middlewares
        $response = MiddlewareRunner::run($request, $middlewares);

        //Run grouped middlewares
        $response = MiddlewareRunner::run(
            $request,
            $groupedMiddlewares,
            $response,
            'web'
        );


        $deferred = new Deferred();

        //Validating response return value
        if ($response instanceof PromiseInterface) {
            $response->then(function ($finalReturn) use ($deferred) {

                $html = ob_get_contents();

                ob_end_clean();

                if ($finalReturn) {
                    $deferred->resolve($this->generateProperResponse($finalReturn));
                    return;
                }

                if ($html) {
                    $deferred->resolve($this->generateProperResponse(response()->ok($html)));
                    return;
                }

                $deferred->resolve(response()->internalServerError('Something went wrong and no response is returned'));

            })->otherwise(function (Throwable $exception) use ($deferred) {
                handleApplicationException($exception);
                $deferred->resolve(response()->internalServerError($exception));
            });
        } else {
            $html = ob_get_contents();
            ob_end_clean();

            if ($html) {
                $response = response()->ok($html);
            } else {
                $response = $this->generateProperResponse($response);
            }

            $deferred->resolve($response);
        }

        return $deferred->promise();
    }

    /**
     * @param mixed $response
     * @return Response
     */
    public function generateProperResponse($response): Response
    {
        if ($response instanceof PromiseInterface) {
            return $response->then(function ($returnedResponse) {
                return $this->generateProperResponse($returnedResponse);
            })->otherwise(function ($returnedResponse) {
                return $this->generateProperResponse($returnedResponse);
            });
        } elseif ($response instanceof ResponseInterface) {
            //Handle local response class
            return \App\Core\Http\Response::respondWith($response);
        } elseif (!$response instanceof Response) {
            //Let's see if object is callable
            if (is_callable($response)) {
                return $this->generateProperResponse($response());
            } //Since object is not callable, let's figure out a way to handle it
            else {
                //if object can be used as string
                switch ($response) {
                    case ($response instanceof Throwable):
                        if ($_ENV['APP_ENVIRONMENT'] == 'development') {
                            $response = response()->ok($response);
                        } else {
                            $response = response()->internalServerError('Server returns an unexpected response, please check server logs');
                            //handleApplicationException($response);
                        }
                        break;
                    case (
                        is_string($response) ||
                        is_int($response) ||
                        is_float($response) ||
                        is_double($response) ||
                        is_bool($response)
                    ):
                        $response = response()->ok($response);
                        break;
                    case (is_array($response)):
                        $response = response()->json($response);
                        break;
                    default:
                        $briefLogNAme = 'logs/http-response-' . date('d_m_Y-H_i_s') . '.log';
                        $responseLogFile = root_path('storage/' . $briefLogNAme);
                        $message = "Server returns an unexpected response.\n Please check {$responseLogFile}.";
                        file_put_contents($responseLogFile, serialize($response));
                        handleApplicationException(new Exception($message));
                        $response = response()->internalServerError($message);
                        break;
                }
            }
        }

        return $response;
    }

}