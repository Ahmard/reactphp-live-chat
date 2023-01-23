<?php


namespace Server\Auth;

use DomainException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class Token
{
    public static function encode(array $user): string
    {
        $user['expiry'] = time() + $_ENV['AUTH_TOKE_LIFE_TIME'];
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