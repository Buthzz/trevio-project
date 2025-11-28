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

    public function index() {
        // Ambil filter dari URL
        $statusFilter = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
        $selectedId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Ambil Statistik
        $stats = [
            'revenue'  => $this->paymentModel->getTotalRevenue(),
            'verified' => $this->paymentModel->countByStatus('verified'),
            'pending'  => $this->paymentModel->countByStatus('pending_verification')
        ];

        // Ambil List Pembayaran
        // (Sederhana: menggunakan getAll, idealnya ditambahkan fitur search di model)
        $payments = $this->paymentModel->getAll($statusFilter ?: null);

        // Ambil Detail Pembayaran Terpilih (jika ada ID di URL)
        $selectedPayment = null;
        if ($selectedId) {
            $selectedPayment = $this->paymentModel->find($selectedId);
        }

        $data = [
            'title' => 'Verifikasi Pembayaran',
            'payments' => $payments,
            'selectedPayment' => $selectedPayment,
            'stats' => $stats,
            'filters' => [
                'status' => $statusFilter,
                'search' => $search
            ],
            'user' => $_SESSION,
            'csrf_token' => $_SESSION['csrf_token']
        ];

        $this->view('admin/payments/index', $data);
    }

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

        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            $_SESSION['flash_error'] = "Data pembayaran tidak ditemukan.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // Logic Approve/Reject (Sama seperti sebelumnya, disesuaikan)
        try {
            $db = new \App\Core\Database();
            $db->beginTransaction();

            if ($action === 'approve') {
                // Update Payment
                $q1 = "UPDATE payments SET payment_status = 'verified', admin_note = :note, verified_at = NOW(), verified_by = :admin WHERE id = :pid";
                $db->query($q1);
                $db->bind(':note', $adminNote ?: 'Pembayaran diterima.');
                $db->bind(':admin', $_SESSION['user_id']);
                $db->bind(':pid', $paymentId);
                $db->execute();

                // Update Booking
                $q2 = "UPDATE bookings SET booking_status = 'confirmed', updated_at = NOW() WHERE id = :bid";
                $db->query($q2);
                $db->bind(':bid', $payment['booking_id']);
                $db->execute();

                $msg = "Pembayaran berhasil diverifikasi.";

            } else { // Reject
                // Update Payment
                $q1 = "UPDATE payments SET payment_status = 'failed', admin_note = :note, verified_at = NOW(), verified_by = :admin WHERE id = :pid";
                $db->query($q1);
                $db->bind(':note', $adminNote ?: 'Pembayaran ditolak.');
                $db->bind(':admin', $_SESSION['user_id']);
                $db->bind(':pid', $paymentId);
                $db->execute();

                // Update Booking -> Cancelled / Pending Payment (Tergantung kebijakan)
                // Di sini kita ubah jadi pending_payment agar user bisa upload ulang, atau cancelled jika mau strict.
                // Kita gunakan logika: Reject = Minta upload ulang (pending_payment)
                $q2 = "UPDATE bookings SET booking_status = 'pending_payment', updated_at = NOW() WHERE id = :bid";
                $db->query($q2);
                $db->bind(':bid', $payment['booking_id']);
                $db->execute();
                
                $msg = "Pembayaran ditolak. Status booking dikembalikan ke pending.";
            }

            $db->commit();
            $_SESSION['flash_success'] = $msg;

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['flash_error'] = "Error: " . $e->getMessage();
        }

        // Redirect kembali ke list, bukan ke detail ID yang barusan diproses
        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    private function ensureCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}