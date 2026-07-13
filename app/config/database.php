<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER']; 
$password = $_ENV['DB_PASS'];

try {
    
    $dsn ="pgsql:host=$host;port=$port;dbname=$dbname;";

    $pdo = new PDO($dsn, $username, $password);

    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>