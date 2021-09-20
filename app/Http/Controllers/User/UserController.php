<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
use Server\Http\Response\JsonResponse;
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

                return $this->response->json([
                    'status' => true,
                    'data' => $userData
                ]);
            })
            ->otherwise(function (Throwable $exception) {
                return $this->response->json([
                    'status' => false,
                    'error' => $exception
                ]);
            });
    }

    public function profile(): Response
    {
        return $this->response->view('user/profile');
    }
}