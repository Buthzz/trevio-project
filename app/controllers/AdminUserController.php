<?php

namespace App\Controllers;

use App\Models\User;

class AdminUserController extends BaseAdminController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Helper sederhana untuk mengambil input GET dengan aman
     * (Pengganti sanitizeGet jika tidak ada di BaseAdminController)
     */
    private function getQuery($key, $default = null) {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    /**
     * Display list of all users
     */
    public function index() {
        // Ambil input filter
        $roleInput = $this->getQuery('role', 'all');
        $statusInput = $this->getQuery('status', null);
        
        // Logika Filter: Model mengharapkan NULL jika ingin mengambil semua data, 
        // bukan string 'all'.
        $roleFilter = ($roleInput === 'all') ? null : $roleInput;

        $data = [
            'title' => 'Manage Users',
            // Pass filter yang sudah dikonversi ke Model
            'users' => $this->userModel->getAll($roleFilter, $statusInput),
            'stats' => [
                'total' => $this->userModel->countAll(),
                'customers' => $this->userModel->countByRole('customer'),
                'owners' => $this->userModel->countByRole('owner'),
                'admins' => $this->userModel->countByRole('admin'),
                'active' => $this->userModel->countByStatus(1),
                'inactive' => $this->userModel->countByStatus(0)
            ],
            // Pass input asli ke view untuk menjaga state dropdown
            'filters' => [
                'role' => $roleInput,
                'status' => $statusInput,
                'search' => $this->getQuery('search', '')
            ],
            'current_role' => $roleInput,
            'current_status' => $statusInput,
            'csrf_token' => $_SESSION['csrf_token'] ?? '', // Pastikan token tersedia
            'user' => $_SESSION
        ];
        
        $this->view('admin/users/index', $data);
    }

    /**
     * View user details
     */
    public function viewUser($id) {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $_SESSION['flash_error'] = "User not found.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $data = [
            'title' => 'User Details',
            'user_data' => $user,
            'user' => $_SESSION
        ];
        
        $this->view('admin/users/view', $data);
    }

    /**
     * Activate user account
     */
    public function activate($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->validateCsrf();

        $userId = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$userId) {
            $_SESSION['flash_error'] = "Invalid user ID.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Prevent self-deactivation
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "You cannot modify your own account status.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($this->userModel->updateStatus($userId, 1)) {
            $_SESSION['flash_success'] = "User activated successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to activate user.";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    /**
     * Deactivate/ban user account
     */
    public function deactivate($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->validateCsrf();

        $userId = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$userId) {
            $_SESSION['flash_error'] = "Invalid user ID.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Prevent self-deactivation
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "You cannot deactivate your own account.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($this->userModel->updateStatus($userId, 0)) {
            $_SESSION['flash_success'] = "User deactivated successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to deactivate user.";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    /**
     * Delete user account (permanent)
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->validateCsrf();

        $userId = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$userId) {
            $_SESSION['flash_error'] = "Invalid user ID.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Prevent self-deletion
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "You cannot delete your own account.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Check if user has active bookings or hotels
        $user = $this->userModel->find($userId);
        if ($user && $user['role'] === 'owner') {
            // Logic tambahan bisa diletakkan di sini untuk cek properti
            // Untuk saat ini kita izinkan delete dengan peringatan saja di front end
        }

        if ($this->userModel->delete($userId)) {
            $_SESSION['flash_success'] = "User deleted successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to delete user.";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    /**
     * Change user role
     */
    public function changeRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->validateCsrf();

        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $newRole = $_POST['new_role'] ?? '';
        
        if (!$userId || !in_array($newRole, ['customer', 'owner', 'admin'])) {
            $_SESSION['flash_error'] = "Invalid parameters.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Prevent changing own role
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "You cannot change your own role.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($this->userModel->updateRole($userId, $newRole)) {
            $_SESSION['flash_success'] = "User role updated successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to update user role.";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }
}