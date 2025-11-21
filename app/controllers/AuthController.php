<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Controller; // Asumsi ada base controller
use App\Core\Database;

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Menampilkan halaman Login
     */
    public function login() {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $data = [
            'title' => 'Login - Trevio',
            'google_auth_url' => $this->getGoogleAuthUrl()
        ];
        
        $this->view('auth/login', $data);
    }

    /**
     * Menampilkan halaman Register
     */
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $data = [
            'title' => 'Daftar - Trevio'
        ];

        $this->view('auth/register', $data);
    }

    /**
     * Proses Login (POST)
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Sanitize inputs
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Validasi dasar
        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = "Email dan Password wajib diisi.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Cari user by email
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // Verifikasi password
            // Catatan: Untuk user Google login, password mungkin null/random, jadi cek auth_provider
            if ($user['auth_provider'] == 'email' && password_verify($password, $user['password'])) {
                $this->createUserSession($user);
            } else {
                $_SESSION['flash_error'] = "Password salah atau akun terdaftar dengan Google.";
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }
        } else {
            $_SESSION['flash_error'] = "Akun tidak ditemukan. Silakan daftar.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Proses Register (POST)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }

        // Ambil data dari form
        $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role = $_POST['user_type'] ?? 'customer'; // guest (customer) atau host (owner)

        // Mapping role dari form value ke database enum
        $dbRole = ($role === 'host') ? 'owner' : 'customer';

        // Validasi
        if (empty($fullName) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = "Semua kolom wajib diisi.";
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = "Password minimal 8 karakter.";
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }

        // Cek email duplikat
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = "Email sudah terdaftar.";
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Simpan ke database
        $data = [
            'name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $dbRole,
            'auth_provider' => 'email',
            'is_verified' => 1, // Auto verified untuk MVP, nanti bisa ubah ke 0 butuh email verif
            'is_active' => 1
        ];

        if ($this->userModel->create($data)) {
            $_SESSION['flash_success'] = "Registrasi berhasil! Silakan login.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan sistem.";
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }
    }

    /**
     * Proses Logout
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    /**
     * Setup User Session
     */
    private function createUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Redirect ke dashboard yang sesuai
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    // ==========================================
    // GOOGLE OAUTH SECTION
    // ==========================================

    /**
     * Generate Google Login URL
     */
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

    /**
     * Handle Callback from Google
     */
    public function googleCallback() {
        if (!isset($_GET['code'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $code = $_GET['code'];
        
        // 1. Exchange code for token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
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
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            $_SESSION['flash_error'] = "Gagal login dengan Google.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // 2. Get User Profile
        $accessToken = $tokenData['access_token'];
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // 3. Check or Create User in DB
        $email = $userInfo['email'];
        $googleId = $userInfo['id'];
        $name = $userInfo['name'];
        $picture = $userInfo['picture'];

        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // User exists, update google_id if empty
            if (empty($user['google_id'])) {
                $this->userModel->update($user['id'], ['google_id' => $googleId, 'profile_image' => $picture]);
            }
            $this->createUserSession($user);
        } else {
            // Create new user
            $newUser = [
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'auth_provider' => 'google',
                'role' => 'customer', // Default role for Google login
                'is_verified' => 1,
                'is_active' => 1,
                'profile_image' => $picture
            ];
            
            $userId = $this->userModel->create($newUser);
            $newUser['id'] = $userId;
            
            $this->createUserSession($newUser);
        }
    }
}