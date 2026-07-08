<?php

require_once "../config/database.php";
require_once "../models/Payment.php";

class PaymentController
{
    private $payment;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->payment = new Payment($db);
    }

    // CREATE PAYMENT
    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $user_id = $_POST['user_id'];
            $plan_id = $_POST['plan_id'];
            $amount = $_POST['amount'];
            $payment_status = "PENDING";

            if ($this->payment->create($user_id, $plan_id, $amount, $payment_status)) {

                header("Location: ../views/payment/index.php?success=1");
                exit();

            } else {

                header("Location: ../views/payment/create.php?error=1");
                exit();

            }
        }
    }

    // READ ALL
    public function index()
    {
        return $this->payment->readAll();
    }

    // READ PAYMENT USER
    public function history($user_id)
    {
        return $this->payment->getByUserId($user_id);
    }

    // UPDATE STATUS
    public function updateStatus()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $id = $_POST['id'];
            $status = $_POST['payment_status'];

            if ($this->payment->updateStatus($id, $status)) {

                header("Location: ../views/payment/index.php?update=1");
                exit();

            } else {

                header("Location: ../views/payment/index.php?error=1");
                exit();

            }
        }
    }

    // DELETE
    public function delete()
    {
        if (isset($_GET['id'])) {

            $id = $_GET['id'];

            if ($this->payment->delete($id)) {

                header("Location: ../views/payment/index.php?delete=1");
                exit();

            } else {

                header("Location: ../views/payment/index.php?error=1");
                exit();

            }
        }
    }
}

// Routing
$controller = new PaymentController();

if (isset($_GET['action'])) {

    switch ($_GET['action']) {

        case "create":
            $controller->create();
            break;

        case "update":
            $controller->updateStatus();
            break;

        case "delete":
            $controller->delete();
            break;

        default:
            break;
    }
}

?>