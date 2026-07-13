<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../controllers/auth/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Method not allowed");
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$url_login = "http://localhost/back-end-user-service/views/login.php"; 

$url_dashboard = "http://localhost/back-end-user-service/views/view.php";



if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email dan password wajib diisi.";
    header("Location: " . $url_login);
    exit;
}

$auth = new AuthController();
$result = $auth->login($email, $password);

if ($result['success']) {
    $_SESSION['token'] = $result['token'];
    $_SESSION['user'] = $result['user'];

    header("Location: " . $url_dashboard);
    exit;
}


$_SESSION['error'] = $result['message'];
header("Location: " . $url_login);
exit;