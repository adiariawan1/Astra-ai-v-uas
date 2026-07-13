<?php
require_once __DIR__ . '/../../models/subscription/SubscriptionPlan.php';
require_once __DIR__ . '/../../models/subscription/Subscription.php';

class SubscriptionController {
    private $planModel;
    private $userSubModel;

    public function __construct($db) {
        $this->planModel = new SubscriptionPlan($db);
        $this->userSubModel = new Subscription($db);
    }

    public function getPlans() {
        $plans = $this->planModel->readAll();
        return ["status" => "success", "data" => $plans];
    }

    public function getPlan($id) {
        if (!$id) return ["status" => "error", "message" => "ID Paket diperlukan."];

        $plan = $this->planModel->readOne($id);
        if ($plan) {
            return ["status" => "success", "data" => $plan];
        }
        http_response_code(404);
        return ["status" => "error", "message" => "Paket tidak ditemukan."];
    }

    public function createPlan($data) {
        if (empty($data['name']) || !isset($data['price']) || empty($data['duration_days'])) {
            http_response_code(400);
            return ["status" => "error", "message" => "Nama, Harga, dan Durasi wajib diisi!"];
        }

        if ($this->planModel->create($data['name'], $data['price'], $data['duration_days'])) {
            http_response_code(201);
            return ["status" => "success", "message" => "Paket langganan berhasil dibuat."];
        }
        http_response_code(500);
        return ["status" => "error", "message" => "Gagal membuat paket langganan."];
    }

    public function updatePlan($id, $data) {
        if (!$id) return ["status" => "error", "message" => "ID Paket diperlukan."];

        if ($this->planModel->update($id, $data['name'], $data['price'], $data['duration_days'])) {
            return ["status" => "success", "message" => "Paket langganan berhasil diperbarui."];
        }
        return ["status" => "error", "message" => "Gagal memperbarui paket."];
    }

    public function deletePlan($id) {
        if (!$id) return ["status" => "error", "message" => "ID Paket diperlukan."];

        if ($this->planModel->delete($id)) {
            return ["status" => "success", "message" => "Paket langganan berhasil dihapus."];
        }
        return ["status" => "error", "message" => "Gagal menghapus paket."];
    }


    public function getUserSubscriptions($user_id) {
        if (!$user_id) return ["status" => "error", "message" => "User ID diperlukan."];

        $history = $this->userSubModel->getByUserId($user_id);
        return ["status" => "success", "data" => $history];
    }


    public function checkMyActiveSubscription($user_id) {
        if (!$user_id) return ["status" => "error", "message" => "User ID diperlukan."];

        $activePlan = $this->userSubModel->checkActiveSubscription($user_id);

        if ($activePlan) {
            return [
                "status" => "success", 
                "message" => "Langganan aktif",
                "data" => $activePlan
            ];
        }

        
        return [
            "status" => "inactive", 
            "message" => "Tidak ada langganan yang aktif. Silakan lakukan pembayaran."
        ];
    }
}
?>