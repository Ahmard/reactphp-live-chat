<?php


namespace App\Core\Http\Response;


final class NotFoundResponse extends BaseResponse
{
    public function __construct()
    {
        $this->with(
            MultiPurposeResponse::create()
                ->statusCode(404)
                ->reason('resources not found.')
                ->body([
                    'message' => 'The resources you are looking does not exists.'
                ])
                ->view('system/404')
        );
    }
}