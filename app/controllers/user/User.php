<?php
require_once __DIR__ . '/../../models/user/User.php';

class UserController {
    private $userModel;

    public function __construct($db) { 
        $this->userModel = new User($db); 
    }

    public function register($data) {
        if (empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
            http_response_code(400);
            return ["status" => "error", "message" => "Email, Password, dan Nama Lengkap wajib diisi!"];
        }

        if ($this->userModel->findByEmail($data['email'])) {
            http_response_code(409);
            return ["status" => "error", "message" => "Email sudah terdaftar, silakan gunakan email lain."];
        }

        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $role = 'user'; 
        
        if ($this->userModel->create($data['email'], $password_hash, $data['full_name'], $role)) {
            http_response_code(201);
            return ["status" => "success", "message" => "Registrasi berhasil! Silakan login."];
        }
        
        http_response_code(500);
        return ["status" => "error", "message" => "Gagal mendaftar akun."];
    }

    public function getProfile($user_id) {
        $user = $this->userModel->readOne($user_id);
        
        if ($user) {
            http_response_code(200);
            return ["status" => "success", "data" => $user];
        }
        
        http_response_code(404);
        return ["status" => "error", "message" => "Profil tidak ditemukan."];
    }

    public function updateProfile($user_id, $data) {
        $currentUser = $this->userModel->readOne($user_id);
        
        if (!$currentUser) {
            http_response_code(404);
            return ["status" => "error", "message" => "Profil tidak ditemukan."];
        }

        $email = !empty($data['email']) ? $data['email'] : $currentUser['email'];
        $full_name = !empty($data['full_name']) ? $data['full_name'] : $currentUser['full_name'];
        $role = $currentUser['role']; 

        if ($this->userModel->update($user_id, $email, $full_name, $role)) {
            http_response_code(200);
            return ["status" => "success", "message" => "Profil berhasil diperbarui."];
        }

        http_response_code(500);
        return ["status" => "error", "message" => "Gagal memperbarui profil."];
    }

    public function deleteAccount($user_id) {
        if ($this->userModel->delete($user_id)) {
            http_response_code(200);
            return ["status" => "success", "message" => "Akun berhasil dihapus secara permanen."];
        }
        
        http_response_code(500);
        return ["status" => "error", "message" => "Gagal menghapus akun."];
    }
}
?>