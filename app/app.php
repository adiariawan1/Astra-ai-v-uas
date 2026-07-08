<?php

require_once __DIR__.'/config/database.php';
require_once __DIR__.'/models/payment/Payment.php';
require_once __DIR__.'/controllers/payment/PaymentController.php';

$controller = new PaymentController($pdo);

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'create':
        $controller->create();
        break;

    case 'read':
        $controller->readAll();
        break;

    case 'user':
        $controller->getByUserId($_GET['id']);
        break;

    case 'update':
        $controller->updateStatus();
        break;

    case 'delete':
        $controller->delete();
        break;

    default:
        echo "API Payment";
}
?>