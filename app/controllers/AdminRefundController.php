<?php

namespace App\Controllers;

use App\Models\Refund;
use App\Models\Booking;

class AdminRefundController extends BaseAdminController {
    private $refundModel;
    private $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->refundModel = new Refund();
        $this->bookingModel = new Booking();
    }

    // Helper pengganti sanitizeGet agar lebih robust
    private function getQuery($key, $default = null) {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    /**
     * Display list of all refunds
     */
    public function index() {
        $status = $this->getQuery('status', 'requested'); // Default ke 'requested' (pending)
        
        $data = [
            'title' => 'Manage Refunds',
            'refunds' => $this->refundModel->getAll($status),
            'pending_count' => $this->refundModel->countPending(),
            'current_status' => $status,
            'user' => $_SESSION,
            'csrf_token' => $_SESSION['csrf_token'] ?? ''
        ];
        
        $this->view('admin/refunds/index', $data);
    }

    /**
     * Display refund processing page
     */
    public function process($id) {
        $refund = $this->refundModel->find($id);
        
        if (!$refund) {
            $_SESSION['flash_error'] = "Refund request not found.";
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        // Generate CSRF token jika belum ada
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $data = [
            'title' => 'Process Refund',
            'refund' => $refund,
            'csrf_token' => $_SESSION['csrf_token'],
            'user' => $_SESSION
        ];
        
        $this->view('admin/refunds/process', $data);
    }

    /**
     * Approve refund request
     */
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        $this->validateCsrf();

        $refundId = filter_input(INPUT_POST, 'refund_id', FILTER_VALIDATE_INT);
        $notes = strip_tags($_POST['admin_notes'] ?? '');
        
        if (!$refundId) {
            $_SESSION['flash_error'] = "Invalid refund ID.";
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        if ($this->refundModel->approve($refundId, $_SESSION['user_id'], $notes)) {
            $_SESSION['flash_success'] = "Refund disetujui. Silakan upload bukti transfer.";
            // Redirect kembali ke halaman process untuk upload bukti
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
        } else {
            $_SESSION['flash_error'] = "Gagal menyetujui refund.";
            header('Location: ' . BASE_URL . '/admin/refunds');
        }
        exit;
    }

    /**
     * Reject refund request
     */
    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        $this->validateCsrf();

        $refundId = filter_input(INPUT_POST, 'refund_id', FILTER_VALIDATE_INT);
        $reason = strip_tags($_POST['rejection_reason'] ?? '');
        
        if (!$refundId) {
            $_SESSION['flash_error'] = "Invalid refund ID.";
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        if (empty($reason)) {
            $_SESSION['flash_error'] = "Alasan penolakan wajib diisi.";
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
            exit;
        }

        if ($this->refundModel->reject($refundId, $_SESSION['user_id'], $reason)) {
            $_SESSION['flash_success'] = "Refund ditolak.";
        } else {
            $_SESSION['flash_error'] = "Gagal menolak refund.";
        }

        header('Location: ' . BASE_URL . '/admin/refunds');
        exit;
    }

    /**
     * Complete refund - upload transfer receipt
     */
    public function complete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        $this->validateCsrf();

        $refundId = filter_input(INPUT_POST, 'refund_id', FILTER_VALIDATE_INT);
        
        if (!$refundId) {
            $_SESSION['flash_error'] = "Invalid refund ID.";
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        // Validate file upload
        if (!isset($_FILES['refund_receipt']) || $_FILES['refund_receipt']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Silakan upload bukti transfer.";
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
            exit;
        }

        // Upload receipt
        $receiptFile = $this->uploadReceipt($_FILES['refund_receipt']);
        
        if (!$receiptFile) {
            $_SESSION['flash_error'] = "Gagal upload bukti. Pastikan format JPG/PNG/PDF max 5MB.";
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
            exit;
        }

        // Complete refund (atomic transaction - restore slots)
        if ($this->refundModel->complete($refundId, $receiptFile, $_SESSION['user_id'])) {
            $_SESSION['flash_success'] = "Refund selesai. Slot kamar telah dikembalikan.";
        } else {
            $_SESSION['flash_error'] = "Gagal memproses penyelesaian refund.";
        }

        header('Location: ' . BASE_URL . '/admin/refunds');
        exit;
    }

    // --- Helpers ---

    private function uploadReceipt($file) {
        // Gunakan path absolute yang benar berdasarkan root project
        $targetDir = __DIR__ . "/../../public/uploads/refunds/";
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Sanitize filename
        $originalName = basename($file["name"]);
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $fileName = time() . '_refund_' . bin2hex(random_bytes(4)) . '_' . $originalName;
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = array('jpg', 'png', 'jpeg', 'pdf');
        
        if (!in_array($fileType, $allowTypes)) {
            return false;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            // Return path relative untuk disimpan di DB
            return 'uploads/refunds/' . $fileName;
        }
        
        return false;
    }
}