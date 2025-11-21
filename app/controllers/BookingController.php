<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Hotel;
use App\Libraries\Mailer; // Untuk kirim email invoice

class BookingController extends Controller {
    private $bookingModel;
    private $roomModel;
    private $hotelModel;

    public function __construct() {
        // Cek login hanya untuk proses submit, view bisa publik (tergantung flow)
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->hotelModel = new Hotel();
    }

    /**
     * Menampilkan Form Booking
     */
    public function create() {
        // Harus login untuk booking
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Silakan login untuk melanjutkan pemesanan.";
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $roomId = $_GET['room_id'] ?? null;
        if (!$roomId) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $room = $this->roomModel->find($roomId);
        $hotel = $this->hotelModel->find($room['hotel_id']);

        // Data untuk form
        $data = [
            'title' => 'Booking Hotel - Trevio',
            'room' => $room,
            'hotel' => $hotel,
            'user' => [
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ]
        ];

        $this->view('booking/create', $data);
    }

    /**
     * Proses Simpan Booking (POST)
     */
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        // 1. Ambil Data Input
        $roomId = $_POST['room_id'];
        $checkIn = $_POST['check_in'];
        $checkOut = $_POST['check_out'];
        $numRooms = (int) $_POST['num_rooms'];
        $guestName = filter_input(INPUT_POST, 'guest_name', FILTER_SANITIZE_STRING);
        $guestPhone = filter_input(INPUT_POST, 'guest_phone', FILTER_SANITIZE_STRING);
        
        // 2. Validasi Input
        if (strtotime($checkIn) < strtotime(date('Y-m-d'))) {
            $_SESSION['flash_error'] = "Tanggal check-in tidak valid.";
            header("Location: " . BASE_URL . "/booking/create?room_id=$roomId");
            exit;
        }

        if (strtotime($checkOut) <= strtotime($checkIn)) {
            $_SESSION['flash_error'] = "Tanggal check-out harus setelah check-in.";
            header("Location: " . BASE_URL . "/booking/create?room_id=$roomId");
            exit;
        }

        // 3. Cek Ketersediaan Slot (Critical Logic)
        // Sesuai ERD: cek field available_slots di table rooms
        $room = $this->roomModel->find($roomId);
        
        if ($room['available_slots'] < $numRooms) {
            $_SESSION['flash_error'] = "Maaf, sisa kamar hanya tersedia: " . $room['available_slots'];
            header("Location: " . BASE_URL . "/booking/create?room_id=$roomId");
            exit;
        }

        // 4. Kalkulasi Harga
        $date1 = new \DateTime($checkIn);
        $date2 = new \DateTime($checkOut);
        $interval = $date1->diff($date2);
        $numNights = $interval->days;

        $pricePerNight = $room['price_per_night'];
        $subtotal = $pricePerNight * $numNights * $numRooms;
        $tax = $subtotal * 0.10; // 10% Tax
        $service = $subtotal * 0.05; // 5% Service
        $totalPrice = $subtotal + $tax + $service;

        // 5. Generate Booking Code
        // Format: BK + Ymd + Random 5 digit (Contoh: BK2025112199382)
        $bookingCode = 'BK' . date('Ymd') . rand(10000, 99999);

        // 6. Siapkan Data Insert
        $bookingData = [
            'booking_code' => $bookingCode,
            'customer_id' => $_SESSION['user_id'],
            'hotel_id' => $room['hotel_id'],
            'room_id' => $roomId,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'num_nights' => $numNights,
            'num_rooms' => $numRooms,
            'price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'service_charge' => $service,
            'total_price' => $totalPrice,
            'guest_name' => $guestName,
            'guest_email' => $_SESSION['user_email'], // Default email akun
            'guest_phone' => $guestPhone,
            'booking_status' => 'pending_payment' // Status awal
        ];

        // 7. Simpan ke Database
        $bookingId = $this->bookingModel->create($bookingData);

        if ($bookingId) {
            // Optional: Kirim email pending payment
            // $mailer = new Mailer();
            // $mailer->sendPendingPayment($bookingData);

            // Redirect ke halaman pembayaran/sukses
            $_SESSION['flash_success'] = "Booking berhasil dibuat! Silakan lakukan pembayaran.";
            header('Location: ' . BASE_URL . '/booking/detail/' . $bookingCode);
            exit;
        } else {
            $_SESSION['flash_error'] = "Gagal membuat booking. Silakan coba lagi.";
            header("Location: " . BASE_URL . "/booking/create?room_id=$roomId");
            exit;
        }
    }

    /**
     * Halaman Detail Booking & Upload Bukti Bayar
     */
    public function detail($bookingCode) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $booking = $this->bookingModel->findByCode($bookingCode);

        // Security: Pastikan yang lihat adalah pemilik booking atau admin/owner
        if ($booking['customer_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] == 'customer') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $data = [
            'title' => 'Detail Booking #' . $bookingCode,
            'booking' => $booking,
            'room' => $this->roomModel->find($booking['room_id']),
            'hotel' => $this->hotelModel->find($booking['hotel_id'])
        ];

        $this->view('booking/detail', $data);
    }

    /**
     * Upload Bukti Transfer
     */
    public function uploadPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $bookingId = $_POST['booking_id'];
        
        // Handle File Upload
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
            $targetDir = "uploads/payments/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES["payment_proof"]["name"]);
            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validasi tipe file
            $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['flash_error'] = "Hanya file JPG, JPEG, PNG, & PDF yang diperbolehkan.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $targetFile)) {
                // Update status booking ke 'pending_verification'
                // Dan insert ke table payments (Logic ada di Model idealnya)
                $this->bookingModel->submitPayment($bookingId, $fileName, $_POST['bank_name'], $_POST['account_name']);
                
                $_SESSION['flash_success'] = "Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.";
            } else {
                $_SESSION['flash_error'] = "Gagal mengupload file.";
            }
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}