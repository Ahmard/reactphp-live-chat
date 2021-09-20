<?php


namespace App\Servers\Http;

use App\Kernel;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Server\Http\MiddlewareRunner;
use Server\Http\Request;
use Server\Servers\HttpServer;
use Server\Servers\HttpServerInterface;
use Throwable;

class Server extends HttpServer implements HttpServerInterface
{
    public function __invoke(ServerRequestInterface $serverRequest): PromiseInterface
    {
        $request = new Request($serverRequest);

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
            $response->then(function ($finalReturn) use ($deferred, $request) {

                $html = ob_get_contents();

                ob_end_clean();

                if ($finalReturn) {
                    $deferred->resolve($this->generateProperResponse($request, $finalReturn));
                    return;
                }

                if ($html) {
                    $deferred->resolve($this->generateProperResponse($request, $request->getResponse()->ok($html)));
                    return;
                }

                $deferred->resolve($request->getResponse()->internalServerError('Something went wrong and no response is returned'));

            })->otherwise(function (Throwable $exception) use ($deferred, $request) {
                handleApplicationException($exception);
                $deferred->resolve($request->getResponse()->internalServerError($exception));
            });
        } else {
            $html = ob_get_contents();
            ob_end_clean();

            if ($html) {
                $response = $request->getResponse()->ok($html);
            } else {
                $response = $this->generateProperResponse($request, $response);
            }

            $deferred->resolve($response);
        }

        return $deferred->promise();
    }

    /**
     * @param Request $request
     * @param mixed $response
     * @return Response
     */
    public function generateProperResponse(Request $request, $response): Response
    {
        if ($response instanceof PromiseInterface) {
            return $response->then(function ($returnedResponse) use ($request) {
                return $this->generateProperResponse($request, $returnedResponse);
            })->otherwise(function ($returnedResponse) use ($request) {
                return $this->generateProperResponse($request, $returnedResponse);
            });
        } elseif (!$response instanceof Response) {
            //Let's see if object is callable
            if (is_callable($response)) {
                return $this->generateProperResponse($request, $response());
            } //Since object is not callable, let's figure out a way to handle it
            else {
                //if object can be used as string
                switch ($response) {
                    case ($response instanceof Throwable):
                        if ($_ENV['APP_ENVIRONMENT'] == 'development') {
                            $response = $request->getResponse()->ok($response);
                        } else {
                            $response = $request->getResponse()->internalServerError('Server returns an unexpected response, please check server logs');
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
                        $response = $request->getResponse()->ok($response);
                        break;
                    case (is_array($response)):
                        $response = $request->getResponse()->json($response);
                        break;
                    default:
                        $briefLogNAme = 'logs/http-response-' . date('d_m_Y-H_i_s') . '.log';
                        $responseLogFile = root_path('storage/' . $briefLogNAme);
                        $message = "Server returns an unexpected response.\n Please check {$responseLogFile}.";
                        file_put_contents($responseLogFile, serialize($response));
                        handleApplicationException(new Exception($message));
                        $response = $request->getResponse()->internalServerError($message);
                        break;
                }
            }
        }

        return $response;
    }

}