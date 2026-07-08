<?php

require_once __DIR__ . '/../helpers/JwtHelper.php';

class JwtMiddleware
{
    public static function authenticate()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['token'])) {
            header("Location: ../login/login.php");
            exit;
        }

        try {

            $user = JwtHelper::verifyToken($_SESSION['token']);

            return $user;

        } catch (Exception $e) {

            $_SESSION = [];
            session_destroy();

            header("Location: ../login/login.php");
            exit;
        }
    }
}