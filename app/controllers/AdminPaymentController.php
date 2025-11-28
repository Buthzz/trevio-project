<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;

class AdminPaymentController extends Controller {
    private $bookingModel;
    private $paymentModel;
    private $roomModel;

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $this->bookingModel = new Booking();
        $this->paymentModel = new Payment();
        $this->roomModel = new Room();
        $this->ensureCsrfToken();
    }

    // HALAMAN LIST
    public function index() {
        $statusFilter = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

        $data = [
            'title' => 'Verifikasi Pembayaran',
            'payments' => $this->paymentModel->getAll($statusFilter ?: null),
            'stats' => [
                'revenue'  => $this->paymentModel->getTotalRevenue(),
                'verified' => $this->paymentModel->countByStatus('verified'),
                'pending'  => $this->paymentModel->countByStatus('pending_verification')
            ],
            'filters' => ['status' => $statusFilter],
            'user' => $_SESSION
        ];

        $this->view('admin/payments/index', $data);
    }

    // HALAMAN DETAIL / VERIFY (INI YANG ANDA CARI)
    public function verify($id) {
        $paymentId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$paymentId) {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $payment = $this->paymentModel->find($paymentId);
        
        if (!$payment) {
            $_SESSION['flash_error'] = "Pembayaran tidak ditemukan.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $data = [
            'title' => 'Detail Verifikasi',
            'payment' => $payment,
            'csrf_token' => $_SESSION['csrf_token']
        ];

        $this->view('admin/payments/verify', $data);
    }

    // PROSES EKSEKUSI (POST)
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = "Token keamanan tidak valid.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';
        $adminNote = strip_tags(trim($_POST['admin_note'] ?? ''));

        if (!$paymentId || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['flash_error'] = "Aksi tidak valid.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // Logic Approve/Reject menggunakan Model yang sudah Anda update
        if ($action === 'approve') {
            $success = $this->paymentModel->verify($paymentId, $_SESSION['user_id'], $adminNote);
            $msg = "Pembayaran diterima dan Booking dikonfirmasi.";
        } else {
            $success = $this->paymentModel->reject($paymentId, $_SESSION['user_id'], $adminNote);
            $msg = "Pembayaran ditolak. User diminta upload ulang.";
        }

        if ($success) {
            $_SESSION['flash_success'] = $msg;
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan saat memproses data.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    private function ensureCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}