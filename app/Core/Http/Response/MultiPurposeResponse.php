<?php


namespace App\Core\Http\Response;


use function request;

final class MultiPurposeResponse extends BaseResponse
{
    public function getHeaders(): array
    {
        switch (true) {
            case request()->expectsJson():
                return array_merge($this->headers, [
                    'content-type' => 'application/json'
                ]);
            case request()->expectsHtml():
                return array_merge($this->headers, [
                    'content-type' => 'text/html'
                ]);
            default:
                return $this->headers;
        }
    }

    public function getBody()
    {
        switch (true) {
            case request()->expectsJson():
                return json_encode($this->body);
            default:
                if ($this->hasView()) {
                    return $this->view;
                }

                //Since this response class can send response to api/web
                //we must make sure that we send appropriate response
                if (!is_string($this->body)) {
                    return json_encode($this->body);
                }

                return $this->body;
        }
    }
}