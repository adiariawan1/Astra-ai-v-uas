<?php
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../models/SubscriptionPlan.php';

class PaymentController {
    private $db;
    private $paymentModel;
    private $subModel;
    private $planModel;

    public function __construct($db) {
        $this->db = $db;
        $this->paymentModel = new Payment($this->db);
        
        $this->subModel = new Subscription($this->db);
        $this->planModel = new SubscriptionPlan($this->db);
    }

    public function create($data) {
        if (!empty($data['user_id']) && !empty($data['plan_id']) && !empty($data['amount']) && !empty($data['payment_status'])) {
            
            $plan = $this->planModel->readOne($data['plan_id']);
            
            if (!$plan) {
                http_response_code(404);
                return ["message" => "Paket langganan tidak ditemukan."];
            }

            if ($this->paymentModel->create($data['user_id'], $data['plan_id'], $data['amount'], $data['payment_status'])) {
                
                if ($data['payment_status'] === 'COMPLETED') {
                    $duration_days = $plan['duration_days'];
                    
                    $start_date = date('Y-m-d H:i:s');
                    $end_date = date('Y-m-d H:i:s', strtotime("+$duration_days days"));
                    
                    $this->subModel->create($data['user_id'], $data['plan_id'], 'ACTIVE', $start_date, $end_date);
                }

                http_response_code(201);
                return ["message" => "Payment berhasil dibuat dan langganan diproses."];
            } else {
                http_response_code(503);
                return ["message" => "Gagal membuat payment."];
            }
        } else {
            http_response_code(400);
            return ["message" => "Data tidak lengkap. Pastikan user_id, plan_id, amount, dan payment_status terisi."];
        }
    }

        $result = $this->paymentModel->readAll();
        
        if ($result) {
            http_response_code(200);
            return ["data" => $result];
        } else {
            http_response_code(404);
            return ["message" => "Tidak ada data payment ditemukan."];
        }
    }

    public function getByUserId($user_id) {
        if (!empty($user_id)) {
            $result = $this->paymentModel->getByUserId($user_id);
            
            if ($result) {
                http_response_code(200);
                return ["data" => $result];
            } else {
                http_response_code(404);
                return ["message" => "Data payment untuk user ini tidak ditemukan."];
            }
        } else {
            http_response_code(400);
            return ["message" => "User ID diperlukan."];
        }
    }

    public function updateStatus($data) {
        if (!empty($data['id']) && !empty($data['payment_status'])) {
            if ($this->paymentModel->updateStatus($data['id'], $data['payment_status'])) {
                http_response_code(200);
                return ["message" => "Status payment berhasil diupdate."];
            } else {
                http_response_code(503);
                return ["message" => "Gagal mengupdate status payment."];
            }
        } else {
            http_response_code(400);
            return ["message" => "ID dan status payment diperlukan."];
        }
    }

    public function delete($id) {
        if (!empty($id)) {
            if ($this->paymentModel->delete($id)) {
                http_response_code(200);
                return ["message" => "Payment berhasil dihapus."];
            } else {
                http_response_code(503);
                return ["message" => "Gagal menghapus payment."];
            }
        } else {
            http_response_code(400);
            return ["message" => "ID payment diperlukan."];
        }
    }
}
?>
