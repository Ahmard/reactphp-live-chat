<?php

namespace App\Core\Colis;

use App\Socket\Request;

class Matcher
{
    protected static $listenersNamespace = 'App\\Listeners\\';

    /**
     * Find appropriate listener for sent command
     * @param Request $request
     * @throws \Exception
     */
    public static function match(Request $request)
    {
        //Listener listeners
        $colis = $request->colis;
        //Message
        $message = $request->message = json_decode($request->message);
        //If the matching listener listener 
        $needle = self::findNeedle($colis, $message);

        if ($needle) {
            $expListener = explode('@', $needle['listener']);
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
                throw new \Exception("Class {$listenerFile} does not exists.");
            }
            //Check if file is readable
            if (!is_readable($listenerFile)) {
                throw new \Exception("Class {$listenerFile} is not readable.");
            }

            $class = (new $listenerClassFile($request))->_initAndFeed_([
                'client' => $request->client,
                'request' => $request,
            ]);

            $class->$listenerMethod($request);
        }
    }

    public static function findNeedle($colis, $message)
    {
        $needle = null;
        foreach ($colis as $coli) {
            foreach ($coli->listeners as $command => $listener) {
                $listenerName = $coli->prefix . $command;

                if ($listenerName == $message->command) {
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