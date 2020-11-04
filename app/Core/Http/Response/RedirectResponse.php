<?php


namespace App\Core\Http\Response;


final class RedirectResponse extends BaseResponse
{
    public function __construct(string $url)
    {
        $this->with(
            MultiPurposeResponse::create()
                ->statusCode(302)
                ->headers([
                    'Location' => $url
                ])
                ->body([
                    'message' => "Redirecting yo to {$url}"
                ])
                ->view('system/302', [
                    'url' => $url
                ])
        );
    }

    public static function create(string $url = '/'): ResponseInterface
    {
        return new static($url);
    }
}