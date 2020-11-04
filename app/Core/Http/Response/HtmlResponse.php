<?php


namespace App\Core\Http\Response;


/**
 * Class HtmlResponse
 * @package App\Core\Http\Response
 */
final class HtmlResponse extends BaseResponse
{
    public function __construct(string $body)
    {
        $this->body = $body;

        $this->headers = [
            'Content-Type' => 'text/html'
        ];
    }

    public static function create(string $body = ''): ResponseInterface
    {
        return new static($body);
    }
}