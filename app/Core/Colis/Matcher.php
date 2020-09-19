<?php

namespace App\Core\Colis;

use App\Core\Socket\Request;
use Exception;

class Matcher
{
    protected static string $listenersNamespace = 'App\\Socket\\Listeners\\';

    /**
     * Find appropriate listener for sent command
     * @param Request $request
     * @throws Exception
     */
    public static function match(Request $request)
    {
        //Listener listeners
        $colis = $request->colis;
        //Message
        $payload = $request->payload;
        //If the matching listener listener 
        $needle = self::findNeedle($colis, $payload);

        if ($needle) {
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

            //Command file
            $listenerFile = listener_path($needle['namespace'] . $listenerClassName . '.php');
            $listenerFile = str_replace('\\', DIRECTORY_SEPARATOR, $listenerFile);
            //Check if file exists
            if (!file_exists($listenerFile)) {
                throw new Exception("Class {$listenerFile} does not exists.");
            }
            //Check if file is readable
            if (!is_readable($listenerFile)) {
                throw new Exception("Class {$listenerFile} is not readable.");
            }

            $class = (new $listenerClassFile($request))->_initAndFeed_([
                'client' => $request->client,
                'request' => $request,
            ]);

            return $class->$listenerMethod($request);
        }

        return resp($request->client)->send('system.response.404', 'Command not found');
    }

    public static function findNeedle($colis, $payload)
    {
        $needle = null;
        foreach ($colis as $coli) {
            foreach ($coli->listeners as $command => $listener) {
                $listenerName = $coli->prefix . $command;

                if ($listenerName == ($payload->command ?? null)) {
                    $needle = [
                        'namespace' => $coli->namespace,
                        'listener' => $listener
                    ];
                    break;
                }
            }
            if ($needle) break;
        }
        return $needle;
    }
}