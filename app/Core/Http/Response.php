<?php


namespace App\Core\Http;

use App\Core\Http\Response\HtmlResponse;
use App\Core\Http\Response\InternalServerErrorResponse;
use App\Core\Http\Response\JsonResponse;
use App\Core\Http\Response\MethodNotAllowedResponse;
use App\Core\Http\Response\NotFoundResponse;
use App\Core\Http\Response\RedirectResponse;
use App\Core\Http\Response\ResponseInterface;
use App\Core\Http\View\View;
use Exception;
use React\Http\Message\Response as HttpResponse;
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

    /**
     * Response constructor.
     * @param int $statusCode
     */
    public function __construct(int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Create response from another response data
     * @param ResponseInterface $response
     * @return HttpResponse
     */
    public static function respondWith(ResponseInterface $response): HttpResponse
    {
        return (new Response())->with($response);
    }

    /**
     * Send response with classes
     * @param ResponseInterface $response
     * @return HttpResponse
     */
    public function with(ResponseInterface $response): HttpResponse
    {
        if ($response->hasWith()) {
            return $this->with($response->getWith());
        }

        return $this->sendResponse(
            $response->getStatusCode(),
            $response->getBody(),
            $response->getHeaders(),
            $response->getVersion(),
            $response->getReason(),
        );
    }

    /**
     * @param int $code
     * @param string $body
     * @param array $headers
     * @param string $version
     * @param string $reason
     * @return HttpResponse
     */
    protected function sendResponse(
        int    $code,
        string $body,
        array  $headers = [],
        string $version = '',
        string $reason = ''
    ): HttpResponse
    {
        $headers = $headers ?? $this->headers;
        $reason = $reason ?? $this->reason ?? null;
        $version = $version ?? $this->version ?? null;
        $statusCode = $code ?? $this->statusCode;

        return new HttpResponse($statusCode, $headers, $body, $version, $reason);
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
    public function ok(string $body, $headers = []): HttpResponse
    {
        return $this->sendResponse(200, $body, $headers);
    }

    /**
     * @param string $view
     * @param array $data
     * @return HttpResponse
     */
    public function view(string $view, array $data = []): HttpResponse
    {
        return $this->with(HtmlResponse::create(
            View::load($view, $data)
        ));
    }

    /**
     * Send 404 response
     * @return HttpResponse
     */
    public function notFound(): HttpResponse
    {
        return $this->with(NotFoundResponse::create());
    }

    /**
     * Send 500 response
     * @param string|null|Throwable $exception
     * @return HttpResponse
     */
    public function internalServerError($exception = null): HttpResponse
    {
        if (is_string($exception)) {
            $exception = new Exception($exception);
        }

        return $this->with(InternalServerErrorResponse::create($exception));
    }

    public function methodNotAllowed(): HttpResponse
    {
        return $this->with(MethodNotAllowedResponse::create());
    }

    /**
     * Redirect to new url
     * @param string $url
     * @return HttpResponse
     */
    public function redirect(string $url): HttpResponse
    {
        return $this->with(RedirectResponse::create($url));
    }

    /**
     * @param mixed $body
     * @param int $statusCode
     * @param array $headers
     * @return HttpResponse
     */
    public function json($body, int $statusCode = 200, array $headers = []): HttpResponse
    {
        return $this->with(
            JsonResponse::create($body)
                ->statusCode($statusCode)
                ->headers($headers)
        );
    }
}