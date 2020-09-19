<?php


namespace App\Core\Http;

use App\Core\Http\Response\Html;
use App\Core\Http\Response\NotFound;
use App\Core\Http\Response\ResponseFactory;
use App\Core\Http\View\View;
use React\Http\Message\Response as HttpResponse;
use WyriHaximus\React\Http\Middleware\SessionMiddleware;

/**
 * Class Response
 * @package App\HttpServer
 */
class Response
{
    protected $statusCode = 200;

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

        //Send request along with registered session variables
        request()->getAttribute(SessionMiddleware::ATTRIBUTE_NAME)
            ->setContents(session()->getSessionData());

        return new HttpResponse($statusCode, $headers, $body, $version, $reason);
    }

    /**
     * @param string $view
     * @param array $data
     * @return HttpResponse
     */
    public function view(string $view, array $data = [])
    {
        return $this->with(Html::create(View::load($view, $data)));
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
     * Send 404 response
     * @return HttpResponse
     */
    public function notFound()
    {
        return $this->with(NotFound::create(View::load('system/404')));
    }

    /**
     * Send 500 response
     * @return HttpResponse
     */
    public function internalServerError()
    {
        return $this->with(NotFound::create(View::load('system/500')));
    }

    public function redirect($url)
    {
        return $this->json($url);
    }

    /**
     * @param $body
     * @param array $headers
     */
    public function json($body, array $headers = [])
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }
        return $this->sendResponse($body, $headers);
    }
}