<?php


namespace App\Core\Http\Response;


final class MultiPurposeResponse extends BaseResponse
{
    public function getHeaders(): array
    {
        switch (true) {
            case request()->expectsJson():
                return array_merge($this->headers, [
                    'content-type' => 'applications/json'
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
            case request()->expectsHtml():
                if ($this->hasView()) {
                    return $this->view;
                }

                return $this->body;
            default:
                return $this->body;
        }
    }
}