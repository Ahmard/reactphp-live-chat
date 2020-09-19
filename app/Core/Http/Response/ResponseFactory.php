<?php


namespace App\Core\Http\Response;


class ResponseFactory
{
    public static function create(): self
    {
        return new self();
    }

    public function statusCode(): int
    {
        return 200;
    }

    public function body()
    {
        return 'Hello, This is some magic of ReactPHP';
    }

    public function headers(): array
    {
        return ['Content-type' => 'text/html'];
    }

    public function reason(): string
    {
        return 'ok';
    }

    public function version(): string
    {
        return '1.1';
    }
}