<?php
// trevio-project/config/database.php

/**
 * Konfigurasi Koneksi MySQL
 * Menggunakan variabel dari lingkungan (misalnya dari .env)
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_DATABASE', getenv('DB_DATABASE') ?: 'trevio');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');