<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

// Ambil body JSON dari Postman
$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$full_name = trim($data['full_name'] ?? '');

if (empty($email) || empty($password) || empty($full_name)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Semua field wajib diisi."
    ]);
    exit;
}

$userModel = new User($pdo);

// Cek email sudah ada atau belum
if ($userModel->findByEmail($email)) {
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Email sudah digunakan."
    ]);
    exit;
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Simpan user
$success = $userModel->create(
    $email,
    $passwordHash,
    $full_name,
    "user"
);

if ($success) {
    echo json_encode([
        "success" => true,
        "message" => "Registrasi berhasil."
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Registrasi gagal."
    ]);
}