<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengaturan Header CORS & JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Ambil koneksi database dan dependencies
require_once __DIR__ . '/config/database.php'; 
require_once __DIR__ . '/helpers/JwtHelpers.php'; 
require_once __DIR__ . '/middleware/JwtMiddleware.php';
require_once __DIR__ . '/controllers/auth/AuthController.php';
require_once __DIR__ . '/controllers/user/User.php'; 
require_once __DIR__ . '/controllers/subscription/SubscriptionController.php';
require_once __DIR__ . '/controllers/payment/PaymentController.php';

$route = $_GET['route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$input_data = json_decode(file_get_contents("php://input"), true) ?? $_POST;

global $pdo;

// =================================================================
// FUNGSI PENGGANTI MIDDLEWARE (Menggunakan Session PHP)
// =================================================================
function getAuthenticatedUser() {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Sesi tidak valid, silakan login ulang."]);
        exit;
    }
    return $_SESSION['user'];
}
// =================================================================

switch ($route) {
    case 'login':
        if ($method === 'POST') {
            $auth = new AuthController();
            $email = trim($input_data['email'] ?? '');
            $password = $input_data['password'] ?? '';
            
            $result = $auth->login($email, $password);
            if ($result['success']) {
                $_SESSION['token'] = $result['token'];
                $_SESSION['user'] = $result['user'];
                $_SESSION['user_session_id'] = "sesi-" . $result['user']['id'];
            }
            echo json_encode($result);
        }
        break;

    case 'register':
        if ($method === 'POST') {
            $userCtrl = new UserController($pdo);
            echo json_encode($userCtrl->register($input_data));
        }
        break;

    case 'plans':
        if ($method === 'GET') {
            $subCtrl = new SubscriptionController($pdo);
            echo json_encode($subCtrl->getPlans());
        }
        break;

    case 'my-subscription':
        if ($method === 'GET') {
            $user_session = getAuthenticatedUser(); // Menggunakan fungsi baru
            $subCtrl = new SubscriptionController($pdo);
            echo json_encode($subCtrl->checkMyActiveSubscription($user_session['id']));
        }
        break;

    case 'payment':
        if ($method === 'POST') {
            $user_session = getAuthenticatedUser(); // Menggunakan fungsi baru
            $payCtrl = new PaymentController($pdo);
            
            $input_data['user_id'] = $user_session['id'];
            echo json_encode($payCtrl->create($input_data));
        }
        break;

    case 'chat':
        if ($method === 'POST') {
            $user_session = getAuthenticatedUser(); // Menggunakan fungsi baru

            $subCtrl = new SubscriptionController($pdo);
            $subCheck = $subCtrl->checkMyActiveSubscription($user_session['id']);
            
            if (!$subCheck || $subCheck['status'] !== 'success') {
                http_response_code(403);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Akses Ditolak. Fitur chat hanya tersedia untuk pelanggan Pro atau Plus."
                ]);
                exit;
            }

            $message = trim($input_data['message'] ?? '');
            if (empty($message)) {
                echo json_encode(["status" => "error", "message" => "Pesan tidak boleh kosong"]);
                exit;
            }

            // Gunakan ID user asli untuk memisahkan obrolan antar akun
            $session_id = $_SESSION['user_session_id'] ?? "sesi-".$user_session['id'];

            $payload = json_encode([
                "session_id" => $session_id,
                "message" => $message,
                "user_name" => $user_session['full_name']
            ]);

            $ch = curl_init("http://127.0.0.1:8000/chat");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);

            $response = curl_exec($ch);
            
            // PELACAK ERROR CURL DITAMBAHKAN DI SINI
            if ($response === false) {
                $error_msg = curl_error($ch);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Gagal terhubung ke AI Python: " . $error_msg
                ]);
            } else {
                echo $response;
            }
            
            curl_close($ch);
        }
        break;

    case 'chat-history':
        if ($method === 'GET') {
            $user_session = getAuthenticatedUser(); // Menggunakan fungsi baru
            $session_id = $_SESSION['user_session_id'] ?? "sesi-".$user_session['id'];
            
            $ch_get = curl_init("http://127.0.0.1:8000/chat/" . $session_id);
            curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);
            $response_get = curl_exec($ch_get);
            
            // PELACAK ERROR CURL DITAMBAHKAN DI SINI
            if ($response_get === false) {
                echo json_encode([]); // Kembalikan array kosong jika gagal
            } else {
                echo $response_get;
            }
            
            curl_close($ch_get);
        }
        break;
        case 'update-profile':
        if ($method === 'POST') {
            $user_session = getAuthenticatedUser();
            $new_name = trim($input_data['full_name'] ?? '');
            
            if (empty($new_name)) {
                echo json_encode(["status" => "error", "message" => "Nama tidak boleh kosong."]);
                exit;
            }

            try {
                $stmt = $pdo->prepare("UPDATE users SET full_name = :name WHERE id = :id");
                $stmt->execute([':name' => $new_name, ':id' => $user_session['id']]);
                
                // Ubah nama di session agar langsung berubah di tampilan setelah refresh
                $_SESSION['user']['full_name'] = $new_name; 
                
                echo json_encode(["status" => "success", "message" => "Profil berhasil diperbarui!"]);
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Gagal update profil: " . $e->getMessage()]);
            }
        }
        break;

    case 'cancel-subscription':
        if ($method === 'POST') {
            $user_session = getAuthenticatedUser();
            try {
                // Ubah status langganan menjadi 'canceled'
                $stmt = $pdo->prepare("UPDATE user_subscriptions SET status = 'canceled' WHERE user_id = :id AND status = 'active'");
                $stmt->execute([':id' => $user_session['id']]);
                
                echo json_encode(["status" => "success", "message" => "Langganan premium Anda telah dihentikan."]);
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Gagal membatalkan langganan."]);
            }
        }
        break;

    case 'delete-user':
        if ($method === 'POST') {
            $user_session = getAuthenticatedUser();
            try {
                // PENTING: Hapus data terkait (Riwayat Bayar & Langganan) dulu agar tidak terjadi Error Foreign Key
                $pdo->prepare("DELETE FROM payments WHERE user_id = :id")->execute([':id' => $user_session['id']]);
                $pdo->prepare("DELETE FROM user_subscriptions WHERE user_id = :id")->execute([':id' => $user_session['id']]);
                
                // Terakhir, baru kita hapus akun utamanya
                $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $user_session['id']]);
                
                echo json_encode(["status" => "success", "message" => "Akun beserta seluruh riwayat Anda berhasil dihapus permanen. Selamat tinggal!"]);
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Gagal menghapus akun."]);
            }
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Endpoint tidak ditemukan."]);
        break;
}