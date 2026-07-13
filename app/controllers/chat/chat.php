<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$session_id = $_SESSION['user_session_id'] ?? "sesi-default-01";
$user_name = $_SESSION['user_name'] ?? "User"; 

$api_url_post = "http://127.0.0.1:8000/chat";
$api_url_get = "http://127.0.0.1:8000/chat/" . $session_id;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = json_decode(file_get_contents('php://input'), true);
    $message = $input_data['message'] ?? '';

    if (empty($message)) {
        echo json_encode(["status" => "error", "message" => "Pesan kosong"]);
        exit;
    }

    $payload = json_encode([
        "session_id" => $session_id,
        "message" => $message,
        "user_name" => $user_name
    ]);

    $ch = curl_init($api_url_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    

    curl_setopt($ch, CURLOPT_TIMEOUT, 300); 
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $response;
    exit;
}


function getChatHistory() {
    global $api_url_get;
    $ch_get = curl_init($api_url_get);
    curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);
    $response_get = curl_exec($ch_get);
    curl_close($ch_get);

    $data = json_decode($response_get, true);
    return $data['history'] ?? [];
}