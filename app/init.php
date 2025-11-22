<?php

//fix

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

// Helper constants
// Pastikan ini sesuai dengan path VPS kamu atau gunakan logic dinamis
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Sesuaikan folder project jika tidak di root domain
define('BASE_URL', $protocol . '://' . $host); 

// Load config jika ada
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}

// Load environment variables from .env file
// - Skips lines starting with #
// - Does not override existing $_ENV/$_SERVER values
// - Format: KEY=value (quotes not supported)
// - Security: Do not commit secrets in .env to version control
if (file_exists(__DIR__ . '/../.env')) {
    $lines = @file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        // Optionally log error or handle gracefully
        return;
    }
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip komentar
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (empty($name)) continue;
        // Remove surrounding quotes if present
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}