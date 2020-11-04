<?php


namespace App\Core\Http\Response;


interface ResponseInterface
{
    public static function create(): self;

    /**
     * Set status code
     * @param int $code
     * @return $this
     */
    public function statusCode(int $code): self;

    /**
     * Set http response body
     * @param mixed $body
     * @return $this
     */
    public function body($body): self;

    /**
     * Set http response headers
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers): self;

    /**
     * Set http response reason
     * @param string $reason
     * @return $this
     */
    public function reason(string $reason): self;

    /**
     * Set server version
     * @param string $version
     * @return $this
     */
    public function version(string $version): self;

    /**
     * Use other response class instead
     * @param ResponseInterface $response
     * @return $this
     */
    public function with(self $response): self;

    /**
     * Send http response with view file
     * @param string $viewFile
     * @param array $viewData
     * @return $this
     */
    public function view(string $viewFile, array $viewData = []): self;

    /**
     * Check whether another response class is used
     * @return bool
     */
    public function hasWith(): bool;

    /**
     * Check whether view file is used
     * @return bool
     */
    public function hasView(): bool;

    /**
     * Get response status code
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Get response body
     * @return mixed
     */
    public function getBody();

    /**
     * Get http response view used
     * @return mixed
     */
    public function getView();

    /**
     * Get response headers
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Get response reason
     * @return string
     */
    public function getReason(): string;

    /**
     * Get server version
     * @return string
     */
    public function getVersion(): string;

    /**
     * Get another response class used
     * @return $this
     */
    public function getWith(): self;
}