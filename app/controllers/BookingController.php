<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Hotel;
use Exception;

class BookingController extends Controller {
    private $bookingModel;
    private $roomModel;
    private $hotelModel;

    public function __construct() {
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->hotelModel = new Hotel();
    }

    public function create() {
        $this->requireLogin();
        
        $roomId = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);
        $hotelId = filter_input(INPUT_GET, 'hotel_id', FILTER_VALIDATE_INT);

        // [FIX FLOW]: Jika room_id kosong, jangan lempar ke HOME.
        // Cek jika ada hotel_id, arahkan user ke halaman detail hotel tersebut untuk pilih kamar.
        if (!$roomId) {
            if ($hotelId) {
                // Pertahankan parameter search saat redirect
                $queryParams = $_GET;
                unset($queryParams['url']); // Hapus parameter routing internal
                $queryString = http_build_query($queryParams);
                
                header('Location: ' . BASE_URL . '/hotel/detail?id=' . $hotelId . '&' . $queryString . '#rooms');
                exit;
            } else {
                // Jika sama sekali tidak ada data, baru lempar ke home
                header('Location: ' . BASE_URL);
                exit;
            }
        }

        // Ambil Parameter Pencarian dari URL untuk Pre-fill Form (Logika Baru)
        $checkIn = filter_input(INPUT_GET, 'check_in', FILTER_SANITIZE_SPECIAL_CHARS);
        $checkOut = filter_input(INPUT_GET, 'check_out', FILTER_SANITIZE_SPECIAL_CHARS);
        $numRooms = filter_input(INPUT_GET, 'num_rooms', FILTER_VALIDATE_INT);

        // Generate CSRF Token if not exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }

        $room = $this->roomModel->find($roomId);
        if (!$room) {
            $_SESSION['flash_error'] = "Kamar tidak ditemukan.";
            header('Location: ' . BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Booking Hotel',
            'room' => $room,
            'hotel' => $this->hotelModel->find($room['hotel_id']),
            'user' => ['name' => $_SESSION['user_name'], 'email' => $_SESSION['user_email']],
            'csrf_token' => $_SESSION['csrf_token'],
            // Kirim parameter pencarian ke view agar form terisi otomatis
            'search_params' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_rooms' => $numRooms ?? 1
            ]
        ];

        $this->view('booking/create', $data);
    }

    // Method store(), uploadPayment(), dll biarkan seperti kode asli Anda...
    // Sertakan kembali method store dan lainnya di sini agar file tetap utuh/runnable
    public function store() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->validateCsrf();

        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $numRooms = filter_input(INPUT_POST, 'num_rooms', FILTER_VALIDATE_INT);
        
        if (!$roomId || !$numRooms) {
            $this->redirectBack($roomId ?: 0, "Data tidak lengkap.");
        }
        
        $maxRooms = defined('BOOKING_MAX_ROOMS') ? BOOKING_MAX_ROOMS : 10;
        if ($numRooms <= 0 || $numRooms > $maxRooms) {
            $this->redirectBack($roomId, "Jumlah kamar tidak valid (max {$maxRooms}).");
        }
        
        $checkIn = htmlspecialchars(strip_tags($_POST['check_in'] ?? ''), ENT_QUOTES, 'UTF-8');
        $checkOut = htmlspecialchars(strip_tags($_POST['check_out'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        if (!$checkIn || !$checkOut) {
            $this->redirectBack($roomId, "Tanggal tidak valid.");
        }
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkIn) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkOut)) {
            $this->redirectBack($roomId, "Format tanggal tidak valid.");
        }
        
        $checkInTime = strtotime($checkIn);
        $checkOutTime = strtotime($checkOut);
        $today = strtotime(date('Y-m-d'));
        
        if ($checkInTime < $today) {
            $this->redirectBack($roomId, "Tanggal check-in tidak boleh di masa lalu.");
        }
        
        if ($checkOutTime <= $checkInTime) {
            $this->redirectBack($roomId, "Check-out harus setelah check-in.");
        }
        
        $maxDays = 30;
        $numNights = (new \DateTime($checkIn))->diff(new \DateTime($checkOut))->days;
        if ($numNights > $maxDays) {
            $this->redirectBack($roomId, "Maksimal booking {$maxDays} hari.");
        }

        $guestName = strip_tags(trim($_POST['guest_name'] ?? ''));
        $guestEmail = filter_input(INPUT_POST, 'guest_email', FILTER_VALIDATE_EMAIL);
        $guestPhone = strip_tags(trim($_POST['guest_phone'] ?? ''));
        
        if (empty($guestName) || strlen($guestName) < 3) {
            $this->redirectBack($roomId, "Nama tamu tidak valid (min 3 karakter).");
        }
        
        if (!$guestEmail) {
            $guestEmail = $_SESSION['user_email'] ?? '';
        }
        
        if (empty($guestPhone) || !preg_match('/^[0-9+\-\s()]+$/', $guestPhone)) {
            $this->redirectBack($roomId, "Nomor telepon tidak valid.");
        }

        $room = $this->roomModel->find($roomId);
        
        if (!$room) {
            $this->redirectBack($roomId, "Kamar tidak ditemukan.");
        }
        
        if ($room['available_slots'] < $numRooms) {
            $this->redirectBack($roomId, "Slot kamar tidak mencukupi. Tersedia: {$room['available_slots']}");
        }

        $pricePerNight = abs((float)$room['price_per_night']);
        $subtotal = $pricePerNight * $numNights * $numRooms;
        $taxAmount = $subtotal * 0.10;
        $serviceCharge = $subtotal * 0.05;
        $totalPrice = $subtotal + $taxAmount + $serviceCharge;

        $maxRetries = 10;
        $retryCount = 0;
        do {
            $code = 'BK' . date('Ymd') . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $exists = $this->bookingModel->findByCode($code);
            $retryCount++;
            if ($retryCount > $maxRetries) {
                $this->redirectBack($roomId, "Terjadi kesalahan sistem saat generate kode. Silakan coba lagi.");
            }
        } while ($exists);

        $bookingData = [
            'booking_code' => $code,
            'customer_id' => (int)$_SESSION['user_id'],
            'hotel_id' => (int)$room['hotel_id'],
            'room_id' => $roomId,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'num_nights' => $numNights,
            'num_rooms' => $numRooms,
            'price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'service_charge' => $serviceCharge,
            'total_price' => $totalPrice,
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'booking_status' => 'pending_payment'
        ];

        $bookingId = $this->bookingModel->createSecurely($bookingData);
        
        if (!$bookingId) {
            $this->redirectBack($roomId, "Mohon maaf, kamar baru saja penuh atau terjadi kesalahan sistem.");
        }

        $_SESSION['flash_success'] = "Booking berhasil! Silakan upload bukti pembayaran.";
        header('Location: ' . BASE_URL . '/booking/detail/' . $code);
        exit;
    }

    public function uploadPayment() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $this->validateCsrf();

        $bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
        if (!$bookingId) {
            $_SESSION['flash_error'] = "Booking ID tidak valid.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $booking = $this->bookingModel->find($bookingId);
        if (!$booking || $booking['customer_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Booking tidak ditemukan atau bukan milik Anda.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($booking['booking_status'] !== 'pending_payment') {
            $_SESSION['flash_error'] = "Booking ini tidak memerlukan upload pembayaran.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "File tidak ditemukan atau terjadi error saat upload.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $file = $_FILES['payment_proof'];
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $_SESSION['flash_error'] = "Ukuran file terlalu besar (Max 5MB).";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes)) {
            $_SESSION['flash_error'] = "Format file tidak valid. Hanya JPG, PNG, atau PDF yang diperbolehkan.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (in_array($mime, ['image/jpeg', 'image/png', 'image/jpg'])) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                $_SESSION['flash_error'] = "File bukan gambar yang valid.";
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            }
        }

        $bankName = strip_tags(trim($_POST['bank_name'] ?? ''));
        $accountName = strip_tags(trim($_POST['account_name'] ?? ''));
        $accountNumber = strip_tags(trim($_POST['account_number'] ?? ''));
        
        if (empty($bankName) || strlen($bankName) < 2) {
            $_SESSION['flash_error'] = "Nama bank tidak valid.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        if (empty($accountName) || strlen($accountName) < 3) {
            $_SESSION['flash_error'] = "Nama pemilik rekening tidak valid.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $targetDir = __DIR__ . "/../../public/uploads/payments/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $extension = strtolower(preg_replace('/[^a-z0-9]/', '', $extension));
        $fileName = 'payment_' . $bookingId . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $targetDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['flash_error'] = "Gagal menyimpan file. Silakan coba lagi.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $success = $this->bookingModel->submitPayment(
            $bookingId, 
            $fileName, 
            $bankName, 
            $accountName,
            $accountNumber
        );

        if ($success) {
            $_SESSION['flash_success'] = "Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.";
        } else {
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
            $_SESSION['flash_error'] = "Gagal menyimpan data pembayaran. Silakan coba lagi.";
        }

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    private function validateCsrf(): void {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            http_response_code(403);
            die("CSRF token missing. Please refresh the page and try again.");
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            http_response_code(403);
            die("CSRF Validation Failed. Please refresh the page and try again.");
        }
        
        if (isset($_SESSION['csrf_token_time'])) {
            $tokenAge = time() - $_SESSION['csrf_token_time'];
            $expiry = defined('CSRF_TOKEN_EXPIRE') ? CSRF_TOKEN_EXPIRE : 3600;
            
            if ($tokenAge > $expiry) {
                http_response_code(403);
                die("CSRF token expired. Please refresh the page and try again.");
            }
        }
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }

    private function requireLogin(): void {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Silakan login terlebih dahulu.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    private function redirectBack(int $roomId, string $msg): void {
        $_SESSION['flash_error'] = $msg;
        
        $params = [];
        if (isset($_POST['check_in'])) $params['check_in'] = $_POST['check_in'];
        if (isset($_POST['check_out'])) $params['check_out'] = $_POST['check_out'];
        if (isset($_POST['num_rooms'])) $params['num_rooms'] = $_POST['num_rooms'];
        
        $queryString = http_build_query($params);
        
        header("Location: " . BASE_URL . "/booking/create?room_id=$roomId&" . $queryString);
        exit;
    }
    
    public function detail($code) {
        $this->requireLogin();
        
        $code = strip_tags(trim($code));
        
        if (empty($code)) {
            $_SESSION['flash_error'] = "Kode booking tidak valid.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        $booking = $this->bookingModel->findByCode($code);
        
        if (!$booking) {
            $_SESSION['flash_error'] = "Booking tidak ditemukan.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        $isOwner = false;
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner') {
            $hotel = $this->hotelModel->find($booking['hotel_id']);
            $isOwner = ($hotel && $hotel['owner_id'] == $_SESSION['user_id']);
        }
        
        $isAuthorized = (
            $booking['customer_id'] == $_SESSION['user_id'] ||
            $isOwner ||
            (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
        );
        
        if (!$isAuthorized) {
            $_SESSION['flash_error'] = "Anda tidak memiliki akses ke booking ini.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        $this->view('booking/detail', [
            'title' => 'Detail Booking',
            'booking' => $booking,
            'csrf_token' => $_SESSION['csrf_token'],
            'user' => $_SESSION
        ]);
    }
}