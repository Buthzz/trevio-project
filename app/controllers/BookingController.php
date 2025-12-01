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

    public function index() {
        header('Location: ' . BASE_URL);
        exit;
    }

    public function ticket($code) {
        $this->requireLogin();
        
        $code = strip_tags(trim($code));
        $booking = $this->bookingModel->findByCode($code);
        
        if (!$booking) {
            $_SESSION['flash_error'] = "Tiket tidak ditemukan.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Validasi Akses
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
            $_SESSION['flash_error'] = "Akses ditolak.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $data = [
            'title' => 'E-Ticket - ' . $booking['booking_code'],
            'booking' => $booking,
            'hotel' => $this->hotelModel->find($booking['hotel_id']),
            'room' => $this->roomModel->find($booking['room_id'])
        ];

        // [FIX]: Path view tiket aman
        $ticketView = __DIR__ . '/../views/booking/ticket.php';
        if (file_exists($ticketView)) {
            require_once $ticketView;
        } else {
            die("Error: File view tiket tidak ditemukan.");
        }
    }

    public function create() {
        $this->requireLogin();
        
        $roomId = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);
        $hotelId = filter_input(INPUT_GET, 'hotel_id', FILTER_VALIDATE_INT);

        if (!$roomId) {
            if ($hotelId) {
                $queryParams = $_GET;
                unset($queryParams['url']); 
                $queryString = http_build_query($queryParams);
                header('Location: ' . BASE_URL . '/hotel/detail?id=' . $hotelId . '&' . $queryString . '#rooms');
                exit;
            } else {
                header('Location: ' . BASE_URL);
                exit;
            }
        }

        $checkIn = filter_input(INPUT_GET, 'check_in', FILTER_SANITIZE_SPECIAL_CHARS);
        $checkOut = filter_input(INPUT_GET, 'check_out', FILTER_SANITIZE_SPECIAL_CHARS);
        $numRooms = filter_input(INPUT_GET, 'num_rooms', FILTER_VALIDATE_INT);

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
            'user' => ['name' => $_SESSION['user_name'] ?? '', 'email' => $_SESSION['user_email'] ?? ''],
            'csrf_token' => $_SESSION['csrf_token'],
            'search_params' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_rooms' => $numRooms ?? 1
            ]
        ];

        $this->view('booking/create', $data);
    }

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
        
        // ... (Validasi input tanggal & tamu sama seperti sebelumnya) ...
        $checkIn = htmlspecialchars(strip_tags($_POST['check_in'] ?? ''), ENT_QUOTES, 'UTF-8');
        $checkOut = htmlspecialchars(strip_tags($_POST['check_out'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        if (!$checkIn || !$checkOut) $this->redirectBack($roomId, "Tanggal tidak valid.");
        
        $checkInTime = strtotime($checkIn);
        $checkOutTime = strtotime($checkOut);
        $today = strtotime(date('Y-m-d'));
        
        if ($checkInTime < $today) $this->redirectBack($roomId, "Tanggal check-in tidak boleh di masa lalu.");
        if ($checkOutTime <= $checkInTime) $this->redirectBack($roomId, "Check-out harus setelah check-in.");
        
        $numNights = (new \DateTime($checkIn))->diff(new \DateTime($checkOut))->days;

        $guestName = strip_tags(trim($_POST['guest_name'] ?? ''));
        $guestEmail = filter_input(INPUT_POST, 'guest_email', FILTER_VALIDATE_EMAIL);
        $guestPhone = strip_tags(trim($_POST['guest_phone'] ?? ''));
        
        if (empty($guestName) || strlen($guestName) < 3) $this->redirectBack($roomId, "Nama tamu tidak valid.");
        if (!$guestEmail) $guestEmail = $_SESSION['user_email'] ?? '';
        
        $room = $this->roomModel->find($roomId);
        if (!$room) $this->redirectBack($roomId, "Kamar tidak ditemukan.");
        
        $pricePerNight = abs((float)$room['price_per_night']);
        $subtotal = $pricePerNight * $numNights * $numRooms;
        $taxAmount = $subtotal * 0.10;
        $serviceCharge = $subtotal * 0.05;
        $totalPrice = $subtotal + $taxAmount + $serviceCharge;

        do {
            $code = 'BK' . date('Ymd') . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $exists = $this->bookingModel->findByCode($code);
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
        
        if (!$bookingId) $this->redirectBack($roomId, "Gagal membuat pesanan.");

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
            $_SESSION['flash_error'] = "Booking tidak ditemukan.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Redirect URL: Kembali ke detail booking
        $redirectUrl = BASE_URL . '/booking/detail/' . $booking['booking_code'];

        if ($booking['booking_status'] !== 'pending_payment') {
            $_SESSION['flash_error'] = "Status booking tidak valid.";
            header("Location: $redirectUrl");
            exit;
        }

        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "File bukti pembayaran wajib diupload.";
            header("Location: $redirectUrl");
            exit;
        }

        $file = $_FILES['payment_proof'];
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $_SESSION['flash_error'] = "Ukuran file terlalu besar (Max 5MB).";
            header("Location: $redirectUrl");
            exit;
        }

        // Ambil Data Form
        $bankName = strip_tags(trim($_POST['bank_name'] ?? ''));
        $accountName = strip_tags(trim($_POST['account_name'] ?? ''));
        $accountNumber = strip_tags(trim($_POST['account_number'] ?? ''));
        
        if (empty($bankName) || empty($accountName)) {
            $_SESSION['flash_error'] = "Data bank tidak lengkap.";
            header("Location: $redirectUrl");
            exit;
        }

        // Upload Logic
        $targetDir = __DIR__ . "/../../public/uploads/payments/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'payment_' . $bookingId . '_' . time() . '.' . $extension;
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $success = $this->bookingModel->submitPayment($bookingId, $fileName, $bankName, $accountName, $accountNumber);
            if ($success) {
                $_SESSION['flash_success'] = "Bukti pembayaran berhasil dikirim. Menunggu verifikasi.";
            } else {
                $_SESSION['flash_error'] = "Gagal menyimpan data.";
            }
        } else {
            $_SESSION['flash_error'] = "Gagal upload file.";
        }

        header("Location: $redirectUrl");
        exit;
    }

    public function detail($code) {
        $this->requireLogin();
        $code = strip_tags(trim($code));
        $booking = $this->bookingModel->findByCode($code);
        
        if (!$booking) {
            $_SESSION['flash_error'] = "Booking tidak ditemukan.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        // Cek Otorisasi
        $isOwner = false;
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner') {
            $hotel = $this->hotelModel->find($booking['hotel_id']);
            $isOwner = ($hotel && $hotel['owner_id'] == $_SESSION['user_id']);
        }
        
        $isAuthorized = ($booking['customer_id'] == $_SESSION['user_id'] || $isOwner || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'));
        
        if (!$isAuthorized) {
            $_SESSION['flash_error'] = "Anda tidak memiliki akses.";
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

    private function validateCsrf(): void {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("Security Error: CSRF token invalid.");
        }
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
        $params = $_POST;
        unset($params['csrf_token']);
        $queryString = http_build_query($params);
        header("Location: " . BASE_URL . "/booking/create?room_id=$roomId&" . $queryString);
        exit;
    }
}