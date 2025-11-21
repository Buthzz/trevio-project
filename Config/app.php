<?php
// trevio-project/config/app.php

/**
 * Konfigurasi Umum Aplikasi Trevio
 */

// URL Dasar Aplikasi (sesuaikan dengan APP_URL di .env)
define('BASE_URL', getenv('APP_URL') ?: 'http://localhost:8000');

// Pengaturan Environment
define('APP_NAME', getenv('APP_NAME') ?: 'Trevio');
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // production, staging, development
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ?: true); // true saat development

// Role Pengguna
define('ROLE_CUSTOMER', 'customer');
define('ROLE_OWNER', 'owner');
define('ROLE_ADMIN', 'admin');

// Status Booking
define('STATUS_PENDING_PAYMENT', 'pending_payment');
define('STATUS_PENDING_VERIFICATION', 'pending_verification');
define('STATUS_CONFIRMED', 'confirmed');
define('STATUS_CHECKED_IN', 'checked_in');
define('STATUS_COMPLETED', 'completed');
define('STATUS_CANCELLED', 'cancelled');
define('STATUS_REFUNDED', 'refunded');

// Pengaturan lain
define('LOG_PATH', __DIR__ . '/../logs/app.log');