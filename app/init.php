<?php

// Autoloader dengan Debugging & Support Folder Lowercase
spl_autoload_register(function ($class) {
    // Prefix Namespace
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';

    // Cek apakah class menggunakan prefix App\
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Ambil nama relative class (contoh: Core\App)
    $relative_class = substr($class, $len);
    
    // Ubah namespace menjadi path file (Core/App.php)
    $path = str_replace('\\', '/', $relative_class) . '.php';
    
    // Coba Load Path 1: Sesuai Namespace (misal: app/Core/App.php)
    $file = $base_dir . $path;
    
    // Coba Load Path 2: Folder Lowercase (misal: app/core/App.php) - Fix untuk folder 'core'
    $file_lower = $base_dir . strtolower(str_replace('\\', '/', dirname($relative_class))) . '/' . basename($path);

    if (file_exists($file)) {
        require $file;
    } else if (file_exists($file_lower)) {
        require $file_lower;
    } else {
        // Debugging: Uncomment baris bawah ini jika masih error untuk melihat path yang dicari
        // echo "Mencari file: $file <br> Atau: $file_lower <br>";
    }
});

// Helper constants
define('BASE_URL', 'http://localhost/trevio-project/public');

// Load config jika ada
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}