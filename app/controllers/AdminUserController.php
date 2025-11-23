<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AdminUserController extends Controller {
    private $userModel;

    public function __construct() {
        // Ensure only admin can access
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $this->userModel = new User();
    }

    /**
     * Display list of all users
     */
    public function index() {
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        
        $data = [
            'title' => 'Manage Users',
            'users' => $this->userModel->getAll($role, $status),
            'stats' => [
                'total' => $this->userModel->countAll(),
                'customers' => $this->userModel->countByRole('customer'),
                'owners' => $this->userModel->countByRole('owner'),
                'admins' => $this->userModel->countByRole('admin'),
                'active' => $this->userModel->countByStatus(1),
                'inactive' => $this->userModel->countByStatus(0)
            ],
            'current_role' => $role,
            'current_status' => $status,
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
            
            // TODO: Send activation notification
            
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
            
            // TODO: Send deactivation notification
            
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
        if ($user['role'] === 'owner') {
            // TODO: Check for active hotels/bookings
            $_SESSION['flash_error'] = "Cannot delete owner with active hotels. Please transfer or delete hotels first.";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
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

    // --- Helpers ---

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
        // Regenerate token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
