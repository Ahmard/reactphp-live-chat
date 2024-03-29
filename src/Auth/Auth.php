<?php


namespace Server\Auth;


use Clue\React\SQLite\Result;
use React\Promise\FulfilledPromise;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
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

    /**
     * @param string|null $token
     * @return PromiseInterface|Promise
     */
    public static function handle(string|null $token): PromiseInterface|Promise
    {
        self::$token = $token ?? '';
        return (new self($token))->authToken();
    }

    /**
     * @return PromiseInterface|Promise
     */
    private function authToken(): PromiseInterface|Promise
    {
        if (self::$token) {

            try {
                $verified = Token::decode(self::$token);
                if ($verified) {
                    return Connection::get()
                        ->query('SELECT * FROM users WHERE id = ?', [$verified['id']])
                        ->then(function (Result $result) {

                            if (!isset($result->rows[0])) {
                                return resolve($this);
                            }

                            $this->user = $result->rows[0];
                            $this->isAuthenticated = true;
                            return resolve($this);
                        })
                        ->otherwise(function (Throwable $throwable) {
                            echo "Auth check failed: ";
                        });
                }
            } catch (Throwable $exception) {
            }
        }

        return resolve($this);
    }


    /**
     * Check if user is authenticated
     * @return bool
     */
    public function check(): bool
    {
        return $this->isAuthenticated;
    }

    public function userId(): ?int
    {
        return $this->user()['id'] ?? null;
    }

    /**
     * @return array
     */
    public function user(): array
    {
        return $this->user;
    }

    /**
     * Get user token
     * @return string
     */
    public function token(): string
    {
        return self::$token;
    }
}