<?php


namespace App\Core\Http\Response;


use App\Core\Http\View\View;

class BaseResponse implements ResponseInterface
{

    protected int $statusCode = 200;

    /**
     * @var mixed
     */
    protected $body = null;

    protected $view = null;

    protected array $headers = [];

    protected string $reason = 'ok';

    protected string $version = '1.1';

    protected ResponseInterface $respondWith;


    public static function create(): ResponseInterface
    {
        return new static();
    }

    public function statusCode(int $code = 200): ResponseInterface
    {
        if (200 !== $code) {
            $this->statusCode = $code;
        }

        return $this;
    }

    public function headers($headers = []): ResponseInterface
    {
        if ([] !== $headers) {
            $this->headers += $headers;
        }

        return $this;
    }

    public function reason(string $reason = 'ok'): ResponseInterface
    {
        if ('ok' !== $reason) {
            $this->reason = $reason;
        }

        return $this;
    }

    public function version(string $version = '1.1'): ResponseInterface
    {
        if ('1.1' !== $version) {
            $this->version = $version;
        }

        return $this;
    }

    public function view(string $viewFile, array $params = []): ResponseInterface
    {
        $this->body(View::load($viewFile, $params));
        return $this;
    }

    public function body($body = null): ResponseInterface
    {
        if (null !== $body) {
            $this->body = $body;
        }

        return $this;
    }

    public function with(ResponseInterface $response): ResponseInterface
    {
        $this->respondWith = $response;
        return $response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getView()
    {
        return $this->view;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getWith(): ResponseInterface
    {
        return $this->respondWith;
    }

    public function hasWith(): bool
    {
        return isset($this->respondWith);
    }

    public function hasView(): bool
    {
        return isset($this->view);
    }
}