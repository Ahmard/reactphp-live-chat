<?php


namespace App\Core\Http\Response;


final class JsonResponse extends BaseResponse
{
    public function __construct($body)
    {
        $this->body($body);

        $this->headers = [
            'Content-Type' => 'application/json'
        ];
    }

    public function body($body = []): ResponseInterface
    {
        if (!is_string($body)) {
            $body = json_encode($body);
        }

        $this->body = $body;

        return $this;
    }

    public static function create($body = []): ResponseInterface
    {
        return new static($body);
    }

    public function success($data)
    {
        return $this->body([
            'success' => true,
            'data' => $data
        ]);
    }

    public function error(array $data)
    {
        return $this->body([
            'success' => false,
            'error' => $data
        ]);
    }
}