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

    /**
     * Display list of all refunds
     */
    public function index() {
        $status = $this->sanitizeGet('status', 'pending');
        
        $data = [
            'title' => 'Manage Refunds',
            'refunds' => $this->refundModel->getAll($status),
            'pending_count' => $this->refundModel->countPending(),
            'current_status' => $status,
            'user' => $_SESSION
        ];
        
        $this->view('admin/refunds/index', $data);
    }

    /**
     * Display refund processing page
     */
    public function process($id) {
        $refund = $this->refundModel->find($id);
        
        if (!$refund) {
            $_SESSION['flash_error'] = "Refund not found.";
            header('Location: ' . BASE_URL . '/admin/refunds');
            exit;
        }

        // Generate CSRF token
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
            $_SESSION['flash_success'] = "Refund approved. Please proceed with bank transfer and upload receipt.";
            
            // Redirect to upload receipt page
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
        } else {
            $_SESSION['flash_error'] = "Failed to approve refund.";
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
            $_SESSION['flash_error'] = "Please provide rejection reason.";
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
            exit;
        }

        if ($this->refundModel->reject($refundId, $_SESSION['user_id'], $reason)) {
            $_SESSION['flash_success'] = "Refund rejected.";
            
            // TODO: Send notification to customer
            
        } else {
            $_SESSION['flash_error'] = "Failed to reject refund.";
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
            $_SESSION['flash_error'] = "Please upload refund receipt.";
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
            exit;
        }

        // Upload receipt
        $receiptFile = $this->uploadReceipt($_FILES['refund_receipt']);
        
        if (!$receiptFile) {
            $_SESSION['flash_error'] = "Failed to upload receipt. Please check file format and size.";
            header('Location: ' . BASE_URL . '/admin/refunds/process/' . $refundId);
            exit;
        }

        // Complete refund (atomic transaction - restore slots)
        if ($this->refundModel->complete($refundId, $receiptFile, $_SESSION['user_id'])) {
            $_SESSION['flash_success'] = "Refund completed successfully. Room slots restored and booking marked as refunded.";
            
            // TODO: Send completion notification to customer
            
        } else {
            $_SESSION['flash_error'] = "Failed to complete refund.";
        }

        header('Location: ' . BASE_URL . '/admin/refunds');
        exit;
    }

    // --- Helpers ---

    private function uploadReceipt($file) {
        $targetDir = "../public/uploads/refunds/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Sanitize filename
        $originalName = basename($file["name"]);
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $fileName = time() . '_refund_' . bin2hex(random_bytes(4)) . '_' . $originalName;
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allow specific file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'pdf');
        
        if (!in_array($fileType, $allowTypes)) {
            return false;
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Validate is image or PDF
        if (in_array($fileType, ['jpg', 'png', 'jpeg'])) {
            $check = getimagesize($file["tmp_name"]);
            if ($check === false) {
                return false;
            }
        }

        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return '/uploads/refunds/' . $fileName;
        }
        
        return false;
    }
}
