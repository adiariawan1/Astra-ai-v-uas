<?php
class Subscription {
    public $conn;
    private $table_name = "user_subscriptions";

    public function __construct($db) { $this->conn = $db; }

    // CREATE
    public function create($user_id, $plan_id, $status, $start_date, $end_date) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, plan_id, status, start_date, end_date) 
                  VALUES (:user_id, :plan_id, :status, :start_date, :end_date)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':plan_id' => $plan_id,
            ':status' => htmlspecialchars(strip_tags($status)),
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
    }

    // READ ALL
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ BY USER 
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY end_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE STATUS 
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':status' => htmlspecialchars(strip_tags($status)),
            ':id' => $id
        ]);
    }

    // DELETE
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function checkActiveSubscription($user_id) {
        $query = "SELECT us.*, sp.name as plan_name 
                  FROM " . $this->table_name . " us
                  LEFT JOIN subscription_plans sp ON us.plan_id = sp.id
                  WHERE us.user_id = :user_id AND us.status = 'active' AND us.end_date > CURRENT_TIMESTAMP 
                  ORDER BY us.end_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>