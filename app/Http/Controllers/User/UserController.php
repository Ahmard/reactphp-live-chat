<?php


namespace App\Http\Controllers\User;


use App\Core\Database\Connection;
use App\Core\Http\Response\JsonResponse;
use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Throwable;

class UserController extends Controller
{
    public function view(ServerRequestInterface $request, array $params): PromiseInterface
    {
        return Connection::get()->query('SELECT * FROM users WHERE id = ?', [$params['id']])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => $result->rows[0]
                ]);
            })
            ->otherwise(function (Throwable $exception) {
                return response()->json([
                    'status' => false,
                    'error' => $exception
                ]);
            });
    }

    public function profile(): Response
    {
        return view('user/profile');
    }
}