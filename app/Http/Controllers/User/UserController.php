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
                $userData = $result->rows[0];

                //Remove sensitive data
                unset($userData['password']);
                unset($userData['token']);

                return response()->json([
                    'status' => true,
                    'data' => $userData
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