<?php

namespace App\Core\Http\Response;

use React\Http\Message\Response;

class StaticFileResponse extends ResponseFactory
{
    public function __invoke($request, $next)
    {
        $url = $request->getUri();

        if ($_ENV['SHOW_HTTP_RESOURCE_REQUEST'] == 'true') {
            echo "\n" . date('H:i:s');
            echo " -> New request({$url}).\n";
        }

        $parsedUrl = parse_url($url);

        //Handle files in public dir
        $fileToCheck = public_path(substr($parsedUrl['path'], 1, strlen($parsedUrl['path'])));

        $file = filesystem()->file($fileToCheck);

        $nextRequest = function () use ($next, $request) {
            return $next($request);
        };

        //Non-blocking code
        /*
        return $file->stat()
            ->then(function($stat) use($file, $fileToCheck, $nextRequest) {
                if((0x8000 & $stat['mode']) == 0x8000){
                    return $file->getContents()->then(function($content) use($file,$fileToCheck) {
                        //Get file mime
                        $expFile = explode('.', $fileToCheck);
                        
                        $fileMime = config('mime')[end($expFile)] ?? 'text/plain';
                        //Send response with file source
                        return new Response(200, [
                            'Content-Type' => $fileMime,
                            'Access-Control-Allow-Origin' => '*',
                        ], $content);
                    });
                }else{
                    return $nextRequest();
                }
            }, function() use($nextRequest) {
                return $nextRequest();
            });
        */
        //Blocking code
        if (is_file($fileToCheck)) {
            $content = file_get_contents($fileToCheck);
            //Get file mime
            $expFile = explode('.', $fileToCheck);

            $fileMime = config('mime')[end($expFile)] ?? 'text/plain';
            //Send response with file source
            return new Response(200, [
                'Content-Type' => $fileMime,
                'Access-Control-Allow-Origin' => '*',
            ], $content);
        }

        return $nextRequest();
    }
}