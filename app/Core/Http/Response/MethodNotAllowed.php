<?php


namespace App\Core\Http\Response;

use App\Core\Http\View\View;

class MethodNotAllowed extends ResponseFactory
{
    protected $htmlCode;

    public function __construct($htmlCode)
    {
        $this->htmlCode = $htmlCode;
    }

    public static function create($htmlCode = ''): ResponseFactory
    {
        return new self($htmlCode);
    }

    public function statusCode(): int
    {
        return 405;
    }

    public function body(): string
    {
        return View::load('system/405.php');
    }

    public function headers(): array
    {
        return ['Content-Type' => 'text/html'];
    }

    public function version(): string
    {
        return '1.1';
    }

    public function reason(): string
    {
        return 'Method Not Allowed.';
    }
}