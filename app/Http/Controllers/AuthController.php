<?php


namespace App\Http\Controllers;

use App\Models\User;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Auth\Token;
use Server\Database\Connection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Throwable;

class AuthController extends Controller
{
    public function showRegisterForm(): Response
    {
        return $this->response->view('auth/register');
    }

    public function showLoginForm(): Response
    {
        return $this->response->view('auth/login');
    }

    /**
     * @return Response|PromiseInterface
     */
    public function doRegister()
    {
        $requestData = $this->request->getParsedBody();

        $errors = validator()->validate($requestData, [
            'username' => [
                new Length([
                    'min' => 3,
                    'max' => 50
                ])
            ],
            'email' => [
                new Length([
                    'min' => 3,
                    'max' => 200
                ]),
                new Email()
            ],
            'password' => [
                new Length(['min' => 3]),
                new Length(['max' => 99]),
            ]
        ]);

        if (0 !== count($errors)) {
            return $this->response->view('auth/register', [
                'errors' => $errors
            ]);
        }

        return Connection::create()
            ->query('SELECT id FROM users WHERE email = ?', [$requestData['email']])
            ->then(function (Result $result) use ($requestData) {
                if (0 === count($result->rows)) {
                    return Connection::get()
                        ->query('INSERT INTO users (username, email, password, time) VALUES (?, ?, ?, ?)', [
                            $requestData['username'],
                            $requestData['email'],
                            password_hash($requestData['password'], PASSWORD_DEFAULT),
                            time()
                        ])
                        ->then(function (Result $result) {
                            return $this->response->view('auth/register-success', [
                                'user_id' => $result->insertId
                            ]);
                        })
                        ->otherwise(function (Throwable $error) {
                            return $this->response->view('auth/register', [
                                'error' => $error
                            ]);
                        });
                } else {
                    return $this->response->view('auth/register', [
                        'error' => 'Email address already exists'
                    ]);
                }
            })->otherwise(function (Throwable $error) {
                return $this->response->view('auth/register', [
                    'errors' => $error
                ]);
            });
    }

    /**
     * @return Response|PromiseInterface
     */
    public function doLogin()
    {
        $requestData = $this->request->getParsedBody();

        $errors = validator()->validate($requestData, [
            'email' => [
                new Length([
                    'min' => 3,
                    'max' => 200
                ]),
                new Email()
            ],
            'password' => [
                new Length(['min' => 3]),
                new Length(['max' => 99]),
            ]
        ]);

        if (0 !== count($errors)) {
            return $this->response->view('auth/login', [
                'errors' => $errors
            ]);
        }

        return Connection::create()
            ->query('SELECT id, username, password FROM users WHERE email = ?', [$requestData['email']])
            ->then(function (Result $result) use ($requestData) {

                if (1 === count($result->rows)) {
                    if (password_verify($requestData['password'], $result->rows[0]['password'])) {
                        //Set session variable
                        //session()->set('user_id', $result->rows[0]['id']);

                        //Set user token
                        User::setToken($result->rows[0]['id'], Token::encode([
                            'id' => $result->rows[0]['id']
                        ]));

                        return $this->response->view('auth/login-success');
                    } else {
                        return $this->response->view('auth/login', [
                            'error' => 'Password does not match.'
                        ]);
                    }
                } else {
                    return $this->response->view('auth/login', [
                        'error' => 'No user with matching credentials found.'
                    ]);
                }
            })
            ->otherwise(function (Throwable $error) {
                return $this->response->view('auth/login', [
                    'error' => $error->getMessage()
                ]);
            });
    }
}
