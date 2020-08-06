<?php


namespace App\Http;

use App\Http\Response\Html;
use App\Http\Response\ResponseFactory;
use App\Http\View\View;
use React\Http\Message\Response as HttpResponse;

/**
 * Class Response
 * @package App\Http
 */
class Response
{
    protected $statusCode;

    protected $headers = [];

    protected $reason;

    protected $version = '1.1';

    /**
     * Response constructor.
     * @param int|void $statusCode
     */
    public function __construct(int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers): Response
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @param string $reason
     * @return $this
     */
    public function reason(string $reason): Response
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function version(string $version): Response
    {
        $this->$version = $version;
        return $this;
    }

    /**
     * @param string $body
     * @param array $headers
     * @return HttpResponse
     */
    public function ok(string $body, $headers = []): HttpResponse
    {
        $this->statusCode = 200;
        return $this->sendResponse($body, $headers);
    }

    /**
     * @param string $body
     * @param array $headers
     * @param string $version
     * @param string $reason
     * @return HttpResponse
     */
    protected function sendResponse(
        string $body,
        array $headers = [],
        string $version = '',
        string $reason = ''
    ): HttpResponse
    {
        $headers = $headers ?? $this->headers;
        $reason = $reason ?? $this->reason ?? null;
        $version = $version ?? $this->version ?? null;
        $statusCode = $this->statusCode;
        return new HttpResponse($statusCode, $headers, $body, $version, $reason);
    }

    /**
     * Send response with classes
     * @param ResponseFactory $responseClass
     * @return HttpResponse
     */
    public function with(ResponseFactory $responseClass): HttpResponse
    {
        $this->statusCode = $responseClass->statusCode();

        return $this->sendResponse(
            $responseClass->body(),
            $responseClass->headers(),
            $responseClass->version(),
            $responseClass->reason(),
        );
    }

    /**
     * @param $body
     * @param array $headers
     */
    public function json($body, array $headers = [])
    {
        $this->sendResponse($body, $headers);
    }

    /**
     * @param string $view
     * @return HttpResponse
     */
    public function view(string $view, array $data = [])
    {
        return $this->with(Html::create(View::load($view, $data)));
    }
}