<?php
class SubscriptionPlan {
    public $conn;
    private $table_name = "subscription_plans";

    public function __construct($db) { $this->conn = $db; }

    // CREATE
    public function create($name, $price, $duration_days) {
        $query = "INSERT INTO " . $this->table_name . " (name, price, duration_days) 
                  VALUES (:name, :price, :duration_days)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => htmlspecialchars(strip_tags($name)),
            ':price' => $price,
            ':duration_days' => $duration_days
        ]);
    }

    // READ ALL
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY price ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ ONE
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $name, $price, $duration_days) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, price = :price, duration_days = :duration_days 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => htmlspecialchars(strip_tags($name)),
            ':price' => $price,
            ':duration_days' => $duration_days,
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