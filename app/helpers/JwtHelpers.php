<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function generateToken($user)
    {
        $payload = [
            "iss" => "AstraAI",
            "iat" => time(),
            "exp" => time() + (int) $_ENV['JWT_EXPIRE'],
            "data" => [
                "id" => $user['id'],
                "email" => $user['email'],
                "role" => $user['role']
            ]
        ];

        return JWT::encode(
            $payload,
            $_ENV['JWT_SECRET'],
            'HS256'
        );
    }

    public static function verifyToken($token)
    {
        return JWT::decode(
            $token,
            new Key($_ENV['JWT_SECRET'], 'HS256')
        );
    }
}