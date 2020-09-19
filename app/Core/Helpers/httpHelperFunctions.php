<?php

use App\Core\Helpers\Classes\FormHelper;
use App\Core\Helpers\Classes\RequestHelper;
use App\Core\Helpers\Classes\SessionHelper;
use App\Core\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

function response(int $statusCode = 200)
{
    return new Response($statusCode);
}

function view(string $viewPath, array $data = [])
{
    return response()->view($viewPath, $data);
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

function old(string $key)
{
    return FormHelper::getOldData($key);
}

function form_error(string $key)
{
    return FormHelper::getFormError($key);
}

function request(): ServerRequestInterface
{
    return RequestHelper::getInstance();
}

function session(): SessionHelper
{
    return SessionHelper::getInstance();
}