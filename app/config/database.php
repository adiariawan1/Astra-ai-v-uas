<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? 'your_database_name';
$username = $_ENV['DB_USERNAME'] ?? 'your_username';
$password = $_ENV['DB_PASSWORD'] ?? 'your_password';

try {
    $dsn ="pgsql:host=$host;port=$port;dbname=$db;";

    $pdo = new PDO($dsn, $username, $password);

    pdo ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo("berhasil terhubung");
}catch(\PDOException $e){
    die("connection failed.".$e->getMessage());
}