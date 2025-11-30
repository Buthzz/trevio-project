<?php

//fix
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
// Autoloader dengan Fix untuk Linux/VPS Case-Sensitivity
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
    
    // 1. Normalisasi namespace ke path (ubah \ jadi /)
    $path = str_replace('\\', '/', $relative_class);
    
    // Path 1: Sesuai Namespace (Capitalized) - Contoh: app/Core/App.php
    // Ini standar PSR-4 yang benar
    $file_psr4 = $base_dir . $path . '.php';
    
    // Path 2: Folder Lowercase (Lowercase) - Contoh: app/core/App.php
    // Ini fix untuk struktur folder lowercase kamu
    $folder = strtolower(dirname($path)); // Ambil folder dan kecilkan hurufnya
    $filename = basename($path);          // Ambil nama file (Case sensitive, biasanya tetap Capital)
    $file_lower = $base_dir . $folder . '/' . $filename . '.php';

    // Cek keberadaan file
    if (file_exists($file_psr4)) {
        require $file_psr4;
    } else if (file_exists($file_lower)) {
        require $file_lower;
    } else {
        // Debugging (Hapus // jika masih error untuk melihat apa yang dicari sistem)
        // echo "Class not found. Tried: <br>1. $file_psr4 <br>2. $file_lower <br>";
    }
});

// Load application configuration
if (file_exists(__DIR__ . '/../config/app.php')) {
    require_once __DIR__ . '/../config/app.php';
}

// Environment variables are loaded by config/app.php
// Helper functions can be loaded here if needed
if (file_exists(__DIR__ . '/../helpers/functions.php')) {
    require_once __DIR__ . '/../helpers/functions.php';
}