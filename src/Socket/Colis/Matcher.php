<?php

namespace Server\Socket\Colis;

use App\Kernel;
use Exception;
use React\Promise\PromiseInterface;
use Server\Socket\Payload;
use Server\Socket\Request;
use Server\Socket\Response;
use Throwable;
use function React\Promise\resolve;

class Matcher
{
    protected static string $listenersNamespace = 'App\\Socket\\Listeners\\';

    /**
     * Find appropriate listener for sent command
     * @param Request $request
     * @return Response|PromiseInterface
     * @throws Exception
     */
    public static function match(Request $request)
    {
        //Listener listeners
        $colis = $request->colis();
        //Message
        $payload = $request->payload();
        //If the matching listener listener
        $needle = self::findNeedle($colis, $payload);

        if (!$needle) {
            return resp($request->client())->send(
                'system.response.404',
                "Command '{$payload->command}' not found"
            );
        }

        $providedListener = $needle['listener'];

        if (is_callable($providedListener)) {
            return call_user_func($providedListener, $request);
        }

        $expListener = explode('@', $providedListener);
        $listenerClassName = $expListener[0];
        $listenerMethod = $expListener[1];
        //Listener namespace
        $listenerNS = self::$listenersNamespace . $needle['namespace'];

        //Apply default namespace
        $listenerClassFile = $listenerNS . $listenerClassName;

        try {
            $class = (new $listenerClassFile)->_initAndFeed_([
                'client' => $request->client(),
                'request' => $request,
            ]);

            if (!empty($needle['middleware'])) {
                $middlewares = Kernel::getMiddlewares();
                if (isset($middlewares['colis-middleware'][$needle['middleware']])) {
                    $middleware = $middlewares['colis-middleware'][$needle['middleware']];

                    return (new $middleware())
                        ->handle($request, fn() => resolve())
                        ->then(function () use ($class, $listenerMethod, $request) {
                            return $class->$listenerMethod($request);
                        });
                } else {
                    throw new Exception("Middleware {$needle['middleware']} not found.");
                }
            }

            return $class->$listenerMethod($request);
        } catch (Throwable $exception) {
            handleApplicationException($exception);
            resp($request->client())->send('system.response.500');
        }

        return resp($request->client())->internalServerError();
    }

    /**
     * @param TheColis[] $colis
     * @param Payload $payload
     * @return array
     */
    public static function findNeedle(array $colis, Payload $payload)
    {
        $needle = null;
        foreach ($colis as $coli) {
            $coliData = $coli->getListeners();
            foreach ($coliData['listeners'] as $command => $listener) {
                $listenerName = $coliData['prefix'] . $command;

                if ($listenerName == ($payload->command ?? null)) {
                    $needle = [
                        'namespace' => $coliData['namespace'],
                        'listener' => $listener,
                        'middleware' => $coliData['middleware']
                    ];
                    break;
                }
            }
            if ($needle) break;
        }

        return $needle;
    }
}