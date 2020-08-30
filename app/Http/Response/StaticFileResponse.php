<?php

namespace App\Http\Response;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Filesystem\Node\File;

class StaticFileResponse extends ResponseFactory
{
    public function __invoke($request, $next) 
    {
        $url = $request->getUri();

        $parsedUrl = parse_url($url);

        //Handle files in public dir
        $fileToCheck = public_path(substr($parsedUrl['path'], 1, strlen($parsedUrl['path'])));

        $file = filesystem()->file($fileToCheck);
        
        $nextRequest = function() use($next, $request)
        {
            return $next($request);
        };
        
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
    }
}