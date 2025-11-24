<?php
require_once __DIR__ . '/../../../helpers/functions.php';
// Mulai sesi jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// [SECURITY]: Verifikasi CSRF Token untuk logout
// Mencegah logout via CSRF attack (misal: image src="logout.php")
if (!isset($_GET['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
    // Jika token tidak valid, redirect ke home atau tampilkan error
    // Untuk UX, kita redirect ke home saja tanpa logout
    $homeUrl = (function_exists('trevio_view_route') ? trevio_view_route('home/index.php') : '../home/index.php');
    header("Location: $homeUrl");
    exit;
}

// Hapus semua variabel sesi
$_SESSION = [];

// Hapus cookie sesi jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi
session_destroy();

// Helper functions path (untuk trevio_view_route)


// Redirect ke halaman login
$loginUrl = trevio_view_route('auth/login.php');
header("Location: " . $loginUrl);
exit;
?>