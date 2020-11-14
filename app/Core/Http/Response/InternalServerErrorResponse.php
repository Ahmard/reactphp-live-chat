<?php


namespace App\Core\Http\Response;


use App\Core\Error;

final class InternalServerErrorResponse extends BaseResponse
{
    public function __construct($exception)
    {
        $error = Error::create($exception)->getMessage() ?? 'Internal server error.';
        $this->with(
            MultiPurposeResponse::create()
                ->statusCode(500)
                ->reason('internal server error.')
                ->body([
                    'status' => false,
                    'message' => $error
                ])
                ->view('system/500', [
                    'exception' => $exception
                ])
        );

    }

    public static function create($exception = null): ResponseInterface
    {
        return new static($exception);
    }
}