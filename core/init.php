<?php

// Autoloader untuk memuat class sesuai namespace
spl_autoload_register(function ($class) {
    // Konversi Namespace ke Path File
    // App\Controllers\AuthController -> ../app/controllers/AuthController.php
    // App\Core\App -> ../app/core/App.php
    
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    
    // Ganti backslash (\) dengan directory separator (/)
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Helper constants
// Sesuaikan dengan URL lokal Anda jika berbeda
define('BASE_URL', 'http://localhost'); 

// Load file konfigurasi jika ada (opsional)
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}