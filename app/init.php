<?php

// Load Environment Variables from .env file
if (file_exists(__DIR__ . '/../helpers/env_loader.php')) {
    require_once __DIR__ . '/../helpers/env_loader.php';
}

// Load Composer Autoloader jika ada
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Custom Autoloader
spl_autoload_register(function ($class) {
    // Prefix Namespace
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/'; // Menunjuk ke folder app/

    // Cek apakah class menggunakan prefix App\
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Ambil nama relative class (contoh: Core\Controller)
    $relative_class = substr($class, $len);

    // Normalisasi namespace ke path (ubah \ jadi /)
    $path = str_replace('\\', '/', $relative_class);

    // Pecah path untuk manipulasi folder
    $parts = explode('/', $path);
    $filename = array_pop($parts); // Ambil nama file (misal: Controller)
    $folder_path = implode('/', $parts); // Ambil sisa path folder (misal: Core)

    // Definisikan beberapa kemungkinan lokasi file untuk mengatasi masalah Case Sensitivity
    $paths_to_check = [
        // 1. Path Standar (Sesuai Namespace) -> app/Core/Controller.php
        $base_dir . $folder_path . '/' . $filename . '.php',

        // 2. Folder Lowercase (Paling sering dipakai di framework custom) -> app/core/Controller.php
        $base_dir . strtolower($folder_path) . '/' . $filename . '.php',

        // 3. Full Lowercase -> app/core/controller.php
        $base_dir . strtolower($folder_path) . '/' . strtolower($filename) . '.php'
    ];

    // Cek satu per satu
    foreach ($paths_to_check as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load Config & Helpers
if (file_exists(__DIR__ . '/../config/app.php'))
    require_once __DIR__ . '/../config/app.php';
if (file_exists(__DIR__ . '/../helpers/functions.php'))
    require_once __DIR__ . '/../helpers/functions.php';