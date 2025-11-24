<?php
// v3
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        // Rate limiting dihapus sesuai permintaan
    }

    public function login() {
        $this->checkGuest();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $data = [
            'title' => 'Login - Trevio',
            'google_auth_url' => $this->getGoogleAuthUrl(),
            'csrf_token' => $_SESSION['csrf_token']
        ];
        $this->view('auth/login', $data);
    }

    public function register() {
        $this->checkGuest();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $data = ['title' => 'Daftar - Trevio', 'csrf_token' => $_SESSION['csrf_token']];
        $this->view('auth/register', $data);
    }

    // === BAGIAN YANG DIMODIFIKASI DENGAN DEBUGGING ===
    public function authenticate() {
        $this->validateRequest();
        $this->validateCsrf();

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || empty($password)) {
            $this->redirectWithError('/auth/login', "Email tidak valid atau password kosong.");
        }

        // Ambil user dari database
        $user = $this->userModel->findByEmail($email);

        // --- 🛠️ START DEBUGGING CODE 🛠️ ---
        // HAPUS BLOCK INI SETELAH MASALAH SELESAI
        echo "<div style='background:#f8f9fa; padding:20px; border:2px solid #333; font-family:monospace;'>";
        echo "<h3>🔍 DEBUG LOGIN PROCESS</h3>";
        echo "<strong>Input Email:</strong> " . htmlspecialchars($email) . "<br>";
        echo "<strong>Input Password:</strong> " . htmlspecialchars($password) . "<br><hr>";

        if (!$user) {
            echo "<strong style='color:red;'>❌ ERROR: User tidak ditemukan di database!</strong><br>";
            echo "Saran: Cek tabel 'users' di database, pastikan email '$email' sudah ada.";
        } else {
            echo "<strong style='color:green;'>✅ User Ditemukan!</strong><br>";
            echo "<strong>Data User DB:</strong><pre>" . print_r($user, true) . "</pre><hr>";
            
            echo "<strong>Cek Password:</strong><br>";
            echo "Hash di DB: " . $user['password'] . "<br>";
            
            $checkPassword = password_verify($password, $user['password']);
            
            if ($checkPassword) {
                echo "<strong style='color:green;'>✅ Password COCOK!</strong><br>";
            } else {
                echo "<strong style='color:red;'>❌ Password TIDAK COCOK!</strong><br>";
                echo "Kemungkinan: <br>";
                echo "1. Password input salah ketik.<br>";
                echo "2. Hash di database bukan hasil dari password_hash(..., PASSWORD_BCRYPT).<br>";
            }

            echo "<hr><strong>Cek Auth Provider:</strong> " . $user['auth_provider'];
            if ($user['auth_provider'] !== 'email') {
                 echo " <strong style='color:orange;'>(⚠️ Perhatikan! Provider harus 'email')</strong>";
            }
        }
        echo "</div>";
        die(); // Menghentikan eksekusi agar Anda bisa membaca pesan debug
        // --- 🛠️ END DEBUGGING CODE 🛠️ ---

        // Logika Asli (Tidak akan dieksekusi selama ada die() di atas)
        if ($user && $user['auth_provider'] === 'email' && password_verify($password, $user['password'])) {
            $this->loginUser($user);
        } else {
            $this->redirectWithError('/auth/login', "Kredensial tidak cocok.");
        }
    }
    // ===============================================

    public function store() {
        $this->validateRequest();
        $this->validateCsrf();

        $fullName = strip_tags(trim($_POST['full_name']));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role = ($_POST['user_type'] === 'host') ? 'owner' : 'customer';

        if (!$email || empty($fullName) || strlen($password) < 8) {
            $this->redirectWithError('/auth/register', "Data tidak valid. Password minimal 8 karakter.");
        }

        if ($this->userModel->findByEmail($email)) {
            $this->redirectWithError('/auth/register', "Email sudah terdaftar.");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $data = [
            'name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'auth_provider' => 'email',
            'is_verified' => 1, 
            'is_active' => 1
        ];

        if ($this->userModel->create($data)) {
            $_SESSION['flash_success'] = "Registrasi berhasil! Silakan login.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $this->redirectWithError('/auth/register', "Terjadi kesalahan sistem.");
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    private function loginUser($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: ' . BASE_URL . '/admin/dashboard');
        } elseif ($user['role'] === 'owner') {
            header('Location: ' . BASE_URL . '/owner');
        } else {
            header('Location: ' . BASE_URL . '/dashboard');
        }
        exit;
    }

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
    }

    private function validateRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    private function checkGuest() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    private function redirectWithError($path, $message) {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    private function getGoogleAuthUrl() {
        $params = [
            'client_id'     => getenv('GOOGLE_CLIENT_ID'),
            'redirect_uri'  => getenv('GOOGLE_REDIRECT_URI'),
            'response_type' => 'code',
            'scope'         => 'email profile',
            'access_type'   => 'online'
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public function googleCallback() {
        if (!isset($_GET['code'])) {
            $this->redirectWithError('/auth/login', "Gagal autentikasi Google.");
        }

        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $_GET['code'],
            'client_id' => getenv('GOOGLE_CLIENT_ID'),
            'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => getenv('GOOGLE_REDIRECT_URI'),
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->redirectWithError('/auth/login', "Connection Error: " . curl_error($ch));
        }
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            $this->redirectWithError('/auth/login', "Invalid Google Token.");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenData['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $email = $userInfo['email'];
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            if (empty($user['google_id'])) {
                $this->userModel->update($user['id'], ['google_id' => $userInfo['id'], 'profile_image' => $userInfo['picture']]);
            }
            $this->loginUser($user);
        } else {
            $newUser = [
                'name' => $userInfo['name'],
                'email' => $email,
                'google_id' => $userInfo['id'],
                'auth_provider' => 'google',
                'role' => 'customer',
                'is_verified' => 1,
                'is_active' => 1,
                'profile_image' => $userInfo['picture']
            ];
            $userId = $this->userModel->create($newUser);
            $newUser['id'] = $userId;
            $this->loginUser($newUser);
        }
    }
}