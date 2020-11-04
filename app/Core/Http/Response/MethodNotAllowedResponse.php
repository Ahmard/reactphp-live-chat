<?php


namespace App\Core\Http\Response;


final class MethodNotAllowedResponse extends BaseResponse
{
    public function __construct()
    {
        $this->with(
            MultiPurposeResponse::create()
                ->statusCode(405)
                ->reason('method not allowed')
                ->body([
                    'message' => 'Request method not allowed.'
                ])
                ->view('system/405')
        );
    }
}