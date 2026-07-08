<?php
// Memanggil model yang dibutuhkan
require_once _DIR_ . '/../models/Payment.php';
require_once _DIR_ . '/../models/Subscription.php';
require_once _DIR_ . '/../models/SubscriptionPlan.php';

class PaymentController {
    private $db;
    private $paymentModel;
    private $subModel;
    private $planModel;

    public function __construct($db) {
        $this->db = $db;
        $this->paymentModel = new Payment($this->db);
        
        // Inisialisasi model tambahan untuk logika otomatisasi
        $this->subModel = new Subscription($this->db);
        $this->planModel = new SubscriptionPlan($this->db);
    }

    // ==========================================
    // CREATE (Proses Pembayaran + Auto Langganan)
    // ==========================================
    public function create($data) {
        if (!empty($data['user_id']) && !empty($data['plan_id']) && !empty($data['amount']) && !empty($data['payment_status'])) {
            
            // 1. Ambil detail durasi paket dari database
            $plan = $this->planModel->readOne($data['plan_id']);
            
            if (!$plan) {
                http_response_code(404);
                return ["message" => "Paket langganan tidak ditemukan."];
            }

            // 2. Simpan record pembayaran
            if ($this->paymentModel->create($data['user_id'], $data['plan_id'], $data['amount'], $data['payment_status'])) {
                
                // 3. Otomatis Aktifkan Langganan jika statusnya COMPLETED
                if ($data['payment_status'] === 'COMPLETED') {
                    $duration_days = $plan['duration_days'];
                    
                    // Hitung waktu aktif langganan
                    $start_date = date('Y-m-d H:i:s');
                    $end_date = date('Y-m-d H:i:s', strtotime("+$duration_days days"));
                    
                    // Masukkan hak akses ke tabel user_subscriptions
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

    // ==========================================
    // READ ALL
    // ==========================================
    public function readAll() {
        $result = $this->paymentModel->readAll();
        
        if ($result) {
            http_response_code(200);
            return ["data" => $result];
        } else {
            http_response_code(404);
            return ["message" => "Tidak ada data payment ditemukan."];
        }
    }

    // ==========================================
    // READ BY USER ID
    // ==========================================
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

    // ==========================================
    // UPDATE STATUS
    // ==========================================
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

    // ==========================================
    // DELETE
    // ==========================================
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