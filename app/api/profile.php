<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/JwtHelpers.php';

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Token tidak ditemukan."
    ]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);

try {

    $user = JwtHelper::verifyToken($token);

    echo json_encode([
        "success" => true,
        "data" => $user->data
    ]);

} catch (Exception $e) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Token tidak valid."
    ]);

}