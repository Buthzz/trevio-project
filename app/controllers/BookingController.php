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
        
        if (!$roomId) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Generate CSRF Token if not exists
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $data = [
            'title' => 'Booking Hotel',
            'room' => $this->roomModel->find($roomId),
            'hotel' => $this->hotelModel->find($this->roomModel->find($roomId)['hotel_id']),
            'user' => ['name' => $_SESSION['user_name'], 'email' => $_SESSION['user_email']],
            'csrf_token' => $_SESSION['csrf_token']
        ];

        $this->view('booking/create', $data);
    }

    public function store() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        
        // CSRF Check
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Error");

        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $numRooms = filter_input(INPUT_POST, 'num_rooms', FILTER_VALIDATE_INT);
        
        // Validation: Negative values & max duration
        if ($numRooms <= 0) $this->redirectBack($roomId, "Jumlah kamar tidak valid.");
        
        $checkIn = $_POST['check_in'];
        $checkOut = $_POST['check_out'];
        
        if (strtotime($checkIn) < strtotime(date('Y-m-d'))) $this->redirectBack($roomId, "Tanggal check-in invalid.");
        if (strtotime($checkOut) <= strtotime($checkIn)) $this->redirectBack($roomId, "Check-out harus setelah check-in.");

        // --- CRITICAL: ATOMIC TRANSACTION START ---
        try {
            $this->bookingModel->db->beginTransaction();

            // SELECT FOR UPDATE (Lock the room row)
            // Assuming roomModel has a method or we access DB directly
            // Logic moved here for atomicity:
            $this->bookingModel->db->query("SELECT available_slots, price_per_night, hotel_id FROM rooms WHERE id = :id FOR UPDATE");
            $this->bookingModel->db->bind(':id', $roomId);
            $room = $this->bookingModel->db->single();

            if (!$room || $room['available_slots'] < $numRooms) {
                throw new Exception("Slot kamar tidak mencukupi.");
            }

            // Calculate Price
            $numNights = (new \DateTime($checkIn))->diff(new \DateTime($checkOut))->days;
            $subtotal = $room['price_per_night'] * $numNights * $numRooms;
            $totalPrice = $subtotal * 1.15; // Tax 10% + Service 5%

            // Unique Booking Code
            do {
                $code = 'BK' . date('Ymd') . rand(10000, 99999);
                $exists = $this->bookingModel->findByCode($code);
            } while ($exists);

            $bookingData = [
                'booking_code' => $code,
                'customer_id' => $_SESSION['user_id'],
                'hotel_id' => $room['hotel_id'],
                'room_id' => $roomId,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'num_nights' => $numNights,
                'num_rooms' => $numRooms,
                'price_per_night' => $room['price_per_night'],
                'subtotal' => $subtotal,
                'tax_amount' => $subtotal * 0.10,
                'service_charge' => $subtotal * 0.05,
                'total_price' => $totalPrice,
                'guest_name' => strip_tags($_POST['guest_name']),
                'guest_email' => filter_input(INPUT_POST, 'guest_email', FILTER_VALIDATE_EMAIL) ?: $_SESSION['user_email'],
                'guest_phone' => strip_tags($_POST['guest_phone']),
                'booking_status' => 'pending_payment'
            ];

            // Insert Booking
            $this->bookingModel->create($bookingData);

            // Commit Transaction
            $this->bookingModel->db->commit();

            $_SESSION['flash_success'] = "Booking berhasil!";
            header('Location: ' . BASE_URL . '/booking/detail/' . $code);
            exit;

        } catch (Exception $e) {
            $this->bookingModel->db->rollBack();
            $this->redirectBack($roomId, $e->getMessage());
        }
        // --- TRANSACTION END ---
    }

    public function uploadPayment() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

        // Constants check
        $maxSize = defined('MAX_FILE_SIZE') ? MAX_FILE_SIZE : 2 * 1024 * 1024; // Default 2MB

        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] != 0) {
            $_SESSION['flash_error'] = "File error atau kosong.";
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }

        if ($_FILES['payment_proof']['size'] > $maxSize) {
            $_SESSION['flash_error'] = "Ukuran file terlalu besar (Max 2MB).";
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['payment_proof']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes)) {
            $_SESSION['flash_error'] = "Format file tidak valid.";
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $target = "uploads/payments/" . $fileName;

        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target)) {
            $this->bookingModel->submitPayment($_POST['booking_id'], $fileName, strip_tags($_POST['bank_name']), strip_tags($_POST['account_name']));
            $_SESSION['flash_success'] = "Bukti terupload.";
        }

        // Use specific redirect, not REFERER
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    private function redirectBack($roomId, $msg) {
        $_SESSION['flash_error'] = $msg;
        header("Location: " . BASE_URL . "/booking/create?room_id=$roomId");
        exit;
    }
    
    // Fix missing method error in original review
    public function detail($code) {
         $booking = $this->bookingModel->findByCode($code);
         if (!$booking || ($booking['customer_id'] !== $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin')) {
             header('Location: ' . BASE_URL . '/dashboard'); exit;
         }
         $this->view('booking/detail', ['booking' => $booking]);
    }
}