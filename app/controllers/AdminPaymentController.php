<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use App\Services\NotificationService;

class AdminPaymentController extends BaseAdminController {
    private $paymentModel;
    private $bookingModel;
    private $hotelModel;
    private $userModel;
    private $notificationService;
    
    // KUNCI RAHASIA untuk Token Deep Link (Sebaiknya simpan di .env)
    private $secretKey = "TREVIO_SECRET_KEY_2025"; 

    public function __construct() {
        // 1. Inisialisasi Model & Service
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking(); // Diperlukan untuk update status
        $this->hotelModel = new Hotel();     // Diperlukan untuk info Owner
        $this->userModel = new User();       // Diperlukan untuk kontak Owner
        $this->notificationService = new NotificationService();

        // 2. LOGIKA AUTH HYBRID (Admin vs Public Deep Link)
        // Kita perlu tahu method apa yang sedang diakses saat ini
        // Menggunakan debug backtrace atau global $_GET['url'] tergantung routing Anda.
        // Cara paling aman & generik: Cek URL request.
        
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Daftar method yang BOLEH diakses tanpa login (Public)
        $publicMethods = ['quick_verify', 'process_deep_link'];
        
        $isPublicAccess = false;
        foreach ($publicMethods as $method) {
            if (strpos($requestUri, $method) !== false) {
                $isPublicAccess = true;
                break;
            }
        }

        // 3. Enforce Login Admin HANYA jika bukan akses public
        if (!$isPublicAccess) {
            // Panggil konstruktor parent untuk cek sesi admin standard
            parent::__construct(); 
        } else {
            // Jika akses public, kita bypass check login parent
            // tapi pastikan session aktif untuk CSRF token di form deep link
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }
    }

    // =========================================================================
    // BAGIAN 1: STANDARD ADMIN DASHBOARD (Butuh Login)
    // =========================================================================

    public function index() {
        $status = $this->getQuery('status', 'pending');

        $data = [
            'title' => 'Manage Payments',
            'payments' => $this->paymentModel->getAll($status),
            'pending_count' => $this->paymentModel->countPending(),
            'current_status' => $status,
            'csrf_token' => $_SESSION['csrf_token'] ?? '', 
            'user' => $_SESSION
        ];

        $this->view('admin/payments/index', $data);
    }

    public function verify($id) {
        $payment = $this->paymentModel->find($id);

        if (!$payment) {
            $_SESSION['flash_error'] = "Data pembayaran tidak ditemukan.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $data = [
            'title' => 'Verify Payment',
            'payment' => $payment,
            'is_deep_link' => false, // Flag: Ini mode admin biasa
            'csrf_token' => $_SESSION['csrf_token'],
            'user' => $_SESSION
        ];

        $this->view('admin/payments/verify', $data);
    }

    /**
     * Proses Konfirmasi dari Dashboard Admin
     */
    public function confirm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();
        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);

        // Eksekusi Konfirmasi (Update Status Payment & Booking)
        if ($this->paymentModel->confirm($paymentId, $_SESSION['user_id'])) {
            
            // Kirim Notifikasi (WA ke Customer & Owner)
            $this->triggerNotifications($paymentId);
            
            $_SESSION['flash_success'] = "Pembayaran diverifikasi. Notifikasi telah dikirim.";
        } else {
            $_SESSION['flash_error'] = "Gagal memverifikasi pembayaran.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    /**
     * Proses Penolakan dari Dashboard Admin
     */
    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();
        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $reason = $_POST['reason'] ?? 'Bukti tidak valid';

        if ($this->paymentModel->reject($paymentId, $_SESSION['user_id'], $reason)) {
            $_SESSION['flash_success'] = "Pembayaran ditolak.";
        } else {
            $_SESSION['flash_error'] = "Gagal menolak pembayaran.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    // =========================================================================
    // BAGIAN 2: PUBLIC DEEP LINK (Tanpa Login, Pakai Token)
    // =========================================================================

    /**
     * Menampilkan Halaman Verifikasi (Mode Tamu)
     * URL: /admin/payment/quick_verify?id=123&token=xyz...
     */
    public function quick_verify() {
        $paymentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $token = $_GET['token'] ?? '';

        if (!$paymentId || empty($token)) {
            die("Link tidak valid.");
        }

        // 1. Validasi Token Keamanan
        $expectedToken = hash_hmac('sha256', $paymentId . 'VERIFY', $this->secretKey);

        if (!hash_equals($expectedToken, $token)) {
            die("<h3>Link Kadaluarsa atau Tidak Valid</h3><p>Security check failed. Mohon minta link baru.</p>");
        }

        // 2. Ambil Data Payment
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            die("Data pembayaran tidak ditemukan.");
        }

        // 3. Tampilkan View Verify (Mode Deep Link)
        $data = [
            'title' => 'Verifikasi Cepat - ' . $payment['booking_code'],
            'payment' => $payment,
            'is_deep_link' => true,      // Flag: Ini mode deep link
            'security_token' => $token,  // Teruskan token untuk form action
            'user' => null 
        ];

        // Gunakan view yang sama dengan admin
        $this->view('admin/payments/verify', $data);
    }

    /**
     * Memproses Aksi dari Halaman Deep Link
     */
    public function process_deep_link() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Metode request tidak valid.");
        }

        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $token = $_POST['security_token'] ?? '';
        $action = $_POST['action'] ?? ''; // 'confirm' atau 'reject'
        $reason = $_POST['reason'] ?? '';

        // 1. Validasi Token Lagi (Penting untuk keamanan POST)
        $expectedToken = hash_hmac('sha256', $paymentId . 'VERIFY', $this->secretKey);
        if (!hash_equals($expectedToken, $token)) {
            die("Token keamanan tidak valid. Silakan refresh link dari WhatsApp.");
        }

        // 2. Proses Sesuai Action
        $actorId = 0; // 0 = System/DeepLink user

        if ($action === 'confirm') {
            if ($this->paymentModel->confirm($paymentId, $actorId)) {
                // Kirim Notifikasi
                $this->triggerNotifications($paymentId);
                
                // Tampilan Sukses
                echo $this->getSuccessHtml("Pembayaran Dikonfirmasi", "Booking telah aktif. Notifikasi WA & Email telah dikirim ke tamu dan owner.");
            } else {
                echo "Gagal mengkonfirmasi. Mungkin status sudah berubah.";
            }
        } elseif ($action === 'reject') {
            if ($this->paymentModel->reject($paymentId, $actorId, $reason)) {
                echo $this->getSuccessHtml("Pembayaran Ditolak", "Alasan: $reason");
            } else {
                echo "Gagal menolak pembayaran.";
            }
        } else {
            die("Aksi tidak dikenali.");
        }
    }

    // =========================================================================
    // HELPER FUNCTIONS
    // =========================================================================

    /**
     * Helper: Trigger Notifikasi Lengkap (WA Admin, WA Owner, WA/Email Customer)
     */
    private function triggerNotifications($paymentId) {
        try {
            // Ambil data payment terbaru (sudah join dengan booking & customer)
            $paymentData = $this->paymentModel->find($paymentId);
            
            if (!$paymentData) return;

            // 1. Notifikasi ke CUSTOMER (WA + Email Invoice)
            $this->notificationService->sendBookingConfirmation($paymentData);

            // 2. Notifikasi ke OWNER HOTEL (WA)
            // Kita perlu cari data owner berdasarkan hotel_id
            $hotel = $this->hotelModel->find($paymentData['hotel_id']);
            if ($hotel && !empty($hotel['owner_id'])) {
                $owner = $this->userModel->find($hotel['owner_id']);
                
                if ($owner && !empty($owner['phone'])) {
                    $this->notificationService->notifyOwnerBookingSold(
                        $paymentData,     // Data booking/payment
                        $owner['phone'],  // No HP Owner
                        $hotel['name']    // Nama Hotel
                    );
                }
            }

        } catch (\Exception $e) {
            error_log("Notification Error: " . $e->getMessage());
            // Jangan die(), biarkan proses konfirmasi tetap sukses meski notif gagal
        }
    }

    private function getQuery($key, $default = null) {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("CSRF Validation Failed.");
        }
    }

    private function getSuccessHtml($title, $message) {
        return "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Sukses</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-gray-50 flex items-center justify-center h-screen'>
            <div class='max-w-md w-full bg-white p-8 rounded-xl shadow-lg text-center border border-gray-100'>
                <div class='mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6'>
                    <svg class='h-8 w-8 text-green-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path></svg>
                </div>
                <h2 class='text-2xl font-bold text-gray-900 mb-2'>{$title}</h2>
                <p class='text-gray-600 mb-6'>{$message}</p>
                <button onclick='window.close()' class='px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition'>Tutup Halaman</button>
            </div>
        </body>
        </html>";
    }
}