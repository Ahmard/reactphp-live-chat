<?php

use App\Core\Helpers\Classes\FormHelper;
use App\Core\Helpers\Classes\RequestHelper;
use App\Core\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * HTTP Response helper
 * @param int $statusCode
 * @return Response
 */
function response(int $statusCode = 200): Response
{
    return new Response($statusCode);
}

/**
 * Send http response with source file content
 * @param string $viewPath
 * @param array $data
 * @return \React\Http\Message\Response
 */
function view(string $viewPath, array $data = []): \React\Http\Message\Response
{
    return response()->view($viewPath, $data);
}

/**
 * Redirect to new url
 * @param string $url
 * @return \React\Http\Message\Response
 */
function redirect(string $url): \React\Http\Message\Response
{
    return \response()->redirect($url);
}

function view_path(?string $viewPath): string
{
    global $slash;
    return root_path("resources{$slash}views{$slash}{$viewPath}");
}

function clientCounter(): int
{
    static $counter = 0;
    $counter++;
    return $counter;
}

/**
 * @param string $key
 * @return mixed|null
 */
function old(string $key)
{
    return FormHelper::getOldData($key);
}

/**
 * @param string $key
 * @return mixed
 */
function form_error(string $key)
{
    return FormHelper::getFormError($key);
}

/**
 * Request helper
 * @return ServerRequestInterface|RequestHelper
 */
function request()
{
    return new RequestHelper();
}

/**
 * Append auth token to constructed url
 * @param string $routePath
 * @return string
 */
function authRoute(string $routePath): string
{
    return url($routePath) . '/' . request()->auth()->token();
}