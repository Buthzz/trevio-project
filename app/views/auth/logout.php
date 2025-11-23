<?php
// Mulai sesi jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
require_once __DIR__ . '/../../../helpers/functions.php';

// Redirect ke halaman login
$loginUrl = trevio_view_route('auth/login.php');
header("Location: " . $loginUrl);
exit;
?>
