<?php


namespace App\Core\Auth;


use App\Core\Database\Connection;
use Clue\React\SQLite\Result;
use Throwable;
use function React\Promise\resolve;

final class Auth
{
    private static string $token = '';

    private array $user = [];

    private bool $isAuthenticated = false;

    public function __construct(string $token)
    {
        self::$token = $token;
    }

    public static function handle(string $token)
    {
        self::$token = $token;
        return (new self($token))->authToken();
    }

    private function authToken()
    {
        if (self::$token) {
            $verified = Token::decode(self::$token);
            if ($verified) {
                return Connection::get()
                    ->query('SELECT * FROM users WHERE id = ?', [$verified['id']])
                    ->then(function (Result $result) {
                        $this->user = $result->rows[0];
                        $this->isAuthenticated = true;
                        return resolve($this);
                    })
                    ->otherwise(function (Throwable $throwable) {
                        echo "Auth check failed: ";
                        dump($throwable);
                    });
            }
        }

        return resolve($this);
    }


    /**
     * Check if user is authenticated
     * @return bool
     */
    public function check()
    {
        return $this->isAuthenticated;
    }

    /**
     * @return array
     */
    public function user(): array
    {
        return $this->user;
    }

    public function userId()
    {
        //dump()
        return $this->user()['id'] ?? null;
    }

    /**
     * Get user token
     * @return string
     */
    public function token()
    {
        return self::$token;
    }
}