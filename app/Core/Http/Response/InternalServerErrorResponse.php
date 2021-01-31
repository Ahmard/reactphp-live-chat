<?php


namespace App\Core\Http\Response;


use App\Core\Error;
use Throwable;

final class InternalServerErrorResponse extends BaseResponse
{
    /**
     * InternalServerErrorResponse constructor.
     * @param ?Throwable $exception
     */
    public function __construct(?Throwable $exception)
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
                    'error' => $exception
                ])
        );

    }

    public static function create(?Throwable $exception = null): ResponseInterface
    {
        return new InternalServerErrorResponse($exception);
    }
}