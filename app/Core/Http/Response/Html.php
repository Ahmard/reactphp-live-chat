<?php


namespace App\Core\Http\Response;

class Html extends ResponseFactory
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
        return 200;
    }

    public function body(): string
    {
        return $this->htmlCode;
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
        return 'Ok';
    }
}