<?php
class Payment {
    public $conn;
    private $table_name = "payments";

    public function __construct($db) { $this->conn = $db; }

    // CREATE 
    public function create($user_id, $plan_id, $amount, $payment_status) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, plan_id, amount, payment_status, payment_date) 
                  VALUES (:user_id, :plan_id, :amount, :payment_status, CURRENT_TIMESTAMP)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':plan_id' => $plan_id,
            ':amount' => $amount,
            ':payment_status' => htmlspecialchars(strip_tags($payment_status))
        ]);
    }

    // READ ALL 
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY payment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ BY USER 
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY payment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE STATUS PEMBAYARAN (Misal dari PENDING menjadi COMPLETED atau FAILED)
    public function updateStatus($id, $payment_status) {
        $query = "UPDATE " . $this->table_name . " SET payment_status = :payment_status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':payment_status' => htmlspecialchars(strip_tags($payment_status)),
            ':id' => $id
        ]);
    }

    // DELETE
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>