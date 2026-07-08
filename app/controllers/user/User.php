<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct($db) { 
        $this->userModel = new User($db); 
    }

    // ==========================================
    // 1. CREATE (REGISTER AKUN BARU)
    // ==========================================
    public function register($data) {
        // Validasi input kosong
        if (empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
            http_response_code(400);
            return ["status" => "error", "message" => "Email, Password, dan Nama Lengkap wajib diisi!"];
        }

        // Cek apakah email sudah terdaftar
        if ($this->userModel->findByEmail($data['email'])) {
            http_response_code(409);
            return ["status" => "error", "message" => "Email sudah terdaftar, silakan gunakan email lain."];
        }

        // Hash password menggunakan Bcrypt (Standar keamanan tertinggi PHP)
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        // Default role untuk pendaftar baru adalah 'user'
        $role = 'user'; 
        
        if ($this->userModel->create($data['email'], $password_hash, $data['full_name'], $role)) {
            http_response_code(201);
            return ["status" => "success", "message" => "Registrasi berhasil! Silakan login."];
        }
        
        http_response_code(500);
        return ["status" => "error", "message" => "Gagal mendaftar akun."];
    }

    // ==========================================
    // 2. READ (LIHAT PROFIL SENDIRI)
    // ==========================================
    public function getProfile($user_id) {
        $user = $this->userModel->readOne($user_id);
        
        if ($user) {
            http_response_code(200);
            return ["status" => "success", "data" => $user];
        }
        
        http_response_code(404);
        return ["status" => "error", "message" => "Profil tidak ditemukan."];
    }

    // ==========================================
    // 3. UPDATE (EDIT PROFIL SENDIRI)
    // ==========================================
    public function updateProfile($user_id, $data) {
        // Ambil data user yang lama dulu untuk mempertahankan Role-nya
        // (Agar user biasa tidak bisa nge-hack sistem mengubah dirinya jadi admin)
        $currentUser = $this->userModel->readOne($user_id);
        
        if (!$currentUser) {
            http_response_code(404);
            return ["status" => "error", "message" => "Profil tidak ditemukan."];
        }

        // Gunakan data baru jika dikirim, jika kosong gunakan data lama
        $email = !empty($data['email']) ? $data['email'] : $currentUser['email'];
        $full_name = !empty($data['full_name']) ? $data['full_name'] : $currentUser['full_name'];
        $role = $currentUser['role']; // Tetap paksa gunakan role yang lama

        if ($this->userModel->update($user_id, $email, $full_name, $role)) {
            http_response_code(200);
            return ["status" => "success", "message" => "Profil berhasil diperbarui."];
        }

        http_response_code(500);
        return ["status" => "error", "message" => "Gagal memperbarui profil."];
    }

    // ==========================================
    // 4. DELETE (HAPUS AKUN SENDIRI)
    // ==========================================
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