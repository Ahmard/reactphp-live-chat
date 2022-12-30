<?php


namespace Server\Http;

use React\Http\Message\Response as HttpResponse;
use Server\Http\View\View;
use Throwable;

/**
 * Class Response
 * @package App\HttpServer
 */
class Response
{
    protected int $statusCode = 200;

    protected array $headers = [];

    protected string $reason;

    protected string $version = '1.1';

    protected Request $request;


    /**
     * Response constructor.
     * @param Request $request
     * @param int $statusCode
     */
    public function __construct(Request $request, int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
        $this->request = $request;
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
     * Set version
     * @param string $version
     * @return $this
     */
    public function version(string $version): Response
    {
        $this->$version = $version;
        return $this;
    }

    /**
     * Send 200 status response
     * @param string $body
     * @param array $headers
     * @return HttpResponse
     */
    public function ok(string $body, array $headers = []): HttpResponse
    {
        return $this->sendResponse($body,200,  $headers);
    }

    /**
     * @param int $code
     * @param string $body
     * @param array $headers
     * @param string|null $version
     * @param string|null $reason
     * @return HttpResponse
     */
    protected function sendResponse(
        string  $body,
        int     $code = 200,
        array   $headers = [],
        ?string $version = null,
        ?string $reason = null
    ): HttpResponse
    {
        $headers = array_merge($this->headers, $headers);
        $reason = $reason ?? $this->reason ?? null;
        $version = $version ?? $this->version ?? null;

        return new HttpResponse($code, $headers, $body, $version, $reason);
    }

    /**
     * @param string $view
     * @param array $data
     * @return HttpResponse
     */
    public function view(string $view, array $data = []): HttpResponse
    {
        return $this->html(View::create($this->request)->load($view, $data));
    }

    /**
     * @param string $code
     * @param int $status
     * @param array $headers
     * @return HttpResponse
     */
    public function html(string $code, int $status = 200, array $headers = []): HttpResponse
    {
        return $this->sendResponse(
            $code,
            $status,
            array_merge(['Content-Type' => 'text/html'], $headers),
        );
    }

    /**
     * Send 404 response
     * @return HttpResponse
     */
    public function notFound(): HttpResponse
    {
        return $this->conditionalResponse(
            htmlBody: View::create($this->request)->load('system/404'),
            code: 404,
            apiBody: [
                'status' => false,
                'message' => 'The resources you are looking does not exists.'
            ]
        );
    }

    public function conditionalResponse(
        string $htmlBody,
        int    $code,
        array  $apiBody = [],
        array  $headers = []
    ): HttpResponse
    {
        if ($this->request->expectsJson()) {
            return $this->json($apiBody, $code, $headers);
        }

        return $this->html($htmlBody, $code, $headers);
    }

    /**
     * @param mixed $body
     * @param int $statusCode
     * @param array $headers
     * @return HttpResponse
     */
    public function json($body, int $statusCode = 200, array $headers = []): HttpResponse
    {
        return $this->sendResponse(
            json_encode($body),
            $statusCode,
            array_merge(['Content-Type' => 'application/json'], $headers)
        );
    }

    /**
     * Send 500 response
     * @param string|null|Throwable $exception
     * @return HttpResponse
     */
    public function internalServerError($exception = null): HttpResponse
    {
        return $this->conditionalResponse(
            View::create($this->request)->load('system/500', ['exception' => $exception]),
            500,
            [
                'status' => false,
                'message' => $exception,
            ]
        );
    }

    public function methodNotAllowed(): HttpResponse
    {
        return $this->conditionalResponse(
            View::create($this->request)->load('system/405'),
            405,
            [
                'status' => false,
                'message' => 'method not allowed.'
            ]
        );
    }

    /**
     * Redirect to new url
     * @param string $url
     * @return HttpResponse
     */
    public function redirect(string $url): HttpResponse
    {
        return $this->conditionalResponse(
            View::create($this->request)->load('system/302', [
                'url' => $url
            ]),
            302,
            [
                'status' => false,
                'message' => "Redirecting yo to $url"
            ],
            ['Location' => $url]
        );
    }

    public function jsonSuccess(array|object $data): HttpResponse
    {
        return $this->json([
            'status' => 200,
            'success' => true,
            'data' => $data
        ]);
    }

    public function jsonSuccessMessage(string $message): HttpResponse
    {
        return $this->json([
            'status' => 200,
            'success' => true,
            'data' => ['message' => $message]
        ]);
    }

    public function jsonError(string|Throwable $data): HttpResponse
    {
        if ($data instanceof Throwable) {
            $data = [
                'exception' => [
                    'message' => $data->getMessage(),
                    'trace' => $data->getTrace(),
                    'file' => $data->getFile(),
                    'line' => $data->getLine(),
                    'code' => $data->getCode(),
                ]
            ];
        }

        return $this->json([
            'status' => 500,
            'success' => false,
            'error' => $data
        ]);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}