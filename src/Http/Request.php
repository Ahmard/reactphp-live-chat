<?php

namespace Server\Http;

use Psr\Http\Message\ServerRequestInterface;
use QuickRoute\Router\DispatchResult;
use Server\Auth\Auth;
use Server\Helpers\Classes\FormHelper;

/**
 * @mixin ServerRequestInterface
 */
class Request
{
    protected ServerRequestInterface $request;
    protected FormHelper $formHelper;
    protected DispatchResult $dispatchResult;
    protected ?Auth $auth = null;
    protected string $token = '';

    public function __construct(
        ServerRequestInterface $request
    )
    {
        $this->request = $request;
        $this->formHelper = new FormHelper($this);
    }

    public function init($name, $value): void
    {
        $this->$name = $value;
    }

    public function __get(string $name)
    {
        return $this->request->$name;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->request->$name(...$arguments);
    }

    public function expectsJson(): bool
    {
        if (
            $this->request->hasHeader('X-Requested-With')
            && $this->request->getHeaderLine('X-Requested-With') == 'XMLHttpRequest'
        ) {
            return true;
        }

        return false;
    }

    public function expectsHtml(): bool
    {
        $contentType = $this->request->getHeaderLine('Accept');
        $headers = explode(',', $contentType);
        if (in_array('text/html', $headers)) {
            return true;
        }

        return false;
    }

    /**
     * Append auth token to constructed url
     *
     * @param string $routePath
     * @return string
     */
    public function authRoute(string $routePath): string
    {
        if (empty($_ENV['DOMAIN'])) {
            return "/$routePath/" . $this->auth()->token();
        }

        return url($routePath) . '/' . $this->auth()->token();
    }

    public function auth(): Auth
    {
        return $this->auth ?? new Auth(md5('asdsadqwsqasdasdsada'));
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return new Response($this);
    }

    /**
     * @return FormHelper
     */
    public function form(): FormHelper
    {
        return $this->formHelper;
    }

    /**
     * @param string|null $fieldName
     * @return array|mixed|object|null
     */
    public function post(?string $fieldName = null)
    {
        if (!$fieldName) {
            return $this->getParsedBody();
        }

        return $this->getParsedBody()[$fieldName] ?? null;
    }

    public function isGET(): bool
    {
        return 'GET' == $this->request->getMethod();
    }

    public function isPOST(): bool
    {
        return 'POST' == $this->request->getMethod();
    }

    /**
     * Get route parameter
     *
     * @param string|null $name
     * @return mixed
     */
    public function getParam(?string $name = null)
    {
        if (null !== $name) {
            return $this->getDispatchResult()->getUrlParameters()[$name] ?? null;
        }

        return $this->getDispatchResult()->getUrlParameters();
    }

    /**
     * @return DispatchResult
     */
    public function getDispatchResult(): DispatchResult
    {
        return $this->dispatchResult;
    }

    public function getToken(): string
    {
        if ('' == $this->token) {
            $expUrl = explode('/', $this->request->getUri()->getPath());
            $routeToken = end($expUrl);
            $expRouteToken = explode('?', $routeToken);
            $this->token = current($expRouteToken);
        }

        return $this->token;
    }
}