<?php


namespace App\Core\Auth;

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;

final class Token
{
    /**
     * Expiry time of token
     * Default = 24 * 60 * 60
     * @var int $expiryTime
     */
    private static int $expiryTime = 8640;

    public static function encode(array $user)
    {
        $user['expiry'] = time() + self::$expiryTime;
        return JWT::encode($user, $_ENV['APP_KEY'] ?? 'ahmard',);
    }

    public static function decode(string $jwtKey)
    {
        $decodedToken = (array)JWT::decode(
            $jwtKey,
            $_ENV['APP_KEY'] ?? 'ahmard',
            ['HS256']
        );


        if (array_key_exists('expiry', $decodedToken)) {
            if ($decodedToken['expiry'] > time()) {
                unset($decodedToken['expiry']);
                return $decodedToken;
            }
        }

        return false;
    }
}