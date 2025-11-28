<?php

namespace App\Controllers;

use App\Models\Payment;

class AdminPaymentController extends BaseAdminController {
    private $paymentModel;

    public function __construct() {
        parent::__construct();
        $this->paymentModel = new Payment();
    }

    // Helper aman untuk GET request
    private function getQuery($key, $default = null) {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    public function index() {
        // Default status 'pending' agar sesuai dengan tab "Menunggu Verifikasi"
        $status = $this->getQuery('status', 'Pending_verification');

        $data = [
            'title' => 'Manage Payments',
            'payments' => $this->paymentModel->getAll($status),
            'pending_count' => $this->paymentModel->countPending(),
            'current_status' => $status,
            'csrf_token' => $_SESSION['csrf_token'] ?? '', // Pastikan token ada
            'user' => $_SESSION
        ];

        $this->view('admin/payments/index', $data);
    }

    /**
     * Verifikasi Pembayaran (Halaman Detail)
     */
    public function verify($id) {
        $payment = $this->paymentModel->find($id);

        if (!$payment) {
            $_SESSION['flash_error'] = "Data pembayaran tidak ditemukan.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }
        
        // Generate token jika belum ada
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
     * Proses Konfirmasi (Terima)
     */
    public function confirm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();
        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);

        if ($this->paymentModel->confirm($paymentId, $_SESSION['user_id'])) {
            $_SESSION['flash_success'] = "Pembayaran berhasil diverifikasi. Booking dikonfirmasi.";
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