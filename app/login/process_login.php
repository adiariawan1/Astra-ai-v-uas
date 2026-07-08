<?php

session_start();

require_once __DIR__ . '/../controllers/auth/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Method not allowed");
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email dan password wajib diisi.";
    header("Location: login.php");
    exit;
}

$auth = new AuthController();
$result = $auth->login($email, $password);

if ($result['success']) {

    $_SESSION['token'] = $result['token'];
    $_SESSION['user'] = $result['user'];

    header("Location: ../dashboard.php");
    exit;
}

$_SESSION['error'] = $result['message'];
header("Location: login.php");
exit;