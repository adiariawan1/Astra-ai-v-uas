<?php
class User {
    public $conn;
    private $table_name = "users";

    public function __construct($db) { $this->conn = $db; }

    // CREATE
    public function create($email, $password_hash, $full_name, $role = 'user') {
        $query = "INSERT INTO " . $this->table_name . " (email, password_hash, full_name, role) 
                  VALUES (:email, :password_hash, :full_name, :role)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':email' => htmlspecialchars(strip_tags($email)),
            ':password_hash' => $password_hash,
            ':full_name' => htmlspecialchars(strip_tags($full_name)),
            ':role' => htmlspecialchars(strip_tags($role))
        ]);
    }

    // READ ALL
    public function readAll() {
        $query = "SELECT id, email, full_name, role, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ ONE
    public function readOne($id) {
        $query = "SELECT id, email, full_name, role, created_at FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $email, $full_name, $role) {
        $query = "UPDATE " . $this->table_name . " 
                  SET email = :email, full_name = :full_name, role = :role 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':email' => htmlspecialchars(strip_tags($email)),
            ':full_name' => htmlspecialchars(strip_tags($full_name)),
            ':role' => htmlspecialchars(strip_tags($role)),
            ':id' => $id
        ]);
    }

    // DELETE
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // LOGIN
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>