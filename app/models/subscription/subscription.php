<?php

class Subscription{
    private $conn;
    private $table_name = "user_subscriptions";

    public $id;
    public $user_id;
    public $plan_id;
    public $status;
    public $start_date;
    public $end_date;

    public function __construct($db){
        $this->conn = $db;
    }
}

public function create(){
    $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, plan_id=:plan_id, status=:status, start_date=:start_date, end_date=:end_date";
    $stmt = $this->conn->prepare($query);

}

public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        return $stmt;
    }

public function delete(){
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt->bindParam(":id", $this->id);

    if($stmt->execute()){
        return true;
    }
    return false;
}
