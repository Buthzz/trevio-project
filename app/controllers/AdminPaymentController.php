<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Services\NotificationService; // Import Service Baru

class AdminPaymentController extends BaseAdminController {
    private $paymentModel;
    private $notificationService;

    public function __construct() {
        parent::__construct();
        $this->paymentModel = new Payment();
        // Inisialisasi Service
        $this->notificationService = new NotificationService();
    }

    // Helper aman untuk GET request
    private function getQuery($key, $default = null) {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

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
            'csrf_token' => $_SESSION['csrf_token'],
            'user' => $_SESSION
        ];

        $this->view('admin/payments/verify', $data);
    }

    /**
     * Proses Konfirmasi (Terima) + KIRIM NOTIFIKASI
     */
    public function confirm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();
        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);

        // 1. Ambil data lengkap untuk notifikasi SEBELUM update status (opsional) atau SESUDAHNYA
        // Kita ambil sesudah konfirmasi berhasil agar data valid
        if ($this->paymentModel->confirm($paymentId, $_SESSION['user_id'])) {
            
            // 2. Ambil detail lengkap booking untuk notifikasi
            $paymentData = $this->paymentModel->find($paymentId);
            
            if ($paymentData) {
                // 3. Panggil Service untuk kirim WA & Email Invoice
                try {
                    $this->notificationService->sendBookingConfirmation($paymentData);
                    $_SESSION['flash_success'] = "Pembayaran diverifikasi. Notifikasi WA & Email telah dikirim.";
                } catch (\Exception $e) {
                    // Jika notifikasi gagal, booking tetap confirm, tapi beri info error
                    $_SESSION['flash_success'] = "Pembayaran diverifikasi, tapi gagal mengirim notifikasi: " . $e->getMessage();
                }
            } else {
                $_SESSION['flash_success'] = "Pembayaran berhasil diverifikasi.";
            }

        } else {
            $_SESSION['flash_error'] = "Gagal memverifikasi pembayaran.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    /**
     * Proses Reject (Tolak)
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
}