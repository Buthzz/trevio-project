<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Booking;

class AdminPaymentController extends BaseAdminController {
    private $paymentModel;
    private $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
    }

    /**
     * Display list of all payments
     */
    public function index() {
        $status = $this->sanitizeGet('status', 'pending');
        
        $data = [
            'title' => 'Manage Payments',
            'payments' => $this->paymentModel->getAll($status),
            'pending_count' => $this->paymentModel->countPending(),
            'current_status' => $status,
            'user' => $_SESSION
        ];
        
        $this->view('admin/payments/index', $data);
    }

    /**
     * Display payment verification page
     */
    public function verify($id) {
        $payment = $this->paymentModel->find($id);
        
        if (!$payment) {
            $_SESSION['flash_error'] = "Payment not found.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // Generate CSRF token
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
     * Process payment verification (approve)
     */
    public function processVerify() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();

        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $notes = strip_tags($_POST['notes'] ?? '');
        
        if (!$paymentId) {
            $_SESSION['flash_error'] = "Invalid payment ID.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        // Verify payment (atomic transaction in model)
        if ($this->paymentModel->verify($paymentId, $_SESSION['user_id'], $notes)) {
            $_SESSION['flash_success'] = "Payment verified successfully. Booking confirmed and room slots updated.";
            
            // TODO: Send notification to customer (email/whatsapp)
            
        } else {
            $_SESSION['flash_error'] = "Failed to verify payment. Please check room availability.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    /**
     * Process payment rejection
     */
    public function processReject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();

        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $reason = strip_tags($_POST['rejection_reason'] ?? '');
        
        if (!$paymentId) {
            $_SESSION['flash_error'] = "Invalid payment ID.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        if (empty($reason)) {
            $_SESSION['flash_error'] = "Please provide rejection reason.";
            header('Location: ' . BASE_URL . '/admin/payments/verify/' . $paymentId);
            exit;
        }

        // Reject payment (atomic transaction in model)
        if ($this->paymentModel->reject($paymentId, $_SESSION['user_id'], $reason)) {
            $_SESSION['flash_success'] = "Payment rejected. Customer can re-upload payment proof.";
            
            // TODO: Send rejection notification to customer
            
        } else {
            $_SESSION['flash_error'] = "Failed to reject payment.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

    /**
     * Quick approve payment (from list page)
     */
    public function quickApprove($id) {
        // Security: Use POST for state-changing actions
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        $this->validateCsrf();

        $paymentId = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$paymentId) {
            $_SESSION['flash_error'] = "Invalid payment ID.";
            header('Location: ' . BASE_URL . '/admin/payments');
            exit;
        }

        if ($this->paymentModel->verify($paymentId, $_SESSION['user_id'], 'Quick approval')) {
            $_SESSION['flash_success'] = "Payment verified successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to verify payment.";
        }

        header('Location: ' . BASE_URL . '/admin/payments');
        exit;
    }

}
