<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../controllers/auth/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email dan password wajib diisi."
    ]);
    exit;
}

$auth = new AuthController();

$result = $auth->login($email, $password);

echo json_encode($result);