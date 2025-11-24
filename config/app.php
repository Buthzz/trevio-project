<?php

/**
 * Application Configuration
 * Core settings for the Trevio application
 */

// Prevent duplicate loading
if (defined('TREVIO_CONFIG_LOADED')) {
    return;
}
define('TREVIO_CONFIG_LOADED', true);

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue; // Skip lines without '='
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        $value = trim($value, '"\'');
        
        if (!empty($name)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Application Settings
define('APP_NAME', getenv('APP_NAME') ?: 'Trevio');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ?: true);
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?: 'Asia/Jakarta');

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Base URL Configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);

// Clean up script directory path
$scriptDir = str_replace('\\', '/', $scriptDir);
$scriptDir = rtrim($scriptDir, '/');

define('BASE_URL', $protocol . '://' . $host . $scriptDir);

// Directory Paths
define('ROOT_PATH', dirname(__DIR__)); // Only go up 1 level to project root
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// File Upload Settings
define('UPLOAD_MAX_SIZE', (int)(getenv('UPLOAD_MAX_SIZE') ?: 5242880)); // 5MB default
define('UPLOAD_ALLOWED_TYPES', getenv('UPLOAD_ALLOWED_TYPES') ?: 'jpg,jpeg,png,pdf');

// Upload directories
define('UPLOAD_HOTEL_DIR', UPLOAD_PATH . '/hotels');
define('UPLOAD_ROOM_DIR', UPLOAD_PATH . '/rooms');
define('UPLOAD_PAYMENT_DIR', UPLOAD_PATH . '/payments');
define('UPLOAD_REFUND_DIR', UPLOAD_PATH . '/refunds');
define('UPLOAD_REVIEW_DIR', UPLOAD_PATH . '/reviews');

// Create upload directories if they don't exist
$uploadDirs = [
    UPLOAD_PATH,
    UPLOAD_HOTEL_DIR,
    UPLOAD_ROOM_DIR,
    UPLOAD_PAYMENT_DIR,
    UPLOAD_REFUND_DIR,
    UPLOAD_REVIEW_DIR,
    LOG_PATH
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        // Suppress warnings if parent directory not writable
        @mkdir($dir, 0755, true);
    }
}

// Session Configuration
define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: 120)); // 2 hours
define('SESSION_COOKIE_NAME', getenv('SESSION_COOKIE_NAME') ?: 'trevio_session');
define('SESSION_SECURE', filter_var(getenv('SESSION_SECURE'), FILTER_VALIDATE_BOOLEAN));
define('SESSION_HTTP_ONLY', filter_var(getenv('SESSION_HTTP_ONLY'), FILTER_VALIDATE_BOOLEAN, ['options' => ['default' => true]]));

// Security Settings
define('CSRF_TOKEN_NAME', getenv('CSRF_TOKEN_NAME') ?: '_csrf_token');
define('CSRF_TOKEN_EXPIRE', (int)(getenv('CSRF_TOKEN_EXPIRE') ?: 3600));
define('PASSWORD_MIN_LENGTH', (int)(getenv('PASSWORD_MIN_LENGTH') ?: 8));

// Logging
define('LOG_LEVEL', getenv('LOG_LEVEL') ?: 'debug');
define('LOG_QUERIES', filter_var(getenv('LOG_QUERIES'), FILTER_VALIDATE_BOOLEAN));
define('SHOW_ERRORS', filter_var(getenv('SHOW_ERRORS'), FILTER_VALIDATE_BOOLEAN, ['options' => ['default' => APP_DEBUG]]));

// Error Reporting
if (APP_DEBUG && SHOW_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// Set error log file
ini_set('error_log', LOG_PATH . '/php_errors.log');

// Mail Configuration
define('MAIL_ENABLED', filter_var(getenv('MAIL_ENABLED'), FILTER_VALIDATE_BOOLEAN));
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_PORT', (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?: 'noreply@trevio.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

// External APIs
define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: '');

// Rate Limiting
define('RATE_LIMIT_ENABLED', filter_var(getenv('RATE_LIMIT_ENABLED'), FILTER_VALIDATE_BOOLEAN));
define('RATE_LIMIT_MAX_REQUESTS', (int)(getenv('RATE_LIMIT_MAX_REQUESTS') ?: 100));

// Application-specific settings
define('BOOKING_MAX_DURATION_DAYS', 30);
define('BOOKING_MAX_ROOMS', 10);
define('REVIEW_MIN_RATING', 1);
define('REVIEW_MAX_RATING', 5);

// Date formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd M Y');
define('DISPLAY_DATETIME_FORMAT', 'd M Y H:i');

// Currency settings
define('CURRENCY_CODE', 'IDR');
define('CURRENCY_SYMBOL', 'Rp');

// Tax and service charge rates
define('TAX_RATE', 0.10); // 10%
define('SERVICE_CHARGE_RATE', 0.05); // 5%

// Pagination
define('ITEMS_PER_PAGE', 20);

// Security headers (to be sent in response)
$securityHeaders = [
    'X-Frame-Options' => 'SAMEORIGIN',
    'X-Content-Type-Options' => 'nosniff',
    'X-XSS-Protection' => '1; mode=block',
    'Referrer-Policy' => 'strict-origin-when-cross-origin'
];

// Only set security headers if not CLI
if (php_sapi_name() !== 'cli') {
    foreach ($securityHeaders as $header => $value) {
        header("$header: $value");
    }
    
    // HTTPS redirect in production
    if (APP_ENV === 'production' && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Helper function to get config value
function config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE && php_sapi_name() !== 'cli') {
    ini_set('session.cookie_httponly', SESSION_HTTP_ONLY ? 1 : 0);
    ini_set('session.cookie_secure', SESSION_SECURE ? 1 : 0);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME * 60);
    
    session_name(SESSION_COOKIE_NAME);
    session_start();
    
    // Session hijacking prevention
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    } elseif ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
        session_unset();
        session_destroy();
        session_start();
    }
    
    // Session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > (SESSION_LIFETIME * 60)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}
