<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room; // Penting untuk restore slot

class AdminPaymentController extends Controller {
    private $bookingModel;
    private $paymentModel;
    private $roomModel;

    public function __construct() {
        // Cek Login Admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $this->bookingModel = new Booking();
        $this->paymentModel = new Payment();
        $this->roomModel = new Room(); // Model room diperlukan
    }

    public function index() {
        $data = [
            'title' => 'Verifikasi Pembayaran',
            'payments' => $this->paymentModel->getPendingPayments()
        ];
        $this->view('admin/payments/index', $data);
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // CSRF Protection (Sederhana)
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Error");
        }

        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';
        $adminNote = strip_tags(trim($_POST['admin_note'] ?? ''));

        if (!$paymentId || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['flash_error'] = "Data tidak valid.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // Ambil data payment dan booking terkait
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            $_SESSION['flash_error'] = "Data pembayaran tidak ditemukan.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $booking = $this->bookingModel->find($payment['booking_id']);
        if (!$booking) {
            $_SESSION['flash_error'] = "Data booking terkait tidak ditemukan.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // --- LOGIC APPROVE / REJECT ---
        try {
            $db = new \App\Core\Database();
            $db->beginTransaction();

            if ($action === 'approve') {
                // 1. Update Payment Status
                $q1 = "UPDATE payments SET payment_status = 'verified', admin_note = :note, verified_at = NOW(), verified_by = :admin WHERE id = :pid";
                $db->query($q1);
                $db->bind(':note', $adminNote ?: 'Payment Accepted');
                $db->bind(':admin', $_SESSION['user_id']);
                $db->bind(':pid', $paymentId);
                $db->execute();

                // 2. Update Booking Status
                $q2 = "UPDATE bookings SET booking_status = 'confirmed', updated_at = NOW() WHERE id = :bid";
                $db->query($q2);
                $db->bind(':bid', $payment['booking_id']);
                $db->execute();

                $msg = "Pembayaran berhasil diverifikasi. Booking dikonfirmasi.";

            } elseif ($action === 'reject') {
                // 1. Update Payment Status
                $q1 = "UPDATE payments SET payment_status = 'failed', admin_note = :note, verified_at = NOW(), verified_by = :admin WHERE id = :pid";
                $db->query($q1);
                $db->bind(':note', $adminNote ?: 'Payment Rejected');
                $db->bind(':admin', $_SESSION['user_id']);
                $db->bind(':pid', $paymentId);
                $db->execute();

                // 2. Update Booking Status -> Cancelled
                $q2 = "UPDATE bookings SET booking_status = 'cancelled', updated_at = NOW() WHERE id = :bid";
                $db->query($q2);
                $db->bind(':bid', $payment['booking_id']);
                $db->execute();

                // 3. RESTORE ROOM SLOT (PENTING!)
                // Mengembalikan slot kamar karena booking batal
                $q3 = "UPDATE rooms SET available_slots = available_slots + :num WHERE id = :rid";
                $db->query($q3);
                $db->bind(':num', $booking['num_rooms']);
                $db->bind(':rid', $booking['room_id']);
                $db->execute();

                $msg = "Pembayaran ditolak. Slot kamar telah dikembalikan.";
            }

            $db->commit();
            $_SESSION['flash_success'] = $msg;

        } catch (\PDOException $e) {
            $db->rollBack();
            error_log("Payment Process Error: " . $e->getMessage());
            $_SESSION['flash_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }
}