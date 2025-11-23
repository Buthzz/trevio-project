<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Base Admin Controller
 * Provides shared functionality for all admin controllers
 */
abstract class BaseAdminController extends Controller {
    
    /**
     * Constructor - Enforce admin authentication
     */
    public function __construct() {
        $this->requireAdminLogin();
    }
    
    /**
     * Require admin authentication
     * @throws void Redirects to login if not authenticated as admin
     */
    protected function requireAdminLogin(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash_error'] = "Access denied. Admin privileges required.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }
    
    /**
     * Validate CSRF token with expiration check
     * @throws void Dies with 403 if validation fails
     */
    protected function validateCsrf(): void {
        // Check token exists
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            http_response_code(403);
            die("CSRF token missing. Please refresh the page and try again.");
        }
        
        // Validate token match using timing-safe comparison
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            http_response_code(403);
            die("CSRF Validation Failed. Please refresh the page and try again.");
        }
        
        // Check token expiration
        if (isset($_SESSION['csrf_token_time'])) {
            $tokenAge = time() - $_SESSION['csrf_token_time'];
            $expiry = defined('CSRF_TOKEN_EXPIRE') ? CSRF_TOKEN_EXPIRE : 3600;
            
            if ($tokenAge > $expiry) {
                http_response_code(403);
                die("CSRF token expired. Please refresh the page and try again.");
            }
        }
        
        // Regenerate token to prevent replay attacks
        $this->generateCsrfToken();
    }
    
    /**
     * Generate new CSRF token
     */
    protected function generateCsrfToken(): void {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    /**
     * Ensure CSRF token exists
     */
    protected function ensureCsrfToken(): void {
        if (empty($_SESSION['csrf_token'])) {
            $this->generateCsrfToken();
        }
    }
    
    /**
     * Sanitize GET parameter
     * @param string $key Parameter name
     * @param string|null $default Default value if not set
     * @return string|null
     */
    protected function sanitizeGet(string $key, ?string $default = null): ?string {
        if (!isset($_GET[$key])) {
            return $default;
        }
        return htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize POST parameter
     * @param string $key Parameter name
     * @param string|null $default Default value if not set
     * @return string|null
     */
    protected function sanitizePost(string $key, ?string $default = null): ?string {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return htmlspecialchars(strip_tags($_POST[$key]), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Redirect with flash message
     * @param string $url Relative URL
     * @param string $message Flash message
     * @param string $type Message type (success, error, warning, info)
     */
    protected function redirectWithMessage(string $url, string $message, string $type = 'info'): void {
        $_SESSION["flash_{$type}"] = $message;
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    /**
     * Validate required POST fields
     * @param array $fields Required field names
     * @return bool
     */
    protected function validateRequired(array $fields): bool {
        foreach ($fields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                return false;
            }
        }
        return true;
    }
}
