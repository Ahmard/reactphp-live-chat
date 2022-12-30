<?php


namespace Server\Auth;

use DomainException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class Token
{
    /**
     * Expiry time of token
     * Default = 24 * 60 * 60
     * @var int $expiryTime
     */
    private static int $expiryTime = 8640;

    public static function encode(array $user): string
    {
        $user['expiry'] = time() + self::$expiryTime;
        return JWT::encode(payload: $user, key: self::getAppKey(), alg: 'HS256');
    }

    /**
     * @param string $jwt
     * @return array|false
     */
    public static function decode(string $jwt): bool|array
    {
        try {
            $decodedToken = (array)JWT::decode(
                jwt: $jwt,
                keyOrKeyArray: new Key(self::getAppKey(), 'HS256'),
            );
        } catch (DomainException $domainException) {
            echo $domainException;
            return false;
        }

        if (array_key_exists('expiry', $decodedToken)) {
            if ($decodedToken['expiry'] > time()) {
                unset($decodedToken['expiry']);
                return $decodedToken;
            }
        }

        return false;
    }

    private static function getAppKey(): string
    {
        return $_ENV['APP_KEY'] ?? 'ahmard';
    }
}