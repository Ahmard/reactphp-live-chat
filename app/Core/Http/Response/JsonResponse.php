<?php


namespace App\Core\Http\Response;


final class JsonResponse extends BaseResponse
{
    /**
     * JsonResponse constructor.
     * @param mixed $body
     */
    public function __construct($body)
    {
        $this->body($body);

        $this->headers = [
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * @param mixed $body
     * @return ResponseInterface
     */
    public function body($body = []): ResponseInterface
    {
        if (!is_string($body)) {
            $body = json_encode($body);
        }

        $this->body = $body;

        return $this;
    }

    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public static function success($data): ResponseInterface
    {
        return JsonResponse::create([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @param mixed $body
     * @return ResponseInterface
     */
    public static function create($body = []): ResponseInterface
    {
        return new JsonResponse($body);
    }

    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public static function error($data): ResponseInterface
    {
        return JsonResponse::create([
            'success' => false,
            'error' => $data
        ]);
    }
}