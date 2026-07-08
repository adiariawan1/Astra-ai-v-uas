<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/user/User.php';
require_once __DIR__ . '/../../helpers/JwtHelpers.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        global $pdo;
        $this->userModel = new User($pdo);
    }

    public function login($email, $password)
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return [
                "success" => false,
                "message" => "Email tidak ditemukan."
            ];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return [
                "success" => false,
                "message" => "Password salah."
            ];
        }

        $token = JwtHelper::generateToken($user);

        return [
            "success" => true,
            "message" => "Login berhasil.",
            "token" => $token,
            "user" => [
                "id" => $user["id"],
                "email" => $user["email"],
                "full_name" => $user["full_name"],
                "role" => $user["role"]
            ]
        ];
    }
}