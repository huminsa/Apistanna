<?php

namespace App\Helpers;

use \Firebase\JWT\JWT;

class JwtHelper
{
    private static $secretKey = "rahasia_jwt_key";

    public static function generateToken($data, $expire = 3600)
    {
        $payload = [
            "iss" => "siakad",
            "iat" => time(),
            "exp" => time() + $expire,
            "data" => $data
        ];
        return JWT::encode($payload, self::$secretKey);
    }

    public static function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, self::$secretKey, ['HS256']);
            return $decoded->data;
        } catch (\Exception $e) {
            return false;
        }
    }
}
