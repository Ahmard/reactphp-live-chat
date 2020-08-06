<?php

use App\Http\Response;

require 'generalHelperFunctions.php';

function response(int $statusCode = 200)
{
    return new Response($statusCode);
}

function view_path(?string $viewPath): string
{
    global $slash;
    return root_path("resources{$slash}views{$slash}{$viewPath}");
}

function clientCounter()
{
    static $counter = 0;
    $counter++;
    return $counter;
}